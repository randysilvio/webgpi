<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatSk extends Model
{
    use HasFactory;

    protected $table = 'riwayat_sk_pegawai';

    protected $fillable = [
        'pegawai_id',
        'nomor_sk',
        'tanggal_sk',
        'tmt_sk',       // Terhitung Mulai Tanggal
        'jenis_sk',     // Pengangkatan, Kenaikan Pangkat, Mutasi, dll
        'golongan_baru',
        'jabatan_baru',
        'gaji_pokok_baru',
        'pejabat_penanda_tangan',
        'file_sk',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tmt_sk' => 'date',
        'gaji_pokok_baru' => 'decimal:2',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}