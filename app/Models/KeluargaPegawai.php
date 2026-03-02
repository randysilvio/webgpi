<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeluargaPegawai extends Model
{
    use HasFactory;

    protected $table = 'keluarga_pegawai';

    protected $fillable = [
        'pegawai_id',
        'nama_lengkap',
        'hubungan', // Suami, Istri, Anak
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'pendidikan_terakhir',
        'pekerjaan',
        'status_tunjangan', // true/false
        'keterangan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status_tunjangan' => 'boolean',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}