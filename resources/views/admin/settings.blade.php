@extends('layouts.app')

@section('title', 'Pengaturan Website')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Pengaturan Umum & Akses Modul</h2>
            <p class="text-sm text-slate-500">Konfigurasi website dan pembagian wewenang menu sidebar per Peran (Role).</p>
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

        {{-- USER-FRIENDLY INTERFACE: MODERN MODULAR ACCESS CONTROL --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="mb-6 border-b border-slate-100 pb-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center">
                    <i class="fas fa-sliders-h mr-2 text-brand-600 text-sm"></i> Otoritas Akses Menu Sidebar
                </h3>
                <p class="text-[10px] text-slate-400 mt-1 font-bold">Pilih Peran (Role) mana saja yang diizinkan untuk melihat dan mengelola masing-masing modul di bawah ini.</p>
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
                    'bidang1_sakramen' => ['icon' => 'fa-hand-holding-water', 'color' => 'text-cyan-500 bg-cyan-50', 'label' => 'Bidang 1: Administrasi Sakramen', 'desc' => 'Mengelola Data Baptisan, Sidi, dan Pernikahan.'],
                    'bidang1_tata'     => ['icon' => 'fa-gavel', 'color' => 'text-slate-500 bg-slate-100', 'label' => 'Bidang 1: Tata Gereja', 'desc' => 'Mengelola Pejabat Gerejawi dan Risalah Sidang.'],
                    'bidang2_keuangan' => ['icon' => 'fa-wallet', 'color' => 'text-emerald-500 bg-emerald-50', 'label' => 'Bidang 2: Pembangunan & Keuangan', 'desc' => 'Mengelola Buku Kas Umum, Rencana RAPB, dan Inventaris Aset.'],
                    'bidang3_hris'     => ['icon' => 'fa-id-card', 'color' => 'text-pink-500 bg-pink-50', 'label' => 'Bidang 3: Kepegawaian (HRIS)', 'desc' => 'Mengelola Direktori Personel, Data Pendeta, dan Riwayat Mutasi.'],
                    'bidang4_popup'    => ['icon' => 'fa-bullhorn', 'color' => 'text-amber-500 bg-amber-50', 'label' => 'Bidang 4: Kelola Popup Info', 'desc' => 'Mengatur Pengumuman Popup Ads/Iklan di halaman depan website.'],
                    'bidang4_berita'   => ['icon' => 'fa-newspaper', 'color' => 'text-indigo-500 bg-indigo-50', 'label' => 'Bidang 4: Berita & Post', 'desc' => 'Mengelola Berita, Kegiatan Publik, dan Pesan Masuk.'],
                    'bidang4_eoffice'  => ['icon' => 'fa-envelope-open-text', 'color' => 'text-orange-500 bg-orange-50', 'label' => 'Bidang 4: E-Office Persuratan', 'desc' => 'Mengelola Registrasi Agenda Surat Masuk dan Surat Keluar.'],
                    'wilayah_master'   => ['icon' => 'fa-database', 'color' => 'text-blue-500 bg-blue-50', 'label' => 'Struktur: Data Master Wilayah', 'desc' => 'Mengakses Data Pokok Klasis, Jemaat, dan Data Anggota Jemaat.'],
                    'wilayah_wadah'    => ['icon' => 'fa-users', 'color' => 'text-purple-500 bg-purple-50', 'label' => 'Struktur: Wadah Kategorial', 'desc' => 'Mengelola Susunan Pengurus Wadah Kategorial.'],
                    'laporan_terpadu'  => ['icon' => 'fa-file-alt', 'color' => 'text-teal-500 bg-teal-50', 'label' => 'Pusat Pelaporan & Analisis Terpadu', 'desc' => 'Mengakses Dokumen Analisis Renstra, Statistik Wadah, dan Konsolidasi Kas.'],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($modules as $key => $mod)
                    @php
                        // PENCEGAHAN ERROR 500: Pastikan nilainya berupa array meskipun kosong
                        $accessMap = is_array($setting->module_access) ? $setting->module_access : [];
                        $savedRoles = $accessMap[$key] ?? $defaultAccess[$key] ?? [];
                    @endphp
                    <div class="p-4 border border-slate-200 rounded-xl bg-white shadow-sm flex flex-col justify-between gap-3 group hover:border-slate-300 transition-all">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ $mod['color'] }}">
                                <i class="fas {{ $mod['icon'] }} text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-tight">{{ $mod['label'] }}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5 leading-relaxed">{{ $mod['desc'] }}</p>
                            </div>
                        </div>

                        {{-- CUSTOM DROPDOWN CHECKLIST --}}
                        <div class="relative dropdown-container w-full">
                            {{-- PENTING: Ganti onClick function agar tidak bentrok dengan toggleMenu di app.blade.php --}}
                            <button type="button" onclick="toggleSettingsDropdown(this)" class="w-full flex items-center justify-between px-3 py-1.5 border border-slate-300 rounded bg-slate-50 hover:bg-slate-100 text-left text-xs transition z-20 relative">
                                <span class="text-slate-600 font-medium truncate select-none placeholder-text">
                                    @if(count($savedRoles) > 0)
                                        Diberikan ke: {{ implode(', ', $savedRoles) }}
                                    @else
                                        <span class="text-red-500 italic font-bold">Hanya Super Admin</span>
                                    @endif
                                </span>
                                <i class="fas fa-chevron-down text-[10px] text-slate-400 pointer-events-none transition-transform duration-200"></i>
                            </button>

                            {{-- Dropdown Panel Menu --}}
                            <div class="settings-dropdown-panel absolute left-0 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-xl hidden flex-col z-30 max-h-48 overflow-y-auto divide-y divide-slate-50 py-1 transition-all">
                                @foreach($roles as $role)
                                    <label class="flex items-center px-4 py-2 hover:bg-slate-50 cursor-pointer transition select-none text-xs font-bold text-slate-700">
                                        <input type="checkbox" name="module_access[{{ $key }}][]" value="{{ $role->name }}" 
                                               class="w-3.5 h-3.5 text-brand-600 border-gray-300 rounded focus:ring-brand-500 mr-3 checkbox-item"
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
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mt-8">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">
                <i class="fas fa-globe mr-2"></i> Identitas & Brand
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Website Utama</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $setting->site_name ?? '') }}" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tagline / Slogan</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $setting->site_tagline ?? '') }}" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
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
                    <textarea name="hero_text" rows="2" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">{{ old('hero_text', $setting->hero_text ?? '') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tentang Kami (Ringkasan)</label>
                        <textarea name="about_us" rows="4" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">{{ old('about_us', $setting->about_us ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Visi & Misi (Ringkasan)</label>
                        <textarea name="vision" rows="4" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">{{ old('vision', $setting->vision ?? '') }}</textarea>
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
                    <textarea name="contact_address" rows="2" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">{{ old('contact_address', $setting->contact_address ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nomor Telepon</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $setting->contact_phone ?? '') }}" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email Resmi</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $setting->contact_email ?? '') }}" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Website URL</label>
                    <input type="url" name="contact_website" value="{{ old('contact_website', $setting->contact_website ?? '') }}" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jam Operasional</label>
                    <input type="text" name="work_hours" value="{{ old('work_hours', $setting->work_hours ?? '') }}" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
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
                        <input type="url" name="social_facebook" value="{{ old('social_facebook', $setting->social_facebook ?? '') }}" placeholder="https://facebook.com/..." class="w-full pl-10 border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">YouTube URL</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fab fa-youtube"></i></span>
                        <input type="url" name="social_youtube" value="{{ old('social_youtube', $setting->social_youtube ?? '') }}" placeholder="https://youtube.com/..." class="w-full pl-10 border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Instagram URL</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fab fa-instagram"></i></span>
                        <input type="url" name="social_instagram" value="{{ old('social_instagram', $setting->social_instagram ?? '') }}" placeholder="https://instagram.com/..." class="w-full pl-10 border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Twitter / X URL</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fab fa-twitter"></i></span>
                        <input type="url" name="social_twitter" value="{{ old('social_twitter', $setting->social_twitter ?? '') }}" placeholder="https://x.com/..." class="w-full pl-10 border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
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
                <textarea name="footer_description" rows="3" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">{{ old('footer_description', $setting->footer_description ?? '') }}</textarea>
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
                container.querySelector('.fa-chevron-down').classList.remove('rotate-180');
            }
        });

        // Toggle dropdown yang diklik
        if (currentMenu.classList.contains('hidden')) {
            currentMenu.classList.remove('hidden');
            currentMenu.classList.add('flex');
            currentIcon.classList.add('rotate-180');
        } else {
            currentMenu.classList.add('hidden');
            currentMenu.classList.remove('flex');
            currentIcon.classList.remove('rotate-180');
        }
    }

    // Mengubah teks placeholder tombol saat item dicentang/dilepas
    function updateDropdownPlaceholder(checkbox) {
        const container = checkbox.closest('.dropdown-container');
        const placeholder = container.querySelector('.placeholder-text');
        const checkedBoxes = container.querySelectorAll('.checkbox-item:checked');
        
        if (checkedBoxes.length > 0) {
            const names = Array.from(checkedBoxes).map(cb => cb.value);
            placeholder.innerHTML = 'Diberikan ke: ' + names.join(', ');
            placeholder.classList.remove('text-red-500', 'italic');
        } else {
            placeholder.innerHTML = '<span class="text-red-500 italic font-bold">Hanya Super Admin</span>';
        }
    }

    // Menutup dropdown jika admin mengklik di luar area menu dropdown
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-container')) {
            document.querySelectorAll('.dropdown-container').forEach(container => {
                container.querySelector('.settings-dropdown-panel').classList.add('hidden');
                container.querySelector('.settings-dropdown-panel').classList.remove('flex');
                const icon = container.querySelector('.fa-chevron-down');
                if(icon) icon.classList.remove('rotate-180');
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