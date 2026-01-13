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
use App\Models\AnggaranInduk;
use App\Models\Pegawai;
use App\Models\AsetGereja;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tahunSekarang = date('Y');
        
        // 1. Inisialisasi Data Statistik
        $stats = [
            'klasis' => 0, 'jemaat' => 0, 'anggota' => 0, 'pendeta' => 0,
            'aset' => 0, 'keuangan_target' => 0, 'keuangan_realisasi' => 0, 'saldo_kas' => 0,
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
        $indukQuery = AnggaranInduk::with('mataAnggaran')->where('tahun_anggaran', $tahunSekarang);
        $wadahQuery = WadahKategorialAnggaran::where('tahun_anggaran', $tahunSekarang);

        // Filter Scoping
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
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

        $stats['jemaat'] = $jemaatQuery->count();
        $stats['anggota'] = $anggotaQuery->count();
        $stats['aset'] = $asetQuery->count();

        if (class_exists(Pegawai::class)) {
            $pegawaiQuery = Pegawai::query()->where('status_aktif', 'Aktif');
            if ($user->hasRole('Admin Klasis') && $user->klasis_id) $pegawaiQuery->where('klasis_id', $user->klasis_id);
            elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) $pegawaiQuery->where('jemaat_id', $user->jemaat_id);
            $stats['pendeta'] = $pegawaiQuery->count();
        } else {
            $stats['pendeta'] = Pendeta::query()->where('status_kepegawaian', 'Aktif')->count();
        }

        // Hitung Keuangan
        $dataInduk = $indukQuery->get();
        $indukTarget = $dataInduk->sum('jumlah_target');
        $indukRealisasi = $dataInduk->sum('jumlah_realisasi');
        $indukMasuk = $dataInduk->filter(fn($i) => $i->mataAnggaran->jenis == 'Pendapatan')->sum('jumlah_realisasi');
        $indukKeluar = $dataInduk->filter(fn($i) => $i->mataAnggaran->jenis == 'Belanja')->sum('jumlah_realisasi');

        $dataWadah = $wadahQuery->get();
        $wadahTarget = $dataWadah->sum('jumlah_target');
        $wadahRealisasi = $dataWadah->sum('jumlah_realisasi');
        $wadahMasuk = $dataWadah->where('jenis_anggaran', 'penerimaan')->sum('jumlah_realisasi');
        $wadahKeluar = $dataWadah->where('jenis_anggaran', 'pengeluaran')->sum('jumlah_realisasi');

        $stats['keuangan_target'] = $indukTarget + $wadahTarget;
        $stats['keuangan_realisasi'] = $indukRealisasi + $wadahRealisasi;
        $stats['saldo_kas'] = ($indukMasuk + $wadahMasuk) - ($indukKeluar + $wadahKeluar);

        // Peringatan Pensiun
        $pensiunAkanDatang = collect();
        if (class_exists(Pegawai::class) && $user->hasAnyRole(['Super Admin', 'Admin Sinode', 'Admin Klasis'])) {
            $pensiunQuery = Pegawai::where('status_aktif', 'Aktif')
                ->whereBetween('tanggal_pensiun', [now(), now()->addYear()])
                ->orderBy('tanggal_pensiun', 'asc');
            if ($user->hasRole('Admin Klasis') && $user->klasis_id) $pensiunQuery->where('klasis_id', $user->klasis_id);
            $pensiunAkanDatang = $pensiunQuery->take(5)->get();
        }

        // Data Peta tidak perlu di load disini lagi karena akan di-load di iframe
        return view('admin.dashboard', compact('stats', 'pensiunAkanDatang'));
    }

    /**
     * TAMPILAN PETA WIDGET (IFRAME/TERISOLASI)
     * Metode ini memuat peta dalam lingkungan HTML yang bersih agar CSS tidak konflik.
     */
    public function petaWidget(Request $request)
    {
        $user = Auth::user();
        $petaKlasis = collect();

        if ($user->hasAnyRole(['Super Admin', 'Admin Sinode', 'Admin Klasis'])) {
            $queryPeta = Klasis::whereNotNull('latitude')->whereNotNull('longitude');
            if ($user->hasRole('Admin Klasis')) {
                $queryPeta->where('id', $user->klasis_id);
            }
            // Select kolom spesifik
            $petaKlasis = $queryPeta->get(['id', 'nama_klasis', 'kabupaten_kota', 'latitude', 'longitude', 'warna_peta']);
        }

        $isPrint = $request->has('print');

        return view('admin.peta_widget', compact('petaKlasis', 'isPrint'));
    }
}