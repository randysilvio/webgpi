<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log; // Untuk logging

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Log::info('Memulai RolesAndPermissionsSeeder...');

        // --- DEVINISI PERMISSIONS ---
        // (Nama permission sebaiknya deskriptif, misal: 'view users', 'create users', 'edit users', 'delete users')

        // Manajemen Konten Publik (Bidang 4 / Super Admin)
        Permission::firstOrCreate(['name' => 'manage settings']); // Kelola Pengaturan Situs
        Permission::firstOrCreate(['name' => 'manage posts']);    // Kelola Berita/Posts
        Permission::firstOrCreate(['name' => 'manage services']); // Kelola Layanan
        Permission::firstOrCreate(['name' => 'manage messages']); // Kelola Pesan Masuk

        // Manajemen Struktur & Kepegawaian (Bidang 3 / Super Admin)
        Permission::firstOrCreate(['name' => 'manage users']);    // Kelola User & Roles
        Permission::firstOrCreate(['name' => 'view users']);      // Lihat Daftar User
        Permission::firstOrCreate(['name' => 'manage pendeta']);   // Kelola Data Pendeta (CRUD)
        Permission::firstOrCreate(['name' => 'manage klasis']);    // Kelola Data Klasis (CRUD)
        Permission::firstOrCreate(['name' => 'manage jemaat']);    // Kelola Data Jemaat (CRUD Penuh)

        // Manajemen Data Jemaat (Level Klasis & Jemaat)
        Permission::firstOrCreate(['name' => 'view pendeta']);     // Lihat Daftar Pendeta
        Permission::firstOrCreate(['name' => 'view klasis']);     // Lihat Daftar Klasis
        Permission::firstOrCreate(['name' => 'edit own klasis']);  // Edit info kontak Klasis sendiri

        Permission::firstOrCreate(['name' => 'view jemaat']);      // Lihat Daftar Jemaat
        Permission::firstOrCreate(['name' => 'create jemaat']);    // Buat Jemaat baru (Admin Klasis)
        Permission::firstOrCreate(['name' => 'edit jemaat']);      // Edit Jemaat (Admin Klasis/Jemaat)
        Permission::firstOrCreate(['name' => 'delete jemaat']);    // Hapus Jemaat (Admin Klasis)

        Permission::firstOrCreate(['name' => 'view anggota jemaat']);
        Permission::firstOrCreate(['name' => 'create anggota jemaat']);
        Permission::firstOrCreate(['name' => 'edit anggota jemaat']);
        Permission::firstOrCreate(['name' => 'delete anggota jemaat']);
        Permission::firstOrCreate(['name' => 'import anggota jemaat']);
        Permission::firstOrCreate(['name' => 'export anggota jemaat']);

        // Import/Export
        Permission::firstOrCreate(['name' => 'import pendeta']);
        Permission::firstOrCreate(['name' => 'export pendeta']);
        Permission::firstOrCreate(['name' => 'import klasis']);
        Permission::firstOrCreate(['name' => 'export klasis']);
        Permission::firstOrCreate(['name' => 'import jemaat']);
        Permission::firstOrCreate(['name' => 'export jemaat']);


        // ... (Tambahkan permissions untuk Bidang 1 & 2 nanti jika perlu) ...
        // Permission::firstOrCreate(['name' => 'view assets']); // Contoh Bidang 2
        // Permission::firstOrCreate(['name' => 'view education data']); // Contoh Bidang 1

        Log::info('Permissions dibuat/diperbarui.');

        // --- DEFINISI ROLES & PENETAPAN PERMISSIONS ---

        // 1. Role: Pendeta (Akses lihat data penempatan & profil sendiri)
        $rolePendeta = Role::firstOrCreate(['name' => 'Pendeta']);
        $rolePendeta->syncPermissions([
            'view pendeta', // Bisa lihat data pendeta (terbatas profil sendiri nanti)
            'view klasis',
            'view jemaat',
            'view anggota jemaat',
            // 'edit own profile' // (Perlu controller profile terpisah)
        ]);
        Log::info('Role Pendeta disinkronkan.');

        // 2. Role: Admin Jemaat (CRUD Anggota Jemaat di Jemaatnya)
        $roleAdminJemaat = Role::firstOrCreate(['name' => 'Admin Jemaat']);
        $roleAdminJemaat->syncPermissions([
            'view jemaat',           // Lihat Jemaat (terbatas di controller)
            'edit jemaat',           // Edit Jemaat (terbatas di controller)
            'view anggota jemaat',   // CRUD Anggota (terbatas di controller)
            'create anggota jemaat',
            'edit anggota jemaat',
            'delete anggota jemaat',
            'import anggota jemaat', // Impor/Ekspor (terbatas di controller)
            'export anggota jemaat',
        ]);
        Log::info('Role Admin Jemaat disinkronkan.');

        // 3. Role: Admin Klasis (CRUD Jemaat di Klasisnya, View Anggota)
        $roleAdminKlasis = Role::firstOrCreate(['name' => 'Admin Klasis']);
        $roleAdminKlasis->syncPermissions([
            'view klasis',           // Lihat Klasis (terbatas)
            'edit own klasis',       // Edit Klasis (terbatas)
            'view jemaat',           // CRUD Jemaat (terbatas)
            'create jemaat',
            'edit jemaat',
            'delete jemaat',
            'import jemaat',         // Impor/Ekspor Jemaat (terbatas)
            'export jemaat',
            'view anggota jemaat',   // CRUD Anggota (terbatas)
            'create anggota jemaat',
            'edit anggota jemaat',
            'delete anggota jemaat',
            'import anggota jemaat', // Impor/Ekspor Anggota (terbatas)
            'export anggota jemaat',
        ]);
        Log::info('Role Admin Klasis disinkronkan.');

        // 4. Role: Admin Bidang 1 (Pelayanan & Pendidikan) - Read Only Data
        $roleBidang1 = Role::firstOrCreate(['name' => 'Admin Bidang 1']);
        $roleBidang1->syncPermissions([
            'view pendeta',
            'view klasis',
            'view jemaat',
            'view anggota jemaat',
            // 'manage education data' // (Nanti)
        ]);
        Log::info('Role Admin Bidang 1 disinkronkan.');

        // 5. Role: Admin Bidang 2 (Keuangan & Pembangunan) - Read Only Data
        $roleBidang2 = Role::firstOrCreate(['name' => 'Admin Bidang 2']);
        $roleBidang2->syncPermissions([
            'view klasis',
            'view jemaat',
            // 'manage assets' // (Nanti)
        ]);
        Log::info('Role Admin Bidang 2 disinkronkan.');

        // 6. Role: Admin Bidang 3 (Organisasi & Kepegawaian)
        $roleBidang3 = Role::firstOrCreate(['name' => 'Admin Bidang 3']);
        $roleBidang3->syncPermissions([
            'manage pendeta',   // CRUD Penuh Pendeta
            'manage klasis',    // CRUD Penuh Klasis
            'manage jemaat',    // CRUD Penuh Jemaat
            'view anggota jemaat',
            'export anggota jemaat',
            'view users',       // Bisa lihat user, tapi tidak kelola
            'import pendeta',   // Impor/Ekspor Penuh
            'export pendeta',
            'import klasis',
            'export klasis',
            'import jemaat',
            'export jemaat',
        ]);
        Log::info('Role Admin Bidang 3 disinkronkan.');

        // 7. Role: Admin Bidang 4 (Kominfo)
        $roleBidang4 = Role::firstOrCreate(['name' => 'Admin Bidang 4']);
        $roleBidang4->syncPermissions([
            'manage settings',
            'manage posts',
            'manage services',
            'manage messages',
            // Akses lihat data (opsional)
            'view klasis',
            'view jemaat',
        ]);
        Log::info('Role Admin Bidang 4 disinkronkan.');

        // 8. Role: Super Admin (Akses Penuh)
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        // 👇👇👇 Perbaikan: Memberikan semua permission secara eksplisit ke Super Admin 👇👇👇
        $allPermissions = Permission::pluck('name')->all(); // Ambil semua nama permission
        $roleSuperAdmin->syncPermissions($allPermissions); // Assign semua permission
        Log::info('Semua permissions disinkronkan ke Super Admin.');
        // Catatan: Gate::before() di AuthServiceProvider juga bisa digunakan untuk Super Admin,
        // tapi assign eksplisit seperti ini lebih jelas jika Gate::before() tidak di-setup.

        Log::info('RolesAndPermissionsSeeder selesai.');
    }
}