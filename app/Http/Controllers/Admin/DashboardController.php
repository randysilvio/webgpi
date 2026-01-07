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
use App\Models\AnggaranInduk; // Model Keuangan Induk
use App\Models\Pegawai;
use App\Models\AsetGereja;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Utama dengan statistik terintegrasi (Induk + Wadah).
     */
    public function index()
    {
        $user = Auth::user();
        $tahunSekarang = date('Y');
        
        // 1. Inisialisasi Data Statistik
        $stats = [
            'klasis' => 0,
            'jemaat' => 0,
            'anggota' => 0,
            'pendeta' => 0,
            'aset' => 0,
            'keuangan_target' => 0,    // Gabungan
            'keuangan_realisasi' => 0, // Gabungan
            'saldo_kas' => 0,          // (Penerimaan - Pengeluaran) Gabungan
        ];

        // 2. Statistik Wilayah & Personalia
        if ($user->hasRole(['Super Admin', 'Admin Sinode'])) {
            $stats['klasis'] = Klasis::count();
        } elseif ($user->hasRole('Admin Klasis')) {
            $stats['klasis'] = 1;
        }

        // Query Builders Dasar
        $jemaatQuery = Jemaat::query();
        $anggotaQuery = AnggotaJemaat::query()->where('status_keanggotaan', 'Aktif');
        $asetQuery = AsetGereja::query();
        
        // Query Keuangan
        $indukQuery = AnggaranInduk::with('mataAnggaran')->where('tahun_anggaran', $tahunSekarang);
        $wadahQuery = WadahKategorialAnggaran::where('tahun_anggaran', $tahunSekarang);

        // Filter Scoping (Wilayah)
        if ($user->hasRole(['Super Admin', 'Admin Sinode'])) {
            // No filter
        } elseif ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $jemaatQuery->where('klasis_id', $user->klasis_id);
            $anggotaQuery->whereHas('jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
            $asetQuery->where('klasis_id', $user->klasis_id);
            $indukQuery->where('klasis_id', $user->klasis_id);
            $wadahQuery->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $jemaatQuery->where('id', $user->jemaat_id);
            $anggotaQuery->where('jemaat_id', $user->jemaat_id);
            $asetQuery->where('jemaat_id', $user->jemaat_id);
            $indukQuery->where('jemaat_id', $user->jemaat_id);
            $wadahQuery->where('jemaat_id', $user->jemaat_id);
        }

        // Eksekusi Statistik Dasar
        $stats['jemaat'] = $jemaatQuery->count();
        $stats['anggota'] = $anggotaQuery->count();
        $stats['aset'] = $asetQuery->count();

        // Hitung Pegawai/Pendeta
        if (class_exists(Pegawai::class)) {
            $pegawaiQuery = Pegawai::query()->where('status_aktif', 'Aktif');
            if ($user->hasRole('Admin Klasis') && $user->klasis_id) $pegawaiQuery->where('klasis_id', $user->klasis_id);
            elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) $pegawaiQuery->where('jemaat_id', $user->jemaat_id);
            $stats['pendeta'] = $pegawaiQuery->count();
        } else {
            $stats['pendeta'] = Pendeta::query()->where('status_kepegawaian', 'Aktif')->count();
        }

        // --- HITUNG KEUANGAN GABUNGAN (INDUK + WADAH) ---
        
        // A. Keuangan Induk
        $dataInduk = $indukQuery->get();
        $indukTarget = $dataInduk->sum('jumlah_target');
        $indukRealisasi = $dataInduk->sum('jumlah_realisasi');
        
        // Hitung Saldo Induk (Pendapatan - Belanja)
        $indukMasuk = $dataInduk->filter(fn($i) => $i->mataAnggaran->jenis == 'Pendapatan')->sum('jumlah_realisasi');
        $indukKeluar = $dataInduk->filter(fn($i) => $i->mataAnggaran->jenis == 'Belanja')->sum('jumlah_realisasi');

        // B. Keuangan Wadah
        $dataWadah = $wadahQuery->get();
        $wadahTarget = $dataWadah->sum('jumlah_target');
        $wadahRealisasi = $dataWadah->sum('jumlah_realisasi');

        // Hitung Saldo Wadah (Penerimaan - Pengeluaran)
        $wadahMasuk = $dataWadah->where('jenis_anggaran', 'penerimaan')->sum('jumlah_realisasi');
        $wadahKeluar = $dataWadah->where('jenis_anggaran', 'pengeluaran')->sum('jumlah_realisasi');

        // C. Gabungkan
        $stats['keuangan_target'] = $indukTarget + $wadahTarget;
        $stats['keuangan_realisasi'] = $indukRealisasi + $wadahRealisasi;
        $stats['saldo_kas'] = ($indukMasuk + $wadahMasuk) - ($indukKeluar + $wadahKeluar);

        // Fitur Peringatan Pensiun (Opsional)
        $pensiunAkanDatang = collect();
        if (class_exists(Pegawai::class) && $user->hasAnyRole(['Super Admin', 'Admin Sinode', 'Admin Klasis'])) {
            $pensiunQuery = Pegawai::where('status_aktif', 'Aktif')
                ->whereBetween('tanggal_pensiun', [now(), now()->addYear()])
                ->orderBy('tanggal_pensiun', 'asc');
            if ($user->hasRole('Admin Klasis') && $user->klasis_id) $pensiunQuery->where('klasis_id', $user->klasis_id);
            $pensiunAkanDatang = $pensiunQuery->take(5)->get();
        }

        return view('admin.dashboard', compact('stats', 'pensiunAkanDatang'));
    }
}