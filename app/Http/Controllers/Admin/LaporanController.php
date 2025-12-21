<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggaranInduk;
use App\Models\AsetGereja;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    /**
     * Menampilkan Laporan Realisasi Anggaran (LRA) dengan Logo Standar Web.
     */
    public function realisasi(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->get('tahun', date('Y'));
        $setting = Setting::first(); // Data pengaturan untuk logo

        $query = AnggaranInduk::with('mataAnggaran')->where('tahun_anggaran', $tahun);

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
     * Menampilkan Laporan Inventaris Aset dengan Logo Standar Web.
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
}