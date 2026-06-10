<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Sinode GPI Papua') }}</title>
    
    <link rel="icon" href="{{ asset('gpi_logo.png') }}" type="image/png">
    
    {{-- CDN Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script> 
        tailwind.config = { 
            theme: { 
                extend: { 
                    colors: { primary: '#1e40af', secondary: '#f59e0b', accent: '#10b981' },
                    transitionProperty: { 'height': 'height', 'spacing': 'margin, padding' }
                } 
            } 
        } 
    </script>
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px;}
        ::-webkit-scrollbar-track { background: #1f2937;}
        ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 3px;}
        ::-webkit-scrollbar-thumb:hover { background: #6b7280;}
        .submenu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
        .submenu.open { max-height: 1000px; transition: max-height 0.5s ease-in; }
        .rotate-icon { transition: transform 0.3s; }
        .group.active .rotate-icon { transform: rotate(180deg); }
        .sidebar-link.active { background-color: #1e40af; color: white; border-right: 3px solid #60a5fa; }
        .sidebar-link.active i { color: white; }
        .sub-link.active { color: #60a5fa; font-weight: 600; }
        .sub-link:hover { color: white; transform: translateX(4px); }
        .flash-message { animation: fadeOut 5s forwards; }
        @keyframes fadeOut { 0% { opacity: 1; } 90% { opacity: 1; } 100% { opacity: 0; display: none; } }
    </style>
</head>
<body class="h-full flex antialiased text-gray-800 font-sans">

    {{-- SIDEBAR --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-gray-300 flex flex-col transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full shadow-2xl" id="sidebar">
        
        {{-- Header Logo --}}
        <div class="flex items-center justify-center h-16 border-b border-gray-800 bg-gray-900 shadow-sm sticky top-0 z-10">
             <div class="flex items-center space-x-3">
                 @if(file_exists(public_path('gpi_logo.png')))
                    <img src="{{ asset('gpi_logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                 @else
                    <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center shadow-md text-white font-bold text-xs">GPI</div>
                 @endif
                <span class="text-white text-lg font-bold tracking-tight">SIM-G <span class="text-blue-500">PAPUA</span></span>
            </div>
        </div>

        {{-- MENU NAVIGASI --}}
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
            
            {{-- DASHBOARD --}}
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-all mb-4 @if(Request::routeIs('admin.dashboard')) active @endif">
                <i class="fas fa-th-large w-6 h-6 mr-1 text-center"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            {{-- 1. BIDANG 1: TEOLOGI & PELAYANAN --}}
            @hasanyrole('Super Admin|Admin Bidang 1')
            <div class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Bidang 1: Pelayanan</div>

            <div class="menu-group" id="group-sakramen">
                <button onclick="toggleMenu('sakramen')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-hand-holding-water w-6 text-center mr-1 text-cyan-400"></i>
                        <span class="font-medium">Sakramen</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-sakramen">
                    <a href="{{ route('admin.sakramen.baptis.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.sakramen.baptis.*')) active @endif">Baptisan Kudus</a>
                    <a href="{{ route('admin.sakramen.sidi.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.sakramen.sidi.*')) active @endif">Sidi</a>
                    <a href="{{ route('admin.sakramen.nikah.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.sakramen.nikah.*')) active @endif">Pemberkatan Nikah</a>
                </div>
            </div>

            <div class="menu-group" id="group-tata">
                <button onclick="toggleMenu('tata')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-gavel w-6 text-center mr-1 text-gray-400"></i>
                        <span class="font-medium">Tata Gereja</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-tata">
                    <a href="{{ route('admin.tata-gereja.pejabat.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.tata-gereja.pejabat.*')) active @endif">Pejabat Gereja</a>
                    <a href="{{ route('admin.tata-gereja.sidang.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.tata-gereja.sidang.*')) active @endif">Risalah Sidang</a>
                </div>
            </div>
            @endhasanyrole

            {{-- 2. BIDANG 2: PEMBANGUNAN & EKONOMI --}}
            @hasanyrole('Super Admin|Admin Bidang 2')
            <div class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Bidang 2: Pembangunan</div>

            <div class="menu-group" id="group-keuangan">
                <button onclick="toggleMenu('keuangan')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-coins w-6 text-center mr-1 text-green-400"></i>
                        <span class="font-medium">Keuangan & Aset</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-keuangan">
                    <a href="{{ route('admin.perbendaharaan.transaksi.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.perbendaharaan.transaksi.*')) active @endif">Buku Kas Umum</a>
                    <a href="{{ route('admin.perbendaharaan.anggaran.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.perbendaharaan.anggaran.*')) active @endif">Rencana APB</a>
                    <a href="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.perbendaharaan.mata-anggaran.*')) active @endif">Mata Anggaran</a>
                    <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.perbendaharaan.aset.*')) active @endif">Inventaris Aset</a>
                    <div class="pt-2 mt-2 border-t border-gray-700">
                        <span class="text-[10px] uppercase text-gray-500 font-bold">Laporan</span>
                    </div>
                    <a href="{{ route('admin.perbendaharaan.laporan.gabungan') }}" class="sub-link block py-2 text-yellow-400 hover:text-yellow-300 font-bold">Laporan Konsolidasi</a>
                    <a href="{{ route('admin.perbendaharaan.laporan.realisasi') }}" class="sub-link block py-2">Laporan Realisasi</a>
                    <a href="{{ route('admin.perbendaharaan.laporan.aset') }}" class="sub-link block py-2">Laporan Aset</a>
                </div>
            </div>
            @endhasanyrole

            {{-- 3. BIDANG 3: KEPEGAWAIAN --}}
            @hasanyrole('Super Admin|Admin Bidang 3')
            <div class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Bidang 3: Kepegawaian</div>

            <div class="menu-group" id="group-hris">
                <button onclick="toggleMenu('hris')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-id-badge w-6 text-center mr-1 text-pink-400"></i>
                        <span class="font-medium">HRIS (Personel)</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-hris">
                    <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.kepegawaian.pegawai.index') && !request('jenis')) active @endif">Semua Personel</a>
                    <a href="{{ route('admin.kepegawaian.pegawai.index', ['jenis' => 'Pendeta']) }}" class="sub-link block py-2 @if(request('jenis') == 'Pendeta') active @endif">Data Pendeta</a>
                    <a href="{{ route('admin.mutasi.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.mutasi.*')) active @endif">Riwayat Mutasi</a>
                </div>
            </div>

            <div class="menu-group" id="group-master-b3">
                <button onclick="toggleMenu('master-b3')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-map w-6 text-center mr-1 text-blue-400"></i>
                        <span class="font-medium">Ref. Wilayah</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-master-b3">
                    <a href="{{ route('admin.klasis.index') }}" class="sub-link block py-2">Data Klasis</a>
                    <a href="{{ route('admin.jemaat.index') }}" class="sub-link block py-2">Data Jemaat</a>
                </div>
            </div>
            @endhasanyrole

            {{-- 4. BIDANG 4: INFORKOM --}}
            @hasanyrole('Super Admin|Admin Bidang 4')
            <div class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Bidang 4: Inforkom</div>

            <div class="menu-group" id="group-web">
                <button onclick="toggleMenu('web')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-globe w-6 text-center mr-1 text-gray-400 group-hover:text-white"></i>
                        <span class="font-medium">Konten Website</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-web">
                    <a href="{{ route('admin.popup.index') }}" class="sub-link block py-2 text-yellow-500 font-bold hover:text-yellow-400">
                        <i class="fas fa-bullhorn mr-1 text-xs"></i> Info Popup / Iklan
                    </a>
                    <a href="{{ route('admin.posts.index') }}" class="sub-link block py-2">Berita & Kegiatan</a>
                    <a href="{{ route('admin.services.index') }}" class="sub-link block py-2">Jadwal Layanan</a>
                    <a href="{{ route('admin.messages') }}" class="sub-link block py-2">Pesan Masuk</a>
                </div>
            </div>

            <div class="menu-group" id="group-office">
                <button onclick="toggleMenu('office')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-envelope-open-text w-6 text-center mr-1 text-orange-400"></i>
                        <span class="font-medium">E-Office</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-office">
                    <a href="{{ route('admin.e-office.surat-masuk.index') }}" class="sub-link block py-2">Surat Masuk</a>
                    <a href="{{ route('admin.e-office.surat-keluar.index') }}" class="sub-link block py-2">Surat Keluar</a>
                </div>
            </div>
            
            <a href="{{ route('admin.settings') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors group @if(Request::routeIs('admin.settings')) active @endif">
                <i class="fas fa-cogs w-6 text-center mr-1 text-gray-400 group-hover:text-white"></i>
                <span class="font-medium text-sm">Pengaturan Sistem</span>
            </a>
            @endhasanyrole

            {{-- 5. ADMIN WILAYAH (KLASIS/JEMAAT) --}}
            @hasanyrole('Super Admin|Admin Klasis|Admin Jemaat')
            {{-- Kecuali jika dia juga pengurus bidang, judul ini disembunyikan agar tidak dobel --}}
            @unlessrole('Admin Bidang 1|Admin Bidang 2|Admin Bidang 3|Admin Bidang 4')
                <div class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Manajemen Wilayah</div>
            @endunlessrole

            <div class="menu-group" id="group-master-wilayah">
                <button onclick="toggleMenu('master-wilayah')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-database w-6 text-center mr-1 text-blue-400"></i>
                        <span class="font-medium">Data Master</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-master-wilayah">
                    @can('view klasis')
                    <a href="{{ route('admin.klasis.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.klasis.*')) active @endif">
                        @role('Admin Klasis') Profil Klasis @else Data Klasis @endrole
                    </a>
                    @endcan
                    @can('view jemaat')
                    <a href="{{ route('admin.jemaat.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.jemaat.*')) active @endif">
                         @role('Admin Jemaat') Profil Jemaat @else Data Jemaat @endrole
                    </a>
                    @endcan
                    @can('view anggota jemaat')
                    <a href="{{ route('admin.anggota-jemaat.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.anggota-jemaat.*')) active @endif">Anggota Jemaat</a>
                    @endcan
                </div>
            </div>
            
            <div class="menu-group" id="group-wadah-wilayah">
                <button onclick="toggleMenu('wadah-wilayah')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-users w-6 text-center mr-1 text-purple-400"></i>
                        <span class="font-medium">Wadah Kategorial</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-wadah-wilayah">
                    <a href="{{ route('admin.wadah.statistik.index') }}" class="sub-link block py-2">Statistik Anggota</a>
                    <a href="{{ route('admin.wadah.pengurus.index') }}" class="sub-link block py-2">Data Pengurus</a>
                </div>
            </div>
            @endhasanyrole

            {{-- SUPER ADMIN --}}
            @role('Super Admin')
            <div class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Super Admin</div>
            <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors group">
                <i class="fas fa-users-cog w-6 text-center mr-1 text-gray-400 group-hover:text-white"></i>
                <span class="font-medium text-sm">Manajemen User</span>
            </a>
            @endrole

            {{-- LOGOUT --}}
            <div class="mt-8 pt-4 border-t border-gray-800 pb-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3 py-2 text-red-400 hover:text-red-300 hover:bg-red-900/20 rounded-lg transition-colors text-sm">
                        <i class="fas fa-sign-out-alt w-6 text-center mr-1"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    {{-- MAIN CONTENT WRAPPER --}}
    <div class="flex-1 flex flex-col md:pl-64 h-full">
        {{-- Header Bar --}}
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 flex-shrink-0 z-20">
            <div class="flex items-center">
                <button class="md:hidden text-gray-500 hover:text-primary mr-4" id="hamburger-btn">
                     <i class="fas fa-bars fa-lg"></i>
                </button>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight">
                    @yield('header-title', 'Dashboard')
                </h1>
            </div>
            <div class="flex items-center space-x-4">
                 @auth
                 <div class="text-right hidden sm:block">
                     <div class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</div>
                     <div class="text-xs text-gray-500">
                        {{ Auth::user()->roles->pluck('name')->first() ?? 'User' }}
                        @if(Auth::user()->klasisTugas) | {{ Auth::user()->klasisTugas->nama_klasis }} @endif
                     </div>
                 </div>
                 @endauth
                 <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary ring-2 ring-white shadow-sm">
                    <i class="fas fa-user-circle text-xl"></i>
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 p-6 overflow-y-auto bg-gray-50">
             @if (session('success'))
                 <div class="flash-message mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm flex items-center"> 
                    <i class="fas fa-check-circle mr-3 text-lg"></i>
                    <div><p class="font-bold text-sm">Berhasil</p><p class="text-sm">{{ session('success') }}</p></div>
                </div>
             @endif
             @if (session('error'))
                 <div class="flash-message mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm flex items-center"> 
                    <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
                    <div><p class="font-bold text-sm">Terjadi Kesalahan</p><p class="text-sm">{{ session('error') }}</p></div>
                </div>
             @endif
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="bg-white border-t border-gray-200 py-3 px-6 text-center md:text-right text-xs text-gray-400">
             &copy; {{ date('Y') }} Sistem Informasi Manajemen Gereja - Sinode GPI Papua.
        </footer>
    </div>

    {{-- Mobile Sidebar Overlay --}}
    <div class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity" id="sidebar-overlay"></div>

    {{-- SCRIPTS --}}
    <script>
        // Toggle Sidebar Dropdown
        function toggleMenu(id) {
            const submenu = document.getElementById(`submenu-${id}`);
            const group = document.getElementById(`group-${id}`);
            if (submenu.classList.contains('open')) {
                submenu.classList.remove('open');
                group.classList.remove('active');
            } else {
                submenu.classList.add('open');
                group.classList.add('active');
            }
        }
        
        // Auto Open Active Menu
        document.addEventListener("DOMContentLoaded", function() {
            const submenus = document.querySelectorAll('.submenu');
            submenus.forEach(menu => {
                if (menu.querySelector('.active')) {
                    menu.classList.add('open');
                    menu.parentElement.classList.add('active');
                }
            });
        });
        
        // Mobile Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const overlay = document.getElementById('sidebar-overlay');
        function toggleSidebar() { 
            sidebar.classList.toggle('-translate-x-full'); 
            overlay.classList.toggle('hidden'); 
        }
        if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);
        
        // Auto Logout
        setTimeout(function(){ window.location.href = "{{ route('login') }}"; }, 900000);
    </script>
    @stack('scripts')
</body>
</html>