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

    // --- PERBAIKAN RELASI ---
    
    // Ganti 'Pendeta' menjadi 'Pegawai'
    public function ketuaMp()
    {
        // Pastikan foreign key di database tetap 'ketua_mpk_pendeta_id' (jika belum diubah)
        // atau 'pegawai_id' jika sudah migrasi kolom. 
        // Asumsi: Kolom di tabel klasis masih 'ketua_mpk_pendeta_id' tapi isinya ID Pegawai.
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