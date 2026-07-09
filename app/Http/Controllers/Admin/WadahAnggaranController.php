<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WadahKategorialAnggaran;
use App\Models\WadahKategorialProgramKerja;
use App\Models\JenisWadahKategorial;
use App\Models\Klasis;
use App\Models\Jemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WadahAnggaranController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = WadahKategorialAnggaran::with(['jenisWadah', 'programKerja', 'klasis', 'jemaat']);

        // --- 1. Scoping Data (RBAC) ---
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

        // --- 2. Filter Pencarian ---
        if ($request->filled('search')) {
            $query->where('nama_pos_anggaran', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }
        if ($request->filled('wadah')) {
            $query->where('jenis_wadah_id', $request->wadah);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis_anggaran', $request->jenis);
        }

        $anggarans = $query->latest()->paginate(15);
        $years = WadahKategorialAnggaran::select('tahun_anggaran')->distinct()->orderBy('tahun_anggaran', 'desc')->pluck('tahun_anggaran');
        if($years->isEmpty()) $years = collect([date('Y')]);
        
        $jenisWadahs = JenisWadahKategorial::orderBy('nama_wadah')->get();

        return view('admin.wadah.anggaran.index', compact('anggarans', 'years', 'jenisWadahs'));
    }

    public function create()
    {
        $jenisWadahs = JenisWadahKategorial::orderBy('nama_wadah')->get();
        $klasisList = Klasis::orderBy('nama_klasis')->get();
        $jemaatList = Jemaat::orderBy('nama_jemaat')->get();

        return view('admin.wadah.anggaran.create', compact('jenisWadahs', 'klasisList', 'jemaatList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_anggaran' => 'required|integer',
            'jenis_wadah_id' => 'required|exists:jenis_wadah_kategorials,id',
            'tingkat' => 'required|in:sinode,klasis,jemaat',
            'nama_pos_anggaran' => 'required|string|max:255',
            'jumlah_target' => 'required|numeric|min:0',
            'jenis_anggaran' => 'required|in:penerimaan,pengeluaran',
            'program_kerja_id' => 'nullable|exists:wadah_kategorial_program_kerja,id',
        ]);

        $data = $request->all();

        // Pembersihan Data Otomatis (Sanitasi Tingkat)
        if ($data['tingkat'] == 'sinode') {
            $data['klasis_id'] = null;
            $data['jemaat_id'] = null;
        } elseif ($data['tingkat'] == 'klasis') {
            $data['jemaat_id'] = null;
        }

        // Inisialisasi awal realisasi
        $data['jumlah_realisasi'] = 0;

        WadahKategorialAnggaran::create($data);

        return redirect()->route('admin.wadah.anggaran.index')->with('success', 'Pos Anggaran berhasil didaftarkan.');
    }

    public function edit(WadahKategorialAnggaran $anggaran)
    {
        return view('admin.wadah.anggaran.edit', compact('anggaran'));
    }

    public function update(Request $request, WadahKategorialAnggaran $anggaran)
    {
        $request->validate([
            'nama_pos_anggaran' => 'required|string|max:255',
            'jumlah_target' => 'required|numeric|min:0',
            'jenis_anggaran' => 'required|in:penerimaan,pengeluaran',
        ]);

        // Saat update, tingkat dan wilayah di-lock, kita hanya update rinciannya
        $anggaran->update($request->only(['nama_pos_anggaran', 'jenis_anggaran', 'jumlah_target', 'keterangan', 'program_kerja_id']));

        return redirect()->route('admin.wadah.anggaran.index')->with('success', 'Pos Anggaran berhasil diperbarui.');
    }

    public function destroy(WadahKategorialAnggaran $anggaran)
    {
        $anggaran->delete();
        return redirect()->route('admin.wadah.anggaran.index')->with('success', 'Pos Anggaran berhasil dihapus secara permanen.');
    }

    public function show($id)
    {
        $anggaran = WadahKategorialAnggaran::with(['jenisWadah', 'programKerja', 'klasis', 'jemaat', 'transaksi'])->findOrFail($id);
        return view('admin.wadah.anggaran.show', compact('anggaran'));
    }

    /**
     * API: Get Program Kerja by Wadah & Tahun (untuk Dropdown di Form Create Anggaran)
     */
    public function getPrograms(Request $request)
    {
        $query = WadahKategorialProgramKerja::where('jenis_wadah_id', $request->wadah_id)
            ->where('tahun_program', $request->tahun)
            ->where('tingkat', $request->tingkat);

        // Pastikan hanya menarik program dari wilayah yang sesuai
        if ($request->tingkat == 'klasis') {
            $query->where('klasis_id', $request->klasis_id);
        } elseif ($request->tingkat == 'jemaat') {
            $query->where('jemaat_id', $request->jemaat_id);
        }

        $programs = $query->select('id', 'nama_program')->get();

        return response()->json($programs);
    }
}