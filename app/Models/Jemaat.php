<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import Model yang Benar
use App\Models\Pegawai; 
use App\Models\Klasis;
use App\Models\AnggotaJemaat;
use App\Models\User;

class Jemaat extends Model
{
    use HasFactory;

    protected $table = 'jemaat';

    protected $fillable = [
        'klasis_id',
        'kode_jemaat',
        'nama_jemaat',
        'alamat_gereja',
        'koordinat_gps',
        'foto_gereja_path',
        'status_jemaat',       
        'jenis_jemaat',        
        'tanggal_berdiri',
        'nomor_sk_pendirian',
        'jemaat_induk',
        'sejarah_singkat',
        'nama_ketua_majelis',
        'telepon_ketua_majelis',
        'nama_sekretaris_majelis',
        'periode_majelis',
        'telepon_kantor',
        'email_jemaat',
        'website_jemaat',
        'jumlah_kk',
        'jumlah_total_jiwa',
        'status_gedung_gereja', 
        'kapasitas_gedung',
        'status_tanah_gereja', 
        'luas_tanah', 
    ];

    protected $casts = [
        'tanggal_berdiri' => 'date',
        'tanggal_update_statistik' => 'date',
    ];

    public function klasis()
    {
        return $this->belongsTo(Klasis::class, 'klasis_id');
    }

    public function anggotaJemaat()
    {
        return $this->hasMany(AnggotaJemaat::class, 'jemaat_id');
    }

    /**
     * [FIXED 100%] Relasi ke Pendeta
     * Menggunakan 'jenis_pegawai' (sesuai file Pegawai.php), bukan 'jabatan'.
     */
    public function pendetaDitempatkan()
    {
        // PENTING: Di Pegawai.php kolomnya 'jenis_pegawai', BUKAN 'jabatan'
        return $this->hasMany(Pegawai::class, 'jemaat_id')
                    ->where('jenis_pegawai', 'Pendeta'); 
    }

    public function users()
    {
        return $this->hasMany(User::class, 'jemaat_id');
    }
}