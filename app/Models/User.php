<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Tambahan Import

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'pendeta_id',
        'klasis_id',
        'jemaat_id',
        'jenis_wadah_id', // <-- Tambahan: Kolom baru untuk Pengurus Wadah
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the pendeta record associated with the user.
     */
    public function pendeta(): BelongsTo
    {
        return $this->belongsTo(Pendeta::class, 'pendeta_id');
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
     * Relasi ke Jenis Wadah Kategorial.
     * Digunakan jika user adalah Pengurus Wadah (misal: Ketua PAR).
     */
    public function jenisWadah(): BelongsTo
    {
        return $this->belongsTo(JenisWadahKategorial::class, 'jenis_wadah_id');
    }
}