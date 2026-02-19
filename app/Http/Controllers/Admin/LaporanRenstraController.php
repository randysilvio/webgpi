<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaJemaat;
use App\Models\Jemaat;
use App\Models\Klasis;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanRenstraController extends Controller
{
    /**
     * Engine Filter Utama
     * Menangani semua kemungkinan kombinasi filter
     */
    private function getFilteredQuery(Request $request)
    {
        $user = Auth::user();
        $query = AnggotaJemaat::aktif();

        // 1. RBAC (Keamanan Wilayah)
        if ($user->hasRole('Admin Jemaat')) {
            $query->where('jemaat_id', $user->jemaat_id);
        } elseif ($user->hasRole('Admin Klasis')) {
            $query->whereHas('jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        }

        // 2. Filter Wilayah
        if ($request->filled('klasis_id') && !$user->hasRole('Admin Klasis')) {
            $query->whereHas('jemaat', fn($q) => $q->where('klasis_id', $request->klasis_id));
        }
        if ($request->filled('jemaat_id')) {
            $query->where('jemaat_id', $request->jemaat_id);
        }

        // 3. Filter Demografi
        if ($request->filled('gender')) {
            $query->where('jenis_kelamin', $request->gender);
        }
        if ($request->filled('status_keluarga')) {
            $query->where('status_dalam_keluarga', $request->status_keluarga);
        }
        if ($request->filled('status_nikah')) {
            $query->where('status_pernikahan', $request->status_nikah);
        }
        if ($request->filled('gol_darah')) {
            $query->where('golongan_darah', $request->gol_darah);
        }
        if ($request->filled('usia_min')) {
            $query->whereRaw("TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= ?", [$request->usia_min]);
        }
        if ($request->filled('usia_max')) {
            $query->whereRaw("TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= ?", [$request->usia_max]);
        }

        // 4. Filter Sosial Ekonomi
        if ($request->filled('pendidikan')) {
            $query->where('pendidikan_terakhir', $request->pendidikan);
        }
        if ($request->filled('pekerjaan')) {
            $query->where('pekerjaan_utama', $request->pekerjaan);
        }
        if ($request->filled('penghasilan')) {
            $query->where('rentang_pengeluaran', $request->penghasilan);
        }

        // 5. Filter Renstra & Aset
        if ($request->filled('kondisi_rumah')) {
            $query->where('kondisi_rumah', $request->kondisi_rumah);
        }
        if ($request->filled('disabilitas') && $request->disabilitas == 'Ya') {
            $query->where('disabilitas', '!=', 'Tidak Ada');
        }
        if ($request->filled('aset')) {
            $query->where('aset_ekonomi', 'LIKE', '%' . $request->aset . '%');
        }
        if ($request->filled('digital')) {
            if ($request->digital == 'HP') $query->where('punya_smartphone', 1);
            if ($request->digital == 'Internet') $query->where('akses_internet', 1);
            if ($request->digital == 'Gaptek') $query->where('punya_smartphone', 0)->where('akses_internet', 0);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // --- A. DATA UNTUK DROPDOWN FILTER (DINAMIS) ---
        // Kita ambil data unik dari database agar dropdown sesuai isi data lapangan
        $filterOptions = [
            'klasis' => Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id'),
            'jemaat' => $user->hasRole('Admin Klasis') 
                ? Jemaat::where('klasis_id', $user->klasis_id)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id')
                : ($request->klasis_id ? Jemaat::where('klasis_id', $request->klasis_id)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id') : Jemaat::orderBy('nama_jemaat')->pluck('nama_jemaat', 'id')),
            
            // Ambil unique values untuk filter lanjutan
            'pendidikan' => AnggotaJemaat::aktif()->whereNotNull('pendidikan_terakhir')->distinct()->pluck('pendidikan_terakhir', 'pendidikan_terakhir'),
            'pekerjaan' => AnggotaJemaat::aktif()->whereNotNull('pekerjaan_utama')->distinct()->orderBy('pekerjaan_utama')->pluck('pekerjaan_utama', 'pekerjaan_utama'),
            'penghasilan' => AnggotaJemaat::aktif()->whereNotNull('rentang_pengeluaran')->distinct()->pluck('rentang_pengeluaran', 'rentang_pengeluaran'),
        ];

        // --- B. EKSEKUSI QUERY ---
        $query = $this->getFilteredQuery($request);
        
        // --- C. AGGREGASI DATA (Untuk Grafik & Kartu) ---
        // Menggunakan clone() agar query utama tidak berubah
        
        $totalJiwa = $query->count();
        $totalKK = $query->clone()->where('status_dalam_keluarga', 'Kepala Keluarga')->count();
        
        // Statistik Demografi
        $statsGender = $query->clone()->select('jenis_kelamin', DB::raw('count(*) as total'))->groupBy('jenis_kelamin')->pluck('total', 'jenis_kelamin');
        
        // Statistik Usia (Kategorisasi MySQL)
        $statsUsia = $query->clone()
            ->selectRaw("CASE 
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= 12 THEN 'Anak (0-12)'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= 18 THEN 'Remaja (13-18)'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= 35 THEN 'Pemuda (19-35)'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= 60 THEN 'Dewasa (36-60)'
                ELSE 'Lansia (>60)' END as kategori, count(*) as total")
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        // Statistik Renstra
        $statsRumah = $query->clone()->whereNotNull('kondisi_rumah')->groupBy('kondisi_rumah')->select('kondisi_rumah', DB::raw('count(*) as total'))->pluck('total', 'kondisi_rumah');
        $statsPendidikan = $query->clone()->whereNotNull('pendidikan_terakhir')->groupBy('pendidikan_terakhir')->select('pendidikan_terakhir', DB::raw('count(*) as total'))->orderByDesc('total')->limit(5)->pluck('total', 'pendidikan_terakhir');
        
        // Aset (Parsing CSV)
        $rawAset = $query->clone()->whereNotNull('aset_ekonomi')->pluck('aset_ekonomi');
        $statsAset = [];
        foreach ($rawAset as $row) {
            foreach (explode(',', $row) as $item) if($t=trim($item)) $statsAset[$t] = ($statsAset[$t]??0)+1;
        }
        arsort($statsAset);
        $statsAset = array_slice($statsAset, 0, 5);

        return view('admin.laporan.renstra.index', compact(
            'request', 'filterOptions',
            'totalJiwa', 'totalKK', 'statsGender', 'statsUsia', 'statsRumah', 'statsPendidikan', 'statsAset'
        ));
    }

    public function cetakPdf(Request $request)
    {
        $query = $this->getFilteredQuery($request)->with(['jemaat.klasis']);
        
        // Ambil Data untuk Tabel
        $data = $query->orderBy('jemaat_id')->orderBy('nama_lengkap')->limit(100000)->get();
        $setting = Setting::first();

        // Judul Laporan Dinamis (Context Aware)
        $title = "LAPORAN DATA JEMAAT TERINTEGRASI";
        $subtitle = "SELURUH WILAYAH PELAYANAN";
        $filtersApplied = [];

        if ($request->jemaat_id) {
            $j = Jemaat::find($request->jemaat_id);
            $subtitle = "JEMAAT " . strtoupper($j->nama_jemaat);
        } elseif ($request->klasis_id) {
            $k = Klasis::find($request->klasis_id);
            $subtitle = "KLASIS " . strtoupper($k->nama_klasis);
        }

        // Catat filter apa saja yang aktif untuk ditampilkan di header PDF
        if($request->kondisi_rumah) $filtersApplied[] = "Rumah: ".$request->kondisi_rumah;
        if($request->pekerjaan) $filtersApplied[] = "Pekerjaan: ".$request->pekerjaan;
        if($request->disabilitas == 'Ya') $filtersApplied[] = "Khusus Disabilitas";
        
        // Generate Statistik Mini untuk Header PDF
        $stats = [
            'gender' => $data->groupBy('jenis_kelamin')->map->count(),
            'rumah' => $data->groupBy('kondisi_rumah')->map->count(),
        ];

        $pdf = Pdf::loadView('admin.laporan.renstra.pdf', compact('data', 'setting', 'title', 'subtitle', 'filtersApplied', 'stats'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Renstra_Detail.pdf');
    }
}