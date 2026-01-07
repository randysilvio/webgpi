<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klasis;
use App\Models\Pegawai; 
use App\Models\Jemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KlasisExport;
use App\Imports\KlasisImport;
use Maatwebsite\Excel\Validators\ValidationException;

class KlasisController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('role:Super Admin|Admin Bidang 3')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Klasis::withCount('jemaat'); 

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('nama_klasis', 'like', $search)
                  ->orWhere('kode_klasis', 'like', $search)
                  ->orWhere('pusat_klasis', 'like', $search);
            });
        }

        // Statistik (Safe Mode)
        $statsQuery = Klasis::query();
        $stats = $statsQuery->reorder()->selectRaw('
            count(*) as total_klasis,
            (select count(*) from jemaat) as total_jemaat,
            (select count(*) from jemaat where status_jemaat = "Mandiri") as total_jemaat_mandiri,
            (select count(*) from jemaat where status_jemaat != "Mandiri") as total_jemaat_pos
        ')->first();

        $klasisData = $query->orderBy('kode_klasis', 'asc')->paginate(15)->withQueryString();

        return view('admin.klasis.index', compact('klasisData', 'stats', 'request'));
    }

    public function create()
    {
        $pendetaOptions = Pegawai::pendeta()->orderBy('nama_lengkap')->pluck('nama_lengkap', 'id');
        return view('admin.klasis.create', compact('pendetaOptions'));
    }

    public function store(Request $request)
    {
        // Validasi LENGKAP untuk semua kolom database termasuk Peta
        $validatedData = $request->validate([
            'nama_klasis' => 'required|string|max:255',
            'kode_klasis' => 'required|string|max:50|unique:klasis,kode_klasis',
            'pusat_klasis' => 'nullable|string|max:100',
            'alamat_kantor' => 'nullable|string',
            'koordinat_gps' => 'nullable|string|max:100',
            
            // --- UPDATE MINOR: VALIDASI DATA PETA ---
            'kabupaten_kota' => 'nullable|string', // Penting untuk warna
            'latitude' => 'nullable|string',       // Penting untuk pin
            'longitude' => 'nullable|string',      // Penting untuk pin
            'warna_peta' => 'nullable|string',     // Penting untuk visualisasi
            // ----------------------------------------

            'wilayah_pelayanan' => 'nullable|string',
            'tanggal_pembentukan' => 'nullable|date',
            'nomor_sk_pembentukan' => 'nullable|string|max:100',
            'klasis_induk' => 'nullable|string|max:100', 
            'sejarah_singkat' => 'nullable|string',
            'email_klasis' => 'nullable|email|max:255',
            'telepon_kantor' => 'nullable|string|max:50',
            'website_klasis' => 'nullable|url',
            'ketua_mpk_pendeta_id' => 'nullable|exists:pegawai,id', 
            'foto_kantor_path' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto_kantor_path')) {
            $validatedData['foto_kantor_path'] = $request->file('foto_kantor_path')->store('klasis_photos', 'public');
        }

        Klasis::create($validatedData);

        return redirect()->route('admin.klasis.index')->with('success', 'Data Klasis berhasil ditambahkan.');
    }

    public function show(Klasis $klasis)
    {
        $klasis->load(['jemaat', 'ketuaMp']);
        return view('admin.klasis.show', compact('klasis'));
    }

    public function edit(Klasis $klasis)
    {
        $pendetaOptions = Pegawai::pendeta()->orderBy('nama_lengkap')->pluck('nama_lengkap', 'id');
        return view('admin.klasis.edit', compact('klasis', 'pendetaOptions'));
    }

    public function update(Request $request, Klasis $klasis)
    {
        // Validasi LENGKAP termasuk Peta
        $validatedData = $request->validate([
            'nama_klasis' => 'required|string|max:255',
            'kode_klasis' => 'required|string|max:50|unique:klasis,kode_klasis,' . $klasis->id,
            'pusat_klasis' => 'nullable|string|max:100',
            'alamat_kantor' => 'nullable|string',
            'koordinat_gps' => 'nullable|string|max:100',
            
            // --- UPDATE MINOR: VALIDASI DATA PETA ---
            'kabupaten_kota' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'warna_peta' => 'nullable|string',
            // ----------------------------------------

            'wilayah_pelayanan' => 'nullable|string',
            'tanggal_pembentukan' => 'nullable|date',
            'nomor_sk_pembentukan' => 'nullable|string|max:100',
            'klasis_induk' => 'nullable|string|max:100',
            'sejarah_singkat' => 'nullable|string',
            'email_klasis' => 'nullable|email|max:255',
            'telepon_kantor' => 'nullable|string|max:50',
            'website_klasis' => 'nullable|url',
            'ketua_mpk_pendeta_id' => 'nullable|exists:pegawai,id',
            'foto_kantor_path' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto_kantor_path')) {
            if ($klasis->foto_kantor_path && Storage::disk('public')->exists($klasis->foto_kantor_path)) {
                Storage::disk('public')->delete($klasis->foto_kantor_path);
            }
            $validatedData['foto_kantor_path'] = $request->file('foto_kantor_path')->store('klasis_photos', 'public');
        }

        $klasis->update($validatedData);

        return redirect()->route('admin.klasis.index')->with('success', 'Data Klasis berhasil diperbarui.');
    }

    public function destroy(Klasis $klasis)
    {
        if ($klasis->jemaat()->count() > 0) {
            return back()->with('error', 'Gagal hapus: Klasis ini masih memiliki Jemaat.');
        }
        
        if ($klasis->foto_kantor_path && Storage::disk('public')->exists($klasis->foto_kantor_path)) {
            Storage::disk('public')->delete($klasis->foto_kantor_path);
        }
        
        $klasis->delete();
        return redirect()->route('admin.klasis.index')->with('success', 'Data Klasis berhasil dihapus.');
    }
    
    // --- IMPORT / EXPORT ---
    public function showImportForm() { return view('admin.klasis.import'); }
    
    public function import(Request $request) {
        $request->validate(['import_file' => 'required|file|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new KlasisImport, $request->file('import_file'));
            return redirect()->route('admin.klasis.index')->with('success', 'Data Klasis berhasil diimpor.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
    
    public function export() {
        return Excel::download(new KlasisExport, 'data_klasis_' . date('Ymd_His') . '.xlsx');
    }
}