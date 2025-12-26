<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klasis extends Model
{
    use HasFactory;

    protected $table = 'klasis';

    protected $fillable = [
        'nama_klasis', 'kode_klasis', 'pusat_klasis', 'alamat_kantor', 'koordinat_gps',
        'wilayah_pelayanan', 'tanggal_pembentukan', 'nomor_sk_pembentukan', 'klasis_induk',
        'sejarah_singkat', 'ketua_mpk_pendeta_id', 'telepon_kantor', 'email_klasis',
        'website_klasis', 'foto_kantor_path',
    ];

    protected $casts = [
        'tanggal_pembentukan' => 'date',
    ];

    // Relasi ke Pendeta yang menjadi Ketua MPK
    public function ketuaMp()
    {
        return $this->belongsTo(Pendeta::class, 'ketua_mpk_pendeta_id');
    }

    // Relasi ke Jemaat di bawah Klasis ini
    public function jemaat()
    {
        return $this->hasMany(Jemaat::class);
    }

    // Relasi Pendeta yang ditempatkan di Klasis ini (bukan hanya ketua)
    public function pendetaDitempatkan()
    {
        return $this->hasMany(Pendeta::class, 'klasis_penempatan_id');
    }

    // ===================================================================
    // || RELASI BARU DITAMBAHKAN ||
    // ===================================================================

    /**
     * Mendapatkan semua akun User yang terhubung dengan Klasis ini
     * (misal: Admin Klasis).
     */
    public function users()
    {
        return $this->hasMany(User::class, 'klasis_id');
    }
}