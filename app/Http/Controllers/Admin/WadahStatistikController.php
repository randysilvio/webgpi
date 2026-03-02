<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisWadahKategorial;
use App\Models\Klasis;
use App\Models\Jemaat;
use App\Models\AnggotaJemaat;
use App\Models\Setting; // Tambahan untuk Kop Surat
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Tambahan PDF

class WadahStatistikController extends Controller
{
    /**
     * Logic inti untuk filter data (dipakai di Index dan Print)
     */
    private function getStatistikData($request)
    {
        $user = Auth::user();
        $jenisWadahs = JenisWadahKategorial::all();
        
        if ($jenisWadahs->isEmpty()) return collect([]);

        // Filter Wilayah
        $jemaatId = $request->jemaat_id;
        $klasisId = $request->klasis_id;

        if ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $jemaatId = $user->jemaat_id;
        } elseif ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $klasisId = $user->klasis_id;
        }

        $statistik = [];

        foreach ($jenisWadahs as $wadah) {
            // Query Dasar: Status Aktif & Rentang Usia
            $query = AnggotaJemaat::query();
            
            // 1. Filter Status Aktif
            $query->where(function($q) {
                $q->whereIn('status_keanggotaan', ['Aktif', 'aktif', 'AKTIF', '1', 1]);
            });

            // 2. Filter Wilayah
            if ($jemaatId) {
                $query->where('jemaat_id', $jemaatId);
            } elseif ($klasisId) {
                $query->whereHas('jemaat', fn($q) => $q->where('klasis_id', $klasisId));
            }

            // 3. Filter Usia (Validasi Tanggal)
            $maxDate = Carbon::now()->subYears($wadah->rentang_usia_min)->format('Y-m-d'); 
            $minDate = Carbon::now()->subYears($wadah->rentang_usia_max + 1)->format('Y-m-d');

            $query->whereNotNull('tanggal_lahir')
                  ->where('tanggal_lahir', '!=', '0000-00-00')
                  ->whereDate('tanggal_lahir', '<=', $maxDate)
                  ->whereDate('tanggal_lahir', '>=', $minDate);

            // 4. Logika Gender Spesifik Wadah (Override Query Utama)
            $namaWadahUpper = strtoupper($wadah->nama_wadah);
            $warna = '#6B7280'; // Default

            // Jika Wadah KHUSUS WANITA (PW)
            if (in_array($namaWadahUpper, ['PERWATA', 'PW', 'PEREMPUAN', 'WANITA', 'IBU'])) {
                $query->whereIn('jenis_kelamin', ['P', 'p', 'Perempuan', 'Wanita', '2', 2]);
                $warna = '#EC4899'; // Pink
            } 
            // Jika Wadah KHUSUS PRIA (PKB)
            elseif (in_array($namaWadahUpper, ['PERPRI', 'PKB', 'PRI', 'KAUM BAPAK', 'LAKI-LAKI', 'PRIA'])) {
                $query->whereIn('jenis_kelamin', ['L', 'l', 'Laki-laki', 'Pria', '1', 1]);
                $warna = '#1F2937'; // Gelap
            }
            // Warna Lain
            elseif ($namaWadahUpper == 'PAR') $warna = '#10B981'; // Hijau
            elseif (in_array($namaWadahUpper, ['PAM', 'PP', 'PEMUDA'])) $warna = '#3B82F6'; // Biru
            elseif (str_contains($namaWadahUpper, 'LANSIA')) $warna = '#F59E0B'; // Orange

            // --- HITUNG DETAIL (Cloning Query) ---
            $total = (clone $query)->count();
            
            // Hitung Laki-laki (Jika wadah campuran atau pria)
            $laki = (clone $query)->whereIn('jenis_kelamin', ['L', 'l', 'Laki-laki', '1', 1])->count();
            
            // Hitung Perempuan (Jika wadah campuran atau wanita)
            $perempuan = (clone $query)->whereIn('jenis_kelamin', ['P', 'p', 'Perempuan', '2', 2])->count();

            // Hitung Sidi (Dewasa Iman)
            $sudahSidi = (clone $query)->whereNotNull('tanggal_sidi')->count();
            
            $statistik[] = [
                'id' => $wadah->id,
                'nama' => $wadah->nama_wadah,
                'range' => "{$wadah->rentang_usia_min}-{$wadah->rentang_usia_max} Thn",
                'total' => $total,
                'laki' => $laki,
                'perempuan' => $perempuan,
                'sidi' => $sudahSidi,
                'belum_sidi' => $total - $sudahSidi,
                'warna' => $warna,
                'persen_laki' => $total > 0 ? round(($laki/$total)*100) : 0,
                'persen_perempuan' => $total > 0 ? round(($perempuan/$total)*100) : 0,
            ];
        }

        return $statistik;
    }

    public function index(Request $request)
    {
        $statistik = $this->getStatistikData($request);
        $user = Auth::user();

        // Siapkan Data Chart
        $chartLabels = collect($statistik)->pluck('nama');
        $chartData = collect($statistik)->pluck('total');
        $chartColors = collect($statistik)->pluck('warna');

        // Data Dropdown
        $klasisList = collect();
        $jemaatList = collect();

        if ($user->hasRole(['Super Admin', 'Admin Sinode'])) {
            $klasisList = Klasis::orderBy('nama_klasis')->get();
            if ($request->klasis_id) {
                $jemaatList = Jemaat::where('klasis_id', $request->klasis_id)->orderBy('nama_jemaat')->get();
            }
        } elseif ($user->hasRole('Admin Klasis')) {
            $jemaatList = Jemaat::where('klasis_id', $user->klasis_id)->orderBy('nama_jemaat')->get();
        }

        return view('admin.wadah.statistik.index', compact(
            'statistik', 'klasisList', 'jemaatList', 'chartLabels', 'chartData', 'chartColors'
        ));
    }

    public function print(Request $request)
    {
        $statistik = $this->getStatistikData($request);
        $setting = Setting::first();
        
        // Info Filter untuk Judul Laporan
        $filterInfo = 'Seluruh Wilayah';
        if ($request->jemaat_id) {
            $jemaat = Jemaat::find($request->jemaat_id);
            $filterInfo = 'Jemaat ' . ($jemaat->nama_jemaat ?? '-');
        } elseif ($request->klasis_id) {
            $klasis = Klasis::find($request->klasis_id);
            $filterInfo = 'Klasis ' . ($klasis->nama_klasis ?? '-');
        }

        $pdf = Pdf::loadView('admin.wadah.statistik.print', compact('statistik', 'setting', 'filterInfo'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_Statistik_Wadah_' . date('Ymd') . '.pdf');
    }
}