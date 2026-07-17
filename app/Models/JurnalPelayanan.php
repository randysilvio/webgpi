<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalPelayanan extends Model
{
    use HasFactory;

    protected $table = 'jurnal_pelayanans';

    protected $fillable = [
        'jemaat_id',
        'pegawai_id',
        'kategori',
        'tanggal_kegiatan',
        'konteks_situasi',
        'tindak_lanjut'
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
    ];

    // Relasi ke Jemaat (Pemilik Data)
    public function jemaat()
    {
        return $this->belongsTo(Jemaat::class, 'jemaat_id');
    }

    // Relasi ke Pegawai/Pendeta (Penulis)
    public function pendeta()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}