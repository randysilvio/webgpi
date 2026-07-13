<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KeluargaPegawai;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeluargaPegawaiController extends Controller
{
    public function __construct()
    {
        // 1. Amankan dengan Middleware Auth Dasar
        $this->middleware(['auth']);
    }

    /**
     * Memverifikasi apakah User saat ini berhak mengubah data pegawai tersebut.
     * (Mencegah Admin Klasis A mengubah data pegawai di Klasis B).
     */
    private function checkAccessPermission($pegawaiId)
    {
        $user = Auth::user();
        
        // Super Admin bebas
        if ($user->hasRole('Super Admin') || $user->hasRole('Admin Bidang 3')) {
            return true;
        }

        $pegawai = Pegawai::findOrFail($pegawaiId);

        if ($user->hasRole('Admin Klasis') && $pegawai->klasis_id != $user->klasis_id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang atas pegawai di klasis ini.');
        }

        if ($user->hasRole('Admin Jemaat') && $pegawai->jemaat_id != $user->jemaat_id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang atas pegawai di jemaat ini.');
        }

        return true;
    }

    public function store(Request $request)
    {
        // Pastikan pengguna berhak mengubah data keluarga pegawai ini
        $this->checkAccessPermission($request->pegawai_id);

        $validatedData = $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'nama_lengkap' => 'required|string|max:255',
            'hubungan' => 'required|in:Suami,Istri,Anak,Lainnya',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        // Celah Checkbox Diperbaiki:
        $validatedData['status_tunjangan'] = $request->has('status_tunjangan') ? true : false;

        KeluargaPegawai::create($validatedData);

        return back()->with('success', 'Data tanggungan keluarga berhasil ditambahkan ke profil Pegawai.');
    }

    public function update(Request $request, KeluargaPegawai $keluarga)
    {
        // Pastikan pengguna berhak mengubah data keluarga pegawai ini
        $this->checkAccessPermission($keluarga->pegawai_id);

        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'hubungan' => 'required|in:Suami,Istri,Anak,Lainnya',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        // Checkbox handling yang aman
        $validatedData['status_tunjangan'] = $request->has('status_tunjangan') ? true : false;

        $keluarga->update($validatedData);

        return back()->with('success', 'Modifikasi data tanggungan keluarga berhasil disimpan.');
    }

    public function destroy(KeluargaPegawai $keluarga)
    {
        // Pastikan pengguna berhak menghapus data keluarga pegawai ini
        $this->checkAccessPermission($keluarga->pegawai_id);

        $keluarga->delete();
        
        return back()->with('success', 'Data tanggungan keluarga telah dihapus dari sistem.');
    }
}