<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai';

    protected $fillable = [
        'nipg',
        'user_id',
        'nama_lengkap',
        'gelar_depan',
        'gelar_belakang',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'status_pernikahan',
        'golongan_darah',
        'nik_ktp',
        'alamat_domisili',
        'no_hp',
        'email',
        'jenis_pegawai',      // Pendeta, Pengajar, Tata Usaha, dll
        'status_kepegawaian', // Organik, Kontrak, Honorer
        'status_aktif',       // Aktif, Cuti, Pensiun, Meninggal
        'golongan_terakhir',
        'jabatan_terakhir',
        'tmt_pegawai',
        'tanggal_pensiun',
        'npwp',
        'no_bpjs_kesehatan',
        'no_bpjs_ketenagakerjaan',
        'klasis_id',
        'jemaat_id',
        'foto_diri',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_pegawai' => 'date',
        'tanggal_pensiun' => 'date',
    ];

    // --- RELASI ---

    /**
     * Relasi ke User Login (Akun).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Wilayah Tugas (Klasis).
     */
    public function klasis(): BelongsTo
    {
        return $this->belongsTo(Klasis::class, 'klasis_id');
    }

    /**
     * Relasi ke Wilayah Tugas (Jemaat).
     */
    public function jemaat(): BelongsTo
    {
        return $this->belongsTo(Jemaat::class, 'jemaat_id');
    }

    /**
     * Relasi ke Data Keluarga.
     */
    public function keluarga(): HasMany
    {
        return $this->hasMany(KeluargaPegawai::class, 'pegawai_id');
    }

    /**
     * Relasi ke Riwayat Pendidikan.
     */
    public function pendidikan(): HasMany
    {
        return $this->hasMany(RiwayatPendidikan::class, 'pegawai_id')->orderBy('tahun_lulus', 'desc');
    }

    /**
     * Relasi ke Riwayat SK / Kepangkatan.
     */
    public function riwayatSk(): HasMany
    {
        return $this->hasMany(RiwayatSk::class, 'pegawai_id')->orderBy('tmt_sk', 'desc');
    }
    
    /**
     * Helper untuk Nama Lengkap + Gelar
     */
    public function getNamaGelarAttribute()
    {
        $nama = $this->nama_lengkap;
        if ($this->gelar_depan) $nama = $this->gelar_depan . ' ' . $nama;
        if ($this->gelar_belakang) $nama = $nama . ', ' . $this->gelar_belakang;
        return $nama;
    }
}