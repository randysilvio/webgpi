<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JurnalPelayanan;
use App\Models\Jemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JurnalPelayananController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = JurnalPelayanan::with(['jemaat.klasis', 'pendeta'])->latest('tanggal_kegiatan');

        // LOGIKA PENYEKATAN DATA BERJENJANG (GRANULAR ACCESS)
        if ($user->hasRole('Pendeta')) {
            // Pendeta HANYA bisa melihat jurnal di Jemaat tempatnya bertugas saat ini
            if (!$user->pegawai || !$user->pegawai->jemaat_id) {
                return redirect()->route('admin.dashboard')->with('error', 'Akun Anda belum ditugaskan pada Jemaat manapun.');
            }
            $query->where('jemaat_id', $user->pegawai->jemaat_id);
        } 
        elseif ($user->hasRole('Admin Klasis')) {
            // Klasis hanya melihat jurnal di jemaat bawahannya
            $query->whereHas('jemaat', function($q) use ($user) {
                $q->where('klasis_id', $user->klasis_id);
            });
        }
        // Super Admin & Admin Sinode (Bidang 1/Pertimbangan) bisa melihat semuanya

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $jurnals = $query->paginate(15);
        return view('admin.jurnal.index', compact('jurnals'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // HANYA Pendeta yang sedang bertugas yang boleh mengisi jurnal
        if (!$user->hasRole('Pendeta') || !$user->pegawai || !$user->pegawai->jemaat_id) {
            return redirect()->route('admin.jurnal.index')->with('error', 'Hanya Pendeta yang memiliki SK Penempatan yang diizinkan mengisi Jurnal Pelayanan.');
        }

        $jemaat = Jemaat::find($user->pegawai->jemaat_id);
        return view('admin.jurnal.create', compact('jemaat'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->pegawai || !$user->pegawai->jemaat_id) {
            return back()->with('error', 'Akses Ditolak: Anda tidak terdaftar pada Jemaat manapun.');
        }

        $validated = $request->validate([
            'kategori' => 'required|string',
            'tanggal_kegiatan' => 'required|date',
            'konteks_situasi' => 'required|string',
            'tindak_lanjut' => 'nullable|string',
        ]);

        try {
            JurnalPelayanan::create([
                'jemaat_id' => $user->pegawai->jemaat_id,     // Kunci ke Jemaat saat ini
                'pegawai_id' => $user->pegawai->id,           // Kunci ke Pendeta penulis
                'kategori' => $validated['kategori'],
                'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
                'konteks_situasi' => $validated['konteks_situasi'],
                'tindak_lanjut' => $validated['tindak_lanjut'],
            ]);

            return redirect()->route('admin.jurnal.index')->with('success', 'Dokumen Jurnal Pelayanan berhasil diarsipkan ke Pangkalan Data Jemaat.');
        } catch (\Exception $e) {
            Log::error('Error Jurnal Pelayanan: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem administrasi.');
        }
    }

    public function show(JurnalPelayanan $jurnal)
    {
        return view('admin.jurnal.show', compact('jurnal'));
    }
}