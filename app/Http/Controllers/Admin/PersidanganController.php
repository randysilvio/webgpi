<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RisalahSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PersidanganController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = RisalahSidang::query();

        // Scoping Data Wilayah
        if ($user->hasRole('Admin Klasis')) {
            $query->where('klasis_id', $user->klasis_id)->orWhere('tingkat_sidang', 'Klasis');
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        $sidangs = $query->latest('tanggal_sidang')->paginate(10);
        return view('admin.tata_gereja.sidang.index', compact('sidangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_sidang' => 'required|string',
            'tanggal_sidang' => 'required|date',
            'tingkat_sidang' => 'required|in:Jemaat,Klasis,Sinode',
            'file_risalah' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $data = $request->all();
        $user = Auth::user();

        // Otomatisasi Wilayah
        $data['klasis_id'] = $user->klasis_id;
        $data['jemaat_id'] = $user->jemaat_id;

        if ($request->hasFile('file_risalah')) {
            $data['file_risalah'] = $request->file('file_risalah')->store('risalah_sidang', 'public');
        }

        RisalahSidang::create($data);
        return redirect()->back()->with('success', 'Risalah Sidang berhasil diarsipkan.');
    }
}