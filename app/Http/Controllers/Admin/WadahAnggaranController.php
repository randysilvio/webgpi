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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WadahAnggaranController extends Controller
{
    /**
     * Menampilkan daftar Pos Anggaran (RAB).
     */
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

        // --- 2. Filter ---
        if ($request->filled('tahun')) $query->where('tahun_anggaran', $request->tahun);
        if ($request->filled('wadah')) $query->where('jenis_wadah_id', $request->wadah);
        if ($request->filled('tingkat')) $query->where('tingkat', $request->tingkat);
        if ($request->filled('jenis')) $query->where('jenis_anggaran', $request->jenis); // Penerimaan/Pengeluaran

        // Urutkan: Tahun terbaru, Jenis (Penerimaan dulu), lalu Nama
        $anggarans = $query->orderByDesc('tahun_anggaran')
                           ->orderBy('jenis_anggaran') // p (penerimaan) < p (pengeluaran) secara alfabet, tapi logic bisnis biasanya penerimaan dulu
                           ->orderBy('nama_pos_anggaran')
                           ->paginate(15)
                           ->withQueryString();

        $jenisWadahs = JenisWadahKategorial::all();
        $years = WadahKategorialAnggaran::select('tahun_anggaran')->distinct()->orderByDesc('tahun_anggaran')->pluck('tahun_anggaran');

        return view('admin.wadah.anggaran.index', compact('anggarans', 'jenisWadahs', 'years'));
    }

    /**
     * Form Tambah Pos Anggaran.
     */
    public function create()
    {
        $jenisWadahs = JenisWadahKategorial::all();
        $user = Auth::user();
        
        // Data Lokasi
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

        return view('admin.wadah.anggaran.create', compact('jenisWadahs', 'klasisList', 'jemaatList'));
    }

    /**
     * Simpan Pos Anggaran Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_wadah_id' => 'required|exists:jenis_wadah_kategorial,id',
            'tingkat' => ['required', Rule::in(['sinode', 'klasis', 'jemaat'])],
            'klasis_id' => 'required_if:tingkat,klasis|nullable',
            'jemaat_id' => 'required_if:tingkat,jemaat|nullable',
            'tahun_anggaran' => 'required|integer',
            'jenis_anggaran' => 'required|in:penerimaan,pengeluaran',
            'nama_pos_anggaran' => 'required|string|max:255',
            'jumlah_target' => 'required|numeric|min:0',
            'program_kerja_id' => 'nullable|exists:wadah_kategorial_program_kerja,id',
        ]);

        try {
            // Auto-fill location logic
            $klasisId = $request->klasis_id;
            $jemaatId = null;
            if ($request->tingkat == 'jemaat') {
                $jemaatId = $request->jemaat_id;
                $klasisId = Jemaat::find($jemaatId)->klasis_id;
            } elseif ($request->tingkat == 'sinode') {
                $klasisId = null;
            }

            WadahKategorialAnggaran::create([
                'jenis_wadah_id' => $request->jenis_wadah_id,
                'tingkat' => $request->tingkat,
                'klasis_id' => $klasisId,
                'jemaat_id' => $jemaatId,
                'program_kerja_id' => $request->program_kerja_id,
                'tahun_anggaran' => $request->tahun_anggaran,
                'jenis_anggaran' => $request->jenis_anggaran,
                'nama_pos_anggaran' => $request->nama_pos_anggaran,
                'keterangan' => $request->keterangan,
                'jumlah_target' => $request->jumlah_target,
                'jumlah_realisasi' => 0, 
            ]);

            return redirect()->route('admin.wadah.anggaran.index')
                             ->with('success', 'Pos Anggaran berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Detail Anggaran & Riwayat Transaksi.
     */
    public function show(WadahKategorialAnggaran $anggaran)
    {
        // Load transaksi yang terkait dengan pos anggaran ini
        $anggaran->load(['transaksi' => function($q) {
            $q->latest('tanggal_transaksi');
        }, 'transaksi.user', 'programKerja']);

        return view('admin.wadah.anggaran.show', compact('anggaran'));
    }

    /**
     * Edit Anggaran.
     */
    public function edit(WadahKategorialAnggaran $anggaran)
    {
        // Logic mirip create, tapi load data existing
        $jenisWadahs = JenisWadahKategorial::all();
        // ... logic load klasis/jemaat (bisa disederhanakan jika hanya edit nama/target)
        
        return view('admin.wadah.anggaran.edit', compact('anggaran', 'jenisWadahs'));
    }

    /**
     * Update Anggaran.
     */
    public function update(Request $request, WadahKategorialAnggaran $anggaran)
    {
        $request->validate([
            'nama_pos_anggaran' => 'required|string',
            'jumlah_target' => 'required|numeric|min:0',
            'jenis_anggaran' => 'required|in:penerimaan,pengeluaran',
        ]);

        $anggaran->update($request->only(['nama_pos_anggaran', 'jenis_anggaran', 'jumlah_target', 'keterangan', 'program_kerja_id']));

        return redirect()->route('admin.wadah.anggaran.index')->with('success', 'Pos Anggaran diperbarui.');
    }

    public function destroy(WadahKategorialAnggaran $anggaran)
    {
        $anggaran->delete();
        return redirect()->route('admin.wadah.anggaran.index')->with('success', 'Pos Anggaran dihapus.');
    }
    
    /**
     * API: Get Program Kerja by Wadah & Tahun (untuk Dropdown di Form Create Anggaran)
     */
    public function getPrograms(Request $request)
    {
        $query = WadahKategorialProgramKerja::where('jenis_wadah_id', $request->wadah_id)
            ->where('tahun_program', $request->tahun)
            ->where('tingkat', $request->tingkat);
            
        if($request->jemaat_id) $query->where('jemaat_id', $request->jemaat_id);
        elseif($request->klasis_id) $query->where('klasis_id', $request->klasis_id);

        return response()->json($query->select('id', 'nama_program')->get());
    }
}