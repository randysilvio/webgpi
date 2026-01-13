<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiwayatSk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RiwayatSkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'nomor_sk' => 'required|string',
            'jenis_sk' => 'required|string',
            'tmt_sk' => 'required|date',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('file_sk')) {
            $data['file_sk'] = $request->file('file_sk')->store('dokumen_pegawai/sk', 'public');
        }

        RiwayatSk::create($data);

        return back()->with('success', 'Riwayat SK/Kepangkatan ditambahkan.');
    }

    public function update(Request $request, RiwayatSk $sk)
    {
        $request->validate([
            'nomor_sk' => 'required|string',
            'jenis_sk' => 'required|string',
            'tmt_sk' => 'required|date',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('file_sk');

        if ($request->hasFile('file_sk')) {
            if ($sk->file_sk) {
                Storage::disk('public')->delete($sk->file_sk);
            }
            $data['file_sk'] = $request->file('file_sk')->store('dokumen_pegawai/sk', 'public');
        }

        $sk->update($data);

        return back()->with('success', 'Riwayat SK diperbarui.');
    }

    public function destroy(RiwayatSk $sk)
    {
        if ($sk->file_sk) {
            Storage::disk('public')->delete($sk->file_sk);
        }
        $sk->delete();
        return back()->with('success', 'Riwayat SK dihapus.');
    }
}