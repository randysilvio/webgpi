@extends('layouts.app')

@section('title', 'Pusat Konfigurasi Sistem')
@section('header-title', 'Konfigurasi Sistem')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-2 border-gray-800 pb-4 mb-6">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Pusat Konfigurasi Sistem & Otoritas</h2>
            <p class="text-xs text-gray-600 mt-1">Sistem kendali identitas portal dan matriks pembagian modul kerja.</p>
        </div>
    </div>

    {{-- Form Utama --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- MATRIKS AKSES MODUL --}}
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm">
            <div class="mb-5 border-b border-gray-200 pb-3">
                <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest flex items-center">
                    <i class="fas fa-sitemap mr-2 text-blue-800"></i> Matriks Otoritas Modul Kerja
                </h3>
                <p class="text-[10px] text-gray-500 mt-1 font-medium">Tetapkan kelompok jabatan (Role) yang diizinkan untuk mengakses modul-modul administratif di panel kendali.</p>
            </div>

            @php
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

                $modules = [
                    'bidang1_sakramen' => ['icon' => 'fa-hand-holding-water', 'label' => 'Bidang 1: Sakramen', 'desc' => 'Arsip Baptisan, Sidi & Nikah'],
                    'bidang1_tata'     => ['icon' => 'fa-gavel', 'label' => 'Bidang 1: Tata Gereja', 'desc' => 'Pejabat & Risalah Sidang'],
                    'bidang2_keuangan' => ['icon' => 'fa-wallet', 'label' => 'Bidang 2: Keuangan', 'desc' => 'Buku Kas, RAPB & Aset'],
                    'bidang3_hris'     => ['icon' => 'fa-id-card', 'label' => 'Bidang 3: Kepegawaian', 'desc' => 'HRIS, Pendeta & Mutasi'],
                    'bidang4_popup'    => ['icon' => 'fa-bullhorn', 'label' => 'Bidang 4: Banner Pengumuman', 'desc' => 'Iklan/Popup Portal Depan'],
                    'bidang4_berita'   => ['icon' => 'fa-newspaper', 'label' => 'Bidang 4: Dokumen Berita', 'desc' => 'Publikasi Artikel & Pesan'],
                    'bidang4_eoffice'  => ['icon' => 'fa-envelope-open-text', 'label' => 'Bidang 4: E-Office', 'desc' => 'Agenda Surat Masuk/Keluar'],
                    'wilayah_master'   => ['icon' => 'fa-database', 'label' => 'Struktur: Wilayah', 'desc' => 'Data Klasis, Jemaat & Anggota'],
                    'wilayah_wadah'    => ['icon' => 'fa-users', 'label' => 'Struktur: Kategorial', 'desc' => 'Susunan Pengurus Wadah'],
                    'laporan_terpadu'  => ['icon' => 'fa-file-alt', 'label' => 'Pusat Analisis', 'desc' => 'Renstra, Demografi & Kas'],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($modules as $key => $mod)
                    @php
                        $accessMap = is_array($setting->module_access) ? $setting->module_access : [];
                        $savedRoles = $accessMap[$key] ?? $defaultAccess[$key] ?? [];
                    @endphp
                    <div class="p-4 border border-gray-300 rounded bg-gray-50 flex flex-col h-full hover:shadow transition">
                        <div class="flex items-center gap-3 mb-3 flex-grow">
                            <div class="w-10 h-10 rounded border border-gray-300 bg-white flex flex-shrink-0 items-center justify-center text-gray-700">
                                <i class="fas {{ $mod['icon'] }} text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-[11px] font-black text-gray-900 uppercase leading-tight">{{ $mod['label'] }}</h4>
                                <p class="text-[9px] text-gray-500 mt-1 uppercase tracking-wider">{{ $mod['desc'] }}</p>
                            </div>
                        </div>

                        {{-- CUSTOM DROPDOWN CHECKLIST FORMAL --}}
                        <div class="relative dropdown-container w-full mt-auto">
                            <button type="button" onclick="toggleSettingsDropdown(this)" class="w-full flex items-center justify-between px-3 py-2 border border-gray-300 rounded bg-white hover:bg-gray-100 text-left text-xs transition z-20 relative">
                                <span class="text-gray-800 font-bold truncate select-none placeholder-text text-[10px] uppercase">
                                    @if(count($savedRoles) > 0)
                                        Akses: {{ implode(', ', $savedRoles) }}
                                    @else
                                        <span class="text-red-700">Pusat Sinode / Super Admin</span>
                                    @endif
                                </span>
                                <i class="fas fa-chevron-down text-gray-500 pointer-events-none ml-2"></i>
                            </button>

                            {{-- Dropdown Panel Menu --}}
                            <div class="settings-dropdown-panel absolute left-0 mt-1 w-full bg-white border border-gray-800 rounded shadow-lg hidden flex-col z-30 max-h-48 overflow-y-auto divide-y divide-gray-100 py-1">
                                @foreach($roles as $role)
                                    <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer transition select-none text-[10px] font-bold text-gray-800 uppercase">
                                        <input type="checkbox" name="module_access[{{ $key }}][]" value="{{ $role->name }}" 
                                               class="w-3.5 h-3.5 text-blue-800 border-gray-400 rounded focus:ring-blue-800 mr-3 checkbox-item"
                                               onchange="updateDropdownPlaceholder(this)"
                                               {{ in_array($role->name, $savedRoles) ? 'checked' : '' }}>
                                        {{ $role->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 1. IDENTITAS WEBSITE --}}
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm mt-8">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">
                <i class="fas fa-globe mr-2 text-blue-800"></i> Identitas Eksternal Portal
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nama Organisasi / Portal</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $setting->site_name ?? '') }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Moto / Tagline</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $setting->site_tagline ?? '') }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Lambang / Logo Resmi</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 bg-gray-100 rounded border border-gray-300 flex items-center justify-center overflow-hidden relative shadow-inner">
                            @if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path))
                                <img src="{{ Storage::url($setting->logo_path) }}" id="preview-logo" class="object-contain h-full w-full p-2">
                            @else
                                <span class="text-[10px] text-gray-400 font-bold uppercase absolute">Kosong</span>
                                <img id="preview-logo" class="hidden object-contain h-full w-full p-2 absolute z-10 bg-white">
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="site_logo" accept="image/*" onchange="previewImage(this, 'preview-logo')" class="w-full text-[10px] text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-gray-800 file:text-white hover:file:bg-gray-900 cursor-pointer">
                            <p class="text-[10px] text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i> Format transparan (PNG). Maks 2MB.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. KONTEN LANDING PAGE --}}
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">
                <i class="fas fa-home mr-2 text-blue-800"></i> Konten Beranda Terbuka
            </h3>
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Kalimat Selamat Datang (Hero Text)</label>
                    <textarea name="hero_text" rows="2" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">{{ old('hero_text', $setting->hero_text ?? '') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Profil Sejarah (Tentang Kami)</label>
                        <textarea name="about_us" rows="5" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">{{ old('about_us', $setting->about_us ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Visi & Misi Pelayanan</label>
                        <textarea name="vision" rows="5" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">{{ old('vision', $setting->vision ?? '') }}</textarea>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Ilustrasi Latar Belakang</label>
                    <div class="flex items-center gap-4">
                        <div class="w-32 h-20 bg-gray-100 rounded border border-gray-300 flex items-center justify-center overflow-hidden relative shadow-inner">
                            @if ($setting->about_image_path && Storage::disk('public')->exists($setting->about_image_path))
                                <img src="{{ Storage::url($setting->about_image_path) }}" id="preview-about" class="object-cover h-full w-full">
                            @else
                                <span class="text-[10px] text-gray-400 font-bold uppercase absolute">Kosong</span>
                                <img id="preview-about" class="hidden object-cover h-full w-full absolute z-10 bg-white">
                            @endif
                        </div>
                        <input type="file" name="about_image" accept="image/*" onchange="previewImage(this, 'preview-about')" class="w-full text-[10px] text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-gray-800 file:text-white hover:file:bg-gray-900 cursor-pointer">
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. KONTAK & ALAMAT --}}
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">
                <i class="fas fa-address-book mr-2 text-blue-800"></i> Kontak Resmi & Jam Kerja
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Alamat Kantor Sinode</label>
                    <textarea name="contact_address" rows="2" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">{{ old('contact_address', $setting->contact_address ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomor Kontak Pusat</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $setting->contact_phone ?? '') }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Surat Elektronik (Email)</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $setting->contact_email ?? '') }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tautan Web Resmi</label>
                    <input type="url" name="contact_website" value="{{ old('contact_website', $setting->contact_website ?? '') }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Jam Pelayanan Eksternal</label>
                    <input type="text" name="work_hours" value="{{ old('work_hours', $setting->work_hours ?? '') }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                </div>
            </div>
        </div>

        {{-- 4. MEDIA SOSIAL & FOOTER --}}
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">
                <i class="fas fa-share-alt mr-2 text-blue-800"></i> Publikasi Media Sosial
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tautan Facebook</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fab fa-facebook"></i></span>
                        <input type="url" name="social_facebook" value="{{ old('social_facebook', $setting->social_facebook ?? '') }}" class="w-full pl-10 border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tautan YouTube</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fab fa-youtube"></i></span>
                        <input type="url" name="social_youtube" value="{{ old('social_youtube', $setting->social_youtube ?? '') }}" class="w-full pl-10 border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tautan Instagram</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fab fa-instagram"></i></span>
                        <input type="url" name="social_instagram" value="{{ old('social_instagram', $setting->social_instagram ?? '') }}" class="w-full pl-10 border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tautan Twitter / X</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fab fa-twitter"></i></span>
                        <input type="url" name="social_twitter" value="{{ old('social_twitter', $setting->social_twitter ?? '') }}" class="w-full pl-10 border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Teks Penutup (Footer)</label>
                <textarea name="footer_description" rows="2" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">{{ old('footer_description', $setting->footer_description ?? '') }}</textarea>
            </div>
        </div>

        {{-- TOMBOL SIMPAN --}}
        <div class="flex justify-end pt-2 pb-10">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Terapkan Pembaruan
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    // Membuka/Menutup Dropdown Panel
    function toggleSettingsDropdown(button) {
        const currentContainer = button.closest('.dropdown-container');
        const currentMenu = currentContainer.querySelector('.settings-dropdown-panel');
        const currentIcon = button.querySelector('.fa-chevron-down');

        // Tutup semua dropdown lain yang sedang terbuka
        document.querySelectorAll('.dropdown-container').forEach(container => {
            if (container !== currentContainer) {
                container.querySelector('.settings-dropdown-panel').classList.add('hidden');
                container.querySelector('.settings-dropdown-panel').classList.remove('flex');
            }
        });

        // Toggle dropdown yang diklik
        if (currentMenu.classList.contains('hidden')) {
            currentMenu.classList.remove('hidden');
            currentMenu.classList.add('flex');
        } else {
            currentMenu.classList.add('hidden');
            currentMenu.classList.remove('flex');
        }
    }

    // Mengubah teks placeholder tombol saat item dicentang/dilepas
    function updateDropdownPlaceholder(checkbox) {
        const container = checkbox.closest('.dropdown-container');
        const placeholder = container.querySelector('.placeholder-text');
        const checkedBoxes = container.querySelectorAll('.checkbox-item:checked');
        
        if (checkedBoxes.length > 0) {
            const names = Array.from(checkedBoxes).map(cb => cb.value);
            placeholder.innerHTML = 'Akses: ' + names.join(', ');
            placeholder.classList.remove('text-red-700');
        } else {
            placeholder.innerHTML = '<span class="text-red-700">Pusat Sinode / Super Admin</span>';
        }
    }

    // Menutup dropdown jika admin mengklik di luar area menu dropdown
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-container')) {
            document.querySelectorAll('.dropdown-container').forEach(container => {
                container.querySelector('.settings-dropdown-panel').classList.add('hidden');
                container.querySelector('.settings-dropdown-panel').classList.remove('flex');
            });
        }
    });

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