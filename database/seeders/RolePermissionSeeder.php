<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // === BUAT PERMISSIONS ===

        // Permissions untuk Jemaat
        Permission::firstOrCreate(['name' => 'view jemaat']);
        Permission::firstOrCreate(['name' => 'create jemaat']);
        Permission::firstOrCreate(['name' => 'edit jemaat']);
        Permission::firstOrCreate(['name' => 'delete jemaat']);
        Permission::firstOrCreate(['name' => 'import jemaat']);
        Permission::firstOrCreate(['name' => 'export jemaat']);

        // Permissions untuk Anggota Jemaat
        Permission::firstOrCreate(['name' => 'view anggota jemaat']);
        Permission::firstOrCreate(['name' => 'create anggota jemaat']);
        Permission::firstOrCreate(['name' => 'edit anggota jemaat']);
        Permission::firstOrCreate(['name' => 'delete anggota jemaat']);
        Permission::firstOrCreate(['name' => 'import anggota jemaat']);
        Permission::firstOrCreate(['name' => 'export anggota jemaat']);

        // (Anda bisa tambahkan permission untuk Klasis dan Pendeta jika ingin lebih detail)
        // Permission::firstOrCreate(['name' => 'edit klasis']);
        // Permission::firstOrCreate(['name' => 'edit pendeta']);


        // === BUAT ROLES ===

        // 1. Super Admin
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        // Super Admin tidak perlu permission, dia akan lolos via Gate::before()

        // 2. Admin Bidang 3 (Asumsi bisa kelola data Master)
        $roleAdminBidang3 = Role::firstOrCreate(['name' => 'Admin Bidang 3']);
        $roleAdminBidang3->givePermissionTo(Permission::all()); // Beri semua permission CRUD

        // 3. Admin Klasis
        $roleAdminKlasis = Role::firstOrCreate(['name' => 'Admin Klasis']);
        $roleAdminKlasis->givePermissionTo([
            'view jemaat',
            'create jemaat', // Bisa buat jemaat baru di klasisnya
            'edit jemaat',   // Bisa edit jemaat di klasisnya
            'delete jemaat', // Bisa hapus jemaat di klasisnya
            'import jemaat',
            'export jemaat',
            'view anggota jemaat',
            'create anggota jemaat',
            'edit anggota jemaat',
            'delete anggota jemaat',
            'import anggota jemaat',
            'export anggota jemaat',
        ]);

        // 4. Admin Jemaat
        $roleAdminJemaat = Role::firstOrCreate(['name' => 'Admin Jemaat']);
        $roleAdminJemaat->givePermissionTo([
            'view jemaat', // Hanya bisa lihat jemaatnya sendiri (di-scope di controller)
            'edit jemaat',   // Hanya bisa edit jemaatnya sendiri
            'view anggota jemaat', // Hanya bisa lihat anggota di jemaatnya
            'create anggota jemaat',
            'edit anggota jemaat',
            'delete anggota jemaat',
            'import anggota jemaat',
            'export anggota jemaat',
        ]);
        
        // 5. Pendeta
        $rolePendeta = Role::firstOrCreate(['name' => 'Pendeta']);
        // Pendeta mungkin hanya bisa melihat data (read-only)
        $rolePendeta->givePermissionTo([
            'view jemaat',
            'view anggota jemaat',
        ]);
    }
}