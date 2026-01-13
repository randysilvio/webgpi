<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiPendeta extends Model
{
    use HasFactory;

    protected $table = 'mutasi_pendeta'; 

    protected $fillable = [
        'pegawai_id', // <-- GANTI: Dulu pendeta_id
        'tanggal_sk',
        'nomor_sk',
        'jenis_mutasi', // Penempatan Awal, Pindah Tugas, Emeritus, dll
        
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
     * Relasi ke Pegawai (Pendeta) yang dimutasi.
     * Sekarang mengarah ke model Pegawai.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    // Alias untuk backward compatibility (jika ada kode lama yang panggil ->pendeta)
    // Sebaiknya perlahan diganti jadi ->pegawai di controller/view
    public function pendeta()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /**
     * Relasi ke Klasis Asal.
     */
    public function asalKlasis()
    {
        return $this->belongsTo(Klasis::class, 'asal_klasis_id');
    }

    /**
     * Relasi ke Jemaat Asal.
     */
    public function asalJemaat()
    {
        return $this->belongsTo(Jemaat::class, 'asal_jemaat_id');
    }

    /**
     * Relasi ke Klasis Tujuan.
     */
    public function tujuanKlasis()
    {
        return $this->belongsTo(Klasis::class, 'tujuan_klasis_id');
    }

    /**
     * Relasi ke Jemaat Tujuan.
     */
    public function tujuanJemaat()
    {
        return $this->belongsTo(Jemaat::class, 'tujuan_jemaat_id');
    }
}