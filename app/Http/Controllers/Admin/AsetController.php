<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsetGereja;
use App\Models\Klasis;
use App\Models\Jemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AsetController extends Controller
{
    /**
     * Menampilkan daftar inventaris aset dengan filter dan statistik.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. Query Dasar (TANPA latest() di sini agar aman untuk statistik)
        $query = AsetGereja::with(['klasis', 'jemaat']);

        // 2. Scoping Data Wilayah (RBAC)
        if ($user->hasRole('Admin Klasis')) {
            $query->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        // 3. Filter (Search, Kategori, Kondisi)
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

        // --- 4. HITUNG STATISTIK (SAFE MODE) ---
        // Clone query yang sudah terfilter, lalu hapus sorting dengan reorder()
        // Ini mencegah error "Mixing of GROUP columns"
        $statsQuery = clone $query;
        $stats = $statsQuery->reorder()->selectRaw('
            count(*) as total_item,
            sum(nilai_perolehan) as total_nilai,
            sum(case when kondisi = "Baik" then 1 else 0 end) as total_baik,
            sum(case when kondisi != "Baik" then 1 else 0 end) as total_rusak
        ')->first();

        // 5. Ambil Data Tabel (Baru kita terapkan sorting latest() di sini)
        $asets = $query->latest('tanggal_perolehan')->paginate(15)->withQueryString();

        return view('admin.aset.index', compact('asets', 'stats'));
    }

    /**
     * Form tambah aset baru.
     */
    public function create()
    {
        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        return view('admin.aset.create', compact('klasisOptions'));
    }

    /**
     * Menyimpan data aset baru.
     */
    public function store(Request $request)
    {
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

        return redirect()->route('admin.perbendaharaan.aset.index')->with('success', 'Aset berhasil dicatat.');
    }

    /**
     * Menampilkan detail aset.
     */
    public function show(AsetGereja $aset)
    {
        return view('admin.aset.show', compact('aset'));
    }

    /**
     * Form edit aset.
     */
    public function edit(AsetGereja $aset)
    {
        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        $jemaatOptions = Jemaat::where('klasis_id', $aset->klasis_id)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
        
        return view('admin.aset.edit', compact('aset', 'klasisOptions', 'jemaatOptions'));
    }

    /**
     * Memperbarui data aset.
     */
    public function update(Request $request, AsetGereja $aset)
    {
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
        // Hapus file fisik jika diperlukan (opsional, tergantung kebijakan soft delete)
        // if ($aset->foto_aset_path) Storage::disk('public')->delete($aset->foto_aset_path);
        
        $aset->delete();
        return redirect()->route('admin.perbendaharaan.aset.index')->with('success', 'Aset berhasil dihapus.');
    }
}