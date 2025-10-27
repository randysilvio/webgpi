<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jemaat extends Model
{
    use HasFactory;

    protected $table = 'jemaat';

    protected $fillable = [
        'nama_jemaat', 'kode_jemaat', 'klasis_id', 'alamat_gereja', 'koordinat_gps',
        'tanggal_berdiri', 'status_jemaat', 'jenis_jemaat', 'nomor_sk_pendirian',
        'jemaat_induk', 'sejarah_singkat', 'nama_ketua_majelis', 'telepon_ketua_majelis',
        'nama_sekretaris_majelis', 'jumlah_pendeta', 'jumlah_penatua', 'jumlah_diaken',
        'jumlah_pengajar', 'periode_majelis', 'jumlah_kk', 'jumlah_anggota_baptis_anak',
        'jumlah_anggota_sidi', 'jumlah_total_jiwa', 'jumlah_sektor', 'jumlah_unit',
        'tanggal_update_statistik', 'status_gedung_gereja', 'kapasitas_gedung',
        'status_tanah_gereja', 'luas_tanah', 'telepon_kantor', 'email_jemaat',
        'website_jemaat', 'foto_gereja_path',
    ];

    protected $casts = [
        'tanggal_berdiri' => 'date',
        'tanggal_update_statistik' => 'date',
    ];

    // Relasi ke Klasis induknya
    public function klasis()
    {
        return $this->belongsTo(Klasis::class);
    }

    // Relasi ke Anggota Jemaatnya
    public function anggotaJemaat()
    {
        return $this->hasMany(AnggotaJemaat::class);
    }

    // Relasi ke Pendeta yang ditempatkan di Jemaat ini
    public function pendetaDitempatkan()
    {
        return $this->hasMany(Pendeta::class, 'jemaat_penempatan_id');
    }

    // ===================================================================
    // || RELASI BARU DITAMBAHKAN ||
    // ===================================================================

    /**
     * Mendapatkan semua akun User yang terhubung dengan Jemaat ini
     * (misal: Admin Jemaat).
     */
    public function users()
    {
        return $this->hasMany(User::class, 'jemaat_id');
    }
}