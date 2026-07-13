<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- PWA META TAGS --}}
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('gpi_logo.png') }}">
    
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Sinode GPI Papua') }}</title>
    
    {{-- Favicon --}}
    @php
        $appSetting = \App\Models\Setting::first();
        $faviconUrl = ($appSetting && $appSetting->logo_path) ? \Illuminate\Support\Facades\Storage::url($appSetting->logo_path) : asset('gpi_logo.png');
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}" type="image/png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script> 
        tailwind.config = { 
            theme: { 
                extend: { 
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { brand: { 50: '#f0f9ff', 600: '#0284c7', 800: '#075985', 900: '#0c4a6e' } }
                } 
            } 
        } 
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #1f2937; }
        ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 2px; }
        .submenu { max-height: 0; overflow: hidden; transition: max-height 0.4s ease-in-out, opacity 0.4s ease-in-out; opacity: 0; }
        .submenu.open { max-height: 1000px; opacity: 1; }
        .rotate-icon { transition: transform 0.3s ease; }
        .menu-btn.active .rotate-icon { transform: rotate(180deg); }
        .sidebar-link.active { background-color: rgba(255,255,255,0.08); border-left: 3px solid #38bdf8; color: white; }
        .menu-btn.active { color: white; background-color: #1e293b; }
        .sub-link:hover { color: #e0f2fe; transform: translateX(4px); transition: transform 0.2s; }
        .sub-link.active-page { color: #38bdf8; font-weight: 600; }
        input, select, textarea { border-width: 1px !important; }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col antialiased text-slate-800 bg-slate-50 font-sans">

    {{-- PITA PERINGATAN MODE MENYAMAR --}}
    @if(session()->has('impersonate_by'))
        <div class="bg-red-600 text-white px-4 py-2 text-center text-sm font-bold shadow-md z-[9999] relative flex flex-col sm:flex-row justify-center items-center gap-3 w-full">
            <span><i class="fas fa-user-secret mr-2 animate-pulse"></i> MODE PENYAMARAN: Anda beroperasi secara virtual sebagai {{ Auth::user()->name }}.</span>
            <a href="{{ route('admin.users.stop_impersonate') }}" class="bg-white text-red-700 px-4 py-1 rounded-full text-xs hover:bg-red-50 transition shadow-sm uppercase tracking-wider">
                Hentikan Penyamaran
            </a>
        </div>
    @endif

    <div class="flex-1 flex h-full overflow-hidden relative">
        {{-- SIDEBAR --}}
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-400 flex flex-col transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full shadow-2xl" id="sidebar">
            
            <div class="h-16 flex items-center px-5 border-b border-slate-800 bg-slate-950">
                 <div class="flex items-center gap-3">
                     @if($appSetting && $appSetting->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($appSetting->logo_path))
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($appSetting->logo_path) }}" alt="Logo App" class="w-9 h-9 object-contain drop-shadow-sm">
                     @else
                        <div class="w-9 h-9 bg-brand-600 rounded flex items-center justify-center text-white font-black text-xs shadow-md">GPI</div>
                     @endif
                     <div class="flex flex-col overflow-hidden">
                        <span class="text-white text-sm font-bold tracking-tight uppercase leading-none truncate block w-40">
                            {{ $appSetting->site_name ?? 'SIM-GPI' }}
                        </span>
                        <span class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-1 truncate">
                            {{ $appSetting->site_tagline ?? 'SINODE GPI PAPUA' }}
                        </span>
                     </div>
                </div>
            </div>

            <nav id="sidebar-nav" class="flex-1 px-3 py-6 space-y-1 overflow-y-auto text-sm">
                
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded hover:bg-slate-800 hover:text-white transition-colors mb-4 @if(Request::routeIs('admin.dashboard')) active @endif">
                    <i class="fas fa-chart-line w-5 text-center mr-3"></i>
                    <span class="font-medium">Dashboard Utama</span>
                </a>

                {{-- PORTAL PELAYANAN (JURNAL & LITURGI) --}}
                @hasanyrole('Super Admin|Admin Bidang 1|Pendeta|Admin Klasis')
                <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider">Portal Pelayanan</div>
                
                <div class="menu-group">
                    <button onclick="toggleMenu('portal-pelayanan')" id="btn-portal-pelayanan" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                        <div class="flex items-center"><i class="fas fa-cross w-5 text-center mr-3"></i><span class="truncate">Pastoral & Liturgi</span></div>
                        <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                    </button>
                    <div class="submenu pl-10 space-y-1 mt-1" id="submenu-portal-pelayanan">
                        
                        @hasanyrole('Super Admin|Admin Bidang 1|Pendeta|Admin Klasis')
                        <a href="{{ route('admin.jurnal.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.jurnal.*')) active-page @endif">Jurnal Pastoral</a>
                        @endhasanyrole

                        @hasanyrole('Super Admin|Admin Bidang 1')
                        <a href="{{ route('admin.bursa.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.bursa.*') && !Request::routeIs('admin.bursa.transaksi.*')) active-page @endif">Katalog Tata Ibadah</a>
                        <a href="{{ route('admin.bursa.transaksi.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.bursa.transaksi.*')) active-page @endif">Otorisasi Unduhan</a>
                        @endhasanyrole
                        
                        @role('Pendeta')
                        <a href="{{ route('admin.bursa.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.bursa.index')) active-page @endif">Katalog Tata Ibadah</a>
                        <a href="{{ route('admin.bursa.transaksi.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.bursa.transaksi.*')) active-page @endif">Riwayat Otorisasi</a>
                        @endrole

                    </div>
                </div>
                @endhasanyrole

                {{-- BIDANG 1: PELAYANAN & PENDIDIKAN --}}
                @if(!$appSetting || $appSetting->hasModuleAccess('bidang1_sakramen') || $appSetting->hasModuleAccess('bidang1_tata'))
                <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider truncate">Bidang 1: Pelayanan & Pendidikan</div>
                
                    @if(!$appSetting || $appSetting->hasModuleAccess('bidang1_sakramen'))
                    <div class="menu-group">
                        <button onclick="toggleMenu('sakramen')" id="btn-sakramen" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                            <div class="flex items-center"><i class="fas fa-hand-holding-water w-5 text-center mr-3"></i><span class="truncate">Pelayanan Sakramen</span></div>
                            <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                        </button>
                        <div class="submenu pl-10 space-y-1 mt-1" id="submenu-sakramen">
                            <a href="{{ route('admin.sakramen.baptis.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.sakramen.baptis.*')) active-page @endif">Daftar Baptisan</a>
                            <a href="{{ route('admin.sakramen.sidi.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.sakramen.sidi.*')) active-page @endif">Daftar Sidi</a>
                            <a href="{{ route('admin.sakramen.nikah.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.sakramen.nikah.*')) active-page @endif">Daftar Pernikahan</a>
                        </div>
                    </div>
                    @endif

                    @if(!$appSetting || $appSetting->hasModuleAccess('bidang1_tata'))
                    <div class="menu-group">
                        <button onclick="toggleMenu('tata')" id="btn-tata" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                            <div class="flex items-center"><i class="fas fa-gavel w-5 text-center mr-3"></i><span class="truncate">Tata Gereja</span></div>
                            <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                        </button>
                        <div class="submenu pl-10 space-y-1 mt-1" id="submenu-tata">
                            <a href="{{ route('admin.tata-gereja.pejabat.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.tata-gereja.pejabat.*')) active-page @endif">Pejabat Gerejawi</a>
                            <a href="{{ route('admin.tata-gereja.sidang.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.tata-gereja.sidang.*')) active-page @endif">Risalah Persidangan</a>
                        </div>
                    </div>
                    @endif
                @endif

                {{-- BIDANG 2: KEUANGAN & PEMBANGUNAN --}}
                @if(!$appSetting || $appSetting->hasModuleAccess('bidang2_keuangan'))
                <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider truncate">Bidang 2: Keuangan & Pembangunan</div>
                
                <div class="menu-group">
                    <button onclick="toggleMenu('keuangan')" id="btn-keuangan" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                        <div class="flex items-center"><i class="fas fa-wallet w-5 text-center mr-3"></i><span class="truncate">Perbendaharaan</span></div>
                        <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                    </button>
                    <div class="submenu pl-10 space-y-1 mt-1" id="submenu-keuangan">
                        <a href="{{ route('admin.perbendaharaan.transaksi.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.perbendaharaan.transaksi.*')) active-page @endif">Buku Kas Umum (BKU)</a>
                        <a href="{{ route('admin.perbendaharaan.anggaran.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.perbendaharaan.anggaran.*')) active-page @endif">Rencana APB</a>
                        <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.perbendaharaan.aset.*')) active-page @endif">Harta Milik Gereja</a>
                        <a href="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.perbendaharaan.mata-anggaran.*')) active-page @endif">Kode Akun (COA)</a>
                    </div>
                </div>
                @endif

                {{-- BIDANG 3: ORGANISASI --}}
                @if(!$appSetting || $appSetting->hasModuleAccess('bidang3_hris'))
                <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider truncate">Bidang 3: Organisasi</div>
                
                <div class="menu-group">
                    <button onclick="toggleMenu('hris')" id="btn-hris" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                        <div class="flex items-center"><i class="fas fa-id-card w-5 text-center mr-3"></i><span class="truncate">Kepegawaian</span></div>
                        <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                    </button>
                    <div class="submenu pl-10 space-y-1 mt-1" id="submenu-hris">
                        <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.kepegawaian.pegawai.*')) active-page @endif">Buku Induk Pegawai</a>
                        <a href="{{ route('admin.mutasi.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.mutasi.*')) active-page @endif">Mutasi & Riwayat SK</a>
                    </div>
                </div>
                @endif

                {{-- BIDANG 4: KOMINFO --}}
                @if(!$appSetting || $appSetting->hasModuleAccess('bidang4_popup') || $appSetting->hasModuleAccess('bidang4_berita') || $appSetting->hasModuleAccess('bidang4_eoffice'))
                <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider truncate">Bidang 4: KOMINFO</div>
                
                    @if(!$appSetting || $appSetting->hasModuleAccess('bidang4_popup') || $appSetting->hasModuleAccess('bidang4_berita'))
                    <div class="menu-group">
                        <button onclick="toggleMenu('web')" id="btn-web" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                            <div class="flex items-center"><i class="fas fa-globe w-5 text-center mr-3"></i><span class="truncate">Multi Media</span></div>
                            <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                        </button>
                        <div class="submenu pl-10 space-y-1 mt-1" id="submenu-web">
                            @if(!$appSetting || $appSetting->hasModuleAccess('bidang4_popup'))
                            <a href="{{ route('admin.popup.index') }}" class="sub-link block py-1.5 text-yellow-500 font-medium truncate @if(Request::routeIs('admin.popup.*')) active-page @endif">Pengumuman (Banner)</a>
                            @endif
                            @if(!$appSetting || $appSetting->hasModuleAccess('bidang4_berita'))
                            <a href="{{ route('admin.posts.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.posts.*')) active-page @endif">Dokumen Publikasi</a>
                            <a href="{{ route('admin.messages') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.messages*')) active-page @endif">Pesan Masuk</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(!$appSetting || $appSetting->hasModuleAccess('bidang4_eoffice'))
                    <div class="menu-group">
                        <button onclick="toggleMenu('eoffice')" id="btn-eoffice" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                            <div class="flex items-center"><i class="fas fa-envelope-open-text w-5 text-center mr-3"></i><span class="truncate">Tata Persuratan</span></div>
                            <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                        </button>
                        <div class="submenu pl-10 space-y-1 mt-1" id="submenu-eoffice">
                            <a href="{{ route('admin.e-office.surat-masuk.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.e-office.surat-masuk.*')) active-page @endif">Agenda Surat Masuk</a>
                            <a href="{{ route('admin.e-office.surat-keluar.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.e-office.surat-keluar.*')) active-page @endif">Agenda Surat Keluar</a>
                        </div>
                    </div>
                    @endif
                @endif

                {{-- STRUKTUR WILAYAH --}}
                @if(!$appSetting || $appSetting->hasModuleAccess('wilayah_master') || $appSetting->hasModuleAccess('wilayah_wadah'))
                <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider truncate">Struktur & Wilayah</div>
                
                    @if(!$appSetting || $appSetting->hasModuleAccess('wilayah_master'))
                    <div class="menu-group">
                        <button onclick="toggleMenu('master-wilayah')" id="btn-master-wilayah" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                            <div class="flex items-center"><i class="fas fa-database w-5 text-center mr-3"></i><span class="truncate">Pangkalan Data</span></div>
                            <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                        </button>
                        <div class="submenu pl-10 space-y-1 mt-1" id="submenu-master-wilayah">
                            <a href="{{ route('admin.klasis.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.klasis.*')) active-page @endif">Data Klasis</a>
                            <a href="{{ route('admin.jemaat.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.jemaat.*')) active-page @endif">Data Jemaat</a>
                            <a href="{{ route('admin.anggota-jemaat.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.anggota-jemaat.*')) active-page @endif">Daftar Induk Jemaat</a>
                        </div>
                    </div>
                    @endif

                    @if(!$appSetting || $appSetting->hasModuleAccess('wilayah_wadah'))
                    <div class="menu-group">
                        <button onclick="toggleMenu('wadah-wilayah')" id="btn-wadah-wilayah" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                            <div class="flex items-center"><i class="fas fa-users w-5 text-center mr-3"></i><span class="truncate">Pelayanan Kategorial</span></div>
                            <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                        </button>
                        <div class="submenu pl-10 space-y-1 mt-1" id="submenu-wadah-wilayah">
                            <a href="{{ route('admin.wadah.pengurus.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.wadah.pengurus.*')) active-page @endif">Badan Pengurus</a>
                            <a href="{{ route('admin.wadah.program.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.wadah.program.*')) active-page @endif">Program Pelayanan</a>
                            <a href="{{ route('admin.wadah.anggaran.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.wadah.anggaran.*') || Request::routeIs('admin.wadah.transaksi.*')) active-page @endif">APB Kategorial</a>
                        </div>
                    </div>
                    @endif
                @endif

                {{-- PELAPORAN & ANALISIS --}}
                @if(!$appSetting || $appSetting->hasModuleAccess('laporan_terpadu'))
                <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider truncate">Evaluasi Laporan</div>
                
                <div class="menu-group">
                    <button onclick="toggleMenu('pusat-laporan')" id="btn-pusat-laporan" class="menu-btn w-full flex items-center justify-between px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors">
                        <div class="flex items-center"><i class="fas fa-chart-bar w-5 text-center mr-3"></i><span class="truncate">Pusat Pelaporan</span></div>
                        <i class="fas fa-chevron-down text-[10px] rotate-icon"></i>
                    </button>
                    
                    <div class="submenu pl-10 space-y-1 mt-1" id="submenu-pusat-laporan">
                        <div class="py-1 text-[9px] text-slate-500 uppercase mt-1 font-extrabold tracking-wider border-b border-slate-700 pb-1 w-5/6">Rencana Strategis (RENSTRA)</div>
                        <a href="{{ route('admin.laporan.renstra.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.laporan.renstra.*')) active-page @endif">Laporan Renstra</a>
                        <a href="{{ route('admin.wadah.statistik.index') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.wadah.statistik.*')) active-page @endif">Statistik Kategorial</a>

                        @if(!$appSetting || $appSetting->hasModuleAccess('bidang2_keuangan'))
                        <div class="py-1 text-[9px] text-slate-500 uppercase mt-3 font-bold tracking-wider border-b border-slate-700 pb-1 w-5/6">Daya & Dana</div>
                        <a href="{{ route('admin.perbendaharaan.laporan.gabungan') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.perbendaharaan.laporan.gabungan')) active-page @endif">Laporan Perbendaharaan</a>
                        <a href="{{ route('admin.perbendaharaan.laporan.realisasi') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.perbendaharaan.laporan.realisasi')) active-page @endif">Realisasi APB</a>
                        <a href="{{ route('admin.perbendaharaan.laporan.aset') }}" class="sub-link block py-1.5 truncate @if(Request::routeIs('admin.perbendaharaan.laporan.aset')) active-page @endif">Laporan Harta Milik</a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- MENU KONFIGURASI SISTEM --}}
                @hasanyrole('Super Admin|Admin Bidang 4')
                <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider truncate">Konfigurasi</div>
                <a href="{{ route('admin.settings') }}" class="sidebar-link flex items-center px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors @if(Request::routeIs('admin.settings')) active @endif">
                    <i class="fas fa-cog w-5 text-center mr-3"></i>
                    <span class="truncate">Pengaturan Sistem</span>
                </a>
                @endhasanyrole

                {{-- USER MANAGEMENT --}}
                @role('Super Admin')
                <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase text-slate-600 tracking-wider truncate">Otoritas Akses</div>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center px-3 py-2 rounded hover:bg-slate-800 hover:text-white transition-colors @if(Request::routeIs('admin.users.*')) active @endif">
                    <i class="fas fa-users-cog w-5 text-center mr-3"></i>
                    <span class="truncate">Manajemen Pengguna</span>
                </a>
                @endrole

                {{-- LOGOUT --}}
                <div class="mt-8 pt-6 border-t border-slate-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-3 py-2 text-red-400 hover:text-white hover:bg-red-900/30 rounded transition-colors font-bold">
                            <i class="fas fa-power-off w-5 text-center mr-3"></i>
                            <span class="truncate">Akhiri Sesi (Log Out)</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        {{-- MAIN CONTENT AREA --}}
        <div class="flex-1 flex flex-col md:pl-64 min-w-0 overflow-y-auto">
            <header class="bg-white h-16 flex items-center justify-between px-6 border-b border-slate-200 sticky top-0 z-40 shadow-sm">
                <div class="flex items-center">
                    <button class="md:hidden text-slate-500 hover:text-brand-600 mr-4" id="hamburger-btn">
                         <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-sm font-bold text-slate-800 uppercase tracking-widest truncate">@yield('header-title', 'Pusat Kendali Administrasi')</h1>
                </div>
                
                <div class="flex items-center gap-4">
                     @auth
                     <div class="text-right hidden sm:block">
                         <div class="text-sm font-bold text-slate-800 uppercase tracking-wide truncate">{{ Auth::user()->name }}</div>
                         <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider truncate">
                            {{ Auth::user()->roles->pluck('name')->first() }}
                         </div>
                     </div>
                     @endauth
                     <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                        <i class="fas fa-user-circle text-2xl"></i>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6 lg:p-8">
                
                {{-- STATUS INDIKATOR OFFLINE DASAR --}}
                <div id="offline-indicator" class="hidden mb-6 bg-red-600 text-white p-3 rounded shadow-md flex items-center justify-center animate-pulse">
                    <i class="fas fa-wifi-slash mr-2"></i>
                    <span class="font-bold text-xs uppercase tracking-wider">Koneksi Terputus. Beberapa fitur dinonaktifkan sementara.</span>
                </div>

                {{-- INDIKATOR ANTREAN UNIVERSAL OFFLINE --}}
                <div id="universal-offline-badge" class="hidden mb-6 bg-orange-100 border border-orange-300 p-4 rounded shadow-sm items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-satellite-dish text-orange-600 text-2xl mr-3 animate-pulse"></i>
                        <div>
                            <h4 class="font-bold text-orange-900 text-xs uppercase tracking-widest" id="universal-offline-text">Sistem Menunggu Sinyal</h4>
                            <p class="text-[10px] text-orange-700 mt-1">Terdapat <b id="universal-offline-count">0</b> antrean aksi yang disimpan di perangkat ini dan akan diunggah otomatis saat internet stabil.</p>
                        </div>
                    </div>
                    <button onclick="syncAllOfflineData()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow transition">
                        Paksa Sinkronisasi
                    </button>
                </div>

                @if (session('success'))
                     <div class="mb-6 bg-white border-l-4 border-emerald-600 p-4 rounded shadow-sm flex items-center justify-between"> 
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-emerald-600 mr-3 text-lg"></i>
                            <div><p class="font-bold text-sm text-slate-800 uppercase tracking-wide">Tindakan Berhasil</p><p class="text-xs text-slate-500">{{ session('success') }}</p></div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-sm"></i></button>
                    </div>
                @endif
                @if (session('error'))
                     <div class="mb-6 bg-white border-l-4 border-red-600 p-4 rounded shadow-sm flex items-center justify-between"> 
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-3 text-lg animate-pulse"></i>
                            <div><p class="font-bold text-sm text-slate-800 uppercase tracking-wide">Peringatan Sistem</p><p class="text-xs text-slate-500">{{ session('error') }}</p></div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-sm"></i></button>
                    </div>
                @endif
                @if (session('warning'))
                     <div class="mb-6 bg-white border-l-4 border-yellow-500 p-4 rounded shadow-sm flex items-center justify-between"> 
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-yellow-500 mr-3 text-lg"></i>
                            <div><p class="font-bold text-sm text-slate-800 uppercase tracking-wide">Informasi Perhatian</p><p class="text-xs text-slate-500">{{ session('warning') }}</p></div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-sm"></i></button>
                    </div>
                @endif
                
                @yield('content')
            </main>

            <footer class="bg-white border-t border-slate-200 py-4 px-8 flex justify-between items-center text-slate-500">
                 <span class="text-[10px] font-bold uppercase tracking-widest">&copy; {{ date('Y') }} Sinode Gereja Protestan Indonesia di Papua</span>
                 <span class="text-[9px] uppercase tracking-widest">Sistem Informasi Manajemen Terpadu (SIM-GPI)</span>
            </footer>
        </div>
    </div>

    {{-- REGISTRASI SERVICE WORKER & NAVIGATION SCRIPTS --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('ServiceWorker terdaftar dengan sukses.');
                    })
                    .catch((error) => {
                        console.log('Pendaftaran ServiceWorker gagal:', error);
                    });
            });
        }

        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);

        function updateOnlineStatus() {
            const indicator = document.getElementById('offline-indicator');
            if (navigator.onLine) {
                indicator.classList.add('hidden');
            } else {
                indicator.classList.remove('hidden');
            }
        }
        updateOnlineStatus();

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
            const menuIds = ['portal-pelayanan', 'sakramen', 'tata', 'keuangan', 'hris', 'web', 'eoffice', 'master-wilayah', 'wadah-wilayah', 'pusat-laporan'];
            menuIds.forEach(id => {
                const state = localStorage.getItem(`menu-${id}`);
                const submenu = document.getElementById(`submenu-${id}`);
                const btn = document.getElementById(`btn-${id}`);
                const hasActivePage = submenu ? submenu.querySelector('.active-page') : null;

                if ((state === 'open' || hasActivePage) && submenu) {
                    submenu.classList.add('open');
                    if(btn) btn.classList.add('active');
                }
            });

            const sidebarNav = document.getElementById('sidebar-nav');
            if (sidebarNav) {
                const scrollPos = localStorage.getItem('sidebar-scroll-pos');
                if (scrollPos) {
                    setTimeout(() => { sidebarNav.scrollTop = parseInt(scrollPos, 10); }, 100);
                }
                window.addEventListener('beforeunload', function() {
                    localStorage.setItem('sidebar-scroll-pos', sidebarNav.scrollTop);
                });
            }
        });

        const sidebar = document.getElementById('sidebar');
        const hamburgerBtn = document.getElementById('hamburger-btn');
        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });
        }
    </script>

    {{-- SCRIPT MANAJEMEN OFFLINE UNIVERSAL --}}
    <script src="{{ asset('js/offlineManager.js') }}"></script>
    
    @stack('scripts')
</body>
</html>