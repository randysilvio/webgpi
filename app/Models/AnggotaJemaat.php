<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class AnggotaJemaat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'anggota_jemaat';

    protected $fillable = [
        'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'golongan_darah', 'status_pernikahan', 
        'ayah_id', 'ibu_id',
        'nama_ayah', 'nama_ibu',
        'pendidikan_terakhir', 'pekerjaan_utama', 'alamat_lengkap', 'telepon', 'email',
        'nomor_buku_induk', 'jemaat_id',
        'nomor_kk', 'kode_keluarga_internal', 
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

    /* --- RELASI SAKRAMEN LENGKAP (Baru) --- */
    
    public function dataBaptis()
    {
        return $this->hasOne(SakramenBaptis::class, 'anggota_jemaat_id');
    }

    public function dataSidi()
    {
        return $this->hasOne(SakramenSidi::class, 'anggota_jemaat_id');
    }

    public function dataPernikahan()
    {
        return $this->hasOne(SakramenNikah::class, 'suami_id')
                    ->orWhere('istri_id', $this->id)
                    ->latest('tanggal_nikah');
    }

    /* --- LOGIKA POHON KELUARGA --- */

    public function ayah()
    {
        return $this->belongsTo(AnggotaJemaat::class, 'ayah_id')->withDefault(['nama_lengkap' => $this->nama_ayah]);
    }

    public function ibu()
    {
        return $this->belongsTo(AnggotaJemaat::class, 'ibu_id')->withDefault(['nama_lengkap' => $this->nama_ibu]);
    }

    public function anak()
    {
        return $this->hasMany(AnggotaJemaat::class, 'ayah_id')
                    ->orWhere('ibu_id', $this->id)
                    ->orderBy('tanggal_lahir', 'asc');
    }

    /* --- LOGIKA KARTU KELUARGA --- */

    public function keluarga()
    {
        // 1. Cek Kode Keluarga Gereja (Prioritas Utama)
        if (!empty($this->kode_keluarga_internal)) {
            return $this->hasMany(AnggotaJemaat::class, 'kode_keluarga_internal', 'kode_keluarga_internal')
                        ->where('id', '!=', $this->id) 
                        ->orderByRaw("CASE status_dalam_keluarga
                                        WHEN 'Kepala Keluarga' THEN 1
                                        WHEN 'Istri' THEN 2
                                        WHEN 'Anak' THEN 3
                                        ELSE 4
                                     END")
                        ->orderBy('tanggal_lahir', 'asc');
        }

        // 2. Cek Nomor KK Sipil (Fallback)
        if (!empty($this->nomor_kk)) {
            return $this->hasMany(AnggotaJemaat::class, 'nomor_kk', 'nomor_kk')
                        ->where('id', '!=', $this->id);
        }

        return $this->hasMany(AnggotaJemaat::class, 'id', 'id')->whereRaw('1 = 0');
    }

    public function kepalaKeluarga()
    {
        if (!empty($this->kode_keluarga_internal)) {
            return $this->hasOne(AnggotaJemaat::class, 'kode_keluarga_internal', 'kode_keluarga_internal')
                         ->where('status_dalam_keluarga', 'Kepala Keluarga');
        }

        if (!empty($this->nomor_kk)) {
            return $this->hasOne(AnggotaJemaat::class, 'nomor_kk', 'nomor_kk')
                         ->where('status_dalam_keluarga', 'Kepala Keluarga');
        }
        
        return $this->hasOne(AnggotaJemaat::class, 'id', 'id')->whereRaw('1 = 0');
    }

    public function scopeAktif(Builder $query): void
    {
        $query->where('status_keanggotaan', 'Aktif');
    }

    public function jemaat()
    {
        return $this->belongsTo(Jemaat::class);
    }
}