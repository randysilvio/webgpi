<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PopupAd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PopupAdController extends Controller
{
    public function index()
    {
        $popups = PopupAd::latest()->get();
        return view('admin.popup.index', compact('popups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'mulai_tanggal' => 'required|date',
            'selesai_tanggal' => 'required|date|after_or_equal:mulai_tanggal',
        ]);

        $path = $request->file('gambar')->store('popup_ads', 'public');

        PopupAd::create([
            'judul' => $request->judul,
            'gambar_path' => $path,
            'mulai_tanggal' => $request->mulai_tanggal,
            'selesai_tanggal' => $request->selesai_tanggal,
            'is_active' => true,
        ]);

        return back()->with('success', 'Popup berhasil ditambahkan.');
    }

    public function destroy(PopupAd $popup)
    {
        if ($popup->gambar_path) {
            Storage::disk('public')->delete($popup->gambar_path);
        }
        $popup->delete();
        return back()->with('success', 'Popup dihapus.');
    }

    public function toggle(PopupAd $popup)
    {
        $popup->update(['is_active' => !$popup->is_active]);
        return back()->with('success', 'Status popup diubah.');
    }
}