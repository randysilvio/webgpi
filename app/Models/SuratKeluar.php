<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang didefinisikan di database.
     * * @var string
     */
    protected $table = 'surat_keluar';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     * Sesuai dengan kebutuhan administrasi persuratan.
     * * @var array
     */
    protected $fillable = [
        'no_surat',
        'tanggal_surat',
        'tujuan_surat',
        'perihal',
        'ringkasan',
        'file_path',
        'klasis_id',
        'jemaat_id',
    ];

    /**
     * Casting tipe data kolom tertentu.
     * * @var array
     */
    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    /**
     * Relasi ke Model Klasis.
     * Memungkinkan sistem mengetahui dari klasis mana surat ini dikeluarkan.
     */
    public function klasis()
    {
        return $this->belongsTo(Klasis::class, 'klasis_id');
    }

    /**
     * Relasi ke Model Jemaat.
     * Memungkinkan sistem mengetahui dari jemaat mana surat ini dikeluarkan.
     */
    public function jemaat()
    {
        return $this->belongsTo(Jemaat::class, 'jemaat_id');
    }
}