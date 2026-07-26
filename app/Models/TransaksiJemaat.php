<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiJemaat extends Model
{
    protected $table = 'transaksi_jemaat';
    protected $guarded = [];
    protected $casts = ['tanggal' => 'date'];

    public function jemaat() { return $this->belongsTo(Jemaat::class); }
    public function pembuat() { return $this->belongsTo(User::class, 'created_by'); }
}