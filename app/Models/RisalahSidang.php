<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RisalahSidang extends Model
{
    use HasFactory;
    protected $table = 'risalah_sidang';
    protected $fillable = [
        'judul_sidang', 'tanggal_sidang', 'tingkat_sidang', 
        'ringkasan_keputusan', 'file_risalah', 'klasis_id', 'jemaat_id'
    ];

    public function klasis() { return $this->belongsTo(Klasis::class); }
    public function jemaat() { return $this->belongsTo(Jemaat::class); }
}