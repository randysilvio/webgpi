<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataAnggaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mata_anggaran';

    protected $fillable = [
        'kode',
        'nama_mata_anggaran',
        'jenis',
        'kelompok',
        'deskripsi',
        'is_active',
    ];

    /**
     * Relasi ke Anggaran Induk (One-to-Many)
     * Menghubungkan mata anggaran ini ke rencana anggaran tahunan
     */
    public function anggaranInduk()
    {
        // Asumsi model AnggaranInduk akan dibuat di langkah selanjutnya
        return $this->hasMany(AnggaranInduk::class, 'mata_anggaran_id');
    }

    /**
     * Scope untuk mengambil hanya jenis Pendapatan
     */
    public function scopePendapatan($query)
    {
        return $query->where('jenis', 'Pendapatan');
    }

    /**
     * Scope untuk mengambil hanya jenis Belanja
     */
    public function scopeBelanja($query)
    {
        return $query->where('jenis', 'Belanja');
    }
}