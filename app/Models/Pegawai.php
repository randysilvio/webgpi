<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai';

    protected $fillable = [
        // --- IDENTITAS DASAR (Sama untuk semua) ---
        'nipg', 
        'user_id', 
        'nama_lengkap', 
        'gelar_depan', 
        'gelar_belakang',
        'tempat_lahir', 
        'tanggal_lahir', 
        'jenis_kelamin', // 'L' atau 'P'
        'status_pernikahan',
        'golongan_darah', 
        'nik_ktp', 
        'alamat_domisili', 
        'no_hp', 
        'email',
        'foto_diri', 
        
        // --- KLASIFIKASI & STATUS ---
        'jenis_pegawai',      // 'Pendeta', 'Pengajar', 'Pegawai Kantor', 'Koster', dll
        'status_kepegawaian', // 'Organik', 'Kontrak' (Untuk Staff) ATAU 'Aktif', 'Emeritus' (Untuk Pendeta)
        'status_aktif',       // 'Aktif', 'Cuti', 'Pensiun', 'Meninggal', 'Keluar'
        
        // --- DATA KEPEGAWAIAN UMUM ---
        'golongan_terakhir', 
        'jabatan_terakhir', 
        'tmt_pegawai', 
        'tanggal_pensiun',
        'npwp', 
        'no_bpjs_kesehatan', 
        'no_bpjs_ketenagakerjaan',

        // --- LOKASI TUGAS SAAT INI ---
        'klasis_id', 
        'jemaat_id', 

        // --- DATA KHUSUS PENDETA (Nullable untuk staff lain) ---
        'tanggal_tahbisan',
        'tempat_tahbisan',
        'nomor_sk_kependetaan',
        'pendidikan_teologi_terakhir',  // Bisa S.Th, M.Th, dll
        'institusi_pendidikan_teologi', // Nama Kampus
        'catatan_khusus'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_pegawai' => 'date',
        'tanggal_pensiun' => 'date',
        'tanggal_tahbisan' => 'date',
    ];

    // --- SCOPES (Filter Cepat) ---
    
    public function scopePendeta($query) {
        return $query->where('jenis_pegawai', 'Pendeta');
    }

    public function scopeNonPendeta($query) {
        return $query->where('jenis_pegawai', '!=', 'Pendeta');
    }

    public function scopeAktif($query) {
        return $query->where('status_aktif', 'Aktif');
    }

    // --- RELASI ---

    /**
     * Relasi ke Akun Login.
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Wilayah Tugas (Klasis).
     */
    public function klasis(): BelongsTo {
        return $this->belongsTo(Klasis::class, 'klasis_id');
    }

    /**
     * Relasi ke Wilayah Tugas (Jemaat).
     */
    public function jemaat(): BelongsTo {
        return $this->belongsTo(Jemaat::class, 'jemaat_id');
    }

    /**
     * Relasi ke Riwayat Mutasi (Khusus Pendeta/Pegawai yang dimutasi).
     * Menggantikan relasi lama di model Pendeta.
     */
    public function mutasiHistory(): HasMany {
        return $this->hasMany(MutasiPendeta::class, 'pegawai_id')->orderBy('tanggal_sk', 'desc');
    }
    
    /**
     * Relasi ke Data Keluarga (Suami/Istri/Anak).
     */
    public function keluarga(): HasMany {
        return $this->hasMany(KeluargaPegawai::class, 'pegawai_id');
    }
    
    /**
     * Relasi ke Riwayat Pendidikan Formal.
     */
    public function pendidikan(): HasMany {
        return $this->hasMany(RiwayatPendidikan::class, 'pegawai_id')->orderBy('tahun_lulus', 'desc');
    }
    
    /**
     * Relasi ke Riwayat SK / Kepangkatan.
     */
    public function riwayatSk(): HasMany {
        return $this->hasMany(RiwayatSk::class, 'pegawai_id')->orderBy('tmt_sk', 'desc');
    }
}