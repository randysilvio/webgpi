<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Sinode GPI Papua</title>
    {{-- CDN Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#1e40af', secondary: '#f59e0b', accent: '#10b981' } } } }
    </script>
     <script type="module" src="https://cdn.jsdelivr.net/npm/heroicons@2.1.3/24/outline/index.js"></script>
    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px;}
        ::-webkit-scrollbar-track { background: #f1f1f1;}
        ::-webkit-scrollbar-thumb { background: #a8a8a8;}
        ::-webkit-scrollbar-thumb:hover { background: #888;}

        /* Style untuk link aktif sidebar */
        .sidebar-link.active {
            background-color: theme('colors.primary');
            color: white;
        }
        .sidebar-link.active svg {
            color: white;
        }

        /* Animasi flash message */
        .flash-message { animation: fadeOut 5s forwards; }
        @keyframes fadeOut { 0% { opacity: 1; } 90% { opacity: 1; } 100% { opacity: 0; display: none; } }

        /* Style untuk file input & preview gambar */
        input[type="file"] { cursor: pointer; }
        input[type="file"]::-webkit-file-upload-button {
          background: theme('colors.gray.200'); border: 1px solid theme('colors.gray.300');
          padding: 0.5rem 1rem; border-radius: 0.375rem; cursor: pointer;
          transition: background-color 0.2s ease; font-weight: 500;
          color: theme('colors.gray.700'); margin-right: 1rem;
        }
        input[type="file"]::-webkit-file-upload-button:hover { background-color: theme('colors.gray.300'); }
        .image-preview {
             max-height: 150px; margin-top: 0.5rem; border: 1px solid theme('colors.gray.300');
             padding: 0.25rem; border-radius: 0.375rem; display: block;
        }
        .image-preview[src="#"], .image-preview:not([src]) {
              display: none;
        }

        /* Stack untuk style tambahan per halaman (jika masih perlu) */
        @stack('styles')
    </style>
</head>
<body class="h-full flex antialiased">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-gray-800 text-gray-300 flex flex-col transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full" id="sidebar">
        {{-- Logo/Header Sidebar --}}
        <div class="flex items-center justify-center h-20 border-b border-gray-700 px-4">
             <div class="flex items-center space-x-2">
                 <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L3 7v13h18V7L12 2zm0 2.236L18.99 8H5.01L12 4.236zM5 18V9.618l7 4.118 7-4.118V18H5z"/></svg>
                 </div>
                <span class="text-white text-xl font-semibold">Admin GPI Papua</span>
            </div>
        </div>

        {{-- Navigasi Menu --}}
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            {{-- Link Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.dashboard')) active @endif">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                <span>Dashboard</span>
            </a>
            {{-- Link Pesan Masuk --}}
             @can('manage messages') {{-- Sesuaikan permission jika perlu --}}
             <a href="{{ route('admin.messages') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.messages*')) active @endif">
                 <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                 <span>Pesan Masuk</span>
             </a>
             @endcan
            {{-- Link Manajemen Layanan --}}
             @can('manage services') {{-- Sesuaikan permission jika perlu --}}
             <a href="{{ route('admin.services.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.services.*')) active @endif">
                  <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                 <span>Manajemen Layanan</span>
             </a>
             @endcan
             {{-- Link Manajemen Klasis --}}
             @can('view klasis') {{-- Sesuaikan permission jika perlu --}}
             <a href="{{ route('admin.klasis.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.klasis.*')) active @endif">
                 <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6M9 11.25h6m-6 4.5h6M6.75 21v-2.25a2.25 2.25 0 0 1 2.25-2.25h6a2.25 2.25 0 0 1 2.25 2.25V21" /> </svg>
                 <span>Manajemen Klasis</span>
             </a>
             @endcan
            {{-- Link Manajemen Jemaat --}}
             @can('view jemaat') {{-- Sesuaikan permission jika perlu --}}
             <a href="{{ route('admin.jemaat.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.jemaat.*')) active @endif">
                  <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                 <span>Manajemen Jemaat</span>
             </a>
             @endcan
            {{-- Link Manajemen Anggota Jemaat --}}
             @can('view anggota jemaat') {{-- Sesuaikan permission jika perlu --}}
              <a href="{{ route('admin.anggota-jemaat.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.anggota-jemaat.*')) active @endif">
                  <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /> </svg>
                  <span>Anggota Jemaat</span>
              </a>
              @endcan
            {{-- Link Manajemen Pendeta --}}
             @can('view pendeta') {{-- Sesuaikan permission jika perlu --}}
             <a href="{{ route('admin.pendeta.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.pendeta.*')) active @endif">
                 <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                 <span>Manajemen Pendeta</span>
             </a>
             @endcan
            {{-- Link Berita & Kegiatan --}}
             @can('manage posts') {{-- Sesuaikan permission jika perlu --}}
             <a href="{{ route('admin.posts.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.posts.*')) active @endif">
                 <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" /></svg>
                 <span>Berita & Kegiatan</span>
             </a>
             @endcan
            {{-- Link Pengaturan --}}
             @can('manage settings') {{-- Sesuaikan permission jika perlu --}}
             <a href="{{ route('admin.settings') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.settings')) active @endif">
                  <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.004.827c-.292.24-.437.613-.43.992a6.759 6.759 0 0 1 0 1.255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.6 6.6 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613-.43-.992a6.759 6.759 0 0 1 0-1.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                 <span>Pengaturan</span>
             </a>
             @endcan

             {{-- Link Manajemen User (Hanya Super Admin) --}}
             @can('manage users') {{-- Sesuaikan permission jika perlu --}}
             <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.users.*')) active @endif">
                 <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372A9.337 9.337 0 0 0 21.88 18.24c.487-.514.887-1.121 1.163-1.786V19.5a2.25 2.25 0 0 1-2.25 2.25h-13.5A2.25 2.25 0 0 1 5.25 19.5v-1.046c.276.665.676 1.272 1.163 1.786a9.337 9.337 0 0 0 4.121.952A9.38 9.38 0 0 0 15 19.128ZM12 6.75a4.125 4.125 0 1 0 0 8.25 4.125 4.125 0 0 0 0-8.25ZM12 4.5A6.375 6.375 0 1 1 5.625 10.875a6.375 6.375 0 0 1 6.375-6.375Z" /> </svg>
                 <span>Manajemen User</span>
             </a>
             @endcan

             {{-- Placeholder Menu Bidang --}}
             <div class="pt-4 mt-4 border-t border-gray-700">
                 <span class="px-4 text-xs font-semibold uppercase text-gray-500">Modul Bidang</span>
                 {{-- <a href="#" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group opacity-50 cursor-not-allowed">Bidang 1: Pelayanan</a> --}}
                 {{-- <a href="#" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group opacity-50 cursor-not-allowed">Bidang 2: Keuangan</a> --}}
             </div>

            {{-- Link Logout di bagian bawah --}}
            {{-- Formulir Logout dengan action yang benar --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-full flex items-center px-4 py-2.5 rounded-lg text-red-400 hover:bg-red-700 hover:text-white transition-colors group mt-auto mb-4">
                    <svg class="w-5 h-5 mr-3 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                    <span>Logout</span>
                </button>
            </form>
        </nav>
    </aside>

    {{-- Konten Utama --}}
    <div class="flex-1 flex flex-col md:pl-64">
        {{-- Header Atas --}}
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-4 md:px-6 flex-shrink-0">
            <button class="md:hidden text-gray-500 hover:text-gray-700" id="hamburger-btn" aria-label="Open Sidebar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
             <h1 class="text-xl font-semibold text-gray-800 hidden md:block">@yield('header-title', 'Dashboard')</h1>
            <div class="flex items-center space-x-3">
                 {{-- Tampilkan nama user yg login --}}
                 @auth
                 <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ Auth::user()->name }}</span>
                 @else
                 <span class="text-sm font-medium text-gray-700 hidden sm:block">Guest</span>
                 @endauth
                 <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                </div>
            </div>
        </header>

        {{-- Area Konten Utama --}}
        <main class="flex-1 p-6 md:p-8 overflow-y-auto bg-gray-100">
            {{-- Menampilkan Flash Message jika ada --}}
             @if (session('success'))
                 <div class="flash-message mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
                    <p>{{ session('success') }}</p>
                 </div>
             @endif
             @if (session('error'))
                 <div class="flash-message mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
                    <p>{{ session('error') }}</p>
                 </div>
             @endif
              @if ($errors->any())
                 <div class="flash-message mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
                     <p class="font-bold">Terjadi kesalahan:</p>
                     <ul class="mt-1 list-disc list-inside text-sm">
                         @foreach ($errors->all() as $error)
                             <li>{{ $error }}</li>
                         @endforeach
                     </ul>
                 </div>
             @endif

            {{-- Tempat konten spesifik halaman dimuat --}}
            @yield('content')
        </main>

        {{-- Footer Bawah --}}
        <footer class="bg-white border-t border-gray-200 p-4 text-center text-sm text-gray-500 mt-auto flex-shrink-0">
             &copy; {{ date('Y') }} Sinode GPI Papua Admin Dashboard.
        </footer>
    </div>

    {{-- Overlay untuk sidebar mobile --}}
    <div class="fixed inset-0 bg-black/30 z-30 hidden md:hidden" id="sidebar-overlay"></div>

    {{-- Skrip JavaScript --}}
    <script>
        // Skrip Sidebar Toggle Mobile
        const sidebar = document.getElementById('sidebar');
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', toggleSidebar);
        }
        if (overlay) {
            overlay.addEventListener('click', toggleSidebar);
        }

        // Skrip Preview Gambar Umum
         function previewImage(event, previewId) {
             const reader = new FileReader();
             const imageField = document.getElementById(previewId);

             if(!imageField) {
                 console.error('Preview element not found:', previewId);
                 return;
             }

             reader.onload = function(){
                 if (reader.readyState === 2) {
                     imageField.src = reader.result;
                     imageField.style.display = 'block'; // Tampilkan elemen img
                 }
             }
             if (event.target.files && event.target.files[0]) {
                 reader.readAsDataURL(event.target.files[0]);
             } else {
                 imageField.src = "#";
                 imageField.style.display = 'none'; // Sembunyikan jika tidak ada file
             }
         }

        // Skrip Menghilangkan Flash Message (opsional: bisa dihapus jika animasi CSS sudah cukup)
        /*
         document.addEventListener('DOMContentLoaded', () => {
             const flashMessages = document.querySelectorAll('.flash-message');
             flashMessages.forEach(flash => {
                 setTimeout(() => {
                     flash.style.transition = 'opacity 0.5s ease';
                     flash.style.opacity = '0';
                     setTimeout(() => flash.remove(), 500); // Hapus elemen setelah fade out
                 }, 5000); // Tunggu 5 detik
             });
         });
         */
    </script>
    {{-- Stack untuk skrip JavaScript tambahan per halaman --}}
    @stack('scripts')
</body>
</html>