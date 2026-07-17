<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaJemaat;
use App\Models\SakramenNikah;
use App\Models\Pegawai;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SakramenNikahController extends Controller
{
    /**
     * Menampilkan daftar pernikahan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SakramenNikah::with(['suami.jemaat', 'istri.jemaat']);

        // Filter Wilayah
        if ($user->hasRole('Admin Jemaat')) {
            $query->whereHas('suami', fn($q) => $q->where('jemaat_id', $user->jemaat_id));
        } elseif ($user->hasRole('Admin Klasis')) {
            $query->whereHas('suami.jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        }

        // Pencarian
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('suami', fn($sub) => $sub->where('nama_lengkap', 'like', "%{$request->search}%"))
                  ->orWhereHas('istri', fn($sub) => $sub->where('nama_lengkap', 'like', "%{$request->search}%"))
                  ->orWhere('no_akta_nikah', 'like', "%{$request->search}%");
            });
        }

        $nikahs = $query->latest('tanggal_nikah')->paginate(10);

        return view('admin.sakramen.nikah.index', compact('nikahs'));
    }

    /**
     * Menampilkan Form Tambah
     */
    public function create()
    {
        $user = Auth::user();

        // Ambil Data Anggota Aktif untuk Dropdown
        $anggotaBase = AnggotaJemaat::where('status_keanggotaan', 'Aktif')
            ->whereIn('status_pernikahan', ['Belum Menikah', 'Cerai Mati', 'Cerai Hidup']); // Filter status

        if ($user->hasRole('Admin Jemaat')) {
            $anggotaBase->where('jemaat_id', $user->jemaat_id);
        }

        $pria = (clone $anggotaBase)->where('jenis_kelamin', 'Laki-laki')->orderBy('nama_lengkap')->get();
        $wanita = (clone $anggotaBase)->where('jenis_kelamin', 'Perempuan')->orderBy('nama_lengkap')->get();
        
        $pendetas = Pegawai::where('jenis_pegawai', 'Pendeta')
                           ->where('status_aktif', 'Aktif') 
                           ->orderBy('nama_lengkap')
                           ->get();

        return view('admin.sakramen.nikah.create', compact('pria', 'wanita', 'pendetas'));
    }

    /**
     * Menyimpan Data
     */
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

            // Generate Kode Keluarga Baru
            $kodeKeluargaBaru = $request->no_kk_baru ?? ('KK-' . date('Ymd') . '-' . strtoupper(Str::random(4)));

            $suami = AnggotaJemaat::findOrFail($request->suami_id);
            $istri = AnggotaJemaat::findOrFail($request->istri_id);

            // 1. Update Profil Suami (Kepala Keluarga)
            $suami->update([
                'status_dalam_keluarga' => 'Kepala Keluarga',
                'status_pernikahan' => 'Kawin',
                'kode_keluarga_internal' => $kodeKeluargaBaru,
                'nama_kepala_keluarga' => $suami->nama_lengkap,
            ]);

            // 2. Update Profil Istri
            $istri->update([
                'status_dalam_keluarga' => 'Istri',
                'status_pernikahan' => 'Kawin',
                'kode_keluarga_internal' => $kodeKeluargaBaru,
                'nama_kepala_keluarga' => $suami->nama_lengkap,
                'nomor_kk' => $suami->nomor_kk // Samakan No KK Sipil jika ada
            ]);

            // 3. Simpan Data Sakramen
            SakramenNikah::create($request->all());

            DB::commit();
            return redirect()->route('admin.sakramen.nikah.index')->with('success', 'Pernikahan berhasil dicatat & Status Anggota diperbarui.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal Memproses: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan Form Edit
     */
    public function edit($id)
    {
        $nikah = SakramenNikah::with(['suami', 'istri'])->findOrFail($id);
        
        $pendetas = Pegawai::where('jenis_pegawai', 'Pendeta')->orderBy('nama_lengkap')->get();

        return view('admin.sakramen.nikah.edit', compact('nikah', 'pendetas'));
    }

    /**
     * Update Data
     */
    public function update(Request $request, $id)
    {
        $nikah = SakramenNikah::findOrFail($id);

        $request->validate([
            'no_akta_nikah' => 'required|unique:sakramen_nikah,no_akta_nikah,'.$id,
            'tanggal_nikah' => 'required|date',
            'tempat_nikah' => 'required|string',
            'pendeta_pelayan' => 'required|string',
        ]);

        $nikah->update($request->all());

        return redirect()->route('admin.sakramen.nikah.index')->with('success', 'Data pernikahan diperbarui.');
    }

    /**
     * Hapus Data
     */
    public function destroy($id)
    {
        try {
            $nikah = SakramenNikah::findOrFail($id);
            $nikah->delete();
            // Catatan: Kita tidak mereset status pernikahan anggota secara otomatis untuk keamanan data historis
            return redirect()->route('admin.sakramen.nikah.index')->with('success', 'Arsip pernikahan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Sertifikat Nikah
     */
    public function cetakSurat($id)
    {
        $data = SakramenNikah::with(['suami.jemaat.klasis', 'istri.jemaat'])->findOrFail($id);
        $setting = Setting::first();

        // Pastikan view ada di folder: resources/views/admin/sakramen/cetak/nikah.blade.php
        return view('admin.sakramen.cetak.nikah', compact('data', 'setting'));
    }
}