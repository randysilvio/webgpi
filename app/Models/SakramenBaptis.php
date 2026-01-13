<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SakramenBaptis extends Model
{
    use HasFactory;

    protected $table = 'sakramen_baptis';

    protected $fillable = [
        'anggota_jemaat_id',
        'no_akta_baptis',
        'tanggal_baptis',
        'tempat_baptis',
        'pendeta_pelayan'
    ];

    public function anggotaJemaat()
    {
        return $this->belongsTo(AnggotaJemaat::class, 'anggota_jemaat_id');
    }
}