<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image_path',
        'published_at',
        'status', // Untuk CMS Approval Flow
        'author_id', // Untuk merekam siapa pembuat berita
        'jemaat_id', 
        'klasis_id', 
        'rejection_note' 
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Relasi ke Tabel Users (Penulis Berita)
     * INI ADALAH FUNGSI YANG MENYELESAIKAN ERROR "Undefined relationship [author]"
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}