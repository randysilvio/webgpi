<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaJemaat;
use App\Models\SakramenNikah;
use App\Models\Pegawai; // Menggunakan model Pegawai
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SakramenNikahController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Eager Load relasi nikah
        $query = SakramenNikah::with(['suami.jemaat', 'istri.jemaat']);

        // Scoping Wilayah (Filter Data Berdasarkan Role Login)
        if ($user->hasRole('Admin Jemaat')) {
            $query->whereHas('suami', fn($q) => $q->where('jemaat_id', $user->jemaat_id));
        } elseif ($user->hasRole('Admin Klasis')) {
            $query->whereHas('suami.jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        }

        $nikahs = $query->latest()->paginate(10);
        
        // Filter Mempelai (Hanya Anggota Aktif)
        $anggotaBase = AnggotaJemaat::aktif();
        if ($user->hasRole('Admin Jemaat')) {
            $anggotaBase->where('jemaat_id', $user->jemaat_id);
        }

        $pria = (clone $anggotaBase)->where('jenis_kelamin', 'Laki-laki')->orderBy('nama_lengkap')->get();
        $wanita = (clone $anggotaBase)->where('jenis_kelamin', 'Perempuan')->orderBy('nama_lengkap')->get();
        
        // --- PERBAIKAN DI SINI ---
        // Mengambil data Pendeta yang Status Aktif-nya 'Aktif'
        // (Bukan status_kepegawaian, karena itu isinya Organik/Kontrak)
        $pendetas = Pegawai::where('jenis_pegawai', 'Pendeta')
                           ->where('status_aktif', 'Aktif') 
                           ->orderBy('nama_lengkap')
                           ->get(); 

        return view('admin.sakramen.nikah.index', compact('nikahs', 'pria', 'wanita', 'pendetas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'suami_id' => 'required|exists:anggota_jemaat,id',
            'istri_id' => 'required|exists:anggota_jemaat,id',
            'no_akta_nikah' => 'required|unique:sakramen_nikah,no_akta_nikah',
            'tanggal_nikah' => 'required|date',
            'tempat_nikah' => 'required|string',
            'pendeta_pelayan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Generate Kode Keluarga Baru secara otomatis jika tidak diinput (Opsional logic)
            $kodeKeluargaBaru = $request->no_kk_baru ?? ('KK-' . date('Ymd') . '-' . strtoupper(Str::random(4)));

            $suami = AnggotaJemaat::findOrFail($request->suami_id);
            $istri = AnggotaJemaat::findOrFail($request->istri_id);

            // 1. Update Data Suami (Menjadi Kepala Keluarga)
            $suami->update([
                'status_dalam_keluarga' => 'Kepala Keluarga',
                'status_pernikahan' => 'Kawin',
                'kode_keluarga_internal' => $kodeKeluargaBaru,
                'nama_kepala_keluarga' => $suami->nama_lengkap,
            ]);

            // 2. Update Data Istri (Mengikuti Suami)
            $istri->update([
                'status_dalam_keluarga' => 'Istri',
                'status_pernikahan' => 'Kawin',
                'kode_keluarga_internal' => $kodeKeluargaBaru,
                'nama_kepala_keluarga' => $suami->nama_lengkap,
                // Istri biasanya satu KK dengan suami
                'nomor_kk' => $suami->nomor_kk 
            ]);

            // 3. Simpan Data Sakramen Nikah
            SakramenNikah::create($request->all());

            DB::commit();
            return redirect()->back()->with('success', 'Pernikahan berhasil dicatat.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal Memproses: ' . $e->getMessage());
        }
    }

    public function destroy(SakramenNikah $nikah)
    {
        try {
            $nikah->delete();
            return redirect()->back()->with('success', 'Arsip pernikahan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}