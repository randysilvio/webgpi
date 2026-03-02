<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SakramenNikah extends Model
{
    use HasFactory;

    protected $table = 'sakramen_nikah';

    protected $fillable = [
        'suami_id',
        'istri_id',
        'no_akta_nikah',
        'tanggal_nikah',
        'tempat_nikah',
        'pendeta_pelayan'
    ];

    public function suami()
    {
        return $this->belongsTo(AnggotaJemaat::class, 'suami_id');
    }

    public function istri()
    {
        return $this->belongsTo(AnggotaJemaat::class, 'istri_id');
    }
}