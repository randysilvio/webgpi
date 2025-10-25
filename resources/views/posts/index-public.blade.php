<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Kegiatan - {{ $setting->site_name ?? 'Sinode GPI Papua' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#1e40af', secondary: '#f59e0b', accent: '#10b981' } } } }
    </script>
    <style>
         html { scroll-behavior: smooth; } body { box-sizing: border-box; }
        .nav-logo { height: 3rem; width: 3rem; object-fit: contain; }
        .card-hover { transition: all 0.3s ease; } .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -4px rgba(0,0,0,.1); }
        /* Simple Pagination Style */
        .pagination nav > div:first-child { display: none; } /* Hide 'Showing results' */
        .pagination nav span[aria-current="page"] span { background-color: theme('colors.primary'); color: white; border-color: theme('colors.primary'); }
        .pagination nav a:hover { background-color: theme('colors.blue.100'); }
        .pagination nav span[aria-disabled="true"] span { background-color: theme('colors.gray.200'); color: theme('colors.gray.400'); cursor: not-allowed; }
        .pagination nav a, .pagination nav span span, .pagination nav span[aria-disabled="true"] span { display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; margin: 0 0.125rem; border: 1px solid theme('colors.gray.300'); border-radius: 0.375rem; text-decoration: none; font-size: 0.875rem; color: theme('colors.gray.700'); }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <nav class="sticky top-0 w-full z-50 bg-white/95 backdrop-blur-sm shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <a href="{{ route('home') }}" class="flex items-center space-x-3"> {{-- Link ke home --}}
                    @if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path))
                        <img src="{{ Storage::url($setting->logo_path) }}" alt="Logo {{ $setting->site_name ?? 'GPI Papua' }}" class="nav-logo rounded-lg">
                    @else
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center shadow-md"><svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L3 7v13h18V7L12 2zm0 2.236L18.99 8H5.01L12 4.236zM5 18V9.618l7 4.118 7-4.118V18H5z"/></svg></div>
                    @endif
                    <div> <h1 class="text-xl font-bold text-gray-900">{{ $setting->site_name ?? 'Sinode GPI Papua' }}</h1> <p class="text-sm text-gray-600">{{ $setting->site_tagline ?? 'Gereja Protestan di Indonesia' }}</p> </div>
                </a>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}#beranda" class="nav-item text-gray-700 font-medium">Beranda</a>
                    <a href="{{ route('home') }}#tentang" class="nav-item text-gray-700 font-medium">Tentang</a>
                    <a href="{{ route('home') }}#pelayanan" class="nav-item text-gray-700 font-medium">Pelayanan</a>
                    <a href="{{ route('posts.public.index') }}" class="nav-item text-primary font-semibold">Kegiatan</a> {{-- Aktifkan link ini --}}
                    <a href="{{ route('home') }}#kontak" class="nav-item text-gray-700 font-medium">Kontak</a>
                    <a href="{{ route('admin.dashboard') }}" class="bg-primary hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow hover:shadow-md"> Login Admin </a>
                </div>
                <button class="md:hidden p-2 text-gray-600 hover:text-primary" id="mobile-menu-btn" aria-label="Toggle Menu"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
            </div>
        </div>
        <div class="md:hidden hidden bg-white border-t" id="mobile-menu">
            <div class="px-4 py-2 space-y-2">
                <a href="{{ route('home') }}#beranda" class="block py-2 text-gray-700 hover:text-primary font-medium">Beranda</a>
                <a href="{{ route('home') }}#tentang" class="block py-2 text-gray-700 hover:text-primary font-medium">Tentang</a>
                <a href="{{ route('home') }}#pelayanan" class="block py-2 text-gray-700 hover:text-primary font-medium">Pelayanan</a>
                <a href="{{ route('posts.public.index') }}" class="block py-2 text-primary font-semibold">Kegiatan</a> {{-- Aktifkan link ini --}}
                <a href="{{ route('home') }}#kontak" class="block py-2 text-gray-700 hover:text-primary font-medium">Kontak</a>
                <a href="{{ route('admin.dashboard') }}" class="block w-full text-center bg-primary text-white py-2 rounded-lg mt-2 font-medium hover:bg-blue-700">Login Admin</a>
            </div>
        </div>
    </nav>

    <main class="pt-24 pb-16"> {{-- Beri padding atas bawah --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Berita & Kegiatan</h1>
                <p class="text-lg text-gray-600">Informasi terbaru dari Sinode GPI Papua</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Loop Berita/Kegiatan --}}
                @forelse ($posts as $post)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover border border-gray-100 flex flex-col">
                        @if ($post->image_path && Storage::disk('public')->exists($post->image_path))
                            <img src="{{ Storage::url($post->image_path) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        <div class="p-6 flex flex-col flex-grow">
                            <span class="text-sm text-primary font-semibold">{{ $post->published_at ? $post->published_at->isoFormat('D MMMM YYYY') : 'Draft' }}</span>
                            <h3 class="text-lg font-bold text-gray-900 mt-2 mb-3 hover:text-primary transition-colors flex-grow">
                                <a href="{{ route('posts.public.show', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ Str::limit(strip_tags($post->content), 120) }}
                            </p>
                            <a href="{{ route('posts.public.show', $post->slug) }}" class="mt-auto inline-block bg-primary hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium text-center">
                                Baca Selengkapnya
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 lg:col-span-3 text-center text-gray-500 py-16">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3h.01M17 16h.01"></path></svg>
                        <p class="text-lg">Belum ada berita atau kegiatan yang dipublikasikan.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12 pagination">
                {{ $posts->links() }}
            </div>

        </div>
    </main>

    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <div class="space-y-4"> <div class="flex items-center space-x-3"> @if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path))<img src="{{ Storage::url($setting->logo_path) }}" alt="Logo {{ $setting->site_name ?? 'GPI Papua' }}" class="h-10 w-10 object-contain rounded-lg shadow-md bg-white p-1">@else<div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center shadow-md"><svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L3 7v13h18V7L12 2zm0 2.236L18.99 8H5.01L12 4.236zM5 18V9.618l7 4.118 7-4.118V18H5z"/></svg></div>@endif <div> <h3 class="text-lg font-bold text-white">{{ $setting->site_name ?? 'Sinode GPI Papua' }}</h3> <p class="text-sm">Melayani dengan Kasih</p> </div> </div> <p class="text-sm leading-relaxed"> {{ $setting->footer_description ?? 'Wadah persekutuan dan pelayanan Gereja Protestan Indonesia di Tanah Papua, membawa terang Injil dan membangun komunitas iman yang kuat.' }} </p> <div class="flex space-x-4"> @if($setting->social_facebook)<a href="{{ $setting->social_facebook }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors" aria-label="Facebook"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg></a>@endif @if($setting->social_youtube)<a href="{{ $setting->social_youtube }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors" aria-label="YouTube"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg></a>@endif @if($setting->social_instagram)<a href="{{ $setting->social_instagram }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors" aria-label="Instagram"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.069-4.85.069-3.205 0-3.584-.012-4.849-.069-3.225-.148-4.771-1.664-4.919-4.919-.058-1.265-.069-1.644-.069-4.849 0-3.204.012-3.583.069-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>@endif @if($setting->social_twitter)<a href="{{ $setting->social_twitter }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors" aria-label="Twitter/X"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>@endif </div> </div>
                <div> <h4 class="text-lg font-semibold text-white mb-4">Navigasi Cepat</h4> <ul class="space-y-2 text-sm"> <li><a href="{{ route('home') }}#beranda" class="hover:text-white transition-colors">Beranda</a></li> <li><a href="{{ route('home') }}#tentang" class="hover:text-white transition-colors">Tentang Kami</a></li> <li><a href="{{ route('home') }}#pelayanan" class="hover:text-white transition-colors">Pelayanan</a></li> <li><a href="{{ route('posts.public.index') }}" class="hover:text-white transition-colors">Kegiatan & Berita</a></li> <li><a href="{{ route('home') }}#kontak" class="hover:text-white transition-colors">Kontak</a></li> <li><a href="#" class="hover:text-white transition-colors">Peta Situs</a></li> </ul> </div>
                <div> <h4 class="text-lg font-semibold text-white mb-4">Tautan Terkait</h4> <ul class="space-y-2 text-sm"> <li><a href="https://pgi.or.id/" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">PGI</a></li> <li><a href="#" class="hover:text-white transition-colors">GPI</a></li> <li><a href="#" class="hover:text-white transition-colors">Sekolah Teologi</a></li> <li><a href="#" class="hover:text-white transition-colors">Dokumen</a></li> </ul> </div>
                <div> <h4 class="text-lg font-semibold text-white mb-4">Sekretariat</h4> <div class="space-y-2 text-sm"> <p>{{ Str::limit($setting->contact_address ?? '[Alamat belum diatur]', 50, '') }}</p> <p>Telp: {{ explode(',', $setting->contact_phone ?? '')[0] ?? '[Telepon belum diatur]' }}</p> <p>Email: {{ explode(',', $setting->contact_email ?? '')[0] ?? '[Email belum diatur]' }}</p> </div> </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm"> <p> &copy; {{ date('Y') }} {{ $setting->site_name ?? 'Sinode GPI Papua' }}. Semua hak dilindungi undang-undang. </p> <p class="mt-1">Dibuat dengan ❤️ untuk melayani umat.</p> </div>
        </div>
    </footer>

    <script>
        // Skrip mobile menu
        const mobileMenuBtn = document.getElementById('mobile-menu-btn'); const mobileMenu = document.getElementById('mobile-menu'); const navLinks = mobileMenu.querySelectorAll('a');
        mobileMenuBtn.addEventListener('click', () => { const isHidden = mobileMenu.classList.toggle('hidden'); mobileMenuBtn.innerHTML = isHidden ? `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>` : `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`; });
        navLinks.forEach(link => { link.addEventListener('click', () => { mobileMenu.classList.add('hidden'); mobileMenuBtn.innerHTML = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>`; }); });
    </script>
</body>
</html>