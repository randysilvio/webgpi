<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SuratKeluarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SuratKeluar::query();

        // Barikade Wilayah (Scoping Data)
        if ($user->hasRole('Admin Klasis')) {
            $query->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        $surats = $query->latest('tanggal_surat')->paginate(15);
        return view('admin.e_office.surat_keluar.index', compact('surats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_surat' => 'required|unique:surat_keluar,no_surat',
            'tanggal_surat' => 'required|date',
            'tujuan_surat' => 'required',
            'perihal' => 'required',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $user = Auth::user();
        $data = $request->all();

        // Otomatisasi Wilayah
        $data['klasis_id'] = $user->klasis_id;
        $data['jemaat_id'] = $user->jemaat_id;

        if ($request->hasFile('file_surat')) {
            $data['file_path'] = $request->file('file_surat')->store('surat_keluar', 'public');
        }

        SuratKeluar::create($data);
        return redirect()->back()->with('success', 'Surat Keluar berhasil diarsipkan.');
    }

    public function destroy(SuratKeluar $surat_keluar)
    {
        if ($surat_keluar->file_path) {
            Storage::disk('public')->delete($surat_keluar->file_path);
        }
        $surat_keluar->delete();
        return redirect()->back()->with('success', 'Arsip surat berhasil dihapus.');
    }
}