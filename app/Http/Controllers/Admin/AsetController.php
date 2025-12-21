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
     * Menampilkan daftar inventaris aset dengan filter.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AsetGereja::with(['klasis', 'jemaat']);

        // --- Scoping Data Wilayah ---
        if ($user->hasRole('Admin Klasis')) {
            $query->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        // --- Filter Pencarian ---
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_aset', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_aset', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        $asets = $query->latest()->paginate(15)->withQueryString();

        return view('admin.aset.index', compact('asets'));
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
        $aset->delete();
        return redirect()->route('admin.perbendaharaan.aset.index')->with('success', 'Aset berhasil dihapus.');
    }
}