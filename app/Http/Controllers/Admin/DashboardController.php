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
use App\Models\Pegawai;    // Model Fase 6
use App\Models\AsetGereja; // Model Baru Fase 7

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Utama dengan statistik terintegrasi.
     */
    public function index()
    {
        $user = Auth::user();
        $tahunSekarang = date('Y');
        
        // 1. Inisialisasi Data Statistik Dasar
        $stats = [
            'klasis' => 0,
            'jemaat' => 0,
            'anggota' => 0,
            'pendeta' => 0,
            'aset' => 0,             // Tambahan Fase 7: Inventaris
            'keuangan_target' => 0,
            'keuangan_realisasi' => 0,
        ];

        // 2. Statistik Klasis (Hanya untuk level Sinode/Super Admin)
        if ($user->hasRole(['Super Admin', 'Admin Sinode'])) {
            $stats['klasis'] = Klasis::count();
        } elseif ($user->hasRole('Admin Klasis')) {
            $stats['klasis'] = 1; // Menunjukkan wilayah tugasnya sendiri
        }

        // 3. Siapkan Query Builder Dasar
        $jemaatQuery = Jemaat::query();
        $anggotaQuery = AnggotaJemaat::query()->where('status_keanggotaan', 'Aktif'); 
        $pendetaQuery = Pendeta::query()->where('status_kepegawaian', 'Aktif'); 
        $keuanganQuery = WadahKategorialAnggaran::query()->where('tahun_anggaran', $tahunSekarang);
        $asetQuery = AsetGereja::query(); // Query untuk Aset (Fase 7)

        // 4. Terapkan Filter Berdasarkan Role (Data Scoping)
        if ($user->hasRole(['Super Admin', 'Admin Sinode'])) {
            // Level Sinode melihat seluruh data secara nasional
        } elseif ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            // Level Klasis hanya melihat data di dalam Klasisnya
            $jemaatQuery->where('klasis_id', $user->klasis_id);
            $anggotaQuery->whereHas('jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
            $pendetaQuery->where('klasis_penempatan_id', $user->klasis_id);
            $keuanganQuery->where('klasis_id', $user->klasis_id);
            $asetQuery->where('klasis_id', $user->klasis_id); // Filter Aset Klasis
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            // Level Jemaat hanya melihat data jemaatnya sendiri
            $jemaatQuery->where('id', $user->jemaat_id);
            $anggotaQuery->where('jemaat_id', $user->jemaat_id);
            $pendetaQuery->where('jemaat_penempatan_id', $user->jemaat_id);
            $keuanganQuery->where('jemaat_id', $user->jemaat_id);
            $asetQuery->where('jemaat_id', $user->jemaat_id); // Filter Aset Jemaat
        }

        // 5. Eksekusi Perhitungan Statistik
        $stats['jemaat'] = $jemaatQuery->count();
        $stats['anggota'] = $anggotaQuery->count();
        $stats['aset'] = $asetQuery->count(); // Total Aset terhitung

        // Perhitungan Pegawai (Prioritas menggunakan tabel Pegawai Fase 6)
        if (class_exists(Pegawai::class)) {
            $pegawaiQuery = Pegawai::query()->where('status_aktif', 'Aktif');
            
            if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
                $pegawaiQuery->where('klasis_id', $user->klasis_id);
            } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
                $pegawaiQuery->where('jemaat_id', $user->jemaat_id);
            }
            
            $stats['pendeta'] = $pegawaiQuery->count(); 
        } else {
            $stats['pendeta'] = $pendetaQuery->count(); // Fallback ke data pendeta lama
        }

        // Ringkasan Keuangan (Realisasi Wadah)
        $stats['keuangan_target'] = $keuanganQuery->sum('jumlah_target');
        $stats['keuangan_realisasi'] = $keuanganQuery->sum('jumlah_realisasi');

        // 6. Fitur Peringatan Pensiun (Early Warning System Fase 6)
        $pensiunAkanDatang = collect();
        if (class_exists(Pegawai::class) && $user->hasAnyRole(['Super Admin', 'Admin Sinode', 'Admin Klasis', 'Admin Bidang 3'])) {
            $pensiunQuery = Pegawai::where('status_aktif', 'Aktif')
                ->whereBetween('tanggal_pensiun', [now(), now()->addYear()]) // Range 1 tahun ke depan
                ->orderBy('tanggal_pensiun', 'asc');

            if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
                $pensiunQuery->where('klasis_id', $user->klasis_id);
            }

            $pensiunAkanDatang = $pensiunQuery->take(5)->get();
        }

        // Mengirimkan data ke View Dashboard
        return view('admin.dashboard', compact('stats', 'pensiunAkanDatang'));
    }
}