<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klasis extends Model
{
    use HasFactory;

    protected $table = 'klasis';

    protected $fillable = [
        'nama_klasis', 
        'kode_klasis', 
        'pusat_klasis', 
        'alamat_kantor', 
        'koordinat_gps', 
        
        // --- 4 KOLOM BARU INI WAJIB ADA ---
        'kabupaten_kota', // PENTING: Untuk warna peta
        'latitude',       // PENTING: Untuk titik pin
        'longitude',      
        'warna_peta',     
        // ----------------------------------

        'wilayah_pelayanan', 
        'tanggal_pembentukan', 
        'nomor_sk_pembentukan', 
        'klasis_induk',
        'sejarah_singkat', 
        'ketua_mpk_pendeta_id', 
        'telepon_kantor', 
        'email_klasis',
        'website_klasis', 
        'foto_kantor_path',
    ];

    protected $casts = [
        'tanggal_pembentukan' => 'date',
    ];

    public function ketuaMp()
    {
        return $this->belongsTo(Pegawai::class, 'ketua_mpk_pendeta_id');
    }

    public function jemaat()
    {
        return $this->hasMany(Jemaat::class);
    }

    public function pegawaiDitempatkan()
    {
        return $this->hasMany(Pegawai::class, 'klasis_id');
    }
}