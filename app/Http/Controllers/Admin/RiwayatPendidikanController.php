<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiwayatPendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RiwayatPendidikanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenjang' => 'required|string',
            'nama_institusi' => 'required|string',
            'tahun_lulus' => 'required|integer',
            'file_ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('file_ijazah')) {
            $data['file_ijazah'] = $request->file('file_ijazah')->store('dokumen_pegawai/ijazah', 'public');
        }

        RiwayatPendidikan::create($data);

        return back()->with('success', 'Riwayat pendidikan ditambahkan.');
    }

    public function update(Request $request, RiwayatPendidikan $pendidikan)
    {
        $request->validate([
            'jenjang' => 'required|string',
            'nama_institusi' => 'required|string',
            'tahun_lulus' => 'required|integer',
            'file_ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('file_ijazah');

        if ($request->hasFile('file_ijazah')) {
            // Hapus file lama jika ada
            if ($pendidikan->file_ijazah) {
                Storage::disk('public')->delete($pendidikan->file_ijazah);
            }
            $data['file_ijazah'] = $request->file('file_ijazah')->store('dokumen_pegawai/ijazah', 'public');
        }

        $pendidikan->update($data);

        return back()->with('success', 'Riwayat pendidikan diperbarui.');
    }

    public function destroy(RiwayatPendidikan $pendidikan)
    {
        if ($pendidikan->file_ijazah) {
            Storage::disk('public')->delete($pendidikan->file_ijazah);
        }
        $pendidikan->delete();
        return back()->with('success', 'Riwayat pendidikan dihapus.');
    }
}