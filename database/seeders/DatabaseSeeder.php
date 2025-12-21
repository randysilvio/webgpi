<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Panggil Seeder Role dan Permission (Pondasi Keamanan)
        $this->call(RolesAndPermissionsSeeder::class); 

        // 2. Buat User Super Admin pertama
        $admin = User::updateOrCreate(
            ['email' => 'admin@gpipapua.org'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'), // Ganti dengan password yang lebih aman
            ]
        );
        $admin->assignRole('Super Admin');
        Log::info('User Super Admin default berhasil disiapkan.');

        // 3. Panggil Seeder Mata Anggaran (Fase 7: Perbendaharaan)
        // Pastikan file MataAnggaranSeeder.php sudah Anda buat sebelumnya
        if (class_exists(MataAnggaranSeeder::class)) {
            $this->call(MataAnggaranSeeder::class);
            Log::info('Master Mata Anggaran berhasil dimuat.');
        }

        // Panggil seeder lain jika diperlukan
        // $this->call(KlasisSeeder::class);
    }
}