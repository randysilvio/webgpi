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
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = WadahKategorialPengurus::with(['jenisWadah', 'klasis', 'jemaat', 'anggotaJemaat']);

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

        if ($request->filled('jenis_wadah_id')) $query->where('jenis_wadah_id', $request->jenis_wadah_id);
        if ($request->filled('tingkat')) $query->where('tingkat', $request->tingkat);
        if ($request->filled('klasis_id')) $query->where('klasis_id', $request->klasis_id);
        if ($request->filled('jemaat_id')) $query->where('jemaat_id', $request->jemaat_id);

        $statsQuery = clone $query;
        $stats = $statsQuery->reorder()->selectRaw('
            count(*) as total,
            sum(case when is_active = 1 then 1 else 0 end) as total_aktif,
            sum(case when is_active = 0 then 1 else 0 end) as total_non_aktif,
            sum(case when tingkat = "jemaat" then 1 else 0 end) as level_jemaat
        ')->first();

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

        $pengurus = $query->latest()->paginate(15)->withQueryString();
        
        $jenisWadahs = JenisWadahKategorial::all();
        $klasisList = collect();
        
        if ($user->hasAnyRole(['Super Admin', 'Admin Sinode', 'Admin Bidang 3'])) {
            $klasisList = Klasis::orderBy('nama_klasis')->get();
        } elseif ($user->hasRole('Admin Klasis')) {
            $klasisList = Klasis::where('id', $user->klasis_id)->get();
        }
        
        return view('admin.wadah.pengurus.index', compact('pengurus', 'jenisWadahs', 'klasisList', 'stats'));
    }

    public function create()
    {
        $jenisWadahs = JenisWadahKategorial::all();
        $user = Auth::user();
        
        $klasisList = collect();
        $jemaatList = collect();

        // FIX: Otomatis membaca referensi untuk Jemaat dan Klasis
        if ($user->hasAnyRole(['Super Admin', 'Admin Sinode', 'Admin Bidang 3'])) {
            $klasisList = Klasis::orderBy('nama_klasis')->get();
        } elseif ($user->hasRole('Admin Klasis')) {
            $klasisList = Klasis::where('id', $user->klasis_id)->get();
            $jemaatList = Jemaat::where('klasis_id', $user->klasis_id)->orderBy('nama_jemaat')->get();
        } elseif ($user->hasRole('Admin Jemaat')) {
            $jemaat = Jemaat::find($user->jemaat_id);
            if ($jemaat) {
                $klasisList = Klasis::where('id', $jemaat->klasis_id)->get();
            }
            $jemaatList = Jemaat::where('id', $user->jemaat_id)->get();
        }

        return view('admin.wadah.pengurus.create', compact('jenisWadahs', 'klasisList', 'jemaatList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // PENGAMANAN GANDA WILAYAH (Backend Authorization)
        if ($user->hasRole('Admin Jemaat')) {
            $jemaat = Jemaat::findOrFail($user->jemaat_id);
            $request->merge([
                'tingkat' => 'jemaat',
                'klasis_id' => $jemaat->klasis_id,
                'jemaat_id' => $jemaat->id,
            ]);
        } elseif ($user->hasRole('Admin Klasis')) {
            $request->merge(['klasis_id' => $user->klasis_id]);
            if ($request->tingkat == 'sinode') {
                $request->merge(['tingkat' => 'klasis']); // Paksa turun kasta jika bandel
            }
        }

        $request->validate([
            'jenis_wadah_id' => 'required|exists:jenis_wadah_kategorial,id',
            'tingkat' => ['required', Rule::in(['sinode', 'klasis', 'jemaat'])],
            'klasis_id' => 'required_if:tingkat,klasis|required_if:tingkat,jemaat|nullable|exists:klasis,id',
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
                $klasisId = $request->klasis_id;
                $jemaatId = $request->jemaat_id;

                if ($request->tingkat == 'sinode') {
                    $klasisId = null;
                    $jemaatId = null;
                } elseif ($request->tingkat == 'klasis') {
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

            return redirect()->route('admin.wadah.pengurus.index')->with('success', 'Data Pengurus berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit(WadahKategorialPengurus $pengurus)
    {
        $jenisWadahs = JenisWadahKategorial::all();
        $user = Auth::user();
        
        $klasisList = collect();
        $jemaatList = collect();

        // FIX: Sama seperti create, referensi harus tepat
        if ($user->hasAnyRole(['Super Admin', 'Admin Sinode', 'Admin Bidang 3'])) {
            $klasisList = Klasis::orderBy('nama_klasis')->get();
            if ($pengurus->klasis_id) {
                $jemaatList = Jemaat::where('klasis_id', $pengurus->klasis_id)->orderBy('nama_jemaat')->get();
            }
        } elseif ($user->hasRole('Admin Klasis')) {
            $klasisList = Klasis::where('id', $user->klasis_id)->get();
            $jemaatList = Jemaat::where('klasis_id', $user->klasis_id)->orderBy('nama_jemaat')->get();
        } elseif ($user->hasRole('Admin Jemaat')) {
            $jemaat = Jemaat::find($user->jemaat_id);
            if ($jemaat) {
                $klasisList = Klasis::where('id', $jemaat->klasis_id)->get();
            }
            $jemaatList = Jemaat::where('id', $user->jemaat_id)->get();
        }

        return view('admin.wadah.pengurus.edit', compact('pengurus', 'jenisWadahs', 'klasisList', 'jemaatList'));
    }

    public function update(Request $request, WadahKategorialPengurus $pengurus)
    {
        $user = Auth::user();
        
        // PENGAMANAN GANDA WILAYAH (Backend Authorization)
        if ($user->hasRole('Admin Jemaat')) {
            $jemaat = Jemaat::findOrFail($user->jemaat_id);
            $request->merge([
                'tingkat' => 'jemaat',
                'klasis_id' => $jemaat->klasis_id,
                'jemaat_id' => $jemaat->id,
            ]);
        } elseif ($user->hasRole('Admin Klasis')) {
            $request->merge(['klasis_id' => $user->klasis_id]);
            if ($request->tingkat == 'sinode') {
                $request->merge(['tingkat' => 'klasis']);
            }
        }

        $request->validate([
            'jenis_wadah_id' => 'required|exists:jenis_wadah_kategorial,id',
            'tingkat' => ['required', Rule::in(['sinode', 'klasis', 'jemaat'])],
            'klasis_id' => 'required_if:tingkat,klasis|required_if:tingkat,jemaat|nullable|exists:klasis,id',
            'jemaat_id' => 'required_if:tingkat,jemaat|nullable|exists:jemaat,id',
            'anggota_jemaat_id' => 'nullable|exists:anggota_jemaat,id',
            'jabatan' => 'required|string|max:255',
            'nomor_sk' => 'nullable|string|max:255',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after:periode_mulai',
            'is_active' => 'boolean',
        ]);

        try {
            $klasisId = $request->klasis_id;
            $jemaatId = $request->jemaat_id;

            if ($request->tingkat == 'sinode') {
                $klasisId = null;
                $jemaatId = null;
            } elseif ($request->tingkat == 'klasis') {
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

            return redirect()->route('admin.wadah.pengurus.index')->with('success', 'Data Pengurus berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(WadahKategorialPengurus $pengurus)
    {
        try {
            $pengurus->delete();
            return redirect()->route('admin.wadah.pengurus.index')->with('success', 'Data Pengurus berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}