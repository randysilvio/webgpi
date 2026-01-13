<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jemaat;
use App\Models\Klasis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JemaatExport;
use App\Imports\JemaatImport;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\FromCollection;

class JemaatController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        // Middleware Role/Permission
        $this->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis|Admin Jemaat')->only(['index', 'show']);
        $this->middleware('role:Super Admin|Admin Bidang 3')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Query Dasar & Eager Loading
        $query = Jemaat::with('klasis'); 
        $user = Auth::user();

        // 2. Filter RBAC (Role Based Access Control)
        if ($user->hasRole('Admin Klasis')) {
            $klasisId = $user->klasis_id;
            if ($klasisId) {
                $query->where('klasis_id', $klasisId);
            } else {
                // Admin Klasis tapi tidak punya wilayah = Data Kosong
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->hasRole('Admin Jemaat')) {
            $jemaatId = $user->jemaat_id;
             if ($jemaatId) {
                 $query->where('id', $jemaatId);
             } else {
                 $query->whereRaw('1 = 0');
             }
        }

        // 3. Filter Dropdown (Jika user memilih klasis tertentu di UI)
        if ($request->filled('klasis_id')) {
            $klasisFilterId = $request->klasis_id;
            // Validasi: Admin Klasis tidak boleh filter klasis orang lain
            if ($user->hasRole('Admin Klasis') && $user->klasis_id != $klasisFilterId) {
                 // Abaikan filter ilegal
            } else {
                 $query->where('klasis_id', $klasisFilterId);
            }
        }

        // 4. HITUNG STATISTIK (Safe Mode - 1 Query Aggregation)
        // Statistik ini dinamis, tapi saat ini masih mengambil dari kolom tabel jemaat.
        // Nanti jika tabel AnggotaJemaat sudah terisi, logic ini bisa diubah.
        $statsQuery = clone $query;
        $stats = $statsQuery->reorder()->selectRaw('
            count(*) as total,
            sum(case when status_jemaat = "Mandiri" then 1 else 0 end) as total_mandiri,
            sum(case when status_jemaat = "Bakal Jemaat" then 1 else 0 end) as total_bakal,
            sum(case when status_jemaat = "Pos Pelayanan" then 1 else 0 end) as total_pos,
            sum(jumlah_total_jiwa) as total_jiwa
        ')->first();

        // 5. Pencarian (Search)
         if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                 $q->where('nama_jemaat', 'like', $searchTerm)
                   ->orWhere('kode_jemaat', 'like', $searchTerm);
             });
         }

        // 6. Pagination
        $jemaatData = $query->latest()->paginate(15)->appends($request->query());

        // 7. Data untuk Dropdown Filter
        $klasisFilterOptions = collect();
        if ($user->hasAnyRole(['Super Admin', 'Admin Bidang 3'])) {
            $klasisFilterOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        } elseif ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $klasisFilterOptions = Klasis::where('id', $user->klasis_id)->pluck('nama_klasis', 'id');
        }

        return view('admin.jemaat.index', compact('jemaatData', 'klasisFilterOptions', 'request', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $klasisOptionsQuery = Klasis::orderBy('nama_klasis');
        $user = Auth::user();

        // Batasi pilihan Klasis sesuai hak akses
        if ($user->hasRole('Admin Klasis')) {
             if ($user->klasis_id) {
                $klasisOptionsQuery->where('id', $user->klasis_id);
             } else {
                  return redirect()->route('admin.dashboard')->with('error', 'Akun Anda tidak terhubung ke Klasis manapun.');
             }
        }

        $klasisOptions = $klasisOptionsQuery->pluck('nama_klasis', 'id');

        return view('admin.jemaat.create', compact('klasisOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_jemaat' => 'required|string|max:255',
            'kode_jemaat' => 'nullable|string|max:50|unique:jemaat,kode_jemaat',
            'klasis_id' => 'required|exists:klasis,id',
            'alamat_gereja' => 'nullable|string',
            'status_jemaat' => 'required|in:Mandiri,Bakal Jemaat,Pos Pelayanan',
            'jenis_jemaat' => 'required|in:Umum,Kategorial',
            // Jumlah Jiwa & KK opsional (bisa diisi 0)
            'jumlah_kk' => 'nullable|integer|min:0',
            'jumlah_total_jiwa' => 'nullable|integer|min:0',
            'foto_gereja_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Auto Generate Kode Jemaat jika kosong
        if (empty($validatedData['kode_jemaat'])) {
            $validatedData['kode_jemaat'] = 'J-' . date('Y') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        }

        // Cek Hak Akses Admin Klasis
        $user = Auth::user();
        if ($user->hasRole('Admin Klasis') && $user->klasis_id != $validatedData['klasis_id']) {
            return back()->with('error', 'Anda tidak diizinkan menambah Jemaat untuk Klasis lain.');
        }

        if ($request->hasFile('foto_gereja_path')) {
            $validatedData['foto_gereja_path'] = $request->file('foto_gereja_path')->store('jemaat_photos', 'public');
        }

        Jemaat::create($validatedData);

        return redirect()->route('admin.jemaat.index')->with('success', 'Data Jemaat berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Jemaat $jemaat)
    {
        // Security Check
        $user = Auth::user();
        if ($user->hasRole('Admin Klasis') && $jemaat->klasis_id != $user->klasis_id) abort(403);
        if ($user->hasRole('Admin Jemaat') && $jemaat->id != $user->jemaat_id) abort(403);

        $jemaat->load(['klasis', 'anggotaJemaat', 'pendetaDitempatkan']); 
        return view('admin.jemaat.show', compact('jemaat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jemaat $jemaat)
    {
        $user = Auth::user();
        if ($user->hasRole('Admin Klasis') && $jemaat->klasis_id != $user->klasis_id) abort(403);
        
        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        
        // Filter opsi klasis untuk admin klasis
        if ($user->hasRole('Admin Klasis')) {
             $klasisOptions = Klasis::where('id', $user->klasis_id)->pluck('nama_klasis', 'id');
        }

        return view('admin.jemaat.edit', compact('jemaat', 'klasisOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jemaat $jemaat)
    {
        $user = Auth::user();
        if ($user->hasRole('Admin Klasis') && $jemaat->klasis_id != $user->klasis_id) abort(403);

        $validatedData = $request->validate([
            'nama_jemaat' => 'required|string|max:255',
            'kode_jemaat' => 'nullable|string|max:50|unique:jemaat,kode_jemaat,' . $jemaat->id,
            'klasis_id' => 'required|exists:klasis,id',
            'alamat_gereja' => 'nullable|string',
            'status_jemaat' => 'required|in:Mandiri,Bakal Jemaat,Pos Pelayanan',
            'jenis_jemaat' => 'required|in:Umum,Kategorial',
            'jumlah_kk' => 'nullable|integer|min:0',
            'jumlah_total_jiwa' => 'nullable|integer|min:0',
            'foto_gereja_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto_gereja_path')) {
            if ($jemaat->foto_gereja_path && Storage::disk('public')->exists($jemaat->foto_gereja_path)) {
                Storage::disk('public')->delete($jemaat->foto_gereja_path);
            }
            $validatedData['foto_gereja_path'] = $request->file('foto_gereja_path')->store('jemaat_photos', 'public');
        }

        $jemaat->update($validatedData);

        return redirect()->route('admin.jemaat.index')->with('success', 'Data Jemaat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jemaat $jemaat)
    {
         // Hanya Super Admin atau Bidang 3 yang boleh hapus
         $user = Auth::user();
         if (!$user->hasAnyRole(['Super Admin', 'Admin Bidang 3'])) abort(403);

         if ($jemaat->foto_gereja_path && Storage::disk('public')->exists($jemaat->foto_gereja_path)) {
             Storage::disk('public')->delete($jemaat->foto_gereja_path);
         }
         
         $jemaat->delete();
         return redirect()->route('admin.jemaat.index')->with('success', 'Data Jemaat berhasil dihapus.');
    }

    // --- IMPORT & EXPORT ---

    public function showImportForm()
    {
         return view('admin.jemaat.import');
    }

    public function import(Request $request)
    {
        $request->validate(['import_file' => 'required|file|mimes:xlsx,xls,csv']);

        try {
            $user = Auth::user();
            // Jika Admin Klasis, kirim ID klasisnya agar file import ter-scope
            $klasisId = $user->hasRole('Admin Klasis') ? $user->klasis_id : null;
            
            $import = new JemaatImport($klasisId); 
            Excel::import($import, $request->file('import_file'));

            // Cek jika ada baris yang gagal
            $failures = $import->failures();
            if ($failures->isNotEmpty()) {
                $count = $failures->count();
                return redirect()->route('admin.jemaat.index')->with('warning', "Import selesai namun {$count} data gagal diproses (Cek format Klasis Lama).");
            }

            return redirect()->route('admin.jemaat.index')->with('success', 'Data Jemaat berhasil diimpor & Kode baru telah dibuat.');

        } catch (ValidationException $e) {
             return back()->with('error', 'Format file tidak sesuai: ' . $e->getMessage());
        } catch (\Exception $e) {
             return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
    
    public function export(Request $request)
    {
        $user = Auth::user();
        $klasisId = $user->hasRole('Admin Klasis') ? $user->klasis_id : null;
        return Excel::download(new JemaatExport($klasisId), 'data_jemaat_' . date('Ymd_His') . '.xlsx');
    }
}