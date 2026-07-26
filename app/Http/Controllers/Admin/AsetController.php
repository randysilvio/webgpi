<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsetGereja;
use App\Models\Klasis;
use App\Models\Jemaat;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AsetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar inventaris aset dengan filter dan statistik.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. Query Dasar
        $query = AsetGereja::with(['klasis', 'jemaat']);

        // 2. Scoping Data Wilayah (RBAC)
        if ($user->hasRole('Admin Klasis')) {
            $query->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        // 3. Filter (Kategori, Kondisi, Pencarian)
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_aset', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_aset', 'like', '%' . $request->search . '%');
            });
        }

        // --- 4. HITUNG STATISTIK (SINKRON DENGAN BLADE INDEX BARU) ---
        $statsQuery = clone $query;
        $stats = $statsQuery->reorder()->selectRaw('
            count(*) as total_item,
            sum(nilai_perolehan) as total_nilai,
            sum(case when kondisi = "Baik" then 1 else 0 end) as total_baik,
            sum(case when kondisi != "Baik" then 1 else 0 end) as total_rusak
        ')->first();

        // Mapping variabel statistik ke UI Blade
        $totalAset = $stats->total_item ?? 0;
        $totalNilaiAset = $stats->total_nilai ?? 0;
        $asetBaik = $stats->total_baik ?? 0;
        $asetRusak = $stats->total_rusak ?? 0;

        // 5. Ambil Data Tabel (Sorting diterapkan di sini)
        $asets = $query->latest('tanggal_perolehan')->paginate(15)->withQueryString();

        // Sesuaikan path view dengan struktur folder Anda (admin.aset.index atau admin.perbendaharaan.aset.index)
        return view('admin.aset.index', compact('asets', 'totalAset', 'totalNilaiAset', 'asetBaik', 'asetRusak'));
    }

    /**
     * FITUR BARU: Cetak Laporan Seluruh Aset (Sesuai Filter)
     */
    public function cetakSemua(Request $request)
    {
        $user = Auth::user();
        $query = AsetGereja::with(['klasis', 'jemaat']);

        // Keamanan Wilayah
        if ($user->hasRole('Admin Klasis')) {
            $query->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        // Terapkan Filter yang sama seperti Index
        if ($request->filled('kategori')) $query->where('kategori', $request->kategori);
        if ($request->filled('kondisi')) $query->where('kondisi', $request->kondisi);
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_aset', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_aset', 'like', '%' . $request->search . '%');
            });
        }

        $asets = $query->orderBy('kategori')->latest('tanggal_perolehan')->get();
        $setting = Setting::firstOrCreate(['id' => 1]);

        // Gunakan view cetak yang Anda lampirkan (aset.blade.php)
        // Jika nama filenya "cetak_semua.blade.php", ubah parameter di bawah
        return view('admin.aset.cetak_semua', compact('asets', 'setting')); 
    }

    /**
     * FITUR BARU: Cetak Label / Bukti Aset Spesifik
     */
    public function cetakLabel(AsetGereja $aset)
    {
        $user = Auth::user();
        
        // Keamanan: Tolak jika mencoba cetak aset wilayah lain
        if ($user->hasRole('Admin Klasis') && $aset->klasis_id != $user->klasis_id) abort(403);
        if ($user->hasRole('Admin Jemaat') && $aset->jemaat_id != $user->jemaat_id) abort(403);

        $setting = Setting::firstOrCreate(['id' => 1]);
        
        return view('admin.aset.cetak_label', compact('aset', 'setting'));
    }

    /**
     * Form tambah aset baru.
     */
    public function create()
    {
        $user = Auth::user();
        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        
        if ($user->hasRole('Admin Klasis')) {
            $klasisOptions = Klasis::where('id', $user->klasis_id)->pluck('nama_klasis', 'id');
        }

        return view('admin.aset.create', compact('klasisOptions'));
    }

    /**
     * Menyimpan data aset baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Otorisasi Otomatis agar Jemaat/Klasis tidak memanipulasi wilayah
        if ($user->hasRole('Admin Jemaat')) {
            $request->merge([
                'jemaat_id' => $user->jemaat_id,
                'klasis_id' => Jemaat::find($user->jemaat_id)->klasis_id ?? null,
            ]);
        } elseif ($user->hasRole('Admin Klasis')) {
            $request->merge(['klasis_id' => $user->klasis_id]);
        }

        $request->validate([
            'nama_aset' => 'required|string|max:255',
            'kategori' => 'required',
            'kondisi' => 'required',
            'nilai_perolehan' => 'nullable|numeric',
            'file_dokumen' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'foto_aset' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('file_dokumen')) {
            $data['file_dokumen_path'] = $request->file('file_dokumen')->store('dokumen_aset', 'public');
        }

        if ($request->hasFile('foto_aset')) {
            $data['foto_aset_path'] = $request->file('foto_aset')->store('foto_aset', 'public');
        }

        // Auto Generate Kode Aset jika kosong
        if (!$request->filled('kode_aset')) {
            $data['kode_aset'] = 'AST-' . strtoupper(Str::random(6));
        }

        AsetGereja::create($data);

        return redirect()->route('admin.perbendaharaan.aset.index')->with('success', 'Buku Aset berhasil dicatat.');
    }

    /**
     * Menampilkan detail aset.
     */
    public function show(AsetGereja $aset)
    {
        $user = Auth::user();
        if ($user->hasRole('Admin Klasis') && $aset->klasis_id != $user->klasis_id) abort(403);
        if ($user->hasRole('Admin Jemaat') && $aset->jemaat_id != $user->jemaat_id) abort(403);

        return view('admin.aset.show', compact('aset'));
    }

    /**
     * Form edit aset.
     */
    public function edit(AsetGereja $aset)
    {
        $user = Auth::user();
        
        // Pengecekan Hak Akses
        if ($user->hasRole('Admin Klasis') && $aset->klasis_id != $user->klasis_id) abort(403);
        if ($user->hasRole('Admin Jemaat') && $aset->jemaat_id != $user->jemaat_id) abort(403);

        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        $jemaatOptions = Jemaat::where('klasis_id', $aset->klasis_id)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
        
        if ($user->hasRole('Admin Klasis')) {
            $klasisOptions = Klasis::where('id', $user->klasis_id)->pluck('nama_klasis', 'id');
        }

        return view('admin.aset.edit', compact('aset', 'klasisOptions', 'jemaatOptions'));
    }

    /**
     * Memperbarui data aset.
     */
    public function update(Request $request, AsetGereja $aset)
    {
        $user = Auth::user();

        // Otorisasi Paksa (Mencegah injeksi manipulasi form via inspect element)
        if ($user->hasRole('Admin Jemaat')) {
            $request->merge([
                'jemaat_id' => $user->jemaat_id,
                'klasis_id' => Jemaat::find($user->jemaat_id)->klasis_id ?? null,
            ]);
        } elseif ($user->hasRole('Admin Klasis')) {
            $request->merge(['klasis_id' => $user->klasis_id]);
        }

        $request->validate([
            'nama_aset' => 'required|string|max:255',
            'kategori' => 'required',
            'kondisi' => 'required',
            'nilai_perolehan' => 'nullable|numeric',
            'file_dokumen' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'foto_aset' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        // Update File Dokumen
        if ($request->hasFile('file_dokumen')) {
            if ($aset->file_dokumen_path) Storage::disk('public')->delete($aset->file_dokumen_path);
            $data['file_dokumen_path'] = $request->file('file_dokumen')->store('dokumen_aset', 'public');
        }

        // Update Foto
        if ($request->hasFile('foto_aset')) {
            if ($aset->foto_aset_path) Storage::disk('public')->delete($aset->foto_aset_path);
            $data['foto_aset_path'] = $request->file('foto_aset')->store('foto_aset', 'public');
        }

        $aset->update($data);

        return redirect()->route('admin.perbendaharaan.aset.show', $aset->id)->with('success', 'Data aset diperbarui.');
    }

    /**
     * Menghapus aset (Soft Delete).
     */
    public function destroy(AsetGereja $aset)
    {
        $user = Auth::user();
        if ($user->hasRole('Admin Klasis') && $aset->klasis_id != $user->klasis_id) abort(403);
        if ($user->hasRole('Admin Jemaat') && $aset->jemaat_id != $user->jemaat_id) abort(403);

        // Opsi: Jika ingin langsung menghapus file dari storage (Hard Delete)
        // if ($aset->foto_aset_path) Storage::disk('public')->delete($aset->foto_aset_path);
        // if ($aset->file_dokumen_path) Storage::disk('public')->delete($aset->file_dokumen_path);
        
        $aset->delete();
        return redirect()->route('admin.perbendaharaan.aset.index')->with('success', 'Arsip Aset berhasil dimusnahkan.');
    }
}