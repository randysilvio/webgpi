<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Klasis;
use App\Models\Jemaat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf; // Library PDF

class PegawaiController extends Controller
{
    /**
     * Menampilkan daftar pegawai dengan filter.
     */
    public function index(Request $request)
    {
        $query = Pegawai::with(['klasis', 'jemaat', 'user']);

        // Filter Pencarian (Nama / NIPG)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nipg', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Jenis Pegawai
        if ($request->filled('jenis')) {
            $query->where('jenis_pegawai', $request->jenis);
        }

        // Filter Status Aktif
        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status);
        }

        $pegawais = $query->latest()->paginate(15)->withQueryString();

        return view('admin.pegawai.index', compact('pegawais'));
    }

    /**
     * Menampilkan form tambah pegawai.
     */
    public function create()
    {
        $klasisList = Klasis::orderBy('nama_klasis')->get();
        // Data Jemaat akan di-load via AJAX di view berdasarkan Klasis yang dipilih
        return view('admin.pegawai.create', compact('klasisList'));
    }

    /**
     * Menyimpan data pegawai baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nipg' => 'required|string|unique:pegawai,nipg|max:30',
            'jenis_pegawai' => 'required|string',
            'status_kepegawaian' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'email' => 'nullable|email|unique:users,email',
            'klasis_id' => 'nullable|exists:klasis,id',
            'jemaat_id' => 'nullable|exists:jemaat,id',
            'foto_diri' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Buat Akun User Otomatis
                // Email default jika kosong: nipg@gpipapua.org (dibersihkan dari spasi/karakter aneh)
                $email = $request->email ?? strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->nipg) . '@gpipapua.org');
                
                $user = User::create([
                    'name' => $request->nama_lengkap,
                    'email' => $email,
                    'password' => Hash::make($request->nipg), // Password default = NIPG
                ]);

                // Assign Role berdasarkan Jenis Pegawai
                if ($request->jenis_pegawai == 'Pendeta') {
                    $user->assignRole('Pendeta');
                } 
                // Bisa tambahkan logika role lain disini (Pengajar, Staff, dll)

                // 2. Upload Foto (Jika ada)
                $fotoPath = null;
                if ($request->hasFile('foto_diri')) {
                    $fotoPath = $request->file('foto_diri')->store('foto_pegawai', 'public');
                }

                // 3. Hitung Tanggal Pensiun (Otomatis +60 Tahun dari Tgl Lahir)
                $tanggalPensiun = \Carbon\Carbon::parse($request->tanggal_lahir)->addYears(60);

                // 4. Simpan Data Pegawai
                Pegawai::create([
                    'user_id' => $user->id,
                    'nipg' => $request->nipg,
                    'nama_lengkap' => $request->nama_lengkap,
                    'gelar_depan' => $request->gelar_depan,
                    'gelar_belakang' => $request->gelar_belakang,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'status_pernikahan' => $request->status_pernikahan,
                    'golongan_darah' => $request->golongan_darah,
                    'nik_ktp' => $request->nik_ktp,
                    'alamat_domisili' => $request->alamat_domisili,
                    'no_hp' => $request->no_hp,
                    'email' => $email,
                    'jenis_pegawai' => $request->jenis_pegawai,
                    'status_kepegawaian' => $request->status_kepegawaian,
                    'status_aktif' => 'Aktif', // Default aktif saat dibuat
                    'golongan_terakhir' => $request->golongan_terakhir,
                    'jabatan_terakhir' => $request->jabatan_terakhir,
                    'tmt_pegawai' => $request->tmt_pegawai,
                    'tanggal_pensiun' => $tanggalPensiun,
                    'klasis_id' => $request->klasis_id,
                    'jemaat_id' => $request->jemaat_id,
                    'foto_diri' => $fotoPath,
                    'npwp' => $request->npwp,
                    'no_bpjs_kesehatan' => $request->no_bpjs_kesehatan,
                    'no_bpjs_ketenagakerjaan' => $request->no_bpjs_ketenagakerjaan,
                ]);
            });

            return redirect()->route('admin.kepegawaian.pegawai.index')
                             ->with('success', 'Data Pegawai dan Akun User berhasil dibuat.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail profil pegawai.
     */
    public function show(Pegawai $pegawai)
    {
        // Load relasi untuk ditampilkan di tab-tab profil (Keluarga, Pendidikan, SK)
        $pegawai->load(['keluarga', 'pendidikan', 'riwayatSk', 'klasis', 'jemaat', 'user']);
        return view('admin.pegawai.show', compact('pegawai'));
    }

    /**
     * Menampilkan form edit data dasar.
     */
    public function edit(Pegawai $pegawai)
    {
        $klasisList = Klasis::orderBy('nama_klasis')->get();
        // Load jemaat berdasarkan klasis pegawai saat ini
        $jemaatList = Jemaat::where('klasis_id', $pegawai->klasis_id)->get();
        
        return view('admin.pegawai.edit', compact('pegawai', 'klasisList', 'jemaatList'));
    }

    /**
     * Memperbarui data dasar pegawai.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nipg' => ['required', Rule::unique('pegawai')->ignore($pegawai->id)],
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($pegawai->user_id)],
            'foto_diri' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['foto_diri', 'email']); 
        
        // Update Tanggal Pensiun jika Tanggal Lahir berubah
        if ($request->has('tanggal_lahir')) {
             $data['tanggal_pensiun'] = \Carbon\Carbon::parse($request->tanggal_lahir)->addYears(60);
        }

        // Handle Foto
        if ($request->hasFile('foto_diri')) {
            // Hapus foto lama jika ada
            if ($pegawai->foto_diri && Storage::disk('public')->exists($pegawai->foto_diri)) {
                Storage::disk('public')->delete($pegawai->foto_diri);
            }
            $data['foto_diri'] = $request->file('foto_diri')->store('foto_pegawai', 'public');
        }

        $pegawai->update($data);

        // Sinkronisasi Nama & Email ke User Login
        if($pegawai->user) {
            $updateUser = ['name' => $request->nama_lengkap];
            if ($request->email) {
                $updateUser['email'] = $request->email;
            }
            $pegawai->user->update($updateUser);
        }

        return redirect()->route('admin.kepegawaian.pegawai.show', $pegawai->id)
                         ->with('success', 'Data Pegawai diperbarui.');
    }

    /**
     * Menghapus (Soft Delete) data pegawai.
     */
    public function destroy(Pegawai $pegawai)
    {
        // Opsional: Nonaktifkan user login juga
        if($pegawai->user) {
            // Reset password acak agar tidak bisa login, tapi history data user tetap ada
            $pegawai->user->update([
                'password' => Hash::make(Str::random(16)),
                'email' => 'deleted_' . time() . '_' . $pegawai->user->email // Ubah email agar bisa dipakai lagi jika perlu
            ]);
            $pegawai->user->delete(); // Soft delete user (jika model User pakai SoftDeletes)
        }

        // Hapus foto jika perlu (biasanya di keep kalau soft delete, hapus kalau force delete)
        // if ($pegawai->foto_diri) { Storage::disk('public')->delete($pegawai->foto_diri); }

        $pegawai->delete();

        return redirect()->route('admin.kepegawaian.pegawai.index')
                         ->with('success', 'Data Pegawai dinonaktifkan.');
    }

    /**
     * Cetak Biodata Pegawai ke PDF.
     */
    public function print(Pegawai $pegawai)
    {
        $pegawai->load(['keluarga', 'pendidikan', 'riwayatSk', 'klasis', 'jemaat']);

        $pdf = Pdf::loadView('admin.pegawai.pdf_biodata', compact('pegawai'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Biodata_' . $pegawai->nipg . '.pdf');
    }
}