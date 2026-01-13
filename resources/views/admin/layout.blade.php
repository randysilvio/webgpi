<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Sinode GPI Papua</title>
    
    {{-- Favicon --}}
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
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px;}
        ::-webkit-scrollbar-track { background: #1f2937;}
        ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 3px;}
        ::-webkit-scrollbar-thumb:hover { background: #6b7280;}
        
        /* Sidebar Animations */
        .submenu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
        .submenu.open { max-height: 1000px; transition: max-height 0.5s ease-in; }
        .rotate-icon { transition: transform 0.3s; }
        .group.active .rotate-icon { transform: rotate(180deg); }
        
        /* Active Link Styling */
        .sidebar-link.active { background-color: #1e40af; color: white; border-right: 3px solid #60a5fa; }
        .sidebar-link.active i { color: white; }
        .sub-link.active { color: #60a5fa; font-weight: 600; }
        .sub-link:hover { color: white; transform: translateX(4px); }
        
        .flash-message { animation: fadeOut 5s forwards; }
        @keyframes fadeOut { 0% { opacity: 1; } 90% { opacity: 1; } 100% { opacity: 0; display: none; } }
    </style>
</head>
<body class="h-full flex antialiased text-gray-800">

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

        {{-- Navigation Menu --}}
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
            
            {{-- DASHBOARD --}}
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-all mb-4 @if(Request::routeIs('admin.dashboard')) active @endif">
                <i class="fas fa-th-large w-6 h-6 mr-1 text-center"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            {{-- LABEL: OPERASIONAL --}}
            <div class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Operasional Gereja</div>

            {{-- 1. DATA MASTER (DROPDOWN) --}}
            <div class="menu-group" id="group-master">
                <button onclick="toggleMenu('master')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-database w-6 text-center mr-1 text-blue-400"></i>
                        <span class="font-medium">Data Master</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon group-hover:text-white transition-transform"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-master">
                    @hasanyrole('Super Admin|Admin Sinode')
                    <a href="{{ route('admin.klasis.index') }}" class="sub-link block py-2 transition-all @if(Request::routeIs('admin.klasis.*')) active @endif">Data Klasis</a>
                    @endhasanyrole
                    @can('view jemaat')
                    <a href="{{ route('admin.jemaat.index') }}" class="sub-link block py-2 transition-all @if(Request::routeIs('admin.jemaat.*')) active @endif">Data Jemaat</a>
                    @endcan
                    @can('view anggota jemaat')
                    <a href="{{ route('admin.anggota-jemaat.index') }}" class="sub-link block py-2 transition-all @if(Request::routeIs('admin.anggota-jemaat.*')) active @endif">Anggota Jemaat</a>
                    @endcan
                    
                    {{-- PENDETA SUDAH PINDAH KE KEPEGAWAIAN --}}
                </div>
            </div>

            {{-- 2. KEUANGAN & ASET (DROPDOWN) --}}
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
                    <a href="{{ route('admin.perbendaharaan.anggaran.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.perbendaharaan.anggaran.*')) active @endif">Rencana APB (RAB)</a>
                    @hasanyrole('Super Admin|Admin Sinode')
                    <a href="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.perbendaharaan.mata-anggaran.*')) active @endif">Mata Anggaran</a>
                    @endhasanyrole
                    <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.perbendaharaan.aset.*')) active @endif">Inventaris Aset</a>
                    
                    {{-- SECTION LAPORAN --}}
                    <div class="pt-2 mt-2 border-t border-gray-700">
                        <span class="text-[10px] uppercase text-gray-500 font-bold">Laporan & Cetak</span>
                    </div>
                    <a href="{{ route('admin.perbendaharaan.laporan.gabungan') }}" class="sub-link block py-2 text-yellow-400 hover:text-yellow-300 font-bold @if(Request::routeIs('admin.perbendaharaan.laporan.gabungan')) active @endif">
                        <i class="fas fa-star text-[10px] mr-1"></i> Laporan Konsolidasi
                    </a>
                    <a href="{{ route('admin.perbendaharaan.laporan.realisasi') }}" class="sub-link block py-2 @if(Request::routeIs('admin.perbendaharaan.laporan.realisasi')) active @endif">Laporan Realisasi</a>
                    <a href="{{ route('admin.perbendaharaan.laporan.aset') }}" class="sub-link block py-2 @if(Request::routeIs('admin.perbendaharaan.laporan.aset')) active @endif">Laporan Aset</a>
                </div>
            </div>

            {{-- 3. WADAH KATEGORIAL (DROPDOWN) --}}
            <div class="menu-group" id="group-wadah">
                <button onclick="toggleMenu('wadah')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-users w-6 text-center mr-1 text-purple-400"></i>
                        <span class="font-medium">Wadah Kategorial</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-wadah">
                    <a href="{{ route('admin.wadah.statistik.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.wadah.statistik.*')) active @endif">Statistik Anggota</a>
                    <a href="{{ route('admin.wadah.pengurus.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.wadah.pengurus.*')) active @endif">Data Pengurus</a>
                    <a href="{{ route('admin.wadah.program.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.wadah.program.*')) active @endif">Program Kerja</a>
                    <a href="{{ route('admin.wadah.anggaran.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.wadah.anggaran.*')) active @endif">Keuangan Wadah</a>
                </div>
            </div>

            {{-- 4. SAKRAMEN (DROPDOWN) --}}
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
                    <a href="{{ route('admin.sakramen.sidi.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.sakramen.sidi.*')) active @endif">Sidi (Pengakuan Iman)</a>
                    <a href="{{ route('admin.sakramen.nikah.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.sakramen.nikah.*')) active @endif">Pemberkatan Nikah</a>
                </div>
            </div>

            {{-- 5. E-OFFICE (DROPDOWN) --}}
            <div class="menu-group" id="group-office">
                <button onclick="toggleMenu('office')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-envelope-open-text w-6 text-center mr-1 text-orange-400"></i>
                        <span class="font-medium">E-Office & Arsip</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-office">
                    <a href="{{ route('admin.e-office.surat-masuk.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.e-office.surat-masuk.*')) active @endif">Surat Masuk</a>
                    <a href="{{ route('admin.e-office.surat-keluar.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.e-office.surat-keluar.*')) active @endif">Surat Keluar</a>
                </div>
            </div>

            {{-- LABEL: MANAJEMEN --}}
            <div class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Manajemen & Sistem</div>

            {{-- 6. KEPEGAWAIAN / HRIS (DROPDOWN BARU - UNIFIED) --}}
            <div class="menu-group" id="group-hris">
                <button onclick="toggleMenu('hris')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-id-badge w-6 text-center mr-1 text-pink-400"></i>
                        <span class="font-medium">Kepegawaian (HRIS)</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-hris">
                    {{-- Semua Personel (Pegawai & Pendeta) --}}
                    <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.kepegawaian.pegawai.index') && !request('jenis')) active @endif">
                        Semua Personel
                    </a>
                    
                    {{-- Shortcut Khusus Pendeta --}}
                    <a href="{{ route('admin.kepegawaian.pegawai.index', ['jenis' => 'Pendeta']) }}" class="sub-link block py-2 @if(request('jenis') == 'Pendeta') active @endif">
                        Data Pendeta
                    </a>

                    {{-- Riwayat Mutasi --}}
                    @hasanyrole('Super Admin|Admin Bidang 3')
                    <a href="{{ route('admin.mutasi.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.mutasi.*')) active @endif">
                        Riwayat Mutasi
                    </a>
                    @endhasanyrole
                </div>
            </div>

            {{-- TATA GEREJA --}}
            <div class="menu-group" id="group-tata">
                <button onclick="toggleMenu('tata')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-gavel w-6 text-center mr-1 text-gray-400 group-hover:text-white"></i>
                        <span class="font-medium">Tata Gereja</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-tata">
                    <a href="{{ route('admin.tata-gereja.pejabat.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.tata-gereja.pejabat.*')) active @endif">Penatua & Diaken</a>
                    <a href="{{ route('admin.tata-gereja.sidang.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.tata-gereja.sidang.*')) active @endif">Risalah Sidang</a>
                </div>
            </div>

            {{-- WEBSITE & SETTINGS --}}
            @hasanyrole('Super Admin|Admin Sinode|Admin Bidang 4')
            <div class="menu-group" id="group-web">
                <button onclick="toggleMenu('web')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors text-sm group">
                    <div class="flex items-center">
                        <i class="fas fa-globe w-6 text-center mr-1 text-gray-400 group-hover:text-white"></i>
                        <span class="font-medium">Konten Website</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs rotate-icon"></i>
                </button>
                <div class="submenu pl-9 space-y-1 text-sm" id="submenu-web">
                    @can('manage posts')
                    <a href="{{ route('admin.posts.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.posts.*')) active @endif">Berita & Kegiatan</a>
                    @endcan
                    @can('manage services')
                    <a href="{{ route('admin.services.index') }}" class="sub-link block py-2 @if(Request::routeIs('admin.services.*')) active @endif">Layanan</a>
                    @endcan
                    @can('manage messages')
                    <a href="{{ route('admin.messages') }}" class="sub-link block py-2 @if(Request::routeIs('admin.messages*')) active @endif">Pesan Masuk</a>
                    @endcan
                </div>
            </div>
            
            <a href="{{ route('admin.settings') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors group @if(Request::routeIs('admin.settings')) active @endif">
                <i class="fas fa-cogs w-6 text-center mr-1 text-gray-400 group-hover:text-white"></i>
                <span class="font-medium text-sm">Pengaturan</span>
            </a>
            @endhasanyrole

            @can('manage users')
            <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 transition-colors group @if(Request::routeIs('admin.users.*')) active @endif">
                <i class="fas fa-users-cog w-6 text-center mr-1 text-gray-400 group-hover:text-white"></i>
                <span class="font-medium text-sm">Manajemen User</span>
            </a>
            @endcan

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

    {{-- KONTEN UTAMA --}}
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
                        @if(Auth::user()->jemaatTugas) | {{ Auth::user()->jemaatTugas->nama_jemaat }} @endif
                     </div>
                 </div>
                 @endauth
                 <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary ring-2 ring-white shadow-sm">
                    <i class="fas fa-user-circle text-xl"></i>
                </div>
            </div>
        </header>

        {{-- Main Content Area --}}
        <main class="flex-1 p-6 overflow-y-auto bg-gray-50">
             @if (session('success'))
                 <div class="flash-message mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm flex items-center"> 
                    <i class="fas fa-check-circle mr-3 text-lg"></i>
                    <div>
                        <p class="font-bold text-sm">Berhasil</p>
                        <p class="text-sm">{{ session('success') }}</p> 
                    </div>
                </div>
             @endif
             @if (session('error'))
                 <div class="flash-message mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm flex items-center"> 
                    <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
                    <div>
                        <p class="font-bold text-sm">Terjadi Kesalahan</p>
                        <p class="text-sm">{{ session('error') }}</p> 
                    </div>
                </div>
             @endif
            @yield('content')
        </main>

        <footer class="bg-white border-t border-gray-200 py-3 px-6 text-center md:text-right text-xs text-gray-400">
             &copy; {{ date('Y') }} Sistem Informasi Manajemen Gereja - Sinode GPI Papua.
        </footer>
    </div>

    {{-- Overlay untuk Mobile --}}
    <div class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity" id="sidebar-overlay"></div>

    <script>
        // === LOGIKA DROPDOWN MENU ===
        function toggleMenu(id) {
            const submenu = document.getElementById(`submenu-${id}`);
            const group = document.getElementById(`group-${id}`);
            
            // Toggle class open untuk animasi CSS
            if (submenu.classList.contains('open')) {
                submenu.classList.remove('open');
                group.classList.remove('active');
            } else {
                submenu.classList.add('open');
                group.classList.add('active');
            }
        }

        // === AUTO EXPAND ACTIVE MENU ===
        // Membuka dropdown secara otomatis jika ada link aktif di dalamnya saat halaman dimuat
        document.addEventListener("DOMContentLoaded", function() {
            const submenus = document.querySelectorAll('.submenu');
            submenus.forEach(menu => {
                if (menu.querySelector('.active')) {
                    menu.classList.add('open');
                    menu.parentElement.classList.add('active'); // Putar panah
                }
            });
        });

        // === MOBILE SIDEBAR ===
        const sidebar = document.getElementById('sidebar');
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() { 
            sidebar.classList.toggle('-translate-x-full'); 
            overlay.classList.toggle('hidden'); 
        }

        if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);

        // Auto Logout Timer (15 Menit)
         setTimeout(function(){
            window.location.href = "{{ route('login') }}";
         }, 900000);

        // Image Preview Helper
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