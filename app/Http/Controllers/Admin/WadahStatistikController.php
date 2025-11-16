<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisWadahKategorial;
use App\Models\Klasis;
use App\Models\Jemaat;
use App\Models\AnggotaJemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WadahStatistikController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Tentukan Scope Data (Filter Wilayah berdasarkan Role)
        $jemaatId = $request->jemaat_id;
        $klasisId = $request->klasis_id;

        // Jika user adalah Admin Jemaat, kunci ke jemaatnya sendiri
        if ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $jemaatId = $user->jemaat_id;
        }
        // Jika user adalah Admin Klasis, kunci ke klasisnya sendiri
        elseif ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $klasisId = $user->klasis_id;
        }

        // 2. Ambil Data Master Wadah untuk referensi batasan usia
        $jenisWadahs = JenisWadahKategorial::all();
        $statistik = [];

        foreach ($jenisWadahs as $wadah) {
            // Mulai Query Anggota
            // PERBAIKAN: Menghapus ->where('status_anggota', 'aktif') karena kolom belum ada
            $query = AnggotaJemaat::query();

            // Filter Wilayah
            if ($jemaatId) {
                $query->where('jemaat_id', $jemaatId);
            } elseif ($klasisId) {
                $query->whereHas('jemaat', function ($q) use ($klasisId) {
                    $q->where('klasis_id', $klasisId);
                });
            }

            // Filter Usia (Menghitung tanggal lahir berdasarkan range usia)
            // Rumus: Tgl Lahir <= Hari ini - Min Usia AND Tgl Lahir >= Hari ini - Max Usia - 1 tahun (toleransi)
            $maxDate = Carbon::now()->subYears($wadah->rentang_usia_min)->format('Y-m-d');
            $minDate = Carbon::now()->subYears($wadah->rentang_usia_max + 1)->format('Y-m-d'); // +1 agar mencakup tahun penuh

            $query->whereDate('tanggal_lahir', '<=', $maxDate)
                  ->whereDate('tanggal_lahir', '>=', $minDate);

            // Filter Jenis Kelamin Spesifik (Sesuai Nama Wadah)
            // Perwata (Wanita), Perpri (Pria). PAR/PAM/Lansia biasanya Campuran.
            if (in_array(strtoupper($wadah->nama_wadah), ['PERWATA', 'PW'])) {
                $query->where('jenis_kelamin', 'P'); // P = Perempuan
            } elseif (in_array(strtoupper($wadah->nama_wadah), ['PERPRI', 'PKB', 'PRI'])) {
                $query->where('jenis_kelamin', 'L'); // L = Laki-laki
            }

            // Hitung Jumlah
            $jumlah = $query->count();

            $statistik[] = [
                'wadah' => $wadah,
                'jumlah' => $jumlah,
                'keterangan' => "Usia {$wadah->rentang_usia_min} - {$wadah->rentang_usia_max} Tahun"
            ];
        }

        // 3. Data untuk Dropdown Filter di View
        $klasisList = collect();
        $jemaatList = collect();

        if ($user->hasRole('Super Admin') || $user->hasRole('Admin Sinode')) {
            $klasisList = Klasis::orderBy('nama_klasis')->get();
            if ($request->klasis_id) {
                $jemaatList = Jemaat::where('klasis_id', $request->klasis_id)->orderBy('nama_jemaat')->get();
            }
        } elseif ($user->hasRole('Admin Klasis')) {
            $jemaatList = Jemaat::where('klasis_id', $user->klasis_id)->orderBy('nama_jemaat')->get();
        }

        return view('admin.wadah.statistik.index', compact('statistik', 'klasisList', 'jemaatList'));
    }
}