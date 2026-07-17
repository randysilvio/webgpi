<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnggaranInduk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'anggaran_induk';

    protected $fillable = [
        'mata_anggaran_id',
        'klasis_id',
        'jemaat_id',
        'tahun_anggaran',
        'jumlah_target',
        'jumlah_realisasi',
        'status_anggaran',
        'catatan',
    ];

    /**
     * Relasi ke Mata Anggaran
     */
    public function mataAnggaran()
    {
        return $this->belongsTo(MataAnggaran::class, 'mata_anggaran_id');
    }

    /**
     * Relasi ke Klasis
     */
    public function klasis()
    {
        return $this->belongsTo(Klasis::class, 'klasis_id');
    }

    /**
     * Relasi ke Jemaat
     */
    public function jemaat()
    {
        return $this->belongsTo(Jemaat::class, 'jemaat_id');
    }

    /**
     * Menghitung sisa anggaran (untuk Belanja) atau kekurangan (untuk Pendapatan)
     */
    public function getSelisihAttribute()
    {
        return $this->jumlah_target - $this->jumlah_realisasi;
    }

    /**
     * Menghitung persentase capaian
     */
    public function getPersentaseAttribute()
    {
        if ($this->jumlah_target <= 0) return 0;
        return ($this->jumlah_realisasi / $this->jumlah_target) * 100;
    }
}