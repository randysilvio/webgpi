<?php
// app/Models/Post.php

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
    ];

    // Tambahkan cast untuk published_at agar menjadi objek Carbon
    protected $casts = [
        'published_at' => 'datetime',
    ];
}