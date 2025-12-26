<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KeluargaPegawai;
use Illuminate\Http\Request;

class KeluargaPegawaiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'nama_lengkap' => 'required|string|max:255',
            'hubungan' => 'required|in:Suami,Istri,Anak',
            'tanggal_lahir' => 'nullable|date',
            'status_tunjangan' => 'boolean',
        ]);

        KeluargaPegawai::create($request->all());

        return back()->with('success', 'Data keluarga berhasil ditambahkan.');
    }

    public function update(Request $request, KeluargaPegawai $keluarga)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'hubungan' => 'required|in:Suami,Istri,Anak',
            'tanggal_lahir' => 'nullable|date',
        ]);

        // Checkbox handling (jika tidak dicentang, kirim false)
        $data = $request->all();
        $data['status_tunjangan'] = $request->has('status_tunjangan');

        $keluarga->update($data);

        return back()->with('success', 'Data keluarga diperbarui.');
    }

    public function destroy(KeluargaPegawai $keluarga)
    {
        $keluarga->delete();
        return back()->with('success', 'Data keluarga dihapus.');
    }
}