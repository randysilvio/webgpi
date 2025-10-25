<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendeta extends Model
{
    use HasFactory;

    // Nama tabel jika berbeda dari 'pendetas'
    protected $table = 'pendeta';

    protected $fillable = [
        'nama_lengkap', 'nik', 'nipg', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'status_pernikahan', 'nama_pasangan', 'golongan_darah', 'alamat_domisili',
        'telepon', 'email', 'tanggal_tahbisan', 'tempat_tahbisan', 'nomor_sk_kependetaan',
        'status_kepegawaian', 'pendidikan_teologi_terakhir', 'institusi_pendidikan_teologi',
        'golongan_pangkat_terakhir', 'tanggal_mulai_masuk_gpi', 'klasis_penempatan_id',
        'jemaat_penempatan_id', 'jabatan_saat_ini', 'tanggal_mulai_jabatan_saat_ini',
        'foto_path', 'catatan',
    ];

    // Casts untuk tipe data
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_tahbisan' => 'date',
        'tanggal_mulai_masuk_gpi' => 'date',
        'tanggal_mulai_jabatan_saat_ini' => 'date',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->hasOne(User::class);
    }

    // Relasi ke Klasis tempat penempatan
    public function klasisPenempatan()
    {
        return $this->belongsTo(Klasis::class, 'klasis_penempatan_id');
    }

    // Relasi ke Jemaat tempat penempatan
    public function jemaatPenempatan()
    {
        return $this->belongsTo(Jemaat::class, 'jemaat_penempatan_id');
    }

    // Relasi jika Pendeta jadi Ketua MPK
    public function klasisDipimpin()
    {
        return $this->hasOne(Klasis::class, 'ketua_mpk_pendeta_id');
    }
}