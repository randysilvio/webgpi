@extends('admin.layout')

@section('title', 'Pengaturan Website')
@section('header-title', 'Pengaturan Website')

@section('content')

    {{-- Display Success/Error Messages --}}
    @if (session('success'))
        <div class="flash-message mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
            <p class="font-bold">Sukses!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
     @if (session('error'))
        <div class="flash-message mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            <p class="font-bold">Error!</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif
    {{-- Display Validation Errors --}}
     @if ($errors->any())
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            <p class="font-bold">Oops! Ada kesalahan:</p>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM PENGATURAN WEBSITE --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-8">
            {{-- ... (SEMUA KONTEN FORM ANDA DARI FILE settings.blade.php SEBELUMNYA) ... --}}
            
             {{-- Bagian Identitas & Tampilan --}}
            <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-6">Identitas & Tampilan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama Website --}}
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Website Utama</label>
                        <input type="text" id="site_name" name="site_name" value="{{ old('site_name', $setting->site_name ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    </div>
                    {{-- Sub Nama / Tagline --}}
                    <div>
                        <label for="site_tagline" class="block text-sm font-medium text-gray-700 mb-1">Sub Nama / Tagline</label>
                        <input type="text" id="site_tagline" name="site_tagline" value="{{ old('site_tagline', $setting->site_tagline ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    </div>
                    {{-- Logo Website --}}
                    <div>
                        <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-1">Logo Website</label>
                        <input type="file" id="site_logo" name="site_logo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" onchange="previewImage(event, 'logo-preview')">
                        <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF, SVG maks 2MB. Biarkan kosong jika tidak ingin mengubah.</p>
                        @if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path))
                            <img src="{{ Storage::url($setting->logo_path) }}" alt="Logo Saat Ini" class="image-preview mt-2">
                        @else
                            <span class="text-xs text-gray-500 italic">Belum ada logo.</span>
                        @endif
                        <img id="logo-preview" src="#" alt="Preview Logo Baru" class="image-preview mt-2 hidden">
                    </div>
                </div>
            </div>

            {{-- Bagian Konten Halaman Depan --}}
            <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-6">Konten Halaman Depan</h3>
                <div class="space-y-6">
                    <div>
                        <label for="hero_text" class="block text-sm font-medium text-gray-700 mb-1">Teks Paragraf di Halaman Depan (Hero)</label>
                        <textarea id="hero_text" name="hero_text" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">{{ old('hero_text', $setting->hero_text ?? '') }}</textarea>
                    </div>
                    <div>
                        <label for="about_us" class="block text-sm font-medium text-gray-700 mb-1">Tentang Kami (Paragraf 1)</label>
                        <textarea id="about_us" name="about_us" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">{{ old('about_us', $setting->about_us ?? '') }}</textarea>
                    </div>
                    <div>
                        <label for="vision" class="block text-sm font-medium text-gray-700 mb-1">Visi (Paragraf 2 Tentang Kami)</label>
                        <textarea id="vision" name="vision" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">{{ old('vision', $setting->vision ?? '') }}</textarea>
                    </div>
                    <div>
                        <label for="about_image" class="block text-sm font-medium text-gray-700 mb-1">Gambar Ilustrasi (Bagian Tentang)</label>
                        <input type="file" id="about_image" name="about_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" onchange="previewImage(event, 'about-image-preview')">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG maks 2MB. Rekomendasi rasio 5:3.5. Biarkan kosong jika tidak ingin mengubah.</p>
                        @if ($setting->about_image_path && Storage::disk('public')->exists($setting->about_image_path))
                            <img src="{{ Storage::url($setting->about_image_path) }}" alt="Gambar Saat Ini" class="image-preview mt-2">
                        @else
                             <span class="text-xs text-gray-500 italic">Belum ada gambar ilustrasi.</span>
                        @endif
                        <img id="about-image-preview" src="#" alt="Preview Gambar Ilustrasi Baru" class="image-preview mt-2 hidden">
                    </div>
                </div>
            </div>

            {{-- Bagian Kontak --}}
            <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-6">Informasi Kontak</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="contact_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Kantor</label>
                        <textarea id="contact_address" name="contact_address" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">{{ old('contact_address', $setting->contact_address ?? '') }}</textarea>
                    </div>
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon (pisahkan dengan koma jika > 1)</label>
                        <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $setting->contact_phone ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    </div>
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email (pisahkan dengan koma jika > 1)</label>
                        <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $setting->contact_email ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    </div>
                    <div>
                        <label for="contact_website" class="block text-sm font-medium text-gray-700 mb-1">URL Website</label>
                        <input type="url" id="contact_website" name="contact_website" value="{{ old('contact_website', $setting->contact_website ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    </div>
                     <div>
                        <label for="work_hours" class="block text-sm font-medium text-gray-700 mb-1">Jam Kerja</label>
                        <input type="text" id="work_hours" name="work_hours" value="{{ old('work_hours', $setting->work_hours ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    </div>
                </div>
            </div>

            {{-- Bagian Media Sosial --}}
            <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-6">Media Sosial</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="social_facebook" class="block text-sm font-medium text-gray-700 mb-1">URL Halaman Facebook</label>
                        <input type="url" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $setting->social_facebook ?? '') }}" placeholder="https://facebook.com/namahalaman" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    </div>
                    <div>
                        <label for="social_youtube" class="block text-sm font-medium text-gray-700 mb-1">URL Channel YouTube</label>
                        <input type="url" id="social_youtube" name="social_youtube" value="{{ old('social_youtube', $setting->social_youtube ?? '') }}" placeholder="https://youtube.com/channel/namaChannel" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    </div>
                     <div>
                        <label for="social_instagram" class="block text-sm font-medium text-gray-700 mb-1">URL Profil Instagram</label>
                        <input type="url" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $setting->social_instagram ?? '') }}" placeholder="https://instagram.com/namaakun" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    </div>
                     <div>
                        <label for="social_twitter" class="block text-sm font-medium text-gray-700 mb-1">URL Profil Twitter/X</label>
                        <input type="url" id="social_twitter" name="social_twitter" value="{{ old('social_twitter', $setting->social_twitter ?? '') }}" placeholder="https://x.com/namaakun" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    </div>
                </div>
            </div>

            {{-- Bagian Footer --}}
            <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-6">Footer</h3>
                <div class="space-y-6">
                    <div>
                        <label for="footer_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat di Footer</label>
                        <textarea id="footer_description" name="footer_description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">{{ old('footer_description', $setting->footer_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- Tombol Simpan --}}
         <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                Simpan Pengaturan
            </button>
        </div>

    </form>

@endsection