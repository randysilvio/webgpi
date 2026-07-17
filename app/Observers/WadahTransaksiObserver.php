<?php

namespace App\Observers;

use App\Models\WadahKategorialTransaksi;
use App\Models\WadahKategorialAnggaran;

class WadahTransaksiObserver
{
    /**
     * Handle the WadahKategorialTransaksi "created" event.
     * Saat transaksi baru dibuat, tambahkan jumlahnya ke realisasi anggaran.
     */
    public function created(WadahKategorialTransaksi $transaksi): void
    {
        $anggaran = $transaksi->anggaran;
        
        if ($anggaran) {
            $anggaran->jumlah_realisasi += $transaksi->jumlah;
            $anggaran->save();
        }
    }

    /**
     * Handle the WadahKategorialTransaksi "updated" event.
     * Saat transaksi diedit, sesuaikan selisih jumlah lama dan baru.
     */
    public function updated(WadahKategorialTransaksi $transaksi): void
    {
        // Cek apakah nominal berubah
        if ($transaksi->isDirty('jumlah')) {
            $anggaran = $transaksi->anggaran;
            
            if ($anggaran) {
                $selisih = $transaksi->jumlah - $transaksi->getOriginal('jumlah');
                $anggaran->jumlah_realisasi += $selisih;
                $anggaran->save();
            }
        }
        
        // Jika anggaran_id berubah (transaksi dipindah pos), kurangi dari pos lama, tambah ke pos baru
        if ($transaksi->isDirty('anggaran_id')) {
            $oldAnggaran = WadahKategorialAnggaran::find($transaksi->getOriginal('anggaran_id'));
            $newAnggaran = WadahKategorialAnggaran::find($transaksi->anggaran_id);

            if ($oldAnggaran) {
                $oldAnggaran->jumlah_realisasi -= $transaksi->getOriginal('jumlah');
                $oldAnggaran->save();
            }

            if ($newAnggaran) {
                $newAnggaran->jumlah_realisasi += $transaksi->jumlah;
                $newAnggaran->save();
            }
        }
    }

    /**
     * Handle the WadahKategorialTransaksi "deleted" event.
     * Saat transaksi dihapus, kurangi jumlahnya dari realisasi anggaran.
     */
    public function deleted(WadahKategorialTransaksi $transaksi): void
    {
        $anggaran = $transaksi->anggaran;
        
        if ($anggaran) {
            $anggaran->jumlah_realisasi -= $transaksi->jumlah;
            $anggaran->save();
        }
    }
}