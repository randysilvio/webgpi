<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SakramenSidi;
use App\Models\AnggotaJemaat;
use App\Models\Setting;

class SakramenSidiController extends Controller
{
    /**
     * Menampilkan Daftar Sidi
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SakramenSidi::with(['anggotaJemaat.jemaat.klasis']);

        // Filter Wilayah
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $query->whereHas('anggotaJemaat.jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $query->whereHas('anggotaJemaat', fn($q) => $q->where('jemaat_id', $user->jemaat_id));
        }

        // Pencarian
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('anggotaJemaat', fn($sub) => $sub->where('nama_lengkap', 'like', "%{$request->search}%"))
                  ->orWhere('no_akta_sidi', 'like', "%{$request->search}%");
            });
        }

        $sidi = $query->latest('tanggal_sidi')->paginate(15);

        return view('admin.sakramen.sidi.index', compact('sidi'));
    }

    /**
     * Form Tambah
     */
    public function create()
    {
        $user = Auth::user();
        
        // Cari Anggota Aktif yg BELUM Sidi
        $anggotaQuery = AnggotaJemaat::where('status_keanggotaan', 'Aktif')
            ->whereNull('tanggal_sidi')
            ->doesntHave('dataSidi');

        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $anggotaQuery->whereHas('jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $anggotaQuery->where('jemaat_id', $user->jemaat_id);
        }

        $anggotaTanpaSidi = $anggotaQuery->with('jemaat:id,nama_jemaat')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'jemaat_id', 'tanggal_lahir']);

        return view('admin.sakramen.sidi.create', compact('anggotaTanpaSidi'));
    }

    /**
     * Simpan Data
     */
    public function store(Request $request)
    {
        $request->validate([
            'anggota_jemaat_id' => 'required|exists:anggota_jemaat,id',
            'no_akta_sidi'      => 'required|unique:sakramen_sidi,no_akta_sidi',
            'tanggal_sidi'      => 'required|date',
            'tempat_sidi'       => 'required|string',
            'pendeta_pelayan'   => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan Arsip
            SakramenSidi::create($request->all());

            // 2. Update Status Anggota
            $anggota = AnggotaJemaat::findOrFail($request->anggota_jemaat_id);
            $anggota->update([
                'tanggal_sidi' => $request->tanggal_sidi,
                'tempat_sidi' => $request->tempat_sidi
            ]);

            DB::commit();
            return redirect()->route('admin.sakramen.sidi.index')->with('success', 'Data Sidi berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Form Edit
     */
    public function edit($id)
    {
        $sidi = SakramenSidi::findOrFail($id);
        return view('admin.sakramen.sidi.edit', compact('sidi'));
    }

    /**
     * Update Data
     */
    public function update(Request $request, $id)
    {
        $sidi = SakramenSidi::findOrFail($id);

        $request->validate([
            'no_akta_sidi'    => 'required|unique:sakramen_sidi,no_akta_sidi,'.$id,
            'tanggal_sidi'    => 'required|date',
            'tempat_sidi'     => 'required|string',
            'pendeta_pelayan' => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            
            $sidi->update($request->all());

            // Sinkronisasi ke Profil Anggota
            $anggota = AnggotaJemaat::find($sidi->anggota_jemaat_id);
            if($anggota) {
                $anggota->update([
                    'tanggal_sidi' => $request->tanggal_sidi,
                    'tempat_sidi'  => $request->tempat_sidi
                ]);
            }

            DB::commit();
            return redirect()->route('admin.sakramen.sidi.index')->with('success', 'Data berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Data
     */
    public function destroy($id)
    {
        $sidi = SakramenSidi::findOrFail($id);
        $anggota = AnggotaJemaat::find($sidi->anggota_jemaat_id);

        if ($anggota) {
            $anggota->update([
                'tanggal_sidi' => null,
                'tempat_sidi'  => null
            ]);
        }

        $sidi->delete();
        return redirect()->route('admin.sakramen.sidi.index')->with('success', 'Data dihapus.');
    }

    /**
     * Cetak Sertifikat
     */
    public function cetakSurat($id)
    {
        $data = SakramenSidi::with(['anggotaJemaat.jemaat.klasis'])->findOrFail($id);
        $setting = Setting::first();

        // Pastikan file view ada di folder: resources/views/admin/sakramen/cetak/sidi.blade.php
        return view('admin.sakramen.cetak.sidi', compact('data', 'setting'));
    }
}