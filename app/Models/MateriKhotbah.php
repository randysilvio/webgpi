<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MateriKhotbah extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul_dokumen',
        'kategori',
        'deskripsi_singkat',
        'harga_dokumen',
        'file_path',
        'cover_path',
        'is_active',
        'author_id'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiMateri::class, 'materi_khotbah_id');
    }
}