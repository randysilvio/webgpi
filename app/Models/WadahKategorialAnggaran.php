<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WadahKategorialAnggaran extends Model
{
    use HasFactory;

    protected $table = 'wadah_kategorial_anggaran';

    protected $fillable = [
        'jenis_wadah_id',
        'tingkat',
        'klasis_id',
        'jemaat_id',
        'program_kerja_id',
        'tahun_anggaran',
        'jenis_anggaran', // 'penerimaan' atau 'pengeluaran'
        'nama_pos_anggaran',
        'keterangan',
        'jumlah_target',
        'jumlah_realisasi',
    ];

    protected $casts = [
        'jumlah_target' => 'decimal:2',
        'jumlah_realisasi' => 'decimal:2',
        'tahun_anggaran' => 'integer',
    ];

    /**
     * Menghitung sisa anggaran atau selisih target vs realisasi.
     */
    public function getSelisihAttribute()
    {
        return $this->jumlah_target - $this->jumlah_realisasi;
    }

    public function jenisWadah(): BelongsTo
    {
        return $this->belongsTo(JenisWadahKategorial::class, 'jenis_wadah_id');
    }

    public function programKerja(): BelongsTo
    {
        return $this->belongsTo(WadahKategorialProgramKerja::class, 'program_kerja_id');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(WadahKategorialTransaksi::class, 'anggaran_id');
    }
    
    public function klasis(): BelongsTo
    {
        return $this->belongsTo(Klasis::class, 'klasis_id');
    }

    public function jemaat(): BelongsTo
    {
        return $this->belongsTo(Jemaat::class, 'jemaat_id');
    }
}