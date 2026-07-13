<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MutasiPendeta;
use App\Models\Pegawai;
use App\Models\Klasis;
use App\Models\Jemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MutasiPendetaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('role:Super Admin|Admin Bidang 3');
    }

    /**
     * Tampilkan daftar semua riwayat mutasi personel.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = MutasiPendeta::with([
                    'pegawai',
                    'asalKlasis', 'asalJemaat',
                    'tujuanKlasis', 'tujuanJemaat'
                 ])->latest('tanggal_sk');

        // RBAC WILAYAH
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $query->where(function($q) use ($user) {
                $q->where('asal_klasis_id', $user->klasis_id)
                  ->orWhere('tujuan_klasis_id', $user->klasis_id);
            });
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $query->where(function($q) use ($user) {
                $q->where('asal_jemaat_id', $user->jemaat_id)
                  ->orWhere('tujuan_jemaat_id', $user->jemaat_id);
            });
        }

        // FILTER PENCARIAN
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('nomor_sk', 'like', $searchTerm)
                  ->orWhereHas('pegawai', function($subQ) use ($searchTerm) {
                      $subQ->where('nama_lengkap', 'like', $searchTerm)
                           ->orWhere('nipg', 'like', $searchTerm)
                           ->orWhere('nip', 'like', $searchTerm);
                  });
            });
        }
        if ($request->filled('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        $mutasiHistory = $query->paginate(20)->appends($request->query());
        $jenisMutasiOptions = MutasiPendeta::select('jenis_mutasi')->distinct()->pluck('jenis_mutasi', 'jenis_mutasi');

        return view('admin.mutasi.index', compact('mutasiHistory', 'jenisMutasiOptions', 'request'));
    }

    /**
     * Form penambahan mutasi baru untuk spesifik Pegawai
     */
    public function create(Pegawai $pegawai)
    {
        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        $jemaatOptions = Jemaat::orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
        
        $asalKlasisId = $pegawai->klasis_id;
        $asalJemaatId = $pegawai->jemaat_id;

        return view('admin.mutasi.create', compact(
            'pegawai',
            'klasisOptions',
            'jemaatOptions',
            'asalKlasisId',
            'asalJemaatId'
        ));
    }

    /**
     * Simpan Mutasi Baru dan Lakukan Update Wilayah pada Pegawai & User
     */
    public function store(Request $request, Pegawai $pegawai)
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
        
        $validatedData['pegawai_id'] = $pegawai->id;

        DB::beginTransaction();
        try {
            $mutasi = MutasiPendeta::create($validatedData);

            $tujuanKlasisId = $mutasi->tujuan_klasis_id;
            $tujuanJemaatId = $mutasi->tujuan_jemaat_id;
            
            // Logika Sanitasi Pensiun/Meninggal
            if (in_array(strtolower($mutasi->jenis_mutasi), ['emeritus', 'pensiun', 'keluar', 'meninggal'])) {
                 $tujuanKlasisId = null; 
                 $tujuanJemaatId = null;
                 $pegawai->status_aktif = ucfirst($mutasi->jenis_mutasi);
            }

            // Update penempatan di tabel pegawai
            $pegawai->update([
                'klasis_id' => $tujuanKlasisId,
                'jemaat_id' => $tujuanJemaatId,
            ]);

            // Update penempatan di tabel users (Akses Login)
            if ($pegawai->user) {
                $pegawai->user->update([
                    'klasis_id' => $tujuanKlasisId,
                    'jemaat_id' => $tujuanJemaatId,
                ]);
            }

            DB::commit();
            
            return redirect()->route('admin.kepegawaian.pegawai.show', $pegawai->id)
                             ->with('success', 'Arsip mutasi dan penyesuaian wilayah kedinasan personel berhasil diproses.');
                             
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal simpan mutasi (Pegawai ID: {$pegawai->id}). Error: " . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Gagal memproses surat mutasi. Kesalahan sistem: ' . $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Tampilkan detail dokumen Mutasi
     */
    public function show($id)
    {
        $mutasi = MutasiPendeta::with(['pegawai', 'asalKlasis', 'asalJemaat', 'tujuanKlasis', 'tujuanJemaat'])->findOrFail($id);
        
        return view('admin.mutasi.show', compact('mutasi'));
    }

    /**
     * Tampilkan Form Koreksi Data Historis Mutasi
     */
    public function edit($id)
    {
        $mutasi = MutasiPendeta::with('pegawai')->findOrFail($id);
        
        return view('admin.mutasi.edit', compact('mutasi'));
    }

    /**
     * Update data SK (Tanpa memicu trigger pemindahan lokasi, hanya mengubah label historis)
     */
    public function update(Request $request, $id)
    {
        $mutasi = MutasiPendeta::findOrFail($id);
        
        $validatedData = $request->validate([
            'nomor_sk' => 'required|string|max:255|unique:mutasi_pendeta,nomor_sk,' . $mutasi->id,
            'tanggal_sk' => 'required|date',
            'tanggal_efektif' => 'nullable|date',
            'keterangan' => 'nullable|string',
            // Field khusus ini hanya string label di view edit yang disediakan
            'asal_instansi' => 'nullable|string',
            'tujuan_instansi' => 'nullable|string',
        ]);

        $mutasi->update($validatedData);

        return redirect()->route('admin.mutasi.show', $mutasi->id)->with('success', 'Data historis SK Mutasi berhasil dikoreksi.');
    }

    /**
     * Membatalkan/Menghapus Mutasi
     */
    public function destroy($id)
    {
        $mutasi = MutasiPendeta::findOrFail($id);
        
        try {
            $mutasi->delete();
            return redirect()->route('admin.mutasi.index')->with('success', 'Arsip Surat Mutasi berhasil dihapus/dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus mutasi. Error: ' . $e->getMessage());
        }
    }
}