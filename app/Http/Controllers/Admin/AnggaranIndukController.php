<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggaranInduk;
use App\Models\MataAnggaran;
use App\Models\Klasis;
use App\Models\Jemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnggaranIndukController extends Controller
{
    /**
     * Menampilkan daftar rencana anggaran per tahun.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->get('tahun', date('Y'));
        
        $query = AnggaranInduk::with(['mataAnggaran', 'klasis', 'jemaat'])
                              ->where('tahun_anggaran', $tahun);

        // --- Scoping Data Wilayah ---
        if ($user->hasRole('Admin Klasis')) {
            $query->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        $anggarans = $query->get();
        
        // Hitung Total untuk Summary
        $totalPendapatan = $anggarans->where('mataAnggaran.jenis', 'Pendapatan')->sum('jumlah_target');
        $totalBelanja = $anggarans->where('mataAnggaran.jenis', 'Belanja')->sum('jumlah_target');

        return view('admin.perbendaharaan.anggaran.index', compact('anggarans', 'tahun', 'totalPendapatan', 'totalBelanja'));
    }

    /**
     * Form pengisian target anggaran baru.
     */
    public function create()
    {
        $user = Auth::user();
        $mataAnggarans = MataAnggaran::where('is_active', true)->orderBy('kode')->get();
        
        return view('admin.perbendaharaan.anggaran.create', compact('mataAnggarans'));
    }

    /**
     * Menyimpan rencana anggaran ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun_anggaran' => 'required|numeric',
            'anggaran.*.mata_anggaran_id' => 'required|exists:mata_anggaran,id',
            'anggaran.*.jumlah_target' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        foreach ($request->anggaran as $item) {
            if ($item['jumlah_target'] > 0) {
                AnggaranInduk::updateOrCreate(
                    [
                        'tahun_anggaran' => $request->tahun_anggaran,
                        'mata_anggaran_id' => $item['mata_anggaran_id'],
                        'klasis_id' => $user->klasis_id,
                        'jemaat_id' => $user->jemaat_id,
                    ],
                    [
                        'jumlah_target' => $item['jumlah_target'],
                        'status_anggaran' => 'Draft'
                    ]
                );
            }
        }

        return redirect()->route('admin.perbendaharaan.anggaran.index')
                         ->with('success', 'Rencana Anggaran (RAPB) berhasil disimpan.');
    }
}