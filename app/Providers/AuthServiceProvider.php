<?php

namespace App\Providers;

// Tambahkan 2 baris 'use' ini:
use Illuminate\Support\Facades\Gate;
use App\Models\User;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ===================================================================
        // || TAMBAHKAN BLOK KODE INI ||
        // ===================================================================
        // Method ini akan berjalan sebelum semua pengecekan hak akses lainnya
        Gate::before(function (User $user, string $ability) {
            
            // Jika user memiliki role 'Super Admin', beri dia akses ke SEMUANYA.
            // Ini membuat kita tidak perlu assign semua permission ke Super Admin di Seeder.
            if ($user->hasRole('Super Admin')) {
                return true;
            }

            // Jika tidak, biarkan pengecekan permission berjalan normal
            return null; 
        });
        // ===================================================================
        // || AKHIR BLOK KODE ||
        // ===================================================================
    }
}