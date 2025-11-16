<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Klasis;
use App\Models\Jemaat;
use App\Models\Pendeta;
use App\Models\AnggotaJemaat;
use App\Models\WadahKategorialAnggaran;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Inisialisasi Data Statistik
        $stats = [
            'klasis' => 0,
            'jemaat' => 0,
            'anggota' => 0,
            'pendeta' => 0,
            'keuangan_target' => 0,
            'keuangan_realisasi' => 0,
        ];

        // 1. Statistik Klasis (Hanya relevan untuk level Sinode)
        if ($user->hasRole(['Super Admin', 'Admin Sinode'])) {
            $stats['klasis'] = Klasis::count();
        } elseif ($user->hasRole('Admin Klasis')) {
            $stats['klasis'] = 1; // Klasis dia sendiri
        }

        // 2. Statistik Jemaat & Anggota
        // Query Builder Dasar
        $jemaatQuery = Jemaat::query();
        $anggotaQuery = AnggotaJemaat::query()->where('status_anggota', 'aktif'); // Asumsi ada status aktif
        $pendetaQuery = Pendeta::query()->where('status_kepegawaian', 'aktif');
        $keuanganQuery = WadahKategorialAnggaran::query()->where('tahun_anggaran', date('Y'));

        // Filter Berdasarkan Role (Scoping)
        if ($user->hasRole(['Super Admin', 'Admin Sinode'])) {
            // Tidak ada filter, ambil semua
        } elseif ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $jemaatQuery->where('klasis_id', $user->klasis_id);
            $anggotaQuery->whereHas('jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
            $pendetaQuery->where('klasis_penempatan_id', $user->klasis_id);
            $keuanganQuery->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $jemaatQuery->where('id', $user->jemaat_id);
            $anggotaQuery->where('jemaat_id', $user->jemaat_id);
            $pendetaQuery->where('jemaat_penempatan_id', $user->jemaat_id);
            $keuanganQuery->where('jemaat_id', $user->jemaat_id);
        }

        // Eksekusi Query
        $stats['jemaat'] = $jemaatQuery->count();
        // Cek dulu apakah tabel anggota_jemaat punya kolom status_anggota, jika tidak hitung semua
        try {
            $stats['anggota'] = $anggotaQuery->count();
        } catch (\Exception $e) {
            $stats['anggota'] = \App\Models\AnggotaJemaat::count(); // Fallback jika kolom status belum ada
        }
        
        $stats['pendeta'] = $pendetaQuery->count();

        // Ringkasan Keuangan Wadah (Tahun Ini)
        $stats['keuangan_target'] = $keuanganQuery->sum('jumlah_target');
        $stats['keuangan_realisasi'] = $keuanganQuery->sum('jumlah_realisasi');

        return view('admin.dashboard', compact('stats'));
    }
}