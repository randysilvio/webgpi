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
        'telepon', // 'email' dihapus karena ada di tabel User
        'tanggal_tahbisan', 'tempat_tahbisan', 'nomor_sk_kependetaan',
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
        // Relasi one-to-one ke User, foreign key ada di tabel 'users'
        return $this->hasOne(User::class, 'pendeta_id');
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

    // Relasi jika Pendeta jadi Ketua MPK (asumsi foreign key di tabel klasis adalah ketua_mpk_pendeta_id)
    public function klasisDipimpin()
    {
        // Sesuaikan 'ketua_mpk_pendeta_id' jika nama foreign key di tabel klasis berbeda
        return $this->hasOne(Klasis::class, 'ketua_mpk_pendeta_id');
    }

    // (Baru) Relasi ke Riwayat Mutasi
    public function mutasiHistory()
    {
        // Seorang pendeta memiliki banyak riwayat mutasi
        // Urutkan berdasarkan tanggal SK terbaru
        return $this->hasMany(MutasiPendeta::class, 'pendeta_id')->orderBy('tanggal_sk', 'desc');
    }

    // (Baru - Opsional) Relasi untuk mendapatkan mutasi terakhir
    public function latestMutasi()
    {
        // Mengambil satu record mutasi terbaru berdasarkan tanggal SK
        return $this->hasOne(MutasiPendeta::class, 'pendeta_id')->latest('tanggal_sk');
    }
}