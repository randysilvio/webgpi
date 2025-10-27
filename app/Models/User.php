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
        'pendeta_id', // <-- Sudah ada
        'klasis_id',  // <-- Sudah ada
        'jemaat_id',  // <-- Sudah ada
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
        // Pastikan foreign key 'pendeta_id' benar
        return $this->belongsTo(Pendeta::class, 'pendeta_id');
    }

    /**
      * Get the klasis record associated with the user (if Admin Klasis).
      */
     public function klasisTugas()
     {
         // Pastikan foreign key 'klasis_id' benar
         return $this->belongsTo(Klasis::class, 'klasis_id');
     }

      /**
       * Get the jemaat record associated with the user (if Admin Jemaat).
       */
      public function jemaatTugas()
      {
          // Pastikan foreign key 'jemaat_id' benar
          return $this->belongsTo(Jemaat::class, 'jemaat_id');
      }
}