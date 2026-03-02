<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    {{-- ... (Kode <head> tetap sama) ... --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->site_name ?? 'Sinode GPI Papua' }} - {{ $setting->site_tagline ?? 'Gereja Protestan di Indonesia' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script> tailwind.config = { theme: { extend: { colors: { primary: '#1e40af', secondary: '#f59e0b', accent: '#10b981', 'blue-600': '#2563eb', 'green-600': '#059669', 'orange-600': '#ea580c', 'purple-600': '#9333ea', 'red-600': '#dc2626', 'indigo-600': '#4f46e5', 'blue-100': '#dbeafe', 'green-100': '#d1fae5', 'orange-100': '#ffedd5', 'purple-100': '#f3e8ff', 'red-100': '#fee2e2', 'indigo-100': '#e0e7ff', 'blue-50': '#eff6ff', 'green-50': '#f0fdf4', 'orange-50': '#fff7ed', 'purple-50': '#faf5ff', 'red-50': '#fef2f2', 'indigo-50': '#eef2ff' } } } } </script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/heroicons@2.1.3/24/outline/index.js"></script>
    <style>
        html { scroll-behavior: smooth; } body { box-sizing: border-box; }
        .hero-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; } .card-hover:hover { transform: translateY(-8px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .animate-fade-in { opacity: 0; transform: translateY(30px); animation: fadeIn 0.8s ease-in forwards; }
        .animate-slide-up { opacity: 0; transform: translateY(50px); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
        .text-shadow { text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .nav-item { transition: all 0.2s ease; } .nav-item:hover { color: #f59e0b; transform: translateY(-2px); }
        .floating { animation: floating 3s ease-in-out infinite; }
        @keyframes floating { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        .nav-logo { height: 3rem; width: 3rem; object-fit: contain; } .hero-logo { height: 6rem; width: 6rem; object-fit: contain; }
        .pagination nav > div:first-child { display: none; } .pagination nav span[aria-current="page"] span { background-color: theme('colors.primary'); color: white; border-color: theme('colors.primary'); } .pagination nav a:hover { background-color: theme('colors.blue.100'); } .pagination nav span[aria-disabled="true"] span { background-color: theme('colors.gray.200'); color: theme('colors.gray.400'); cursor: not-allowed; } .pagination nav a, .pagination nav span span, .pagination nav span[aria-disabled="true"] span { display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; margin: 0 0.125rem; border: 1px solid theme('colors.gray.300'); border-radius: 0.375rem; text-decoration: none; font-size: 0.875rem; color: theme('colors.gray.700'); }
        .prose h1 { @apply text-3xl font-bold mb-4 text-gray-900; } .prose h2 { @apply text-2xl font-semibold mt-6 mb-3 text-gray-800; } .prose h3 { @apply text-xl font-semibold mt-5 mb-2 text-gray-800; } .prose p { @apply mb-4 leading-relaxed text-gray-700; } .prose ul { @apply list-disc list-inside mb-4 pl-4 text-gray-700; } .prose ol { @apply list-decimal list-inside mb-4 pl-4 text-gray-700; } .prose a { @apply text-primary hover:underline; } .prose blockquote { @apply border-l-4 border-gray-300 pl-4 italic text-gray-600 my-6; } .prose img { @apply rounded-lg shadow-md my-6 max-w-full h-auto; }
        .flash-message-public { animation: fadeOutPublic 5s forwards; }
        @keyframes fadeOutPublic { 0% { opacity: 1; } 90% { opacity: 1; } 100% { opacity: 0; display: none; } }
    </style>
</head>
<body class="h-full bg-white font-sans antialiased">

    <nav class="fixed w-full z-50 bg-white/95 backdrop-blur-sm shadow-lg">
        {{-- ... (Kode Navigasi Lengkap seperti sebelumnya) ... --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> <div class="flex justify-between items-center py-4"> <div class="flex items-center space-x-3 cursor-pointer" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });"> @if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path))<img src="{{ Storage::url($setting->logo_path) }}" alt="Logo {{ $setting->site_name ?? 'GPI Papua' }}" class="nav-logo rounded-lg shadow-md">@else<div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center shadow-md"><svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L3 7v13h18V7L12 2zm0 2.236L18.99 8H5.01L12 4.236zM5 18V9.618l7 4.118 7-4.118V18H5z"/></svg></div>@endif <div> <h1 class="text-xl font-bold text-gray-900">{{ $setting->site_name ?? 'Sinode GPI Papua' }}</h1> <p class="text-sm text-gray-600">{{ $setting->site_tagline ?? 'Gereja Protestan di Indonesia' }}</p> </div> </div> <div class="hidden md:flex items-center space-x-8"> <a href="#beranda" class="nav-item text-gray-700 font-medium">Beranda</a> <a href="#tentang" class="nav-item text-gray-700 font-medium">Tentang</a> <a href="#pelayanan" class="nav-item text-gray-700 font-medium">Pelayanan</a> <a href="#kegiatan" class="nav-item text-gray-700 font-medium">Kegiatan</a> <a href="#kontak" class="nav-item text-gray-700 font-medium">Kontak</a> <a href="{{ route('admin.dashboard') }}" class="bg-primary hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow hover:shadow-md"> Login Admin </a> </div> <button class="md:hidden p-2 text-gray-600 hover:text-primary" id="mobile-menu-btn" aria-label="Toggle Menu"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button> </div> </div> <div class="md:hidden hidden bg-white border-t" id="mobile-menu"> <div class="px-4 py-2 space-y-2"> <a href="#beranda" class="block py-2 text-gray-700 hover:text-primary font-medium">Beranda</a> <a href="#tentang" class="block py-2 text-gray-700 hover:text-primary font-medium">Tentang</a> <a href="#pelayanan" class="block py-2 text-gray-700 hover:text-primary font-medium">Pelayanan</a> <a href="#kegiatan" class="block py-2 text-gray-700 hover:text-primary font-medium">Kegiatan</a> <a href="#kontak" class="block py-2 text-gray-700 hover:text-primary font-medium">Kontak</a> <a href="{{ route('admin.dashboard') }}" class="block w-full text-center bg-primary text-white py-2 rounded-lg mt-2 font-medium hover:bg-blue-700">Login Admin</a> </div> </div>
    </nav>

    <section id="beranda" class="hero-gradient min-h-screen flex items-center justify-center text-white relative overflow-hidden pt-20">
        {{-- ... (Kode Hero Section Lengkap seperti sebelumnya) ... --}}
        <div class="absolute inset-0 bg-black/30"></div> <div class="relative z-10 text-center px-4 max-w-4xl mx-auto animate-fade-in"> <div class="floating mb-8"> @if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) <img src="{{ Storage::url($setting->logo_path) }}" alt="Logo {{ $setting->site_name ?? 'GPI Papua' }}" class="hero-logo mx-auto opacity-90 shadow-lg rounded-full border-4 border-white/50"> @else <div class="w-24 h-24 mx-auto opacity-90 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center shadow-lg border-4 border-white/50"><svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L3 7v13h18V7L12 2zm0 2.236L18.99 8H5.01L12 4.236zM5 18V9.618l7 4.118 7-4.118V18H5z"/></svg></div> @endif </div> <h1 class="text-5xl md:text-7xl font-bold mb-6 text-shadow"> {{ $setting->site_name ?? 'Sinode GPI Papua' }} </h1> <p class="text-xl md:text-2xl mb-8 text-white/90 max-w-2xl mx-auto"> {{ $setting->hero_text ?? 'Melayani dengan kasih, membangun iman, dan mempersatukan umat Kristiani di tanah Papua.' }} </p> <div class="flex flex-col sm:flex-row gap-4 justify-center"> <a href="#tentang" class="bg-white text-primary px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors shadow-md hover:shadow-lg"> Pelajari Lebih Lanjut </a> <a href="#kontak" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-primary transition-colors shadow-md hover:shadow-lg"> Hubungi Kami </a> </div> </div> <div class="absolute top-20 left-10 w-20 h-20 bg-white/10 rounded-full floating opacity-50"></div> <div class="absolute bottom-20 right-10 w-16 h-16 bg-white/10 rounded-full floating opacity-50" style="animation-delay: 1s;"></div> <div class="absolute top-1/2 left-20 w-12 h-12 bg-white/10 rounded-full floating opacity-50" style="animation-delay: 2s;"></div>
    </section>

    <section id="tentang" class="py-20 bg-gray-50">
        {{-- ... (Kode About Section Lengkap seperti sebelumnya) ... --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> <div class="text-center mb-16 animate-slide-up"> <span class="text-primary font-semibold uppercase tracking-wider text-sm">Tentang Kami</span> <h2 class="text-4xl font-bold text-gray-900 mt-2 mb-4">Mengenal {{ $setting->site_name ?? 'Sinode GPI Papua' }}</h2> <p class="text-xl text-gray-600 max-w-3xl mx-auto"> Sinode Gereja Protestan Indonesia (GPI) di Papua adalah wadah persatuan dan pelayanan gereja-gereja Protestan di Tanah Papua, berkomitmen pada pertumbuhan iman dan kesejahteraan umat. </p> </div> <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center"> <div class="animate-slide-up"> <h3 class="text-2xl font-bold text-gray-900 mb-6">Sejarah Singkat & Visi</h3> <p class="text-gray-600 mb-6 leading-relaxed"> {{ $setting->about_us ?? 'GPI di Papua secara resmi melembaga pada **25 Mei 1985**, lahir dari hasil penginjilan dan pelayanan berbagai badan zending serta Gereja Protestan Maluku (GPM). Sebagai perwujudan gereja Kristus yang Esa, Kudus, Am, dan Rasuli, kami terpanggil untuk menjadi berkat di tengah masyarakat, bangsa, dan negara Indonesia yang berazaskan Pancasila.' }} </p> <p class="text-gray-600 mb-6 leading-relaxed"> {{ $setting->vision ?? 'Visi kami adalah membangun jemaat yang berakar kuat dalam iman kepada Yesus Kristus, mampu bersaksi, melayani, dan membawa dampak positif bagi sesama serta lingkungan hidup, khususnya di Tanah Papua.' }} </p> <div class="flex items-center space-x-4 p-4 bg-blue-50 border-l-4 border-primary rounded"> <svg class="w-6 h-6 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> <span class="text-gray-700 font-medium">Melayani dengan integritas dan kasih Kristus.</span> </div> </div> <div class="animate-slide-up flex justify-center items-center"> @if ($setting->about_image_path && Storage::disk('public')->exists($setting->about_image_path)) <img src="{{ Storage::url($setting->about_image_path) }}" alt="Ilustrasi {{ $setting->site_name ?? 'GPI Papua' }}" class="rounded-lg shadow-lg max-h-80 w-auto"> @else <img src="https://via.placeholder.com/500x350/EBF4FF/1E40AF?text=Ilustrasi+GPI+Papua" alt="Ilustrasi GPI Papua Placeholder" class="rounded-lg shadow-lg"> @endif </div> </div> </div>
    </section>

    <section id="pelayanan" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-slide-up"> <span class="text-primary font-semibold uppercase tracking-wider text-sm">Amanat Pelayanan</span> <h2 class="text-4xl font-bold text-gray-900 mt-2 mb-4">Pelayanan Holistik Kami</h2> <p class="text-xl text-gray-600 max-w-3xl mx-auto"> Meneladani pola pelayanan Yesus Kristus, kami hadir sebagai gembala bagi umat dan masyarakat. </p> </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($services as $service)
                    @php
                        $theme = $service->color_theme ?? 'blue';
                        $gradientClass = match($theme) { 'green' => 'bg-gradient-to-br from-green-50 to-green-100', 'orange' => 'bg-gradient-to-br from-orange-50 to-orange-100', 'purple' => 'bg-gradient-to-br from-purple-50 to-purple-100', 'red' => 'bg-gradient-to-br from-red-50 to-red-100', 'indigo' => 'bg-gradient-to-br from-indigo-50 to-indigo-100', default => 'bg-gradient-to-br from-blue-50 to-blue-100', };
                        $iconBgClass = match($theme) { 'green' => 'bg-green-600', 'orange' => 'bg-orange-600', 'purple' => 'bg-purple-600', 'red' => 'bg-red-600', 'indigo' => 'bg-indigo-600', default => 'bg-blue-600', };
                        $borderColorClass = match($theme) { 'green' => 'border-green-100', 'orange' => 'border-orange-100', 'purple' => 'border-purple-100', 'red' => 'border-red-100', 'indigo' => 'border-indigo-100', default => 'border-blue-100', };
                    @endphp
                    <div class="{{ $gradientClass }} p-8 rounded-xl card-hover animate-slide-up border {{ $borderColorClass }}">
                        <div class="w-16 h-16 {{ $iconBgClass }} rounded-lg flex items-center justify-center mb-6 shadow-lg">
                            @switch($service->icon)
                                @case('book') <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg> @break
                                @case('heart') <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg> @break
                                @case('users') <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> @break
                                @case('hands-helping') <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg> @break
                                @case('calendar') <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> @break
                                @default <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            @endswitch
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $service->title }}</h3>
                        @if($service->description) <p class="text-gray-600 mb-4 text-sm leading-relaxed"> {{ $service->description }} </p> @endif
                        @if(count($service->list_items_array) > 0)
                        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                            @foreach($service->list_items_array as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                @empty
                    <div class="md:col-span-2 lg:col-span-3 text-center text-gray-500 py-10 animate-slide-up"> <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg> <p>Data pelayanan belum diatur oleh Admin.</p> </div>
                @endforelse
            </div>
        </div>
    </section>

     <section id="kegiatan" class="py-20 bg-gray-50">
       {{-- ... (Kode Events Section Lengkap seperti sebelumnya) ... --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> <div class="text-center mb-16 animate-slide-up"> <span class="text-primary font-semibold uppercase tracking-wider text-sm">Agenda Gereja</span> <h2 class="text-4xl font-bold text-gray-900 mt-2 mb-4">Kegiatan & Berita Terbaru</h2> <p class="text-xl text-gray-600 max-w-3xl mx-auto"> Ikuti perkembangan pelayanan dan agenda kegiatan Sinode GPI Papua. </p> </div> <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"> @forelse ($posts as $post) <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover animate-slide-up border border-gray-100 flex flex-col"> @if ($post->image_path && Storage::disk('public')->exists($post->image_path)) <img src="{{ Storage::url($post->image_path) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover"> @else <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400"> <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> </div> @endif <div class="p-6 flex flex-col flex-grow"> <span class="text-sm text-primary font-semibold">{{ $post->published_at ? $post->published_at->isoFormat('D MMMM YYYY') : 'Draft' }}</span> <h3 class="text-lg font-bold text-gray-900 mt-2 mb-3 hover:text-primary transition-colors flex-grow"> <a href="{{ route('posts.public.show', $post->slug) }}"> {{ $post->title }} </a> </h3> <p class="text-gray-600 text-sm mb-4 line-clamp-3"> {{ Str::limit(strip_tags($post->content), 120) }} </p> <a href="{{ route('posts.public.show', $post->slug) }}" class="mt-auto inline-block bg-primary hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium text-center"> Selengkapnya </a> </div> </div> @empty <div class="md:col-span-2 lg:col-span-3 text-center text-gray-500 py-10 animate-slide-up"> <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3h.01M17 16h.01"></path></svg> <p>Belum ada berita atau kegiatan terbaru.</p> </div> @endforelse </div> @if($posts->count() >= 3) <div class="text-center mt-12 animate-slide-up"> <a href="{{ route('posts.public.index') }}" class="inline-block bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors border border-primary shadow hover:shadow-md"> Lihat Semua Kegiatan & Berita </a> </div> @endif </div>
    </section>

    <section id="kontak" class="py-20 bg-gradient-to-b from-white to-gray-50">
       <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-slide-up"> <span class="text-primary font-semibold uppercase tracking-wider text-sm">Terhubung</span> <h2 class="text-4xl font-bold text-gray-900 mt-2 mb-4">Hubungi Sekretariat Sinode</h2> <p class="text-xl text-gray-600 max-w-3xl mx-auto"> Kami siap melayani pertanyaan, masukan, atau kebutuhan informasi Anda. </p> </div>
           
            {{-- Tampilkan Pesan Sukses/Error Form Kontak --}}
            @if (session('success'))
                <div class="flash-message-public mb-6 max-w-4xl mx-auto bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
                    <p class="font-bold">Sukses!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
             @if ($errors->any() || session('error')) {{-- Tampilkan error validasi atau error server --}}
                <div class="flash-message-public mb-6 max-w-4xl mx-auto bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
                    <p class="font-bold">Oops! Ada kesalahan:</p>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @if (session('error'))
                             <li>{{ session('error') }}</li>
                        @endif
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
           
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="animate-slide-up space-y-8">
                    <h3 class="text-2xl font-bold text-gray-900">Informasi Kontak</h3>
                    <div class="flex items-start space-x-4"> <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 border border-blue-200"><svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></div> <div> <h4 class="text-lg font-semibold text-gray-900">Alamat Kantor Sinode</h4> <p class="text-gray-600 text-sm whitespace-pre-line">{{ $setting->contact_address ?? '[Alamat belum diatur]' }}</p> </div> </div>
                    <div class="flex items-start space-x-4"> <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 border border-green-200"><svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg></div> <div> <h4 class="text-lg font-semibold text-gray-900">Telepon & Email</h4> <p class="text-gray-600 text-sm"> Telp: {{ $setting->contact_phone ?? '[Telepon belum diatur]' }}<br> Email: {{ $setting->contact_email ?? '[Email belum diatur]' }} </p> </div> </div>
                    <div class="flex items-start space-x-4"> <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 border border-orange-200"><svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div> <div> <h4 class="text-lg font-semibold text-gray-900">Jam Kerja</h4> <p class="text-gray-600 text-sm"> {{ $setting->work_hours ?? '[Jam kerja belum diatur]' }}<br> Sabtu & Minggu: Tutup </p> </div> </div>
                    <div class="flex items-start space-x-4"> <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 border border-purple-200"><svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg></div> <div> <h4 class="text-lg font-semibold text-gray-900">Website Resmi</h4> <p class="text-gray-600 text-sm hover:text-primary transition-colors"> @if($setting->contact_website)<a href="{{ $setting->contact_website }}" target="_blank" rel="noopener noreferrer">{{ $setting->contact_website }}</a>@else[Website belum diatur]@endif </p> </div> </div>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 animate-slide-up">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Kirim Pesan Langsung</h3>
                    {{-- Form Kontak Dinamis --}}
                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-4" id="contact-form">
                        @csrf {{-- Token Keamanan Laravel --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1 sr-only">Nama Lengkap</label>
                            <input type="text" id="name" name="name" placeholder="Nama Lengkap Anda" required value="{{ old('name') }}"
                                   class="w-full px-4 py-3 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition duration-150 ease-in-out">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1 sr-only">Email</label>
                            <input type="email" id="email" name="email" placeholder="Alamat Email Anda" required value="{{ old('email') }}"
                                   class="w-full px-4 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition duration-150 ease-in-out">
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                         <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1 sr-only">Nomor Telepon</label>
                            <input type="tel" id="phone" name="phone" placeholder="Nomor Telepon (Opsional)" value="{{ old('phone') }}"
                                   class="w-full px-4 py-3 border @error('phone') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition duration-150 ease-in-out">
                             @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                         <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1 sr-only">Subjek</label>
                            <select id="subject" name="subject" required class="w-full px-4 py-3 border @error('subject') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent bg-white text-gray-500 transition duration-150 ease-in-out appearance-none">
                                <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Pilih Subjek Pesan</option>
                                <option value="Informasi Umum" {{ old('subject') == 'Informasi Umum' ? 'selected' : '' }}>Informasi Umum</option>
                                <option value="Pelayanan Gereja" {{ old('subject') == 'Pelayanan Gereja' ? 'selected' : '' }}>Pelayanan Gereja</option>
                                <option value="Kegiatan & Acara" {{ old('subject') == 'Kegiatan & Acara' ? 'selected' : '' }}>Kegiatan & Acara</option>
                                <option value="Dukungan / Donasi" {{ old('subject') == 'Dukungan / Donasi' ? 'selected' : '' }}>Dukungan / Donasi</option>
                                <option value="Lainnya" {{ old('subject') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                             @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1 sr-only">Pesan</label>
                            <textarea id="message" name="message" rows="5" placeholder="Tuliskan pesan Anda di sini..." required
                                      class="w-full px-4 py-3 border @error('message') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition duration-150 ease-in-out">{{ old('message') }}</textarea>
                             @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="w-full bg-primary hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition-colors shadow hover:shadow-md">
                            Kirim Pesan Anda
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-400 py-12">
        {{-- ... (Kode Footer Lengkap seperti sebelumnya) ... --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8"> <div class="space-y-4"> <div class="flex items-center space-x-3"> @if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path))<img src="{{ Storage::url($setting->logo_path) }}" alt="Logo {{ $setting->site_name ?? 'GPI Papua' }}" class="h-10 w-10 object-contain rounded-lg shadow-md bg-white p-1">@else<div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center shadow-md"><svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L3 7v13h18V7L12 2zm0 2.236L18.99 8H5.01L12 4.236zM5 18V9.618l7 4.118 7-4.118V18H5z"/></svg></div>@endif <div> <h3 class="text-lg font-bold text-white">{{ $setting->site_name ?? 'Sinode GPI Papua' }}</h3> <p class="text-sm">Melayani dengan Kasih</p> </div> </div> <p class="text-sm leading-relaxed"> {{ $setting->footer_description ?? 'Wadah persekutuan dan pelayanan Gereja Protestan Indonesia di Tanah Papua, membawa terang Injil dan membangun komunitas iman yang kuat.' }} </p> <div class="flex space-x-4"> @if($setting->social_facebook)<a href="{{ $setting->social_facebook }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors" aria-label="Facebook"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg></a>@endif @if($setting->social_youtube)<a href="{{ $setting->social_youtube }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors" aria-label="YouTube"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg></a>@endif @if($setting->social_instagram)<a href="{{ $setting->social_instagram }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors" aria-label="Instagram"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.069-4.85.069-3.205 0-3.584-.012-4.849-.069-3.225-.148-4.771-1.664-4.919-4.919-.058-1.265-.069-1.644-.069-4.849 0-3.204.012-3.583.069-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>@endif @if($setting->social_twitter)<a href="{{ $setting->social_twitter }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors" aria-label="Twitter/X"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>@endif </div> </div> <div> <h4 class="text-lg font-semibold text-white mb-4">Navigasi Cepat</h4> <ul class="space-y-2 text-sm"> <li><a href="{{ route('home') }}#beranda" class="hover:text-white transition-colors">Beranda</a></li> <li><a href="{{ route('home') }}#tentang" class="hover:text-white transition-colors">Tentang Kami</a></li> <li><a href="{{ route('home') }}#pelayanan" class="hover:text-white transition-colors">Pelayanan</a></li> <li><a href="{{ route('posts.public.index') }}" class="hover:text-white transition-colors">Kegiatan & Berita</a></li> <li><a href="{{ route('home') }}#kontak" class="hover:text-white transition-colors">Kontak</a></li> <li><a href="#" class="hover:text-white transition-colors">Peta Situs</a></li> </ul> </div> <div> <h4 class="text-lg font-semibold text-white mb-4">Tautan Terkait</h4> <ul class="space-y-2 text-sm"> <li><a href="https://pgi.or.id/" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">PGI</a></li> <li><a href="#" class="hover:text-white transition-colors">GPI</a></li> <li><a href="#" class="hover:text-white transition-colors">Sekolah Teologi</a></li> <li><a href="#" class="hover:text-white transition-colors">Dokumen</a></li> </ul> </div> <div> <h4 class="text-lg font-semibold text-white mb-4">Sekretariat</h4> <div class="space-y-2 text-sm"> <p>{{ Str::limit($setting->contact_address ?? '[Alamat belum diatur]', 50, '') }}</p> <p>Telp: {{ explode(',', $setting->contact_phone ?? '')[0] ?? '[Telepon belum diatur]' }}</p> <p>Email: {{ explode(',', $setting->contact_email ?? '')[0] ?? '[Email belum diatur]' }}</p> </div> </div> </div> <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm"> <p> &copy; {{ date('Y') }} {{ $setting->site_name ?? 'Sinode GPI Papua' }}. Semua hak dilindungi undang-undang. </p> <p class="mt-1">KOMINFO GPI PAPUA.</p> </div> </div>
    </footer>

    {{-- Script JavaScript (Sama seperti sebelumnya) --}}
    <script>
        // --- Mobile menu toggle ---
        const mobileMenuBtn = document.getElementById('mobile-menu-btn'); const mobileMenu = document.getElementById('mobile-menu'); const navLinks = mobileMenu.querySelectorAll('a');
        mobileMenuBtn.addEventListener('click', () => { const isHidden = mobileMenu.classList.toggle('hidden'); mobileMenuBtn.innerHTML = isHidden ? `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>` : `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`; });
        navLinks.forEach(link => { link.addEventListener('click', () => { mobileMenu.classList.add('hidden'); mobileMenuBtn.innerHTML = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>`; }); });

        // --- Contact form submission (Contoh, belum fungsional) ---
        // Kita biarkan form submit secara normal ke backend sekarang
        /*
        document.getElementById('contact-form')?.addEventListener('submit', function(e) {
             // Hapus preventDefault agar form bisa dikirim ke backend
             // e.preventDefault(); 
             // showNotification('Pesan berhasil dikirim! Kami akan segera menghubungi Anda.', 'success');
             // this.reset(); 
        });
        */

        // --- Event/Post links ---
         document.querySelectorAll('#kegiatan a[href="#"]').forEach(element => {
             element.addEventListener('click', function(e) {
                e.preventDefault();
                showNotification('Fitur detail belum tersedia.', 'info');
             });
         });

        // --- Animate elements on scroll ---
        const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
        const slideUpObserver = new IntersectionObserver((entries, observer) => { entries.forEach(entry => { if (entry.isIntersecting) { entry.target.style.opacity = '1'; entry.target.style.transform = 'translateY(0)'; entry.target.style.animation = 'slideUp 0.6s ease-out forwards'; observer.unobserve(entry.target); } }); }, observerOptions);
        document.querySelectorAll('.animate-slide-up').forEach(el => { el.style.opacity = '0'; el.style.transform = 'translateY(50px)'; el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out'; slideUpObserver.observe(el); });

        // --- Helper function for notifications ---
        function showNotification(message, type = 'info', duration = 4000) { const notification = document.createElement('div'); let bgColor, textColor, iconSvg; switch (type) { case 'success': bgColor = 'bg-green-500'; textColor = 'text-white'; iconSvg = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`; break; case 'error': bgColor = 'bg-red-500'; textColor = 'text-white'; iconSvg = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v4a1 1 0 102 0V7zm-1 7a1 1 0 110 2 1 1 0 010-2z" clip-rule="evenodd"/></svg>`; break; case 'warning': bgColor = 'bg-yellow-500'; textColor = 'text-white'; iconSvg = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 3.001-1.742 3.001H4.42c-1.53 0-2.493-1.667-1.743-3.001l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>`; break; default: bgColor = 'bg-blue-500'; textColor = 'text-white'; iconSvg = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>`; } notification.className = `fixed top-5 right-5 ${bgColor} ${textColor} px-6 py-4 rounded-lg shadow-lg z-[100] animate-fade-in text-sm`; notification.innerHTML = `<div class="flex items-center space-x-3">${iconSvg}<span>${message}</span></div>`; document.body.appendChild(notification); setTimeout(() => { notification.style.transition = 'opacity 0.5s ease'; notification.style.opacity = '0'; setTimeout(() => notification.remove(), 500); }, duration); }

         // Tampilkan notifikasi flash dari server (setelah redirect)
        document.addEventListener('DOMContentLoaded', () => {
            @if (session('success'))
                showNotification("{{ session('success') }}", 'success');
            @endif
            @if (session('error') || $errors->any())
                 // Ambil error pertama jika ada
                @php
                    $errorMessage = session('error') ?? $errors->first();
                @endphp
                showNotification("{{ $errorMessage }}", 'error');
            @endif
        });
    </script>
</body>
</html>