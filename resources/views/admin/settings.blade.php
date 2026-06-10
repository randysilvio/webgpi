@extends('layouts.app')

@section('title', 'Pengaturan Website')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Pengaturan Umum</h2>
            <p class="text-sm text-slate-500">Konfigurasi informasi utama website, kontak, dan tampilan publik.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg border border-green-200 flex items-center text-sm shadow-sm">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg border border-red-200 flex items-center text-sm shadow-sm">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg border border-red-200 text-sm shadow-sm">
            <p class="font-bold mb-1 flex items-center"><i class="fas fa-times-circle mr-2"></i> Terjadi kesalahan:</p>
            <ul class="list-disc list-inside ml-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- 1. IDENTITAS WEBSITE --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">
                <i class="fas fa-globe mr-2"></i> Identitas & Brand
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Website Utama</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $setting->site_name ?? '') }}" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tagline / Slogan</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $setting->site_tagline ?? '') }}" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Logo Website</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 bg-slate-50 rounded-lg border border-slate-200 flex items-center justify-center overflow-hidden relative">
                            @if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path))
                                <img src="{{ Storage::url($setting->logo_path) }}" id="preview-logo" class="object-contain h-full w-full p-2">
                            @else
                                <span class="text-[10px] text-slate-400 absolute">No Logo</span>
                                <img id="preview-logo" class="hidden object-contain h-full w-full p-2 absolute z-10 bg-white">
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="site_logo" accept="image/*" onchange="previewImage(this, 'preview-logo')" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:uppercase file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            <p class="text-[10px] text-slate-400 mt-2"><i class="fas fa-info-circle mr-1"></i> Format: PNG, JPG, SVG. Maks 2MB.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. KONTEN LANDING PAGE --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">
                <i class="fas fa-home mr-2"></i> Konten Halaman Depan
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Hero Text (Judul Utama)</label>
                    <textarea name="hero_text" rows="2" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">{{ old('hero_text', $setting->hero_text ?? '') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tentang Kami (Ringkasan)</label>
                        <textarea name="about_us" rows="4" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">{{ old('about_us', $setting->about_us ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Visi & Misi (Ringkasan)</label>
                        <textarea name="vision" rows="4" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">{{ old('vision', $setting->vision ?? '') }}</textarea>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Gambar Ilustrasi 'Tentang Kami'</label>
                    <div class="flex items-center gap-4">
                        <div class="w-32 h-20 bg-slate-50 rounded-lg border border-slate-200 flex items-center justify-center overflow-hidden relative">
                            @if ($setting->about_image_path && Storage::disk('public')->exists($setting->about_image_path))
                                <img src="{{ Storage::url($setting->about_image_path) }}" id="preview-about" class="object-cover h-full w-full">
                            @else
                                <span class="text-[10px] text-slate-400 absolute">No Image</span>
                                <img id="preview-about" class="hidden object-cover h-full w-full absolute z-10 bg-white">
                            @endif
                        </div>
                        <input type="file" name="about_image" accept="image/*" onchange="previewImage(this, 'preview-about')" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:uppercase file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. KONTAK & ALAMAT --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">
                <i class="fas fa-address-book mr-2"></i> Kontak & Lokasi
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Alamat Kantor</label>
                    <textarea name="contact_address" rows="2" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">{{ old('contact_address', $setting->contact_address ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nomor Telepon</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $setting->contact_phone ?? '') }}" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email Resmi</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $setting->contact_email ?? '') }}" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Website URL</label>
                    <input type="url" name="contact_website" value="{{ old('contact_website', $setting->contact_website ?? '') }}" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jam Operasional</label>
                    <input type="text" name="work_hours" value="{{ old('work_hours', $setting->work_hours ?? '') }}" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                </div>
            </div>
        </div>

        {{-- 4. MEDIA SOSIAL --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">
                <i class="fas fa-share-alt mr-2"></i> Media Sosial
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Facebook URL</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fab fa-facebook"></i></span>
                        <input type="url" name="social_facebook" value="{{ old('social_facebook', $setting->social_facebook ?? '') }}" placeholder="https://facebook.com/..." class="w-full pl-10 border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">YouTube URL</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fab fa-youtube"></i></span>
                        <input type="url" name="social_youtube" value="{{ old('social_youtube', $setting->social_youtube ?? '') }}" placeholder="https://youtube.com/..." class="w-full pl-10 border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Instagram URL</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fab fa-instagram"></i></span>
                        <input type="url" name="social_instagram" value="{{ old('social_instagram', $setting->social_instagram ?? '') }}" placeholder="https://instagram.com/..." class="w-full pl-10 border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Twitter / X URL</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fab fa-twitter"></i></span>
                        <input type="url" name="social_twitter" value="{{ old('social_twitter', $setting->social_twitter ?? '') }}" placeholder="https://x.com/..." class="w-full pl-10 border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. FOOTER --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">
                <i class="fas fa-shoe-prints mr-2"></i> Pengaturan Footer
            </h3>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Deskripsi Singkat Footer</label>
                <textarea name="footer_description" rows="3" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">{{ old('footer_description', $setting->footer_description ?? '') }}</textarea>
            </div>
        </div>

        {{-- TOMBOL SIMPAN --}}
        <div class="flex justify-end pt-4 pb-10">
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-8 py-3 rounded-lg text-sm font-bold uppercase tracking-wide shadow-lg transition flex items-center transform hover:-translate-y-0.5">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    function previewImage(input, imgId) {
        const preview = document.getElementById(imgId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection