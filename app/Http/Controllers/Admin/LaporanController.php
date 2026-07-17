<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggaranInduk;
use App\Models\WadahKategorialAnggaran;
use App\Models\AsetGereja;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Menampilkan Laporan Realisasi Anggaran (LRA) Induk.
     */
    public function realisasi(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->get('tahun', date('Y'));
        $setting = Setting::first();

        $query = AnggaranInduk::with('mataAnggaran')->where('tahun_anggaran', $tahun);

        // Filter Wilayah
        if ($user->hasRole('Admin Klasis')) {
            $query->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        $anggarans = $query->get();

        $laporan = [
            'Pendapatan' => $anggarans->where('mataAnggaran.jenis', 'Pendapatan'),
            'Belanja' => $anggarans->where('mataAnggaran.jenis', 'Belanja')
        ];

        return view('admin.perbendaharaan.laporan.realisasi', compact('laporan', 'tahun', 'setting'));
    }

    /**
     * Menampilkan Laporan Inventaris Aset.
     */
    public function aset(Request $request)
    {
        $user = Auth::user();
        $setting = Setting::first();
        $query = AsetGereja::query();

        if ($user->hasRole('Admin Klasis')) {
            $query->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        $asets = $query->orderBy('kategori')->get();

        return view('admin.perbendaharaan.laporan.aset', compact('asets', 'setting'));
    }

    /**
     * BARU: Laporan Gabungan (Konsolidasi Keuangan Induk + Wadah).
     */
    public function gabungan(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->get('tahun', date('Y'));
        $setting = Setting::first();

        // 1. Query Data Induk
        $queryInduk = AnggaranInduk::with('mataAnggaran')->where('tahun_anggaran', $tahun);
        
        // 2. Query Data Wadah
        $queryWadah = WadahKategorialAnggaran::with('jenisWadah')->where('tahun_anggaran', $tahun);

        // Terapkan Filter Wilayah (RBAC)
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $queryInduk->where('klasis_id', $user->klasis_id);
            $queryWadah->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $queryInduk->where('jemaat_id', $user->jemaat_id);
            $queryWadah->where('jemaat_id', $user->jemaat_id);
        }

        $induk = $queryInduk->get();
        $wadah = $queryWadah->get();

        // Grouping Data
        $data = [
            'induk_masuk' => $induk->filter(fn($i) => $i->mataAnggaran->jenis == 'Pendapatan'),
            'induk_keluar' => $induk->filter(fn($i) => $i->mataAnggaran->jenis == 'Belanja'),
            'wadah_masuk' => $wadah->where('jenis_anggaran', 'penerimaan'),
            'wadah_keluar' => $wadah->where('jenis_anggaran', 'pengeluaran'),
        ];

        // Hitung Total
        $totals = [
            'induk_masuk' => $data['induk_masuk']->sum('jumlah_realisasi'),
            'induk_keluar' => $data['induk_keluar']->sum('jumlah_realisasi'),
            'wadah_masuk' => $data['wadah_masuk']->sum('jumlah_realisasi'),
            'wadah_keluar' => $data['wadah_keluar']->sum('jumlah_realisasi'),
        ];
        
        // Saldo Bersih = (Semua Masuk) - (Semua Keluar)
        $totals['saldo_bersih'] = ($totals['induk_masuk'] + $totals['wadah_masuk']) - ($totals['induk_keluar'] + $totals['wadah_keluar']);

        // Fitur Export PDF (Opsional)
        if ($request->has('export') && $request->export == 'pdf') {
            $pdf = Pdf::loadView('admin.perbendaharaan.laporan.pdf_gabungan', compact('data', 'totals', 'tahun', 'setting'));
            $pdf->setPaper('a4', 'portrait');
            return $pdf->stream('Laporan_Konsolidasi_Keuangan_'.$tahun.'.pdf');
        }

        return view('admin.perbendaharaan.laporan.gabungan', compact('data', 'totals', 'tahun', 'setting'));
    }
}