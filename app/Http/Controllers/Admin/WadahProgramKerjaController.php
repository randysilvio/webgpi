<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WadahKategorialProgramKerja;
use App\Models\JenisWadahKategorial;
use App\Models\Klasis;
use App\Models\Jemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WadahProgramKerjaController extends Controller
{
    /**
     * Menampilkan daftar program kerja.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = WadahKategorialProgramKerja::with(['jenisWadah', 'klasis', 'jemaat', 'parentProgram']);

        // --- 1. Logic Scoping Data (RBAC) ---
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

        // --- 2. Filter Search & Dropdown ---
        if ($request->filled('jenis_wadah_id')) {
            $query->where('jenis_wadah_id', $request->jenis_wadah_id);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun_program', $request->tahun);
        }
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }
        if ($request->filled('search')) {
            $query->where('nama_program', 'like', '%' . $request->search . '%');
        }

        // Default sort: Tahun terbaru, lalu Tingkat (Sinode -> Klasis -> Jemaat)
        $programs = $query->orderByDesc('tahun_program')
                          ->orderByRaw("FIELD(tingkat, 'sinode', 'klasis', 'jemaat')")
                          ->paginate(15)
                          ->withQueryString();

        // Data untuk Filter UI
        $jenisWadahs = JenisWadahKategorial::all();
        $years = WadahKategorialProgramKerja::select('tahun_program')->distinct()->orderByDesc('tahun_program')->pluck('tahun_program');

        return view('admin.wadah.program.index', compact('programs', 'jenisWadahs', 'years'));
    }

    /**
     * Menampilkan form tambah program kerja.
     */
    public function create()
    {
        $jenisWadahs = JenisWadahKategorial::all();
        $user = Auth::user();
        
        // Load Lokasi (Klasis/Jemaat) sesuai hak akses
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

        return view('admin.wadah.program.create', compact('jenisWadahs', 'klasisList', 'jemaatList'));
    }

    /**
     * Menyimpan program kerja baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_wadah_id' => 'required|exists:jenis_wadah_kategorial,id',
            'tingkat' => ['required', Rule::in(['sinode', 'klasis', 'jemaat'])],
            'klasis_id' => 'required_if:tingkat,klasis|nullable|exists:klasis,id',
            'jemaat_id' => 'required_if:tingkat,jemaat|nullable|exists:jemaat,id',
            'tahun_program' => 'required|integer|digits:4',
            'nama_program' => 'required|string|max:255',
            'target_anggaran' => 'nullable|numeric|min:0',
            'parent_program_id' => 'nullable|exists:wadah_kategorial_program_kerja,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Auto-fill logic untuk lokasi
                $klasisId = $request->klasis_id;
                $jemaatId = null;

                if ($request->tingkat == 'jemaat') {
                    $jemaatId = $request->jemaat_id;
                    $klasisId = Jemaat::find($jemaatId)->klasis_id; // Ambil klasis dari jemaat
                } elseif ($request->tingkat == 'sinode') {
                    $klasisId = null;
                }

                WadahKategorialProgramKerja::create([
                    'jenis_wadah_id' => $request->jenis_wadah_id,
                    'tingkat' => $request->tingkat,
                    'klasis_id' => $klasisId,
                    'jemaat_id' => $jemaatId,
                    'tahun_program' => $request->tahun_program,
                    'nama_program' => $request->nama_program,
                    'deskripsi' => $request->deskripsi,
                    'tujuan' => $request->tujuan,
                    'penanggung_jawab' => $request->penanggung_jawab,
                    'status_pelaksanaan' => 0, // Default: Direncanakan
                    'target_anggaran' => $request->target_anggaran ?? 0,
                    'parent_program_id' => $request->parent_program_id,
                ]);
            });

            return redirect()->route('admin.wadah.program.index')
                             ->with('success', 'Program Kerja berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail program kerja.
     */
    public function show(WadahKategorialProgramKerja $program)
    {
        $program->load(['jenisWadah', 'klasis', 'jemaat', 'parentProgram', 'childPrograms']);
        return view('admin.wadah.program.show', compact('program'));
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(WadahKategorialProgramKerja $program)
    {
        $jenisWadahs = JenisWadahKategorial::all();
        $user = Auth::user();

        // Load Data Lokasi
        $klasisList = Klasis::orderBy('nama_klasis')->get();
        $jemaatList = collect();
        if ($program->klasis_id) {
            $jemaatList = Jemaat::where('klasis_id', $program->klasis_id)->orderBy('nama_jemaat')->get();
        }

        // Load Calon Program Induk (Parent)
        // Logika: Jika ini program Jemaat, cari program Klasis tahun yang sama & wadah yang sama
        $potentialParents = collect();
        if ($program->tingkat == 'jemaat' && $program->klasis_id) {
            $potentialParents = WadahKategorialProgramKerja::where('tingkat', 'klasis')
                ->where('klasis_id', $program->klasis_id)
                ->where('jenis_wadah_id', $program->jenis_wadah_id)
                ->where('tahun_program', $program->tahun_program)
                ->get();
        } elseif ($program->tingkat == 'klasis') {
            $potentialParents = WadahKategorialProgramKerja::where('tingkat', 'sinode')
                ->where('jenis_wadah_id', $program->jenis_wadah_id)
                ->where('tahun_program', $program->tahun_program)
                ->get();
        }

        return view('admin.wadah.program.edit', compact('program', 'jenisWadahs', 'klasisList', 'jemaatList', 'potentialParents'));
    }

    /**
     * Update data program kerja.
     */
    public function update(Request $request, WadahKategorialProgramKerja $program)
    {
        $request->validate([
            'nama_program' => 'required|string|max:255',
            'tahun_program' => 'required|integer',
            'status_pelaksanaan' => 'required|integer',
            'target_anggaran' => 'nullable|numeric',
        ]);

        try {
            $program->update([
                'jenis_wadah_id' => $request->jenis_wadah_id, // Biasanya wadah jarang berubah, tapi disiapkan
                'tahun_program' => $request->tahun_program,
                'nama_program' => $request->nama_program,
                'deskripsi' => $request->deskripsi,
                'tujuan' => $request->tujuan,
                'penanggung_jawab' => $request->penanggung_jawab,
                'status_pelaksanaan' => $request->status_pelaksanaan,
                'target_anggaran' => $request->target_anggaran,
                'parent_program_id' => $request->parent_program_id,
            ]);

            return redirect()->route('admin.wadah.program.index')
                             ->with('success', 'Program Kerja berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }

    /**
     * Hapus program kerja.
     */
    public function destroy(WadahKategorialProgramKerja $program)
    {
        try {
            $program->delete();
            return redirect()->route('admin.wadah.program.index')
                             ->with('success', 'Program Kerja berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }

    /**
     * API Internal: Mendapatkan daftar program induk yang mungkin.
     * Diakses via AJAX saat user memilih Tingkat/Wadah/Tahun di form Create.
     */
    public function getParentPrograms(Request $request)
    {
        $tingkat = $request->tingkat; // Tingkat program yang sedang dibuat (misal: jemaat)
        $wadahId = $request->wadah_id;
        $tahun = $request->tahun;
        $klasisId = $request->klasis_id;

        $query = WadahKategorialProgramKerja::where('jenis_wadah_id', $wadahId)
                                            ->where('tahun_program', $tahun);

        if ($tingkat == 'jemaat') {
            // Jika buat program Jemaat, cari induk di Klasis terkait
            $query->where('tingkat', 'klasis')->where('klasis_id', $klasisId);
        } elseif ($tingkat == 'klasis') {
            // Jika buat program Klasis, cari induk di Sinode
            $query->where('tingkat', 'sinode');
        } else {
            return response()->json([]);
        }

        return response()->json($query->select('id', 'nama_program')->get());
    }
}