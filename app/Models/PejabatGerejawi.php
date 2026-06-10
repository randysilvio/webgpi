<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PejabatGerejawi extends Model
{
    use HasFactory;
    protected $table = 'pejabat_gerejawi';
    protected $fillable = [
        'anggota_jemaat_id', 'jabatan', 'periode_mulai', 
        'periode_selesai', 'no_sk_pelantikan', 'status_aktif'
    ];

    public function anggotaJemaat() {
        return $this->belongsTo(AnggotaJemaat::class, 'anggota_jemaat_id');
    }
}