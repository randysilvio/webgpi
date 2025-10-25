<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Import Auth
use Illuminate\View\View; // <-- Import View

// Import Model jika perlu mengambil data statistik
use App\Models\User;
use App\Models\Pendeta;
use App\Models\Klasis;
use App\Models\Jemaat;
use App\Models\AnggotaJemaat;


class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Siapkan array untuk data statistik (opsional)
        $stats = [];

        // Ambil data statistik berdasarkan role (contoh)
        if ($user->hasRole('Super Admin')) {
            $stats['total_users'] = User::count();
            $stats['total_pendeta'] = Pendeta::where('status_kepegawaian', 'Aktif')->count(); // Misal hanya yg aktif
            $stats['total_klasis'] = Klasis::count();
            $stats['total_jemaat'] = Jemaat::count();
            $stats['total_anggota'] = AnggotaJemaat::where('status_keanggotaan', 'Aktif')->count(); // Misal hanya yg aktif
        } elseif ($user->hasRole('Admin Klasis')) {
            $klasisId = $user->klasis_id;
            if ($klasisId) {
                $stats['total_jemaat_di_klasis'] = Jemaat::where('klasis_id', $klasisId)->count();
                $jemaatIds = Jemaat::where('klasis_id', $klasisId)->pluck('id');
                $stats['total_anggota_di_klasis'] = AnggotaJemaat::whereIn('jemaat_id', $jemaatIds)->where('status_keanggotaan', 'Aktif')->count();
            }
        } elseif ($user->hasRole('Admin Jemaat')) {
            $jemaatId = $user->jemaat_id;
             if ($jemaatId) {
                 $stats['total_anggota_di_jemaat'] = AnggotaJemaat::where('jemaat_id', $jemaatId)->where('status_keanggotaan', 'Aktif')->count();
             }
        }
        // Tambahkan else if untuk role lain jika perlu statistik khusus

        // Kirim data user dan statistik ke view
        return view('admin.dashboard', compact('user', 'stats'));
    }
}