<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsetGereja extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'aset_gereja';

    protected $fillable = [
        'klasis_id',
        'jemaat_id',
        'nama_aset',
        'kode_aset',
        'kategori',
        'tanggal_perolehan',
        'nilai_perolehan',
        'sumber_perolehan',
        'kondisi',
        'status_kepemilikan',
        'lokasi_fisik',
        'nomor_dokumen',
        'file_dokumen_path',
        'foto_aset_path',
        'catatan',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'nilai_perolehan' => 'decimal:2',
    ];

    /**
     * Relasi ke Klasis (Lokasi aset di tingkat Klasis)
     */
    public function klasis()
    {
        return $this->belongsTo(Klasis::class, 'klasis_id');
    }

    /**
     * Relasi ke Jemaat (Lokasi aset di tingkat Jemaat)
     */
    public function jemaat()
    {
        return $this->belongsTo(Jemaat::class, 'jemaat_id');
    }

    /**
     * Accessor untuk format Rupiah nilai perolehan
     */
    public function getFormatNilaiAttribute()
    {
        return 'Rp ' . number_format($this->nilai_perolehan, 0, ',', '.');
    }
}