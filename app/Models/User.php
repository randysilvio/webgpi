<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'pegawai_id', // <-- GANTI: Dulu pendeta_id, sekarang pegawai_id
        
        // Klasis & Jemaat ID di user tetap berguna untuk Admin Klasis/Jemaat
        // yang mungkin bukan pegawai, tapi operator.
        'klasis_id', 
        'jemaat_id',
        'jenis_wadah_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke Data Personil (Bisa Pendeta atau Staff).
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /**
     * Helper: Cek apakah user ini adalah Pendeta
     */
    public function isPendeta(): bool
    {
        return $this->pegawai && $this->pegawai->jenis_pegawai === 'Pendeta';
    }

    /**
      * Get the klasis record associated with the user (if Admin Klasis).
      */
     public function klasisTugas(): BelongsTo
     {
         return $this->belongsTo(Klasis::class, 'klasis_id');
     }

      /**
       * Get the jemaat record associated with the user (if Admin Jemaat).
       */
      public function jemaatTugas(): BelongsTo
      {
          return $this->belongsTo(Jemaat::class, 'jemaat_id');
      }

    /**
     * Relasi ke Jenis Wadah (Jika akun pengurus wadah).
     */
    public function jenisWadah(): BelongsTo
    {
        return $this->belongsTo(JenisWadahKategorial::class, 'jenis_wadah_id');
    }
}