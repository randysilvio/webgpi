<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiPendeta extends Model
{
    use HasFactory;

    protected $table = 'mutasi_pendeta'; // Pastikan nama tabel benar

    protected $fillable = [
        'pendeta_id',
        'tanggal_sk',
        'nomor_sk',
        'jenis_mutasi',
        'asal_klasis_id',
        'asal_jemaat_id',
        'tujuan_klasis_id',
        'tujuan_jemaat_id',
        'tanggal_efektif',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tanggal_efektif' => 'date',
    ];

    /**
     * Relasi ke Pendeta yang dimutasi.
     */
    public function pendeta()
    {
        return $this->belongsTo(Pendeta::class);
    }

    /**
     * Relasi ke Klasis Asal (opsional).
     */
    public function asalKlasis()
    {
        return $this->belongsTo(Klasis::class, 'asal_klasis_id');
    }

    /**
     * Relasi ke Jemaat Asal (opsional).
     */
    public function asalJemaat()
    {
        return $this->belongsTo(Jemaat::class, 'asal_jemaat_id');
    }

    /**
     * Relasi ke Klasis Tujuan (opsional).
     */
    public function tujuanKlasis()
    {
        return $this->belongsTo(Klasis::class, 'tujuan_klasis_id');
    }

    /**
     * Relasi ke Jemaat Tujuan (opsional).
     */
    public function tujuanJemaat()
    {
        return $this->belongsTo(Jemaat::class, 'tujuan_jemaat_id');
    }
}