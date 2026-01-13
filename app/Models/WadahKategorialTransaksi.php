<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WadahKategorialTransaksi extends Model
{
    use HasFactory;

    protected $table = 'wadah_kategorial_transaksi';

    protected $fillable = [
        'anggaran_id',
        'tanggal_transaksi',
        'jenis_transaksi', // 'masuk' atau 'keluar'
        'jumlah',
        'uraian',
        'bukti_transaksi',
        'dicatat_oleh_user_id',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function anggaran(): BelongsTo
    {
        return $this->belongsTo(WadahKategorialAnggaran::class, 'anggaran_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh_user_id');
    }
}