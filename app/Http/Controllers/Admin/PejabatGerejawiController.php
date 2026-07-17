<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PejabatGerejawi;
use App\Models\AnggotaJemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PejabatGerejawiController extends Controller
{
    /**
     * Menampilkan daftar pejabat dengan Filter & Pencarian
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PejabatGerejawi::with(['anggotaJemaat.jemaat']);

        // 1. RBAC: Filter Wilayah
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $query->whereHas('anggotaJemaat.jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $query->whereHas('anggotaJemaat', fn($q) => $q->where('jemaat_id', $user->jemaat_id));
        }

        // 2. Filter Dropdown
        if ($request->filled('jabatan')) {
            $query->where('jabatan', $request->jabatan);
        }
        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status);
        }

        // 3. Pencarian Nama
        if ($request->filled('search')) {
            $query->whereHas('anggotaJemaat', fn($q) => 
                $q->where('nama_lengkap', 'like', "%{$request->search}%")
            );
        }

        $pejabats = $query->latest('periode_mulai')->paginate(15)->withQueryString();

        return view('admin.tata_gereja.pejabat.index', compact('pejabats'));
    }

    /**
     * Form Tambah Pejabat (Create)
     */
    public function create()
    {
        $user = Auth::user();
        
        // Ambil Data Anggota Jemaat Aktif untuk dijadikan calon pejabat
        $anggotaQuery = AnggotaJemaat::where('status_keanggotaan', 'Aktif');

        // RBAC Scoping
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $anggotaQuery->whereHas('jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $anggotaQuery->where('jemaat_id', $user->jemaat_id);
        }

        // PERBAIKAN DI SINI: Mengambil 'tanggal_lahir' bukan 'umur'
        $anggotas = $anggotaQuery->with('jemaat:id,nama_jemaat')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'jemaat_id', 'tanggal_lahir']); 

        return view('admin.tata_gereja.pejabat.create', compact('anggotas'));
    }

    /**
     * Simpan Data (Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'anggota_jemaat_id' => 'required|exists:anggota_jemaat,id',
            'jabatan'           => 'required|in:Penatua,Diaken,Pengajar',
            'status_aktif'      => 'required|in:Aktif,Demisioner,Emeritus,Non-Aktif',
            'periode_mulai'     => 'required|integer|digits:4',
            'periode_selesai'   => 'required|integer|digits:4',
            'no_sk_pelantikan'  => 'nullable|string|max:255'
        ]);

        PejabatGerejawi::create($request->all());

        return redirect()->route('admin.tata-gereja.pejabat.index')
            ->with('success', 'Pejabat Gerejawi berhasil dilantik/terdaftar.');
    }

    /**
     * Form Edit (Edit)
     */
    public function edit($id)
    {
        // Mengambil data pejabat beserta relasi anggota untuk ditampilkan readonly
        $pejabat = PejabatGerejawi::with('anggotaJemaat')->findOrFail($id);
        return view('admin.tata_gereja.pejabat.edit', compact('pejabat'));
    }

    /**
     * Update Data (Update)
     */
    public function update(Request $request, $id)
    {
        $pejabat = PejabatGerejawi::findOrFail($id);

        $request->validate([
            'jabatan'           => 'required|in:Penatua,Diaken,Pengajar',
            'status_aktif'      => 'required|in:Aktif,Demisioner,Emeritus,Non-Aktif',
            'periode_mulai'     => 'required|integer|digits:4',
            'periode_selesai'   => 'required|integer|digits:4',
            'no_sk_pelantikan'  => 'nullable|string|max:255'
        ]);

        $pejabat->update($request->all());

        return redirect()->route('admin.tata-gereja.pejabat.index')
            ->with('success', 'Data pejabat berhasil diperbarui.');
    }

    /**
     * Hapus Data (Destroy)
     */
    public function destroy($id)
    {
        $pejabat = PejabatGerejawi::findOrFail($id);
        $pejabat->delete();

        return redirect()->route('admin.tata-gereja.pejabat.index')
            ->with('success', 'Data pejabat berhasil dihapus.');
    }
}