<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log; // <-- PERBAIKAN: Tambahkan baris ini
use App\Models\User;                 // <-- Tambahan: Ini praktik yang baik

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // Panggil Seeder Role dan Permission
        // Ini harus dijalankan agar Role 'Super Admin' ada
        $this->call(RolesAndPermissionsSeeder::class); 

        // Buat User Super Admin pertama (Opsional, tapi direkomendasikan)
         User::factory()->create([ // <-- Menggunakan 'User::'
             'name' => 'Super Admin',
             'email' => 'admin@gpipapua.org', // Ganti email Anda
             'password' => bcrypt('password'), // Ganti password Anda
         ])->assignRole('Super Admin'); // Langsung assign role

         // Baris ini sekarang akan berfungsi
         Log::info('User Super Admin default dibuat.');

        // Panggil seeder lain jika ada
        // $this->call(AnotherSeeder::class);
    }
}