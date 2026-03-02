<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WadahKategorialPengurus;
use App\Models\JenisWadahKategorial;
use App\Models\Klasis;
use App\Models\Jemaat;
use App\Models\AnggotaJemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WadahKategorialPengurusController extends Controller
{
    /**
     * Menampilkan daftar pengurus wadah dengan statistik.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. Query Dasar (Eager Loading)
        // Jangan pakai latest() di sini agar aman untuk query statistik
        $query = WadahKategorialPengurus::with(['jenisWadah', 'klasis', 'jemaat', 'anggotaJemaat']);

        // 2. Logic Scoping Data (RBAC)
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $query->where(function($q) use ($user) {
                $q->where('klasis_id', $user->klasis_id)
                  ->orWhereHas('jemaat', function($j) use ($user) {
                      $j->where('klasis_id', $user->klasis_id);
                  });
            });
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        // 3. Filter Request (Dropdown)
        if ($request->filled('jenis_wadah_id')) {
            $query->where('jenis_wadah_id', $request->jenis_wadah_id);
        }
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }
        if ($request->filled('klasis_id')) {
            $query->where('klasis_id', $request->klasis_id);
        }
        if ($request->filled('jemaat_id')) {
            $query->where('jemaat_id', $request->jemaat_id);
        }

        // --- 4. HITUNG STATISTIK (DASHBOARD MINI) ---
        // Clone query yang sudah terfilter wilayah & wadah
        $statsQuery = clone $query;
        
        // Gunakan reorder() untuk menghapus default sorting agar COUNT/SUM aman dari error SQL
        $stats = $statsQuery->reorder()->selectRaw('
            count(*) as total,
            sum(case when is_active = 1 then 1 else 0 end) as total_aktif,
            sum(case when is_active = 0 then 1 else 0 end) as total_non_aktif,
            sum(case when tingkat = "jemaat" then 1 else 0 end) as level_jemaat
        ')->first();

        // 5. Filter Pencarian Teks (Search) - Diterapkan SETELAH statistik
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('anggotaJemaat', function($subQ) use ($search) {
                    $subQ->where('nama_lengkap', 'like', "%{$search}%");
                })
                ->orWhere('jabatan', 'like', "%{$search}%")
                ->orWhere('nomor_sk', 'like', "%{$search}%");
            });
        }

        // 6. Ambil Data Tabel (Terapkan sorting latest di sini)
        $pengurus = $query->latest()->paginate(15)->withQueryString();
        
        // Data Pendukung UI
        $jenisWadahs = JenisWadahKategorial::all();
        $klasisList = collect();
        
        if ($user->hasRole(['Super Admin', 'Admin Sinode'])) {
            $klasisList = Klasis::orderBy('nama_klasis')->get();
        } elseif ($user->hasRole('Admin Klasis')) {
            $klasisList = Klasis::where('id', $user->klasis_id)->get();
        }
        
        return view('admin.wadah.pengurus.index', compact('pengurus', 'jenisWadahs', 'klasisList', 'stats'));
    }

    /**
     * Menampilkan form tambah pengurus.
     */
    public function create()
    {
        $jenisWadahs = JenisWadahKategorial::all();
        $user = Auth::user();
        $klasisList = collect();
        $jemaatList = collect();

        if ($user->hasRole(['Super Admin', 'Admin Sinode'])) {
            $klasisList = Klasis::orderBy('nama_klasis')->get();
        } elseif ($user->hasRole('Admin Klasis')) {
            $klasisList = Klasis::where('id', $user->klasis_id)->get();
            $jemaatList = Jemaat::where('klasis_id', $user->klasis_id)->orderBy('nama_jemaat')->get();
        } elseif ($user->hasRole('Admin Jemaat')) {
            $jemaatList = Jemaat::where('id', $user->jemaat_id)->get();
        }

        return view('admin.wadah.pengurus.create', compact('jenisWadahs', 'klasisList', 'jemaatList'));
    }

    /**
     * Menyimpan data pengurus baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_wadah_id' => 'required|exists:jenis_wadah_kategorial,id',
            'tingkat' => ['required', Rule::in(['sinode', 'klasis', 'jemaat'])],
            'klasis_id' => 'required_if:tingkat,klasis|nullable|exists:klasis,id',
            'jemaat_id' => 'required_if:tingkat,jemaat|nullable|exists:jemaat,id',
            'anggota_jemaat_id' => 'nullable|exists:anggota_jemaat,id',
            'jabatan' => 'required|string|max:255',
            'nomor_sk' => 'nullable|string|max:255',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after:periode_mulai',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Auto-fill lokasi
                $klasisId = $request->klasis_id;
                $jemaatId = $request->jemaat_id;

                if ($request->tingkat == 'jemaat' && $jemaatId) {
                    $klasisId = Jemaat::find($jemaatId)->klasis_id;
                } elseif ($request->tingkat == 'sinode') {
                    $klasisId = null;
                    $jemaatId = null;
                }

                WadahKategorialPengurus::create([
                    'jenis_wadah_id' => $request->jenis_wadah_id,
                    'tingkat' => $request->tingkat,
                    'klasis_id' => $klasisId,
                    'jemaat_id' => $jemaatId,
                    'anggota_jemaat_id' => $request->anggota_jemaat_id,
                    'jabatan' => $request->jabatan,
                    'nomor_sk' => $request->nomor_sk,
                    'periode_mulai' => $request->periode_mulai,
                    'periode_selesai' => $request->periode_selesai,
                    'is_active' => $request->has('is_active'),
                ]);
            });

            return redirect()->route('admin.wadah.pengurus.index')
                             ->with('success', 'Data Pengurus berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form edit pengurus.
     */
    public function edit(WadahKategorialPengurus $pengurus)
    {
        $jenisWadahs = JenisWadahKategorial::all();
        $klasisList = Klasis::orderBy('nama_klasis')->get();
        
        $jemaatList = collect();
        if ($pengurus->klasis_id) {
            $jemaatList = Jemaat::where('klasis_id', $pengurus->klasis_id)->orderBy('nama_jemaat')->get();
        }

        return view('admin.wadah.pengurus.edit', compact('pengurus', 'jenisWadahs', 'klasisList', 'jemaatList'));
    }

    /**
     * Memperbarui data pengurus.
     */
    public function update(Request $request, WadahKategorialPengurus $pengurus)
    {
        $request->validate([
            'jenis_wadah_id' => 'required|exists:jenis_wadah_kategorial,id',
            'tingkat' => ['required', Rule::in(['sinode', 'klasis', 'jemaat'])],
            'klasis_id' => 'required_if:tingkat,klasis|nullable|exists:klasis,id',
            'jemaat_id' => 'required_if:tingkat,jemaat|nullable|exists:jemaat,id',
            'anggota_jemaat_id' => 'nullable|exists:anggota_jemaat,id',
            'jabatan' => 'required|string|max:255',
            'nomor_sk' => 'nullable|string|max:255',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after:periode_mulai',
            'is_active' => 'boolean',
        ]);

        try {
            // Auto-fill lokasi update
            $klasisId = $request->klasis_id;
            $jemaatId = $request->jemaat_id;

            if ($request->tingkat == 'jemaat' && $jemaatId) {
                $klasisId = Jemaat::find($jemaatId)->klasis_id;
            } elseif ($request->tingkat == 'sinode') {
                $klasisId = null;
                $jemaatId = null;
            }

            $pengurus->update([
                'jenis_wadah_id' => $request->jenis_wadah_id,
                'tingkat' => $request->tingkat,
                'klasis_id' => $klasisId,
                'jemaat_id' => $jemaatId,
                'anggota_jemaat_id' => $request->anggota_jemaat_id,
                'jabatan' => $request->jabatan,
                'nomor_sk' => $request->nomor_sk,
                'periode_mulai' => $request->periode_mulai,
                'periode_selesai' => $request->periode_selesai,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.wadah.pengurus.index')
                             ->with('success', 'Data Pengurus berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data pengurus.
     */
    public function destroy(WadahKategorialPengurus $pengurus)
    {
        try {
            $pengurus->delete();
            return redirect()->route('admin.wadah.pengurus.index')
                             ->with('success', 'Data Pengurus berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}