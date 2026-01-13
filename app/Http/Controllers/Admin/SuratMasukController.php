<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SuratMasukController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SuratMasuk::query();

        // 1. Barikade Wilayah (Data Scoping)
        if ($user->hasRole('Admin Klasis')) {
            $query->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        // 2. Fitur Pencarian Surat
        if ($request->search) {
            $query->where('perihal', 'like', "%{$request->search}%")
                  ->orWhere('no_surat', 'like', "%{$request->search}%");
        }

        $surats = $query->latest('tanggal_terima')->paginate(15);
        return view('admin.e_office.surat_masuk.index', compact('surats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'tanggal_terima' => 'required|date',
            'asal_surat' => 'required',
            'perihal' => 'required',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $user = Auth::user();
        $data = $request->all();
        
        // Penomoran Agenda Otomatis (Format: AGENDA/TAHUN/NO)
        $count = SuratMasuk::whereYear('created_at', date('Y'))->count() + 1;
        $data['no_agenda'] = "SM-" . date('Y') . "-" . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Otomatisasi Wilayah
        $data['klasis_id'] = $user->klasis_id;
        $data['jemaat_id'] = $user->jemaat_id;

        if ($request->hasFile('file_surat')) {
            $data['file_path'] = $request->file('file_surat')->store('surat_masuk', 'public');
        }

        SuratMasuk::create($data);
        return redirect()->back()->with('success', 'Surat Masuk Berhasil Diagendakan.');
    }

    /**
     * Fitur Disposisi Digital (Tata Gereja: Instruksi Pimpinan)
     */
    public function disposisi(Request $request, SuratMasuk $surat)
    {
        $surat->update(['status_disposisi' => 'Sudah']);
        // Di sini bisa ditambahkan logika pengiriman notifikasi ke Bidang terkait
        return redirect()->back()->with('success', 'Disposisi berhasil diteruskan.');
    }
}