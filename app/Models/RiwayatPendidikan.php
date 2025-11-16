<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPendidikan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pendidikan_pegawai';

    protected $fillable = [
        'pegawai_id',
        'jenjang', // SD, SMP, SMA, S1, S2, S3
        'nama_institusi',
        'jurusan',
        'tahun_lulus',
        'gelar_akademis',
        'nomor_ijazah',
        'file_ijazah',
    ];

    protected $casts = [
        'tahun_lulus' => 'integer',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}