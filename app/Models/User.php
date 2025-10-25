<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // <-- Pastikan ini ada

class User extends Authenticatable
{
    // Tambahkan HasRoles agar bisa pakai $user->assignRole() / $user->hasRole()
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
        'pendeta_id', // <-- Tambahkan ini untuk relasi ke Pendeta
        'klasis_id',  // <-- Tambahkan ini jika Admin Klasis dikaitkan via user
        'jemaat_id',  // <-- Tambahkan ini jika Admin Jemaat dikaitkan via user
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
    public function pendeta()
    {
        return $this->belongsTo(Pendeta::class);
    }

    /**
      * Get the klasis record associated with the user (if Admin Klasis).
      */
     public function klasisTugas()
     {
         return $this->belongsTo(Klasis::class, 'klasis_id');
     }

      /**
       * Get the jemaat record associated with the user (if Admin Jemaat).
       */
      public function jemaatTugas()
      {
          return $this->belongsTo(Jemaat::class, 'jemaat_id');
      }
}