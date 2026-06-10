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

    /**
     * Daftar kolom yang bisa diisi secara massal (Mass Assignment).
     * Update: Ditambahkan field analisis Renstra (Kondisi Rumah, Ekonomi, Digital).
     */
    protected $fillable = [
        // Identitas & Kontak
        'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'golongan_darah', 'disabilitas', 'status_pernikahan', 
        'alamat_lengkap', 'telepon', 'email',
        
        // Data Gerejawi
        'nomor_buku_induk', 'jemaat_id', 
        'sektor_pelayanan', 'unit_pelayanan',
        'tanggal_baptis', 'tempat_baptis', 'tanggal_sidi', 'tempat_sidi',
        'tanggal_masuk_jemaat', 'status_keanggotaan', 'asal_gereja_sebelumnya',
        'nomor_atestasi', 'jabatan_pelayan_khusus', 'wadah_kategorial', 'keterlibatan_lain',
        
        // Keluarga
        'nomor_kk', 'kode_keluarga_internal', 
        'status_dalam_keluarga', 'nama_kepala_keluarga',
        'ayah_id', 'ibu_id', 'nama_ayah', 'nama_ibu',
        
        // Ekonomi & Kesejahteraan (Renstra & Statistik)
        'pendidikan_terakhir', 'pekerjaan_utama', 
        'status_pekerjaan_kk', 'sektor_pekerjaan_kk', 
        'status_kepemilikan_rumah', 'kondisi_rumah', // Update
        'sumber_penerangan', 'sumber_air_minum', 
        'perkiraan_pendapatan_keluarga', 'rentang_pengeluaran', // Update
        'aset_ekonomi', // Update (Disimpan sebagai string/text)
        
        // Digital (Renstra)
        'punya_smartphone', 'akses_internet', // Update
        
        'catatan'
    ];

    // --- RELASI KE WILAYAH ---
    public function jemaat()
    {
        return $this->belongsTo(Jemaat::class, 'jemaat_id');
    }

    // --- RELASI POHON KELUARGA (Orang Tua & Anak) ---
    public function ayah()
    {
        return $this->belongsTo(AnggotaJemaat::class, 'ayah_id');
    }

    public function ibu()
    {
        return $this->belongsTo(AnggotaJemaat::class, 'ibu_id');
    }

    public function anak()
    {
        return $this->hasMany(AnggotaJemaat::class, 'ayah_id')->orWhere('ibu_id', $this->id);
    }

    // --- LOGIKA KELUARGA (KK & Anggota) ---
    
    /**
     * Mengambil seluruh anggota keluarga dalam satu KK yang sama.
     * Prioritas: Kode Keluarga Internal -> Nomor KK Sipil.
     */
    public function keluarga()
    {
        // 1. Cek Kode Internal (Prioritas Utama untuk sinkronisasi)
        if (!empty($this->kode_keluarga_internal)) {
            return $this->hasMany(AnggotaJemaat::class, 'kode_keluarga_internal', 'kode_keluarga_internal')
                        ->where('id', '!=', $this->id) // Jangan ambil diri sendiri
                        ->orderByRaw("CASE 
                                        WHEN status_dalam_keluarga = 'Kepala Keluarga' THEN 1
                                        WHEN status_dalam_keluarga = 'Istri' THEN 2
                                        WHEN status_dalam_keluarga = 'Anak' THEN 3
                                        ELSE 4
                                     END")
                        ->orderBy('tanggal_lahir', 'asc');
        }

        // 2. Cek Nomor KK Sipil (Fallback)
        if (!empty($this->nomor_kk)) {
            return $this->hasMany(AnggotaJemaat::class, 'nomor_kk', 'nomor_kk')
                        ->where('id', '!=', $this->id)
                        ->orderByRaw("CASE 
                                        WHEN status_dalam_keluarga = 'Kepala Keluarga' THEN 1
                                        WHEN status_dalam_keluarga = 'Istri' THEN 2
                                        WHEN status_dalam_keluarga = 'Anak' THEN 3
                                        ELSE 4
                                     END");
        }

        // Jika tidak punya KK, kembalikan relasi kosong
        return $this->hasMany(AnggotaJemaat::class, 'id', 'id')->whereRaw('1 = 0');
    }

    /**
     * Mengambil data Kepala Keluarga dari anggota ini.
     */
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

    // --- RELASI SAKRAMEN ---
    public function dataBaptis()
    {
        return $this->hasOne(SakramenBaptis::class, 'anggota_id');
    }

    public function dataSidi()
    {
        return $this->hasOne(SakramenSidi::class, 'anggota_id');
    }

    // --- SCOPES ---
    public function scopeAktif(Builder $query): void
    {
        $query->where('status_keanggotaan', 'Aktif');
    }
}