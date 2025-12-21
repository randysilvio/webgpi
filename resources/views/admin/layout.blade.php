<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Sinode GPI Papua</title>
    
    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('gpi_logo.png') }}" type="image/png">

    {{-- CDN Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script> tailwind.config = { theme: { extend: { colors: { primary: '#1e40af', secondary: '#f59e0b', accent: '#10b981' } } } } </script>
     {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px;}
        ::-webkit-scrollbar-track { background: #f1f1f1;}
        ::-webkit-scrollbar-thumb { background: #a8a8a8;}
        ::-webkit-scrollbar-thumb:hover { background: #888;}
        .sidebar-link.active { background-color: theme('colors.primary'); color: white; }
        .sidebar-link.active svg, .sidebar-link.active i { color: white; } 
        .sidebar-link svg, .sidebar-link i { color: theme('colors.gray.400'); } 
        .sidebar-link:hover svg, .sidebar-link:hover i { color: white; } 
        .flash-message { animation: fadeOut 5s forwards; }
        @keyframes fadeOut { 0% { opacity: 1; } 90% { opacity: 1; } 100% { opacity: 0; display: none; } }
        .image-preview[src="#"], .image-preview:not([src]) { display: none; }
        @stack('styles')
    </style>
</head>
<body class="h-full flex antialiased">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-gray-800 text-gray-300 flex flex-col transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full" id="sidebar">
        {{-- Logo/Header Sidebar --}}
        <div class="flex items-center justify-center h-20 border-b border-gray-700 px-4">
             <div class="flex items-center space-x-2">
                 @if(file_exists(public_path('gpi_logo.png')))
                    <img src="{{ asset('gpi_logo.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                 @else
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L3 7v13h18V7L12 2zm0 2.236L18.99 8H5.01L12 4.236zM5 18V9.618l7 4.118 7-4.118V18H5z"/></svg>
                    </div>
                 @endif
                <span class="text-white text-xl font-semibold">Admin GPI</span>
            </div>
        </div>

        {{-- Navigasi Menu --}}
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            {{-- Link Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.dashboard')) active @endif">
                <i class="fas fa-tachometer-alt w-5 h-5 mr-3 fa-fw"></i>
                <span>Dashboard</span>
            </a>

             {{-- Grup Data Master --}}
            <div class="pt-4 mt-4 border-t border-gray-700">
                <span class="px-4 text-xs font-semibold uppercase text-gray-500">Data Master</span>
                
                @hasanyrole('Super Admin|Admin Sinode')
                <a href="{{ route('admin.klasis.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.klasis.*')) active @endif">
                     <i class="fas fa-landmark w-5 h-5 mr-3 fa-fw"></i>
                    <span>Manajemen Klasis</span>
                </a>
                @endhasanyrole

                @can('view jemaat')
                <a href="{{ route('admin.jemaat.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.jemaat.*')) active @endif">
                     <i class="fas fa-church w-5 h-5 mr-3 fa-fw"></i>
                    <span>Manajemen Jemaat</span>
                </a>
                @endcan

                @can('view anggota jemaat')
                 <a href="{{ route('admin.anggota-jemaat.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.anggota-jemaat.*')) active @endif">
                     <i class="fas fa-users w-5 h-5 mr-3 fa-fw"></i>
                     <span>Anggota Jemaat</span>
                 </a>
                 @endcan

                @can('view pendeta')
                <a href="{{ route('admin.pendeta.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.pendeta.*') && !Request::routeIs('admin.pendeta.mutasi.*') && !Request::routeIs('admin.mutasi.*')) active @endif">
                     <i class="fas fa-user-tie w-5 h-5 mr-3 fa-fw"></i>
                    <span>Manajemen Pendeta</span>
                </a>
                @endcan

                @hasanyrole('Super Admin|Admin Bidang 3')
                <a href="{{ route('admin.mutasi.index') }}" 
                   class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.pendeta.mutasi.*') || Request::routeIs('admin.mutasi.*') ) active @endif">
                    <i class="fas fa-exchange-alt w-5 h-5 mr-3 fa-fw"></i>
                    <span>Mutasi Pendeta</span>
                </a>
                @endhasanyrole
            </div>

            {{-- Grup Kepegawaian (HRIS) --}}
            <div class="pt-4 mt-4 border-t border-gray-700">
                <span class="px-4 text-xs font-semibold uppercase text-gray-500">Kepegawaian (HRIS)</span>
                <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.kepegawaian.*')) active @endif">
                    <i class="fas fa-id-card-alt w-5 h-5 mr-3 fa-fw"></i>
                    <span>Data Pegawai</span>
                </a>
            </div>

            {{-- Grup Perbendaharaan & Aset (FASE 7) --}}
            <div class="pt-4 mt-4 border-t border-gray-700">
                <span class="px-4 text-xs font-semibold uppercase text-gray-500">Perbendaharaan & Aset</span>
                {{-- Menu Baru: Buku Kas Umum --}}
                <a href="{{ route('admin.perbendaharaan.transaksi.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.perbendaharaan.transaksi.*')) active @endif">
                    <i class="fas fa-book w-5 h-5 mr-3 fa-fw"></i>
                    <span>Buku Kas Umum</span>
                </a>
                <a href="{{ route('admin.perbendaharaan.anggaran.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.perbendaharaan.anggaran.*')) active @endif">
                    <i class="fas fa-file-invoice w-5 h-5 mr-3 fa-fw"></i>
                    <span>Rencana APB</span>
                </a>
                @hasanyrole('Super Admin|Admin Sinode')
                <a href="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.perbendaharaan.mata-anggaran.*')) active @endif">
                    <i class="fas fa-list-ol w-5 h-5 mr-3 fa-fw"></i>
                    <span>Mata Anggaran</span>
                </a>
                @endhasanyrole
                <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.perbendaharaan.aset.*')) active @endif">
                    <i class="fas fa-boxes w-5 h-5 mr-3 fa-fw"></i>
                    <span>Inventaris Aset</span>
                </a>
                
                {{-- Sub-Menu Laporan & Audit (Penambahan Terbaru) --}}
                <div class="mt-2 ml-4 border-l border-gray-600 pl-2">
                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Laporan & Audit</span>
                    <a href="{{ route('admin.perbendaharaan.laporan.realisasi') }}" class="block py-1 text-xs hover:text-white transition-colors @if(Request::routeIs('admin.perbendaharaan.laporan.realisasi')) text-white font-bold @else text-gray-400 @endif">
                        <i class="fas fa-file-contract mr-2"></i>Laporan Realisasi
                    </a>
                    <a href="{{ route('admin.perbendaharaan.laporan.aset') }}" class="block py-1 text-xs hover:text-white transition-colors @if(Request::routeIs('admin.perbendaharaan.laporan.aset')) text-white font-bold @else text-gray-400 @endif">
                        <i class="fas fa-file-alt mr-2"></i>Laporan Aset
                    </a>
                </div>
            </div>

            {{-- Grup Wadah Kategorial --}}
            <div class="pt-4 mt-4 border-t border-gray-700">
                <span class="px-4 text-xs font-semibold uppercase text-gray-500">Wadah Kategorial</span>
                <a href="{{ route('admin.wadah.statistik.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.wadah.statistik.*')) active @endif">
                    <i class="fas fa-chart-pie w-5 h-5 mr-3 fa-fw"></i>
                    <span>Statistik Anggota</span>
                </a>
                <a href="{{ route('admin.wadah.pengurus.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.wadah.pengurus.*')) active @endif">
                    <i class="fas fa-users-cog w-5 h-5 mr-3 fa-fw"></i>
                    <span>Data Pengurus</span>
                </a>
                <a href="{{ route('admin.wadah.program.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.wadah.program.*')) active @endif">
                    <i class="fas fa-tasks w-5 h-5 mr-3 fa-fw"></i>
                    <span>Program Kerja</span>
                </a>
                <a href="{{ route('admin.wadah.anggaran.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.wadah.anggaran.*')) active @endif">
                    <i class="fas fa-file-invoice-dollar w-5 h-5 mr-3 fa-fw"></i>
                    <span>Keuangan (RAB)</span>
                </a>
            </div>

            {{-- Grup Konten Website --}}
            @hasanyrole('Super Admin|Admin Sinode|Admin Bidang 4')
            <div class="pt-4 mt-4 border-t border-gray-700">
                <span class="px-4 text-xs font-semibold uppercase text-gray-500">Konten Website</span>
                @can('manage posts')
                <a href="{{ route('admin.posts.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.posts.*')) active @endif">
                    <i class="fas fa-newspaper w-5 h-5 mr-3 fa-fw"></i>
                    <span>Berita & Kegiatan</span>
                </a>
                @endcan
                @can('manage services')
                <a href="{{ route('admin.services.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.services.*')) active @endif">
                     <i class="fas fa-concierge-bell w-5 h-5 mr-3 fa-fw"></i>
                    <span>Manajemen Layanan</span>
                </a>
                @endcan
                @can('manage messages')
                <a href="{{ route('admin.messages') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.messages*')) active @endif">
                    <i class="fas fa-envelope-open-text w-5 h-5 mr-3 fa-fw"></i>
                    <span>Pesan Masuk</span>
                </a>
                @endcan
            </div>
            @endhasanyrole

            {{-- Grup Administrasi --}}
            <div class="pt-4 mt-4 border-t border-gray-700">
                <span class="px-4 text-xs font-semibold uppercase text-gray-500">Administrasi</span>
                @hasanyrole('Super Admin|Admin Sinode')
                <a href="{{ route('admin.settings') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.settings')) active @endif">
                     <i class="fas fa-cog w-5 h-5 mr-3 fa-fw"></i>
                    <span>Pengaturan</span>
                </a>
                @endhasanyrole
                @can('manage users')
                <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors group @if(Request::routeIs('admin.users.*')) active @endif">
                    <i class="fas fa-users-cog w-5 h-5 mr-3 fa-fw"></i>
                    <span>Manajemen User</span>
                </a>
                @endcan
            </div>

            {{-- Logout --}}
            <div class="mt-auto pt-4 border-t border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-link w-full flex items-center px-4 py-2.5 rounded-lg text-red-400 hover:bg-red-700 hover:text-white transition-colors group">
                        <i class="fas fa-sign-out-alt w-5 h-5 mr-3 fa-fw"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    {{-- Konten Utama --}}
    <div class="flex-1 flex flex-col md:pl-64">
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-4 md:px-6 flex-shrink-0">
            <button class="md:hidden text-gray-500 hover:text-gray-700" id="hamburger-btn" aria-label="Open Sidebar">
                 <i class="fas fa-bars fa-lg"></i>
            </button>
             <h1 class="text-xl font-semibold text-gray-800 hidden md:block">@yield('header-title', 'Dashboard')</h1>
            <div class="flex items-center space-x-3">
                 @auth
                 <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ Auth::user()->name }}</span>
                 @endauth
                 <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 ring-1 ring-gray-400">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6 md:p-8 overflow-y-auto bg-gray-100">
             @if (session('success'))
                 <div class="flash-message mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert"> <p>{{ session('success') }}</p> </div>
             @endif
             @if (session('error'))
                 <div class="flash-message mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert"> <p>{{ session('error') }}</p> </div>
             @endif
            @yield('content')
        </main>

        <footer class="bg-white border-t border-gray-200 p-4 text-center text-sm text-gray-500 mt-auto flex-shrink-0">
             &copy; {{ date('Y') }} Sinode GPI Papua Admin Dashboard.
        </footer>
    </div>

    <div class="fixed inset-0 bg-black/30 z-30 hidden md:hidden" id="sidebar-overlay"></div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const overlay = document.getElementById('sidebar-overlay');
        function toggleSidebar() { sidebar.classList.toggle('-translate-x-full'); overlay.classList.toggle('hidden'); }
        if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);

         setTimeout(function(){
            window.location.href = "{{ route('login') }}";
         }, 900000);

        function previewImage(event, previewId) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(previewId);
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
    @stack('scripts')
</body>
</html>