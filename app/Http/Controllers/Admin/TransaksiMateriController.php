<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransaksiMateri;
use App\Models\MateriKhotbah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransaksiMateriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Pendeta') && $user->pegawai) {
            // Tampilan histori transaksi milik Pendeta itu sendiri
            $transaksis = TransaksiMateri::with('materi')->where('pegawai_id', $user->pegawai->id)->latest()->paginate(15);
            return view('admin.bursa.transaksi.riwayat_pendeta', compact('transaksis'));
        } 
        elseif ($user->hasAnyRole(['Super Admin', 'Admin Bidang 1'])) {
            // Tampilan pusat verifikasi untuk Bidang 1
            $transaksis = TransaksiMateri::with(['materi', 'pendeta'])->latest()->paginate(15);
            $pendingCount = TransaksiMateri::where('status_pembayaran', 'Menunggu Verifikasi')->count();
            return view('admin.bursa.transaksi.pusat_verifikasi', compact('transaksis', 'pendingCount'));
        }

        return redirect()->route('admin.dashboard');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Pendeta') || !$user->pegawai) abort(403);

        $request->validate([
            'materi_khotbah_id' => 'required|exists:materi_khotbahs,id',
            'bukti_transfer' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Cek apakah sudah pernah mengajukan transaksi yang pending/lunas
        $cekAda = TransaksiMateri::where('materi_khotbah_id', $request->materi_khotbah_id)
                                 ->where('pegawai_id', $user->pegawai->id)
                                 ->whereIn('status_pembayaran', ['Menunggu Verifikasi', 'Lunas'])
                                 ->first();
        if ($cekAda) {
            return back()->with('error', 'Dokumen ini sudah dalam status pengajuan atau telah lunas.');
        }

        $buktiPath = $request->file('bukti_transfer')->store('bukti_transfer_liturgi', 'public');
        $nomorRegistrasi = 'TRX-' . date('Ymd') . '-' . rand(1000, 9999);

        TransaksiMateri::create([
            'nomor_registrasi' => $nomorRegistrasi,
            'materi_khotbah_id' => $request->materi_khotbah_id,
            'pegawai_id' => $user->pegawai->id,
            'status_pembayaran' => 'Menunggu Verifikasi',
            'bukti_transfer_path' => $buktiPath,
        ]);

        return redirect()->route('admin.bursa.transaksi.index')->with('success', 'Permohonan otorisasi dokumen berhasil dikirim. Menunggu verifikasi Bidang 1.');
    }

    public function update(Request $request, TransaksiMateri $transaksi)
    {
        if (!Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 1'])) abort(403);

        $request->validate([
            'status_pembayaran' => 'required|in:Lunas,Ditolak',
            'catatan_admin' => 'nullable|string',
        ]);

        $transaksi->update([
            'status_pembayaran' => $request->status_pembayaran,
            'catatan_admin' => $request->catatan_admin,
            'tanggal_verifikasi' => now(),
        ]);

        $pesan = $request->status_pembayaran == 'Lunas' ? 'Akses dokumen telah dibuka.' : 'Permohonan ditolak.';
        return back()->with('success', 'Verifikasi berhasil diproses: ' . $pesan);
    }
}