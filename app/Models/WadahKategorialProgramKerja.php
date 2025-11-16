<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WadahKategorialProgramKerja extends Model
{
    use HasFactory;

    protected $table = 'wadah_kategorial_program_kerja';

    protected $fillable = [
        'jenis_wadah_id',
        'tingkat',           // 'sinode', 'klasis', 'jemaat'
        'klasis_id',
        'jemaat_id',
        'tahun_program',
        'nama_program',
        'deskripsi',
        'tujuan',
        'penanggung_jawab',
        'parent_program_id', // ID program induk (jika ini adalah program turunan)
        'status_pelaksanaan',// 0=Rencana, 1=Berjalan, 2=Selesai, 3=Ditunda, 4=Dibatalkan
        'target_anggaran',
    ];

    protected $casts = [
        'tahun_program' => 'integer',
        'status_pelaksanaan' => 'integer',
        'target_anggaran' => 'decimal:2',
    ];

    /**
     * Relasi ke Jenis Wadah (PAR, PAM, dll).
     */
    public function jenisWadah(): BelongsTo
    {
        return $this->belongsTo(JenisWadahKategorial::class, 'jenis_wadah_id');
    }

    /**
     * Relasi ke Klasis (jika tingkat = klasis atau jemaat).
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
     * Relasi ke Program Induk (Parent).
     * Contoh: Program Jemaat menginduk ke Program Klasis.
     */
    public function parentProgram(): BelongsTo
    {
        return $this->belongsTo(WadahKategorialProgramKerja::class, 'parent_program_id');
    }

    /**
     * Relasi ke Program Turunan (Children).
     * Contoh: Program Klasis memiliki banyak turunan di Jemaat-jemaat.
     */
    public function childPrograms(): HasMany
    {
        return $this->hasMany(WadahKategorialProgramKerja::class, 'parent_program_id');
    }

    /**
     * Helper untuk mendapatkan label status dalam bentuk teks.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_pelaksanaan) {
            0 => 'Direncanakan',
            1 => 'Sedang Berjalan',
            2 => 'Selesai',
            3 => 'Ditunda',
            4 => 'Dibatalkan',
            default => 'Unknown',
        };
    }

    /**
     * Helper untuk mendapatkan warna badge status (untuk UI).
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status_pelaksanaan) {
            0 => 'gray',    // Direncanakan
            1 => 'blue',    // Berjalan
            2 => 'green',   // Selesai
            3 => 'yellow',  // Ditunda
            4 => 'red',     // Dibatalkan
            default => 'gray',
        };
    }
}