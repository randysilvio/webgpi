<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopupAd extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Scope untuk mengambil iklan yang aktif hari ini
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->whereDate('mulai_tanggal', '<=', now())
                     ->whereDate('selesai_tanggal', '>=', now());
    }
}