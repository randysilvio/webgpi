<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting; 
use Spatie\Permission\Models\Role; // Tambahkan import Role
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Log; 

class SettingController extends Controller
{
    public function edit()
    {
        $setting = Setting::firstOrCreate(['id' => 1]); 
        
        // Ambil semua role KECUALI Super Admin (karena Super Admin selalu centang penuh di sistem)
        $roles = Role::where('name', '!=', 'Super Admin')->orderBy('name')->get();

        return view('admin.settings', compact('setting', 'roles'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
            'hero_text' => 'nullable|string',
            'about_us' => 'nullable|string',
            'vision' => 'nullable|string',
            'about_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
            'contact_address' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_website' => 'nullable|url|max:255',
            'work_hours' => 'nullable|string|max:255',
            'social_facebook' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'footer_description' => 'nullable|string',
            'module_access' => 'nullable|array', // Validasi array matriks
        ]);

        $setting = Setting::firstOrCreate(['id' => 1]); 
        $updateData = $request->except(['_token', '_method', 'site_logo', 'about_image']);

        // Pastikan array tersimpan dengan aman (meskipun kosong)
        $updateData['module_access'] = $request->input('module_access', []);

        if ($request->hasFile('site_logo')) {
            if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $logoPath = $request->file('site_logo')->store('logos', 'public');
            $updateData['logo_path'] = $logoPath; 
        }

        if ($request->hasFile('about_image')) {
            if ($setting->about_image_path && Storage::disk('public')->exists($setting->about_image_path)) {
                Storage::disk('public')->delete($setting->about_image_path);
            }
            $aboutImagePath = $request->file('about_image')->store('illustrations', 'public');
            $updateData['about_image_path'] = $aboutImagePath; 
        }

        try {
             $setting->update($updateData);
        } catch (\Exception $e) {
             Log::error('Error updating settings: ' . $e->getMessage());
             return back()->with('error', 'Gagal menyimpan pengaturan. Silakan coba lagi.');
        }

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}