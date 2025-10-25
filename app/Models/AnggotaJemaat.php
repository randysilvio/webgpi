<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaJemaat extends Model
{
    use HasFactory;

    protected $table = 'anggota_jemaat';

    protected $fillable = [
        'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'golongan_darah', 'status_pernikahan', 'nama_ayah', 'nama_ibu',
        'pendidikan_terakhir', 'pekerjaan_utama', 'alamat_lengkap', 'telepon', 'email',
        'nomor_buku_induk', 'jemaat_id', 'sektor_pelayanan', 'unit_pelayanan',
        'tanggal_baptis', 'tempat_baptis', 'tanggal_sidi', 'tempat_sidi',
        'tanggal_masuk_jemaat', 'status_keanggotaan', 'asal_gereja_sebelumnya',
        'nomor_atestasi', 'jabatan_pelayan_khusus', 'wadah_kategorial', 'keterlibatan_lain',
        'status_dalam_keluarga', 'nama_kepala_keluarga', 'status_pekerjaan_kk',
        'sektor_pekerjaan_kk', 'status_kepemilikan_rumah', 'sumber_penerangan',
        'sumber_air_minum', 'perkiraan_pendapatan_keluarga', 'catatan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_baptis' => 'date',
        'tanggal_sidi' => 'date',
        'tanggal_masuk_jemaat' => 'date',
    ];

    // Relasi ke Jemaatnya
    public function jemaat()
    {
        return $this->belongsTo(Jemaat::class);
    }
}