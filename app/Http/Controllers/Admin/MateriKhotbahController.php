<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MateriKhotbah;
use App\Models\TransaksiMateri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MateriKhotbahController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // PENGATURAN TAMPILAN BERDASARKAN ROLE
        if ($user->hasRole('Pendeta')) {
            // Pendeta melihat tampilan Katalog (Card)
            $materis = MateriKhotbah::where('is_active', true)->latest()->paginate(12);
            return view('admin.bursa.katalog', compact('materis'));
        } 
        elseif ($user->hasAnyRole(['Super Admin', 'Admin Bidang 1'])) {
            // Admin Bidang 1 melihat tampilan Tabel Administratif
            $materis = MateriKhotbah::with('author')->latest()->paginate(15);
            return view('admin.bursa.index', compact('materis'));
        }

        return redirect()->route('admin.dashboard')->with('error', 'Akses dibatasi. Modul ini hanya untuk Bidang 1 dan Pendeta.');
    }

    public function create()
    {
        if (!Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 1'])) abort(403);
        return view('admin.bursa.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 1'])) abort(403);

        $request->validate([
            'judul_dokumen' => 'required|string|max:255',
            'kategori' => 'required|string',
            'deskripsi_singkat' => 'required|string',
            'harga_dokumen' => 'required|numeric|min:0',
            'file_dokumen' => 'required|file|mimes:pdf,doc,docx|max:10240', // Maks 10MB
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // Simpan File Dokumen Utama (Tertutup dari publik)
            $filePath = $request->file('file_dokumen')->store('dokumen_liturgi', 'local'); // Disimpan di folder local, bukan public!
            
            // Simpan Cover (Terbuka untuk katalog)
            $coverPath = null;
            if ($request->hasFile('cover_image')) {
                $coverPath = $request->file('cover_image')->store('cover_liturgi', 'public');
            }

            MateriKhotbah::create([
                'judul_dokumen' => $request->judul_dokumen,
                'kategori' => $request->kategori,
                'deskripsi_singkat' => $request->deskripsi_singkat,
                'harga_dokumen' => $request->harga_dokumen,
                'file_path' => $filePath,
                'cover_path' => $coverPath,
                'is_active' => true,
                'author_id' => Auth::id(),
            ]);

            return redirect()->route('admin.bursa.index')->with('success', 'Dokumen berhasil diunggah ke Pangkalan Data Sinode.');
        } catch (\Exception $e) {
            Log::error('Upload Dokumen Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat memproses dokumen.');
        }
    }

    public function edit(MateriKhotbah $bursa)
    {
        if (!Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 1'])) abort(403);
        return view('admin.bursa.edit', compact('bursa'));
    }

    public function update(Request $request, MateriKhotbah $bursa)
    {
        if (!Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 1'])) abort(403);

        $request->validate([
            'judul_dokumen' => 'required|string|max:255',
            'kategori' => 'required|string',
            'deskripsi_singkat' => 'required|string',
            'harga_dokumen' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
            'file_dokumen' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['file_dokumen', 'cover_image']);

        if ($request->hasFile('file_dokumen')) {
            if (Storage::disk('local')->exists($bursa->file_path)) Storage::disk('local')->delete($bursa->file_path);
            $data['file_path'] = $request->file('file_dokumen')->store('dokumen_liturgi', 'local');
        }

        if ($request->hasFile('cover_image')) {
            if ($bursa->cover_path && Storage::disk('public')->exists($bursa->cover_path)) Storage::disk('public')->delete($bursa->cover_path);
            $data['cover_path'] = $request->file('cover_image')->store('cover_liturgi', 'public');
        }

        $bursa->update($data);
        return redirect()->route('admin.bursa.index')->with('success', 'Atribut dokumen berhasil diperbarui.');
    }

    public function destroy(MateriKhotbah $bursa)
    {
        if (!Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 1'])) abort(403);

        if (Storage::disk('local')->exists($bursa->file_path)) Storage::disk('local')->delete($bursa->file_path);
        if ($bursa->cover_path && Storage::disk('public')->exists($bursa->cover_path)) Storage::disk('public')->delete($bursa->cover_path);
        
        $bursa->delete();
        return redirect()->route('admin.bursa.index')->with('success', 'Dokumen beserta lampirannya telah dimusnahkan.');
    }

    /**
     * FUNGSI KEAMANAN PENGUNDUHAN DOKUMEN (SECURE DOWNLOAD)
     */
    public function download(MateriKhotbah $bursa)
    {
        $user = Auth::user();

        // 1. Super Admin dan Bidang 1 Bebas Download
        if ($user->hasAnyRole(['Super Admin', 'Admin Bidang 1'])) {
            return Storage::disk('local')->download($bursa->file_path, $bursa->judul_dokumen . '.' . pathinfo($bursa->file_path, PATHINFO_EXTENSION));
        }

        // 2. Logika Akses Pendeta
        if ($user->hasRole('Pendeta') && $user->pegawai) {
            
            // Jika dokumen gratis (Harga 0), langsung izinkan
            if ($bursa->harga_dokumen == 0) {
                return Storage::disk('local')->download($bursa->file_path, $bursa->judul_dokumen . '.' . pathinfo($bursa->file_path, PATHINFO_EXTENSION));
            }

            // Cek apakah ada transaksi Lunas untuk dokumen ini
            $akses = TransaksiMateri::where('materi_khotbah_id', $bursa->id)
                                    ->where('pegawai_id', $user->pegawai->id)
                                    ->where('status_pembayaran', 'Lunas')
                                    ->first();
            
            if ($akses) {
                return Storage::disk('local')->download($bursa->file_path, $bursa->judul_dokumen . '.' . pathinfo($bursa->file_path, PATHINFO_EXTENSION));
            }
        }

        // Jika lolos semua cek di atas, maka akses ditolak
        return back()->with('error', 'Akses Ditolak: Anda belum memiliki otorisasi pengunduhan atau transaksi belum diverifikasi.');
    }
}