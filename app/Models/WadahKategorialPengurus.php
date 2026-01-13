<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WadahKategorialPengurus extends Model
{
    use HasFactory;

    protected $table = 'wadah_kategorial_pengurus';

    protected $fillable = [
        'jenis_wadah_id',
        'tingkat',           // 'sinode', 'klasis', 'jemaat'
        'klasis_id',         // nullable, terisi jika tingkat = klasis
        'jemaat_id',         // nullable, terisi jika tingkat = jemaat
        'anggota_jemaat_id', // nullable, jika pengurus diambil dari database anggota
        'user_id',           // nullable, jika pengurus memiliki akses login sistem
        'jabatan',
        'nomor_sk',
        'periode_mulai',
        'periode_selesai',
        'is_active',
    ];

    /**
     * Casting tipe data.
     */
    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk mengambil hanya pengurus yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relasi ke Jenis Wadah (PAR, PAM, dll).
     */
    public function jenisWadah(): BelongsTo
    {
        return $this->belongsTo(JenisWadahKategorial::class, 'jenis_wadah_id');
    }

    /**
     * Relasi ke Klasis (jika tingkat = klasis).
     */
    public function klasis(): BelongsTo
    {
        return $this->belongsTo(Klasis::class, 'klasis_id');
    }

    /**
     * Relasi ke Jemaat (jika tingkat = jemaat).
     */
    public function jemaat(): BelongsTo
    {
        return $this->belongsTo(Jemaat::class, 'jemaat_id');
    }

    /**
     * Relasi ke data personil Anggota Jemaat.
     */
    public function anggotaJemaat(): BelongsTo
    {
        return $this->belongsTo(AnggotaJemaat::class, 'anggota_jemaat_id');
    }

    /**
     * Relasi ke User Login.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}