<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

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

        // --- DEFINISI PERMISSIONS ---

        // Manajemen Konten Publik (Bidang 4 / Super Admin)
        Permission::firstOrCreate(['name' => 'manage settings']);
        Permission::firstOrCreate(['name' => 'manage posts']);
        Permission::firstOrCreate(['name' => 'manage services']);
        Permission::firstOrCreate(['name' => 'manage messages']);

        // Manajemen Struktur & Kepegawaian (Bidang 3 / Super Admin)
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'view users']);
        Permission::firstOrCreate(['name' => 'manage pendeta']);
        Permission::firstOrCreate(['name' => 'manage klasis']);
        Permission::firstOrCreate(['name' => 'manage jemaat']);

        // Manajemen Data Jemaat (Level Klasis & Jemaat)
        Permission::firstOrCreate(['name' => 'view pendeta']);
        Permission::firstOrCreate(['name' => 'view klasis']);
        Permission::firstOrCreate(['name' => 'edit own klasis']);
        Permission::firstOrCreate(['name' => 'view jemaat']);
        Permission::firstOrCreate(['name' => 'create jemaat']);
        Permission::firstOrCreate(['name' => 'edit jemaat']);
        Permission::firstOrCreate(['name' => 'delete jemaat']);

        // Anggota Jemaat
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

        // --- PERMISSION WADAH KATEGORIAL (BARU) ---
        // Digunakan untuk PAR, PP, PERWATA, PERPRI, PERLANSIA
        Permission::firstOrCreate(['name' => 'view wadah']);            // Lihat Statistik & Pengurus
        Permission::firstOrCreate(['name' => 'manage program wadah']);  // CRUD Program Kerja
        Permission::firstOrCreate(['name' => 'manage keuangan wadah']); // CRUD Anggaran & Transaksi

        Log::info('Permissions dibuat/diperbarui.');

        // --- DEFINISI ROLES & PENETAPAN PERMISSIONS ---

        // 1. Role: Pendeta
        $rolePendeta = Role::firstOrCreate(['name' => 'Pendeta']);
        $rolePendeta->syncPermissions([
            'view pendeta', 'view klasis', 'view jemaat', 'view anggota jemaat',
        ]);

        // 2. Role: Admin Jemaat
        $roleAdminJemaat = Role::firstOrCreate(['name' => 'Admin Jemaat']);
        $roleAdminJemaat->syncPermissions([
            'view jemaat', 'edit jemaat',
            'view anggota jemaat', 'create anggota jemaat', 'edit anggota jemaat', 'delete anggota jemaat',
            'import anggota jemaat', 'export anggota jemaat',
        ]);

        // 3. Role: Admin Klasis
        $roleAdminKlasis = Role::firstOrCreate(['name' => 'Admin Klasis']);
        $roleAdminKlasis->syncPermissions([
            'view klasis', 'edit own klasis',
            'view jemaat', 'create jemaat', 'edit jemaat', 'delete jemaat',
            'import jemaat', 'export jemaat',
            'view anggota jemaat', 'create anggota jemaat', 'edit anggota jemaat', 'delete anggota jemaat',
            'import anggota jemaat', 'export anggota jemaat',
        ]);

        // 4. Role: Admin Bidang 1
        $roleBidang1 = Role::firstOrCreate(['name' => 'Admin Bidang 1']);
        $roleBidang1->syncPermissions([
            'view pendeta', 'view klasis', 'view jemaat', 'view anggota jemaat',
        ]);

        // 5. Role: Admin Bidang 2
        $roleBidang2 = Role::firstOrCreate(['name' => 'Admin Bidang 2']);
        $roleBidang2->syncPermissions([
            'view klasis', 'view jemaat',
        ]);

        // 6. Role: Admin Bidang 3
        $roleBidang3 = Role::firstOrCreate(['name' => 'Admin Bidang 3']);
        $roleBidang3->syncPermissions([
            'manage pendeta', 'manage klasis', 'manage jemaat',
            'view users', 'view anggota jemaat', 'export anggota jemaat',
            'import pendeta', 'export pendeta', 'import klasis', 'export klasis', 'import jemaat', 'export jemaat',
        ]);

        // 7. Role: Admin Bidang 4
        $roleBidang4 = Role::firstOrCreate(['name' => 'Admin Bidang 4']);
        $roleBidang4->syncPermissions([
            'manage settings', 'manage posts', 'manage services', 'manage messages',
            'view klasis', 'view jemaat',
        ]);

        // --- ROLE WADAH KATEGORIAL (BARU) ---
        // Role ini berlaku umum untuk PAR, PP, PERWATA, PERPRI, PERLANSIA
        // Pembedanya nanti ada di kolom 'jenis_wadah_id' pada tabel users

        // 8. Role: Pengurus Wadah Jemaat
        $roleWadahJemaat = Role::firstOrCreate(['name' => 'Pengurus Wadah Jemaat']);
        $roleWadahJemaat->syncPermissions([
            'view wadah',
            'manage program wadah',
            'manage keuangan wadah',
        ]);

        // 9. Role: Pengurus Wadah Klasis
        $roleWadahKlasis = Role::firstOrCreate(['name' => 'Pengurus Wadah Klasis']);
        $roleWadahKlasis->syncPermissions([
            'view wadah',
            'manage program wadah',
            'manage keuangan wadah',
        ]);

        // 10. Role: Super Admin
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        // Assign semua permission yang ada di database ke Super Admin
        $allPermissions = Permission::pluck('name')->all();
        $roleSuperAdmin->syncPermissions($allPermissions);
        
        Log::info('Semua roles dan permissions berhasil disinkronkan.');
    }
}