<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TransaksiInduk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi_induk';

    protected $fillable = [
        'mata_anggaran_id',
        'klasis_id',
        'jemaat_id',
        'tanggal_transaksi',
        'nomor_bukti',
        'nominal',
        'keterangan',
        'file_bukti_path',
        'created_by',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'nominal' => 'decimal:2',
    ];

    /**
     * Boot function untuk otomatis update realisasi anggaran
     */
    protected static function booted()
    {
        // Setiap kali transaksi disimpan, update jumlah_realisasi di tabel anggaran_induk
        static::saved(function ($transaksi) {
            $transaksi->updateRealisasiAnggaran();
        });

        static::deleted(function ($transaksi) {
            $transaksi->updateRealisasiAnggaran();
        });
    }

    /**
     * Fungsi untuk sinkronisasi nilai realisasi ke tabel AnggaranInduk
     */
    public function updateRealisasiAnggaran()
    {
        $tahun = $this->tanggal_transaksi->format('Y');

        // Hitung total nominal transaksi untuk mata anggaran & wilayah yang sama di tahun tersebut
        $total = self::where('mata_anggaran_id', $this->mata_anggaran_id)
            ->whereYear('tanggal_transaksi', $tahun)
            ->where('klasis_id', $this->klasis_id)
            ->where('jemaat_id', $this->jemaat_id)
            ->sum('nominal');

        // Update ke tabel AnggaranInduk
        AnggaranInduk::where('mata_anggaran_id', $this->mata_anggaran_id)
            ->where('tahun_anggaran', $tahun)
            ->where('klasis_id', $this->klasis_id)
            ->where('jemaat_id', $this->jemaat_id)
            ->update(['jumlah_realisasi' => $total]);
    }

    /**
     * Relasi ke Mata Anggaran
     */
    public function mataAnggaran()
    {
        return $this->belongsTo(MataAnggaran::class, 'mata_anggaran_id');
    }

    /**
     * Relasi ke Pembuat Data
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}