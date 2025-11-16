<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisWadahKategorial extends Model
{
    use HasFactory;

    protected $table = 'jenis_wadah_kategorial';

    protected $fillable = [
        'nama_wadah',
        'rentang_usia_min',
        'rentang_usia_max',
        'deskripsi',
    ];

    /**
     * Relasi ke Pengurus Wadah.
     * Satu jenis wadah (misal: PAR) memiliki banyak pengurus di berbagai tingkatan.
     */
    public function pengurus(): HasMany
    {
        return $this->hasMany(WadahKategorialPengurus::class, 'jenis_wadah_id');
    }

    // Relasi untuk Program Kerja dan Anggaran akan ditambahkan di Fase selanjutnya.
}