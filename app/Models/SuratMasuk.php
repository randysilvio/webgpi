<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    use HasFactory;
    protected $table = 'surat_masuk';
    protected $fillable = [
        'no_agenda', 'no_surat', 'tanggal_surat', 'tanggal_terima', 
        'asal_surat', 'perihal', 'ringkasan', 'file_path', 
        'status_disposisi', 'klasis_id', 'jemaat_id'
    ];

    public function klasis() { return $this->belongsTo(Klasis::class); }
    public function jemaat() { return $this->belongsTo(Jemaat::class); }
}