<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiMateri extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_registrasi',
        'materi_khotbah_id',
        'pegawai_id',
        'status_pembayaran',
        'bukti_transfer_path',
        'catatan_admin',
        'tanggal_verifikasi'
    ];

    protected $casts = [
        'tanggal_verifikasi' => 'datetime',
    ];

    public function materi()
    {
        return $this->belongsTo(MateriKhotbah::class, 'materi_khotbah_id');
    }

    public function pendeta()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}