<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WadahKategorialTransaksi;
use App\Models\WadahKategorialAnggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WadahTransaksiController extends Controller
{
    /**
     * Simpan Transaksi Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'anggaran_id' => 'required|exists:wadah_kategorial_anggaran,id',
            'tanggal_transaksi' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'uraian' => 'required|string|max:255',
            'bukti_transaksi' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
        ]);

        // Validasi Logika: Jenis Transaksi harus sesuai dengan Jenis Anggaran
        $anggaran = WadahKategorialAnggaran::findOrFail($request->anggaran_id);
        // Penerimaan = Masuk, Pengeluaran = Keluar
        $jenisTransaksi = ($anggaran->jenis_anggaran == 'penerimaan') ? 'masuk' : 'keluar';

        // Upload Bukti (Jika ada)
        $buktiPath = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiPath = $request->file('bukti_transaksi')->store('bukti_transaksi', 'public');
        }

        WadahKategorialTransaksi::create([
            'anggaran_id' => $request->anggaran_id,
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'jenis_transaksi' => $jenisTransaksi,
            'jumlah' => $request->jumlah,
            'uraian' => $request->uraian,
            'bukti_transaksi' => $buktiPath,
            'dicatat_oleh_user_id' => Auth::id(),
        ]);

        // Note: Observer akan otomatis mengupdate jumlah_realisasi di tabel Anggaran

        return back()->with('success', 'Transaksi berhasil dicatat.');
    }

    /**
     * Hapus Transaksi.
     */
    public function destroy(WadahKategorialTransaksi $transaksi)
    {
        // Hapus file bukti jika ada
        if ($transaksi->bukti_transaksi) {
            Storage::disk('public')->delete($transaksi->bukti_transaksi);
        }

        $transaksi->delete();
        // Note: Observer akan otomatis mengurangi jumlah_realisasi di tabel Anggaran

        return back()->with('success', 'Transaksi dibatalkan/dihapus.');
    }
}