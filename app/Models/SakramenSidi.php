<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SakramenSidi extends Model
{
    use HasFactory;

    protected $table = 'sakramen_sidi';

    protected $fillable = [
        'anggota_jemaat_id',
        'no_akta_sidi',
        'tanggal_sidi',
        'tempat_sidi',
        'pendeta_pelayan'
    ];

    public function anggotaJemaat()
    {
        return $this->belongsTo(AnggotaJemaat::class, 'anggota_jemaat_id');
    }
}