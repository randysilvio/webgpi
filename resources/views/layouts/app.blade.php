<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Sinode GPI Papua') }}</title>
    
    <link rel="icon" href="{{ asset('gpi_logo.png') }}" type="image/png">
    
    {{-- CDN Tailwind CSS & Font Configuration --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script> 
        tailwind.config = { 
            theme: { 
                extend: { 
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { 
                        brand: { 50: '#f0f9ff', 600: '#0284c7', 800: '#075985', 900: '#0c4a6e' } 
                    }
                } 
            } 
        } 
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        /* Scrollbar Styling */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #1f2937; }
        ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 2px; }
        
        /* Sidebar Submenu Transitions */
        .submenu { 
            max-height: 0; 
            overflow: hidden; 
            transition: max-height 0.4s ease-in-out, opacity 0.4s ease-in-out; 
            opacity: 0;
        }
        .submenu.open { 
            max-height: 1000px;
            opacity: 1;
        }
        .rotate-icon { transition: transform 0.3s ease; }
        .menu-btn.active .rotate-icon { transform: rotate(180deg); }
        
        /* Link Active States */
        .sidebar-link.active { background-color: rgba(255,255,255,0.08); border-left: 3px solid #38bdf8; color: white; }
        .menu-btn.active { color: white; background-color: #1e293b; }
        .sub-link:hover { color: #e0f2fe; transform: translateX(4px); transition: transform 0.2s; }
        .sub-link.active-page { color: #38bdf8; font-weight: 600; }
    </style>
</head>
<body class="h-full flex antialiased text-slate-800 bg-slate-50 font-sans">

    {{-- SIDEBAR --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-400 flex flex-col transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full shadow-2xl" id="sidebar">
        
        {{-- LOGO AREA --}}
        <div class="h-16 flex items-center px-5 border-b border-slate-800 bg-slate-950">
             <div class="flex items-center gap-3">
                 {{-- Tampilkan Logo dari Setting jika tersedia --}}
                 @if(isset($setting) && $setting->logo_path && Storage::disk('public')->exists($setting->logo_path))
                    <img src="{{ Storage::url($setting->logo_path) }}" alt="Logo" class="w-9 h-9 object-contain shadow-sm">
                 @else
                    <div class="w-9 h-9 bg-brand-600 rounded flex items-center justify-center text-white font-black text-sm shadow-md">GPI</div>
                 @endif
                 
                 <div class="flex flex-col">
                    <span class="text-white text-sm font-bold tracking-tight uppercase leading-none">SIM-G</span>
                    <span class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-1">GPI PAPUA</span>
                 </div>
            </div>
        </div>

        {{-- NAVIGATION --}}
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto text-sm">
            
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded hover:bg-slate-800 hover:text-white transition-colors mb-4 @if(Request::routeIs('admin.dashboard')) active @endif">
                <i class="fas fa-chart-line w-5 text-center mr-3"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            {{-- BIDANG 1: PELAYANAN (TEOLOGI) --}}
            @hasanyrole('Super Admin|Admin Bidang 1')
            <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider">Bidang 1: Pelayanan</div>
            
            <div class="menu-group">
                <button onclick="toggleMenu('sakramen')" id="btn-sakramen" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-hand-holding-water w-5 text-center mr-3"></i>
                        <span>Sakramen</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                </button>
                <div class="submenu pl-10 space-y-1 mt-1" id="submenu-sakramen">
                    <a href="{{ route('admin.sakramen.baptis.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.sakramen.baptis.*')) active-page @endif">Baptisan</a>
                    <a href="{{ route('admin.sakramen.sidi.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.sakramen.sidi.*')) active-page @endif">Sidi</a>
                    <a href="{{ route('admin.sakramen.nikah.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.sakramen.nikah.*')) active-page @endif">Pernikahan</a>
                </div>
            </div>
            
            <div class="menu-group">
                <button onclick="toggleMenu('tata')" id="btn-tata" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-gavel w-5 text-center mr-3"></i>
                        <span>Tata Gereja</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                </button>
                <div class="submenu pl-10 space-y-1 mt-1" id="submenu-tata">
                    <a href="{{ route('admin.tata-gereja.pejabat.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.tata-gereja.pejabat.*')) active-page @endif">Pejabat Gereja</a>
                    <a href="{{ route('admin.tata-gereja.sidang.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.tata-gereja.sidang.*')) active-page @endif">Risalah Sidang</a>
                </div>
            </div>
            @endhasanyrole

            {{-- BIDANG 2: PEMBANGUNAN (KEUANGAN) --}}
            @hasanyrole('Super Admin|Admin Bidang 2')
            <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider">Bidang 2: Pembangunan</div>
            
            <div class="menu-group">
                <button onclick="toggleMenu('keuangan')" id="btn-keuangan" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-wallet w-5 text-center mr-3"></i>
                        <span>Keuangan</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                </button>
                <div class="submenu pl-10 space-y-1 mt-1" id="submenu-keuangan">
                    <a href="{{ route('admin.perbendaharaan.transaksi.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.perbendaharaan.transaksi.*')) active-page @endif">Buku Kas Umum</a>
                    <a href="{{ route('admin.perbendaharaan.anggaran.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.perbendaharaan.anggaran.*')) active-page @endif">Rencana RAPB</a>
                    <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.perbendaharaan.aset.*')) active-page @endif">Inventaris Aset</a>
                    <div class="py-1 text-[10px] text-slate-600 uppercase mt-2">Laporan</div>
                    <a href="{{ route('admin.perbendaharaan.laporan.gabungan') }}" class="sub-link block py-1.5 text-yellow-500 font-medium @if(Request::routeIs('admin.perbendaharaan.laporan.*')) active-page @endif">Konsolidasi Kas</a>
                </div>
            </div>
            @endhasanyrole

            {{-- BIDANG 3: KEPEGAWAIAN (HRIS) --}}
            @hasanyrole('Super Admin|Admin Bidang 3')
            <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider">Bidang 3: Kepegawaian</div>
            
            <div class="menu-group">
                <button onclick="toggleMenu('hris')" id="btn-hris" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-id-card w-5 text-center mr-3"></i>
                        <span>SDM / HRIS</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                </button>
                <div class="submenu pl-10 space-y-1 mt-1" id="submenu-hris">
                    <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.kepegawaian.pegawai.index') && !request('jenis')) active-page @endif">Direktori Pegawai</a>
                    <a href="{{ route('admin.kepegawaian.pegawai.index', ['jenis' => 'Pendeta']) }}" class="sub-link block py-1.5 @if(request('jenis') == 'Pendeta') active-page @endif">Data Pendeta</a>
                    <a href="{{ route('admin.mutasi.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.mutasi.*')) active-page @endif">Riwayat Mutasi</a>
                </div>
            </div>
            @endhasanyrole

            {{-- BIDANG 4: INFORKOM & WEBSITE --}}
            @hasanyrole('Super Admin|Admin Bidang 4')
            <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider">Bidang 4: Inforkom</div>
            
            <div class="menu-group">
                <button onclick="toggleMenu('web')" id="btn-web" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-globe w-5 text-center mr-3"></i>
                        <span>Website</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                </button>
                <div class="submenu pl-10 space-y-1 mt-1" id="submenu-web">
                    <a href="{{ route('admin.popup.index') }}" class="sub-link block py-1.5 text-yellow-500 font-medium @if(Request::routeIs('admin.popup.*')) active-page @endif">Kelola Popup Info</a>
                    <a href="{{ route('admin.posts.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.posts.*')) active-page @endif">Berita & Post</a>
                    <a href="{{ route('admin.messages') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.messages*')) active-page @endif">Pesan Masuk</a>
                </div>
            </div>

            <div class="menu-group">
                <button onclick="toggleMenu('eoffice')" id="btn-eoffice" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-envelope-open-text w-5 text-center mr-3"></i>
                        <span>E-Office</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                </button>
                <div class="submenu pl-10 space-y-1 mt-1" id="submenu-eoffice">
                    <a href="{{ route('admin.e-office.surat-masuk.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.e-office.surat-masuk.*')) active-page @endif">Surat Masuk</a>
                    <a href="{{ route('admin.e-office.surat-keluar.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.e-office.surat-keluar.*')) active-page @endif">Surat Keluar</a>
                </div>
            </div>

            <a href="{{ route('admin.settings') }}" class="sidebar-link flex items-center px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors @if(Request::routeIs('admin.settings')) active @endif">
                <i class="fas fa-cog w-5 text-center mr-3"></i>
                <span>Pengaturan Sistem</span>
            </a>
            @endhasanyrole

            {{-- WILAYAH PELAYANAN --}}
            @hasanyrole('Super Admin|Admin Klasis|Admin Jemaat')
            <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider">Struktur & Wilayah</div>
            
            <div class="menu-group">
                <button onclick="toggleMenu('master-wilayah')" id="btn-master-wilayah" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-database w-5 text-center mr-3"></i>
                        <span>Data Master</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                </button>
                <div class="submenu pl-10 space-y-1 mt-1" id="submenu-master-wilayah">
                    @can('view klasis')
                    <a href="{{ route('admin.klasis.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.klasis.*')) active-page @endif">Klasis</a>
                    @endcan
                    @can('view jemaat')
                    <a href="{{ route('admin.jemaat.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.jemaat.*')) active-page @endif">Jemaat</a>
                    @endcan
                    @can('view anggota jemaat')
                    <a href="{{ route('admin.anggota-jemaat.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.anggota-jemaat.*')) active-page @endif">Data Anggota</a>
                    @endcan
                </div>
            </div>
            
            <div class="menu-group">
                <button onclick="toggleMenu('wadah-wilayah')" id="btn-wadah-wilayah" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-users w-5 text-center mr-3"></i>
                        <span>Kategorial</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                </button>
                <div class="submenu pl-10 space-y-1 mt-1" id="submenu-wadah-wilayah">
                    <a href="{{ route('admin.wadah.statistik.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.wadah.statistik.*')) active-page @endif">Statistik</a>
                    <a href="{{ route('admin.wadah.pengurus.index') }}" class="sub-link block py-1.5 @if(Request::routeIs('admin.wadah.pengurus.*')) active-page @endif">Pengurus</a>
                </div>
            </div>
            @endhasanyrole

            {{-- USER MANAGEMENT --}}
            @role('Super Admin')
            <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider">Keamanan</div>
            <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors @if(Request::routeIs('admin.users.*')) active @endif">
                <i class="fas fa-users-cog w-5 text-center mr-3"></i>
                <span>User Manager</span>
            </a>
            @endrole

            {{-- LOGOUT --}}
            <div class="mt-8 pt-6 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3 py-2 text-red-400 hover:text-white hover:bg-red-900/30 rounded transition-colors">
                        <i class="fas fa-sign-out-alt w-5 text-center mr-3"></i>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    {{-- MAIN CONTENT AREA --}}
    <div class="flex-1 flex flex-col md:pl-64 min-h-screen">
        
        {{-- TOPBAR --}}
        <header class="bg-white h-16 flex items-center justify-between px-6 border-b border-slate-200 sticky top-0 z-40 shadow-sm">
            <div class="flex items-center">
                <button class="md:hidden text-slate-500 hover:text-brand-600 mr-4" id="hamburger-btn">
                     <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-sm font-bold text-slate-800 uppercase tracking-widest">@yield('header-title', 'Dashboard')</h1>
            </div>
            
            <div class="flex items-center gap-4">
                 @auth
                 <div class="text-right hidden sm:block">
                     <div class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</div>
                     <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                        {{ Auth::user()->roles->pluck('name')->first() }}
                     </div>
                 </div>
                 @endauth
                 <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                    <i class="fas fa-user-circle text-2xl"></i>
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 p-6 lg:p-8">
            {{-- Alert Notifications --}}
            @if (session('success'))
                 <div class="mb-6 bg-white border-l-4 border-green-500 p-4 rounded shadow-sm flex items-center justify-between transform transition-all duration-500"> 
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                        <div><p class="font-bold text-sm text-slate-800 uppercase tracking-tighter">Berhasil</p><p class="text-xs text-slate-500">{{ session('success') }}</p></div>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-slate-300 hover:text-slate-600"><i class="fas fa-times text-xs"></i></button>
                </div>
            @endif
            
            @yield('content')
        </main>

        {{-- FOOTER --}}
        <footer class="bg-white border-t border-slate-200 py-3 px-8 text-right">
             <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">&copy; {{ date('Y') }} Sinode Gereja Protestan Indonesia di Papua</span>
        </footer>
    </div>

    {{-- SCRIPTS --}}
    <script>
        // Sidebar Submenu Logic
        function toggleMenu(id) {
            const submenu = document.getElementById(`submenu-${id}`);
            const btn = document.getElementById(`btn-${id}`);
            const isOpened = submenu.classList.contains('open');

            if (isOpened) {
                submenu.classList.remove('open');
                btn.classList.remove('active');
                localStorage.setItem(`menu-${id}`, 'closed');
            } else {
                submenu.classList.add('open');
                btn.classList.add('active');
                localStorage.setItem(`menu-${id}`, 'open');
            }
        }
        
        document.addEventListener("DOMContentLoaded", function() {
            // Restore State from LocalStorage
            const menuIds = ['sakramen', 'tata', 'keuangan', 'hris', 'web', 'eoffice', 'master-wilayah', 'wadah-wilayah'];
            menuIds.forEach(id => {
                const state = localStorage.getItem(`menu-${id}`);
                const submenu = document.getElementById(`submenu-${id}`);
                const btn = document.getElementById(`btn-${id}`);

                // Cek apakah submenu mengandung link yang sedang aktif
                const hasActivePage = submenu ? submenu.querySelector('.active-page') : null;

                if ((state === 'open' || hasActivePage) && submenu) {
                    submenu.classList.add('open');
                    if(btn) btn.classList.add('active');
                }
            });
        });

        // Mobile Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const hamburgerBtn = document.getElementById('hamburger-btn');
        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>