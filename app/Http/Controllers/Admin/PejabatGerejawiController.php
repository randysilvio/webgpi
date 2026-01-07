<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PejabatGerejawi;
use App\Models\AnggotaJemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PejabatGerejawiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = PejabatGerejawi::with('anggotaJemaat.jemaat');

        // Scoping Wilayah
        if ($user->hasRole('Admin Klasis')) {
            $query->whereHas('anggotaJemaat.jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->whereHas('anggotaJemaat', fn($q) => $q->where('jemaat_id', $user->jemaat_id));
        }

        $pejabats = $query->latest()->paginate(15);
        
        // Ambil data anggota aktif untuk pilihan pejabat
        $anggotaQuery = AnggotaJemaat::where('status_keanggotaan', 'Aktif');
        if ($user->hasRole('Admin Jemaat')) {
            $anggotaQuery->where('jemaat_id', $user->jemaat_id);
        }
        $anggotas = $anggotaQuery->get();

        return view('admin.tata_gereja.pejabat.index', compact('pejabats', 'anggotas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anggota_jemaat_id' => 'required|exists:anggota_jemaat,id',
            'jabatan' => 'required|in:Penatua,Diaken',
            'periode_mulai' => 'required',
            'periode_selesai' => 'required',
        ]);

        PejabatGerejawi::create($request->all());
        return redirect()->back()->with('success', 'Pejabat Gerejawi berhasil didaftarkan.');
    }
}