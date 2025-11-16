<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. Import SoftDeletes
use Illuminate\Database\Eloquent\Builder;

class AnggotaJemaat extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. Gunakan SoftDeletes

    protected $table = 'anggota_jemaat';

    protected $fillable = [
        'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'golongan_darah', 'status_pernikahan', 'nama_ayah', 'nama_ibu',
        'pendidikan_terakhir', 'pekerjaan_utama', 'alamat_lengkap', 'telepon', 'email',
        'nomor_buku_induk', 'jemaat_id',
        'nomor_kk', 
        'sektor_pelayanan', 'unit_pelayanan',
        'tanggal_baptis', 'tempat_baptis', 'tanggal_sidi', 'tempat_sidi',
        'tanggal_masuk_jemaat', 'status_keanggotaan', 'asal_gereja_sebelumnya',
        'nomor_atestasi', 'jabatan_pelayan_khusus', 'wadah_kategorial', 'keterlibatan_lain',
        'status_dalam_keluarga',
        'nama_kepala_keluarga', 'status_pekerjaan_kk',
        'sektor_pekerjaan_kk', 'status_kepemilikan_rumah', 'sumber_penerangan',
        'sumber_air_minum', 'perkiraan_pendapatan_keluarga', 'catatan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_baptis' => 'date',
        'tanggal_sidi' => 'date',
        'tanggal_masuk_jemaat' => 'date',
    ];

    /**
     * Scope untuk memfilter anggota yang statusnya 'Aktif'.
     * Cara pakai: AnggotaJemaat::aktif()->get();
     */
    public function scopeAktif(Builder $query): void
    {
        $query->where('status_keanggotaan', 'Aktif');
    }

    // Relasi ke Jemaatnya
    public function jemaat()
    {
        return $this->belongsTo(Jemaat::class);
    }

    // Relasi untuk mendapatkan anggota keluarga lain dalam KK yang sama
    public function keluarga()
    {
        if (!empty($this->nomor_kk)) {
            // Ambil semua anggota dengan nomor_kk yang sama, kecuali diri sendiri
            return $this->hasMany(AnggotaJemaat::class, 'nomor_kk', 'nomor_kk')
                        ->where('id', '!=', $this->id)
                        ->orderByRaw("CASE status_dalam_keluarga
                                        WHEN 'Kepala Keluarga' THEN 1
                                        WHEN 'Istri' THEN 2
                                        WHEN 'Anak' THEN 3
                                        ELSE 4
                                     END") // Urutkan KK, Istri, Anak
                        ->orderBy('tanggal_lahir', 'asc'); // Lalu urutkan anak by tgl lahir
        }
        // Jika tidak ada nomor_kk, kembalikan relasi kosong
        return $this->hasMany(AnggotaJemaat::class, 'nomor_kk', 'nomor_kk')->whereRaw('1 = 0');
    }

    // Relasi untuk mendapatkan kepala keluarga (jika ada)
    public function kepalaKeluarga()
    {
         if (!empty($this->nomor_kk)) {
             // Cari anggota lain di KK yang sama dengan status 'Kepala Keluarga'
             // Menggunakan hasOne agar mendapat 1 object model
            return $this->hasOne(AnggotaJemaat::class, 'nomor_kk', 'nomor_kk')
                         ->where('status_dalam_keluarga', 'Kepala Keluarga');
        }
         
         return $this->hasOne(AnggotaJemaat::class, 'nomor_kk', 'nomor_kk')->whereRaw('1 = 0');
    }
}