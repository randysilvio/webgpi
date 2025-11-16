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
use App\Models\Pegawai; // Model Baru Fase 6

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

        // 2. Siapkan Query Builder Dasar
        $jemaatQuery = Jemaat::query();
        
        // Perbaikan: Nama kolom yang benar adalah 'status_keanggotaan', bukan 'status_anggota'
        $anggotaQuery = AnggotaJemaat::query()->where('status_keanggotaan', 'Aktif'); 
        
        $pendetaQuery = Pendeta::query()->where('status_kepegawaian', 'Aktif'); // Fallback data lama
        $keuanganQuery = WadahKategorialAnggaran::query()->where('tahun_anggaran', date('Y'));

        // 3. Terapkan Filter Berdasarkan Role (Scoping)
        if ($user->hasRole(['Super Admin', 'Admin Sinode'])) {
            // Tidak ada filter, ambil semua data
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

        // 4. Eksekusi Query Statistik Dasar
        $stats['jemaat'] = $jemaatQuery->count();
        $stats['anggota'] = $anggotaQuery->count();

        // Statistik Pegawai (Prioritaskan tabel 'pegawai' baru jika ada, jika tidak pakai 'pendeta')
        if (class_exists(Pegawai::class)) {
            $pegawaiQuery = Pegawai::query()->where('status_aktif', 'Aktif');
            
            // Apply Scope untuk Pegawai
            if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
                $pegawaiQuery->where('klasis_id', $user->klasis_id);
            } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
                $pegawaiQuery->where('jemaat_id', $user->jemaat_id);
            }
            
            $stats['pendeta'] = $pegawaiQuery->count(); 
        } else {
            $stats['pendeta'] = $pendetaQuery->count();
        }

        // Ringkasan Keuangan Wadah (Tahun Ini)
        $stats['keuangan_target'] = $keuanganQuery->sum('jumlah_target');
        $stats['keuangan_realisasi'] = $keuanganQuery->sum('jumlah_realisasi');

        // 5. Fitur Baru: Peringatan Pensiun (Early Warning System)
        // Mengambil pegawai yang akan pensiun dalam 1 tahun ke depan
        $pensiunAkanDatang = collect();

        // Fitur ini hanya aktif jika Model Pegawai ada dan User berhak melihat (Sinode/Klasis/Bidang 3)
        if (class_exists(Pegawai::class) && $user->hasAnyRole(['Super Admin', 'Admin Sinode', 'Admin Klasis', 'Admin Bidang 3'])) {
            
            $pensiunQuery = Pegawai::where('status_aktif', 'Aktif')
                ->whereBetween('tanggal_pensiun', [now(), now()->addYear()]) // Range: Hari ini s/d 1 Tahun kedepan
                ->orderBy('tanggal_pensiun', 'asc');

            // Scope Klasis (Hanya lihat pegawai di klasisnya)
            if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
                $pensiunQuery->where('klasis_id', $user->klasis_id);
            }

            $pensiunAkanDatang = $pensiunQuery->take(5)->get();
        }

        return view('admin.dashboard', compact('stats', 'pensiunAkanDatang'));
    }
}