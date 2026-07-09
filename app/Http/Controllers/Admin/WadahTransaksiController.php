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
     * Simpan Transaksi Baru dan Update Saldo.
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
            $buktiPath = $request->file('bukti_transaksi')->store('bukti_transaksi/wadah', 'public');
        }

        // Buat Catatan Transaksi
        WadahKategorialTransaksi::create([
            'anggaran_id' => $request->anggaran_id,
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'jenis_transaksi' => $jenisTransaksi,
            'jumlah' => $request->jumlah,
            'uraian' => $request->uraian,
            'bukti_transaksi' => $buktiPath,
            'dicatat_oleh_user_id' => Auth::id(),
        ]);

        // MENGAMANKAN LOGIKA (SAFEGUARD): Update langsung realisasi anggaran tanpa menunggu Observer
        $anggaran->increment('jumlah_realisasi', $request->jumlah);

        return back()->with('success', 'Transaksi berhasil dicatat dan realisasi pos anggaran telah diperbarui.');
    }

    /**
     * Hapus Transaksi dan Kembalikan Saldo.
     */
    public function destroy(WadahKategorialTransaksi $transaksi)
    {
        $anggaran = $transaksi->anggaran; // Tarik data anggaran sebelum transaksi dihapus

        // Hapus file bukti fisik jika ada
        if ($transaksi->bukti_transaksi && Storage::disk('public')->exists($transaksi->bukti_transaksi)) {
            Storage::disk('public')->delete($transaksi->bukti_transaksi);
        }

        // MENGAMANKAN LOGIKA (SAFEGUARD): Kembalikan (kurangi) saldo realisasi anggaran
        if ($anggaran) {
            $anggaran->decrement('jumlah_realisasi', $transaksi->jumlah);
        }

        $transaksi->delete();

        return back()->with('success', 'Transaksi berhasil dihapus dan saldo realisasi anggaran telah disesuaikan kembali.');
    }
}