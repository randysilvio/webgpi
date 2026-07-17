<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'site_tagline',
        'logo_path',
        'hero_text',
        'about_us',
        'vision',
        'about_image_path',
        'contact_address',
        'contact_phone',
        'contact_email',
        'contact_website',
        'work_hours',
        'social_facebook',
        'social_youtube',
        'social_instagram',
        'social_twitter',
        'footer_description',
        'module_access', // Tambahan matriks akses
    ];

    protected $casts = [
        'module_access' => 'array', 
    ];

    /**
     * Helper Fungsi untuk mengecek apakah User saat ini boleh melihat sebuah modul.
     */
    public function hasModuleAccess($moduleKey)
    {
        $user = auth()->user();
        if (!$user) return false;
        
        // Super Admin selalu bisa melihat semua menu
        if ($user->hasRole('Super Admin')) return true;

        // PENCEGAHAN ERROR 500: Cek apakah data di DB null, jika ya jadikan array kosong
        $accessMap = is_array($this->module_access) ? $this->module_access : [];

        // Mapping fallback default bawaan sistem
        $defaultAccess = [
            'bidang1_sakramen' => ['Admin Bidang 1'],
            'bidang1_tata'     => ['Admin Bidang 1'],
            'bidang2_keuangan' => ['Admin Bidang 2'],
            'bidang3_hris'     => ['Admin Bidang 3'],
            'bidang4_popup'    => ['Admin Bidang 4'],
            'bidang4_berita'   => ['Admin Bidang 4'],
            'bidang4_eoffice'  => ['Admin Bidang 4'],
            'wilayah_master'   => ['Admin Klasis', 'Admin Jemaat'],
            'wilayah_wadah'    => ['Admin Klasis', 'Admin Jemaat'],
            'laporan_terpadu'  => ['Admin Klasis', 'Admin Jemaat', 'Admin Bidang 2'],
        ];

        // Ambil daftar role (Coba dari Database dulu, kalau gagal pakai default)
        $allowedRoles = $accessMap[$moduleKey] ?? $defaultAccess[$moduleKey] ?? [];

        if (empty($allowedRoles)) return false;

        return $user->hasAnyRole($allowedRoles);
    }
}