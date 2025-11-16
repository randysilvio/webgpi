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
     * Menampilkan daftar pengurus wadah.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Eager loading relasi untuk performa
        $query = WadahKategorialPengurus::with(['jenisWadah', 'klasis', 'jemaat', 'anggotaJemaat']);

        // --- Logic Scoping Data (RBAC) ---
        
        // Jika Admin Klasis, hanya tampilkan pengurus tingkat Klasis dia atau Jemaat di bawahnya
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $query->where(function($q) use ($user) {
                $q->where('klasis_id', $user->klasis_id)
                  ->orWhereHas('jemaat', function($j) use ($user) {
                      $j->where('klasis_id', $user->klasis_id);
                  });
            });
        }
        // Jika Admin Jemaat, hanya tampilkan pengurus tingkat Jemaat dia
        elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        // --- Filter dari Request (Search/Filter UI) ---
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
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('anggotaJemaat', function($subQ) use ($search) {
                    $subQ->where('nama', 'like', "%{$search}%");
                })
                ->orWhere('jabatan', 'like', "%{$search}%")
                ->orWhere('nomor_sk', 'like', "%{$search}%");
            });
        }

        $pengurus = $query->latest()->paginate(15)->withQueryString();
        $jenisWadahs = JenisWadahKategorial::all();
        
        // Data untuk dropdown filter (bisa dioptimalkan dengan AJAX jika data besar)
        $klasisList = Klasis::orderBy('nama_klasis')->get();
        // Jemaat list dikosongkan atau di-load via AJAX/API di view index untuk performa
        
        return view('admin.wadah.pengurus.index', compact('pengurus', 'jenisWadahs', 'klasisList'));
    }

    /**
     * Menampilkan form tambah pengurus.
     */
    public function create()
    {
        $jenisWadahs = JenisWadahKategorial::all();
        
        // Load data sesuai hak akses (Scoping)
        $user = Auth::user();
        $klasisList = collect();
        $jemaatList = collect();

        if ($user->hasRole('Super Admin') || $user->hasRole('Admin Sinode')) {
            $klasisList = Klasis::orderBy('nama_klasis')->get();
            // Jemaat di-load via AJAX based on Klasis selection di frontend
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
            // Validasi kondisional: Klasis wajib jika tingkat=klasis, Jemaat wajib jika tingkat=jemaat
            'klasis_id' => 'required_if:tingkat,klasis|nullable|exists:klasis,id',
            'jemaat_id' => 'required_if:tingkat,jemaat|nullable|exists:jemaat,id',
            'anggota_jemaat_id' => 'nullable|exists:anggota_jemaat,id', // Bisa null jika belum terdata di DB anggota
            'jabatan' => 'required|string|max:255',
            'nomor_sk' => 'nullable|string|max:255',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after:periode_mulai',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($request) {
                WadahKategorialPengurus::create([
                    'jenis_wadah_id' => $request->jenis_wadah_id,
                    'tingkat' => $request->tingkat,
                    'klasis_id' => $request->tingkat == 'klasis' ? $request->klasis_id : ($request->tingkat == 'jemaat' ? Jemaat::find($request->jemaat_id)->klasis_id : null), // Auto fill klasis jika jemaat dipilih
                    'jemaat_id' => $request->tingkat == 'jemaat' ? $request->jemaat_id : null,
                    'anggota_jemaat_id' => $request->anggota_jemaat_id,
                    // 'user_id' => ... (Logic untuk link ke user login bisa ditambahkan nanti jika diperlukan)
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
     * Menampilkan detail pengurus.
     */
    public function show(WadahKategorialPengurus $pengurus)
    {
        // Policy check bisa ditambahkan di sini (authorize)
        return view('admin.wadah.pengurus.show', compact('pengurus'));
    }

    /**
     * Menampilkan form edit pengurus.
     */
    public function edit(WadahKategorialPengurus $pengurus)
    {
        $jenisWadahs = JenisWadahKategorial::all();
        $klasisList = Klasis::orderBy('nama_klasis')->get();
        
        // Load jemaat list berdasarkan klasis dari data pengurus (jika ada)
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
            $pengurus->update([
                'jenis_wadah_id' => $request->jenis_wadah_id,
                'tingkat' => $request->tingkat,
                'klasis_id' => $request->tingkat == 'klasis' ? $request->klasis_id : ($request->tingkat == 'jemaat' ? Jemaat::find($request->jemaat_id)->klasis_id : null),
                'jemaat_id' => $request->tingkat == 'jemaat' ? $request->jemaat_id : null,
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