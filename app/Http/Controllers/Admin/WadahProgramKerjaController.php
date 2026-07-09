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

class WadahProgramKerjaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. Query Dasar
        $query = WadahKategorialProgramKerja::with(['jenisWadah', 'klasis', 'jemaat', 'parentProgram']);

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

        // 3. Filter Pencarian
        if ($request->filled('search')) {
            $query->where('nama_program', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('tahun')) {
            $query->where('tahun_program', $request->tahun);
        }
        if ($request->filled('jenis_wadah_id')) {
            $query->where('jenis_wadah_id', $request->jenis_wadah_id);
        }
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        // 4. Hitung Statistik Sederhana
        $statsQuery = clone $query;
        $stats = $statsQuery->selectRaw('
            count(*) as total_program,
            sum(target_anggaran) as total_rab,
            sum(case when status_pelaksanaan = 1 then 1 else 0 end) as total_berjalan,
            sum(case when status_pelaksanaan = 2 then 1 else 0 end) as total_selesai
        ')->first();

        $programs = $query->latest()->paginate(15);
        $jenisWadahs = JenisWadahKategorial::orderBy('nama_wadah')->get();
        $years = WadahKategorialProgramKerja::select('tahun_program')->distinct()->orderBy('tahun_program', 'desc')->pluck('tahun_program');
        if($years->isEmpty()) $years = collect([date('Y')]);

        return view('admin.wadah.program.index', compact('programs', 'jenisWadahs', 'years', 'stats'));
    }

    public function create()
    {
        $jenisWadahs = JenisWadahKategorial::orderBy('nama_wadah')->get();
        $klasisList = Klasis::orderBy('nama_klasis')->get();
        $jemaatList = Jemaat::orderBy('nama_jemaat')->get();

        return view('admin.wadah.program.create', compact('jenisWadahs', 'klasisList', 'jemaatList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_program' => 'required|integer',
            'jenis_wadah_id' => 'required|exists:jenis_wadah_kategorials,id',
            'tingkat' => 'required|in:sinode,klasis,jemaat',
            'nama_program' => 'required|string|max:255',
            'parent_program_id' => 'nullable|exists:wadah_kategorial_program_kerja,id',
            'target_anggaran' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();

        // Pembersihan Data Otomatis (Sanitasi Tingkat)
        if ($data['tingkat'] == 'sinode') {
            $data['klasis_id'] = null;
            $data['jemaat_id'] = null;
            $data['parent_program_id'] = null; // Sinode adalah puncak, tidak punya induk
        } elseif ($data['tingkat'] == 'klasis') {
            $data['jemaat_id'] = null;
        }

        // Set default status jika belum ada
        if (!isset($data['status_pelaksanaan'])) {
            $data['status_pelaksanaan'] = 0; // Rencana
        }

        WadahKategorialProgramKerja::create($data);

        return redirect()->route('admin.wadah.program.index')->with('success', 'Rencana Program Kerja berhasil diajukan.');
    }

    public function edit(WadahKategorialProgramKerja $program)
    {
        $jenisWadahs = JenisWadahKategorial::orderBy('nama_wadah')->get();
        
        // Ambil potensi parent berdasarkan tingkat saat ini
        $potentialParents = collect();
        if ($program->tingkat == 'klasis') {
            $potentialParents = WadahKategorialProgramKerja::where('tingkat', 'sinode')
                ->where('tahun_program', $program->tahun_program)
                ->where('jenis_wadah_id', $program->jenis_wadah_id)
                ->get();
        } elseif ($program->tingkat == 'jemaat') {
            $potentialParents = WadahKategorialProgramKerja::where('tingkat', 'klasis')
                ->where('klasis_id', $program->klasis_id)
                ->where('tahun_program', $program->tahun_program)
                ->where('jenis_wadah_id', $program->jenis_wadah_id)
                ->get();
        }

        return view('admin.wadah.program.edit', compact('program', 'jenisWadahs', 'potentialParents'));
    }

    public function update(Request $request, WadahKategorialProgramKerja $program)
    {
        $request->validate([
            'nama_program' => 'required|string|max:255',
            'tahun_program' => 'required|integer',
            'status_pelaksanaan' => 'required|integer',
            'target_anggaran' => 'nullable|numeric|min:0',
            'parent_program_id' => 'nullable|exists:wadah_kategorial_program_kerja,id',
        ]);

        $data = $request->only([
            'nama_program', 'tahun_program', 'status_pelaksanaan', 'target_anggaran', 
            'deskripsi', 'tujuan', 'penanggung_jawab', 'parent_program_id'
        ]);

        // Proteksi Logika: Sinode tidak boleh dipaksa punya parent
        if ($program->tingkat == 'sinode') {
            $data['parent_program_id'] = null;
        }

        try {
            $program->update($data);
            return redirect()->route('admin.wadah.program.index')
                             ->with('success', 'Program Kerja berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }

    public function destroy(WadahKategorialProgramKerja $program)
    {
        try {
            $program->delete();
            return redirect()->route('admin.wadah.program.index')
                             ->with('success', 'Program Kerja berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data. Pastikan tidak ada anggaran yang masih mengikat program ini.');
        }
    }

    /**
     * API Internal: Mendapatkan daftar program induk berjenjang.
     */
    public function getParentPrograms(Request $request)
    {
        $tingkat = $request->tingkat;
        $wadahId = $request->wadah_id;
        $tahun = $request->tahun;
        $klasisId = $request->klasis_id;

        $query = WadahKategorialProgramKerja::where('jenis_wadah_id', $wadahId)
                                            ->where('tahun_program', $tahun);

        if ($tingkat == 'jemaat') {
            $query->where('tingkat', 'klasis')->where('klasis_id', $klasisId);
        } elseif ($tingkat == 'klasis') {
            $query->where('tingkat', 'sinode');
        } else {
            return response()->json([]); // Sinode tidak punya parent
        }

        $programs = $query->select('id', 'nama_program', 'tingkat')->get();

        return response()->json($programs);
    }
}