<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AnggotaJemaat;
use App\Models\SakramenBaptis;
use App\Models\SakramenSidi;

class SakramenController extends Controller
{
    /**
     * ==========================================
     * 1. MANAJEMEN BUKU BAPTIS
     * ==========================================
     */
    public function baptisIndex(Request $request)
    {
        $user = Auth::user();
        $query = SakramenBaptis::with(['anggotaJemaat.jemaat.klasis']);

        if ($user->hasRole('Admin Klasis')) {
            $query->whereHas('anggotaJemaat.jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->whereHas('anggotaJemaat', fn($q) => $q->where('jemaat_id', $user->jemaat_id));
        }

        if ($request->search) {
            $query->whereHas('anggotaJemaat', fn($q) => $q->where('nama_lengkap', 'like', "%{$request->search}%"))
                  ->orWhere('no_akta_baptis', 'like', "%{$request->search}%");
        }

        $baptis = $query->latest('tanggal_baptis')->paginate(15);

        // Dropdown: Hanya anggota Aktif yang BELUM memiliki tanggal_baptis
        $anggotaQuery = AnggotaJemaat::where('status_keanggotaan', 'Aktif')->whereNull('tanggal_baptis');
        if ($user->hasRole('Admin Klasis')) {
            $anggotaQuery->whereHas('jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        } elseif ($user->hasRole('Admin Jemaat')) {
            $anggotaQuery->where('jemaat_id', $user->jemaat_id);
        }
        $anggotaTanpaBaptis = $anggotaQuery->orderBy('nama_lengkap')->get();

        return view('admin.sakramen.baptis.index', compact('baptis', 'anggotaTanpaBaptis'));
    }

    public function baptisStore(Request $request)
    {
        $request->validate([
            'anggota_jemaat_id' => 'required|exists:anggota_jemaat,id',
            'no_akta_baptis'    => 'required|unique:sakramen_baptis,no_akta_baptis',
            'tanggal_baptis'    => 'required|date',
            'tempat_baptis'     => 'required|string',
            'pendeta_pelayan'   => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan ke Buku Besar Baptis
            SakramenBaptis::create($request->all());

            // 2. Update Profil Anggota (Sinkronisasi Otomatis)
            $anggota = AnggotaJemaat::findOrFail($request->anggota_jemaat_id);
            $anggota->update([
                'tanggal_baptis' => $request->tanggal_baptis,
                'tempat_baptis' => $request->tempat_baptis
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Data Baptisan berhasil dicatat & Profil Anggota telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mencatat: ' . $e->getMessage());
        }
    }

    /**
     * ==========================================
     * 2. MANAJEMEN BUKU SIDI
     * ==========================================
     */
    public function sidiIndex(Request $request)
    {
        $user = Auth::user();
        $query = SakramenSidi::with(['anggotaJemaat.jemaat']);

        if ($user->hasRole('Admin Klasis')) {
            $query->whereHas('anggotaJemaat.jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->whereHas('anggotaJemaat', fn($q) => $q->where('jemaat_id', $user->jemaat_id));
        }

        $sidi = $query->latest('tanggal_sidi')->paginate(15);

        // Dropdown: Anggota Aktif yang BELUM Sidi
        $anggotaQuery = AnggotaJemaat::where('status_keanggotaan', 'Aktif')->whereNull('tanggal_sidi');
        if ($user->hasRole('Admin Klasis')) {
            $anggotaQuery->whereHas('jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        } elseif ($user->hasRole('Admin Jemaat')) {
            $anggotaQuery->where('jemaat_id', $user->jemaat_id);
        }
        $anggotaTanpaSidi = $anggotaQuery->orderBy('nama_lengkap')->get();

        return view('admin.sakramen.sidi.index', compact('sidi', 'anggotaTanpaSidi'));
    }

    public function sidiStore(Request $request)
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

            // 1. Simpan ke Buku Besar Sidi
            SakramenSidi::create($request->all());

            // 2. Update Profil Anggota
            $anggota = AnggotaJemaat::findOrFail($request->anggota_jemaat_id);
            $anggota->update([
                'tanggal_sidi' => $request->tanggal_sidi,
                'tempat_sidi' => $request->tempat_sidi
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Pengakuan Iman (Sidi) berhasil dicatat & disinkronkan ke Profil.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}