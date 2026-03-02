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

        // 1. Manajemen Konten & Website (Bidang 4)
        Permission::firstOrCreate(['name' => 'manage settings']); 
        Permission::firstOrCreate(['name' => 'manage posts']);    
        Permission::firstOrCreate(['name' => 'manage services']); 
        Permission::firstOrCreate(['name' => 'manage messages']); 

        // 2. Manajemen Kepegawaian (HRIS - Bidang 3)
        Permission::firstOrCreate(['name' => 'manage pegawai']);   // Permission Baru untuk HRIS Unified
        Permission::firstOrCreate(['name' => 'manage pendeta']);   // Legacy / Khusus Pendeta
        Permission::firstOrCreate(['name' => 'manage mutasi']);    // Kelola Mutasi
        Permission::firstOrCreate(['name' => 'import pegawai']);
        Permission::firstOrCreate(['name' => 'export pegawai']);

        // 3. Manajemen Struktur Organisasi (Bidang 3/Admin Sinode)
        Permission::firstOrCreate(['name' => 'manage users']);    
        Permission::firstOrCreate(['name' => 'view users']);      
        Permission::firstOrCreate(['name' => 'manage klasis']);    
        Permission::firstOrCreate(['name' => 'manage jemaat']);    

        // 4. Akses Data Wilayah (Read Only untuk dropdown/referensi)
        Permission::firstOrCreate(['name' => 'view pendeta']);     
        Permission::firstOrCreate(['name' => 'view klasis']);     
        Permission::firstOrCreate(['name' => 'edit own klasis']);  
        Permission::firstOrCreate(['name' => 'view jemaat']);      
        
        // 5. Manajemen Data Jemaat (Level Klasis & Jemaat)
        Permission::firstOrCreate(['name' => 'create jemaat']);    
        Permission::firstOrCreate(['name' => 'edit jemaat']);      
        Permission::firstOrCreate(['name' => 'delete jemaat']);    

        Permission::firstOrCreate(['name' => 'view anggota jemaat']);
        Permission::firstOrCreate(['name' => 'create anggota jemaat']);
        Permission::firstOrCreate(['name' => 'edit anggota jemaat']);
        Permission::firstOrCreate(['name' => 'delete anggota jemaat']);
        Permission::firstOrCreate(['name' => 'import anggota jemaat']);
        Permission::firstOrCreate(['name' => 'export anggota jemaat']);

        // 6. Import/Export Umum
        Permission::firstOrCreate(['name' => 'import pendeta']);
        Permission::firstOrCreate(['name' => 'export pendeta']);
        Permission::firstOrCreate(['name' => 'import klasis']);
        Permission::firstOrCreate(['name' => 'export klasis']);
        Permission::firstOrCreate(['name' => 'import jemaat']);
        Permission::firstOrCreate(['name' => 'export jemaat']);

        Log::info('Permissions dibuat/diperbarui.');

        // --- DEFINISI ROLES & PENETAPAN PERMISSIONS ---

        // 1. Role: Pendeta
        $rolePendeta = Role::firstOrCreate(['name' => 'Pendeta']);
        $rolePendeta->syncPermissions([
            'view pendeta', 
            'view klasis',
            'view jemaat',
            'view anggota jemaat',
        ]);

        // 2. Role: Admin Jemaat
        $roleAdminJemaat = Role::firstOrCreate(['name' => 'Admin Jemaat']);
        $roleAdminJemaat->syncPermissions([
            'view jemaat',           
            'edit jemaat',           
            'view anggota jemaat',   
            'create anggota jemaat',
            'edit anggota jemaat',
            'delete anggota jemaat',
            'import anggota jemaat', 
            'export anggota jemaat',
        ]);

        // 3. Role: Admin Klasis
        $roleAdminKlasis = Role::firstOrCreate(['name' => 'Admin Klasis']);
        $roleAdminKlasis->syncPermissions([
            'view klasis',           
            'edit own klasis',       
            'view jemaat',           
            'create jemaat',
            'edit jemaat',
            'delete jemaat',
            'import jemaat',         
            'export jemaat',
            'view anggota jemaat',   
            'create anggota jemaat',
            'edit anggota jemaat',
            'delete anggota jemaat',
            'import anggota jemaat', 
            'export anggota jemaat',
        ]);

        // 4. Role: Admin Bidang 1 (Pelayanan)
        $roleBidang1 = Role::firstOrCreate(['name' => 'Admin Bidang 1']);
        $roleBidang1->syncPermissions([
            'view pendeta',
            'view klasis',
            'view jemaat',
            'view anggota jemaat',
        ]);

        // 5. Role: Admin Bidang 2 (Keuangan & Pembangunan)
        $roleBidang2 = Role::firstOrCreate(['name' => 'Admin Bidang 2']);
        $roleBidang2->syncPermissions([
            'view klasis',
            'view jemaat',
        ]);

        // 6. Role: Admin Bidang 3 (KHUSUS KEPEGAWAIAN)
        // Update: Menghapus akses kelola Klasis/Jemaat, Fokus ke HRIS
        $roleBidang3 = Role::firstOrCreate(['name' => 'Admin Bidang 3']);
        $roleBidang3->syncPermissions([
            // HRIS / Kepegawaian Core
            'manage pegawai',   // Permission utama PegawaiController
            'manage pendeta',   // Backward compatibility
            'manage mutasi',    // Akses Mutasi
            'import pegawai',
            'export pegawai',
            'import pendeta',
            'export pendeta',
            
            // Read Only Data Wilayah (Diperlukan untuk assign lokasi tugas pegawai)
            'view klasis',      
            'view jemaat',
            'view users',       // Lihat user untuk mapping pegawai
            
            // Anggota Jemaat (Read Only - Opsional untuk referensi rekrutmen)
            'view anggota jemaat',
        ]);
        Log::info('Role Admin Bidang 3 disinkronkan (Fokus Kepegawaian).');

        // 7. Role: Admin Bidang 4 (Kominfo)
        $roleBidang4 = Role::firstOrCreate(['name' => 'Admin Bidang 4']);
        $roleBidang4->syncPermissions([
            'manage settings',
            'manage posts',
            'manage services',
            'manage messages',
            'view klasis',
            'view jemaat',
        ]);

        // 8. Super Admin
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $allPermissions = Permission::pluck('name')->all(); 
        $roleSuperAdmin->syncPermissions($allPermissions); 
        
        Log::info('Semua permissions disinkronkan ke Super Admin.');
        Log::info('RolesAndPermissionsSeeder selesai.');
    }
}