<?php

namespace App\Http\Controllers\Admin;

// app/Http/Controllers/Admin/SettingController.php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting; // Import model Setting
use Illuminate\Support\Facades\Storage; // Import Storage facade untuk file
use Illuminate\Support\Facades\Log; // Import Log facade untuk debug

class SettingController extends Controller
{
    /**
     * Tampilkan form untuk mengedit pengaturan aplikasi.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Ambil record pengaturan pertama, atau buat baru jika belum ada
        // Kita asumsikan hanya ada 1 baris data di tabel settings
        $setting = Setting::firstOrCreate(['id' => 1]); // Gunakan ID 1

        // Kirim data setting ke view
        return view('admin.settings', compact('setting'));
    }

    /**
     * Perbarui pengaturan aplikasi di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validasi data yang masuk (tambahkan aturan spesifik jika perlu)
        $validatedData = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi gambar
            'hero_text' => 'nullable|string',
            'about_us' => 'nullable|string',
            'vision' => 'nullable|string',
            'about_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi gambar
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
        ]);

        // Cari record setting (atau buat baru jika belum ada, seharusnya sudah ada dari edit)
        $setting = Setting::firstOrCreate(['id' => 1]); // Gunakan ID 1

        // Siapkan data untuk update (kecualikan file dulu)
        $updateData = $request->except(['_token', '_method', 'site_logo', 'about_image']);

        // Proses Upload Logo
        if ($request->hasFile('site_logo')) {
            // Hapus logo lama jika ada
            if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            // Simpan logo baru dan dapatkan path
            $logoPath = $request->file('site_logo')->store('logos', 'public');
            $updateData['logo_path'] = $logoPath; // Tambahkan path ke data update
        }

        // Proses Upload Gambar Ilustrasi
        if ($request->hasFile('about_image')) {
             // Hapus gambar lama jika ada
            if ($setting->about_image_path && Storage::disk('public')->exists($setting->about_image_path)) {
                Storage::disk('public')->delete($setting->about_image_path);
            }
            // Simpan gambar baru dan dapatkan path
            $aboutImagePath = $request->file('about_image')->store('illustrations', 'public');
            $updateData['about_image_path'] = $aboutImagePath; // Tambahkan path ke data update
        }

        // Update record setting di database
        try {
             $setting->update($updateData);
        } catch (\Exception $e) {
             Log::error('Error updating settings: ' . $e->getMessage());
             // Opsional: kembali dengan pesan error
             return back()->with('error', 'Gagal menyimpan pengaturan. Silakan coba lagi.');
        }

        // Redirect kembali ke halaman pengaturan dengan pesan sukses
        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}