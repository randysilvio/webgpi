<?php

// app/Models/Service.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'list_items',
        'icon',
        'color_theme',
        'order',
    ];

    // Helper untuk memecah list_items menjadi array
    public function getListItemsArrayAttribute()
    {
        if (empty($this->list_items)) {
            return [];
        }
        // Pecah berdasarkan baris baru, hapus spasi ekstra, filter baris kosong
        return array_filter(array_map('trim', explode("\n", $this->list_items)));
    }
}