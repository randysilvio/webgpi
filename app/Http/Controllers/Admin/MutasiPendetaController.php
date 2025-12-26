<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MutasiPendeta;
use App\Models\Pendeta;
use App\Models\Klasis;
use App\Models\Jemaat;
use Illuminate\Http\Request; // <-- Tambahkan Request di index
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MutasiPendetaController extends Controller
{
    // Middleware untuk hak akses
    public function __construct()
    {
        $this->middleware(['auth']);
        // Izinkan Super Admin & Admin Bidang 3 melihat/mengelola semua
        $this->middleware('role:Super Admin|Admin Bidang 3');
        // (Opsional) Jika role lain boleh melihat index, tambahkan middleware terpisah:
        // $this->middleware('role:Super Admin|Admin Bidang 3|NamaRoleLain')->only(['index']);
    }

    /**
     * (Baru) Display a listing of the resource.
     * Tampilkan daftar semua riwayat mutasi pendeta.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = MutasiPendeta::with([
                    'pendeta',
                    'asalKlasis', 'asalJemaat',
                    'tujuanKlasis', 'tujuanJemaat'
                 ])->latest('tanggal_sk'); // Urutkan berdasarkan Tgl SK terbaru

        // --- Fitur Filter (Opsional) ---
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('nomor_sk', 'like', $searchTerm)
                  ->orWhereHas('pendeta', function($subQ) use ($searchTerm) {
                      $subQ->where('nama_lengkap', 'like', $searchTerm)
                           ->orWhere('nipg', 'like', $searchTerm);
                  });
            });
        }
        if ($request->filled('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }
        // Tambahkan filter lain jika perlu (misal berdasarkan klasis tujuan/asal)

        $mutasiHistory = $query->paginate(20)->appends($request->query()); // Paginasi

        // Ambil opsi untuk filter jenis mutasi
        $jenisMutasiOptions = MutasiPendeta::select('jenis_mutasi')->distinct()->pluck('jenis_mutasi', 'jenis_mutasi');

        return view('admin.mutasi_pendeta.index', compact('mutasiHistory', 'jenisMutasiOptions', 'request'));
    }


    /**
     * Show the form for creating a new resource.
     * (Kode create tetap sama)
     *
     * @param  \App\Models\Pendeta  $pendeta
     * @return \Illuminate\View\View
     */
    public function create(Pendeta $pendeta)
    {
        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        $jemaatOptions = Jemaat::orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
        $asalKlasisId = $pendeta->klasis_penempatan_id;
        $asalJemaatId = $pendeta->jemaat_penempatan_id;

        return view('admin.mutasi_pendeta.create', compact(
            'pendeta',
            'klasisOptions',
            'jemaatOptions',
            'asalKlasisId',
            'asalJemaatId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * (Kode store tetap sama)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pendeta  $pendeta
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Pendeta $pendeta)
    {
        $validatedData = $request->validate([
            'tanggal_sk' => 'required|date',
            'nomor_sk' => 'required|string|max:255|unique:mutasi_pendeta,nomor_sk',
            'jenis_mutasi' => 'required|string|max:100',
            'asal_klasis_id' => 'nullable|exists:klasis,id',
            'asal_jemaat_id' => 'nullable|exists:jemaat,id',
            'tujuan_klasis_id' => 'nullable|exists:klasis,id',
            'tujuan_jemaat_id' => 'nullable|exists:jemaat,id',
            'tanggal_efektif' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);
        $validatedData['pendeta_id'] = $pendeta->id;

        DB::beginTransaction();
        try {
            $mutasi = MutasiPendeta::create($validatedData);
            Log::info("Riwayat mutasi baru ID: {$mutasi->id} (Pendeta ID: {$pendeta->id})");

            $tujuanKlasisId = $mutasi->tujuan_klasis_id;
            $tujuanJemaatId = $mutasi->tujuan_jemaat_id;
            if (in_array(strtolower($mutasi->jenis_mutasi), ['emeritus', 'keluar', 'meninggal'])) {
                 $tujuanKlasisId = null; $tujuanJemaatId = null;
            }

            $pendeta->update([
                'klasis_penempatan_id' => $tujuanKlasisId,
                'jemaat_penempatan_id' => $tujuanJemaatId,
            ]);
            Log::info("Penempatan Pendeta ID: {$pendeta->id} diupdate.");

            if ($pendeta->user) {
                $pendeta->user->update([
                    'klasis_id' => $tujuanKlasisId,
                    'jemaat_id' => $tujuanJemaatId,
                ]);
                Log::info("Penempatan User ID: {$pendeta->user->id} diupdate.");
            }

            DB::commit();
            return redirect()->route('admin.pendeta.show', $pendeta->id)
                             ->with('success', 'Data mutasi Pendeta berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal simpan mutasi (Pendeta ID: {$pendeta->id}). Error: " . $e->getMessage());
            return redirect()->route('admin.pendeta.mutasi.create', $pendeta->id)
                             ->with('error', 'Gagal menyimpan data mutasi. Error: ' . $e->getMessage())
                             ->withInput();
        }
    }

    // Method lain (show, edit, update, destroy untuk mutasi) bisa ditambahkan di sini
    // public function show(MutasiPendeta $mutasi) { /* Tampilkan detail 1 mutasi */ }
    // public function edit(MutasiPendeta $mutasi) { /* Form edit mutasi */ }
    // public function update(Request $request, MutasiPendeta $mutasi) { /* Proses update mutasi */ }
    // public function destroy(MutasiPendeta $mutasi) { /* Hapus riwayat mutasi */ }
}