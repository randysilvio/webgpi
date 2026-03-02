<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataAnggaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MataAnggaranController extends Controller
{
    /**
     * Menampilkan daftar Mata Anggaran (COA).
     */
    public function index(Request $request)
    {
        $query = MataAnggaran::query();

        // Filter Pencarian
        if ($request->filled('search')) {
            $query->where('nama_mata_anggaran', 'like', '%' . $request->search . '%')
                  ->orWhere('kode', 'like', '%' . $request->search . '%');
        }

        // Filter Jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $mataAnggarans = $query->orderBy('kode')->paginate(20)->withQueryString();

        return view('admin.perbendaharaan.mata_anggaran.index', compact('mataAnggarans'));
    }

    /**
     * Form tambah mata anggaran baru.
     */
    public function create()
    {
        return view('admin.perbendaharaan.mata_anggaran.create');
    }

    /**
     * Menyimpan mata anggaran baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:mata_anggaran,kode|max:20',
            'nama_mata_anggaran' => 'required|string|max:255',
            'jenis' => 'required|in:Pendapatan,Belanja',
            'kelompok' => 'nullable|string|max:100',
        ]);

        MataAnggaran::create($request->all());

        return redirect()->route('admin.perbendaharaan.mata-anggaran.index')
                         ->with('success', 'Mata Anggaran baru berhasil ditambahkan.');
    }

    /**
     * Form edit mata anggaran.
     */
    public function edit(MataAnggaran $mataAnggaran)
    {
        return view('admin.perbendaharaan.mata_anggaran.edit', compact('mataAnggaran'));
    }

    /**
     * Memperbarui data mata anggaran.
     */
    public function update(Request $request, MataAnggaran $mataAnggaran)
    {
        $request->validate([
            'kode' => ['required', 'string', 'max:20', Rule::unique('mata_anggaran')->ignore($mataAnggaran->id)],
            'nama_mata_anggaran' => 'required|string|max:255',
            'jenis' => 'required|in:Pendapatan,Belanja',
        ]);

        $mataAnggaran->update($request->all());

        return redirect()->route('admin.perbendaharaan.mata-anggaran.index')
                         ->with('success', 'Mata Anggaran berhasil diperbarui.');
    }

    /**
     * Menghapus mata anggaran (Soft Delete).
     */
    public function destroy(MataAnggaran $mataAnggaran)
    {
        // Cek jika sudah digunakan di transaksi/anggaran sebelum hapus (opsional)
        $mataAnggaran->delete();

        return redirect()->route('admin.perbendaharaan.mata-anggaran.index')
                         ->with('success', 'Mata Anggaran telah dinonaktifkan.');
    }
}