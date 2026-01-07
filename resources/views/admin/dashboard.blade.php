@extends('admin.layout')

@section('title', 'Dashboard Utama')
@section('header-title', 'Ringkasan Eksekutif')

@section('content')
    {{-- HEADER SAMBUTAN --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h2>
            <p class="text-gray-600 mt-1">
                Akses Level:
                @forelse (Auth::user()->getRoleNames() as $role)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 mr-1 uppercase">{{ $role }}</span>
                @empty
                    <span class="text-gray-500 italic">Guest</span>
                @endforelse
                
                @if (Auth::user()->klasisTugas)
                    <span class="text-gray-400 mx-2">|</span>
                    <span class="text-sm text-gray-600 font-medium"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> {{ Auth::user()->klasisTugas->nama_klasis }}</span>
                @endif
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <span class="inline-flex items-center px-4 py-2 bg-white rounded-lg shadow-sm border border-gray-200 text-sm font-bold text-gray-700">
                <i class="far fa-calendar-alt text-primary mr-2"></i> {{ now()->isoFormat('dddd, D MMMM Y') }}
            </span>
        </div>
    </div>

    {{-- 1. GRID STATISTIK UTAMA --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Kartu Anggota --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border-b-4 border-blue-600 flex items-center justify-between transition hover:shadow-lg">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Anggota</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['anggota'] ?? 0) }}</p>
                <p class="text-xs text-blue-600 mt-1 font-semibold italic">Jiwa Aktif</p>
            </div>
            <div class="p-4 rounded-xl bg-blue-50 text-blue-600"><i class="fas fa-users fa-2x"></i></div>
        </div>
        
        {{-- Kartu Jemaat --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border-b-4 border-green-600 flex items-center justify-between transition hover:shadow-lg">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Jemaat</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['jemaat'] ?? 0) }}</p>
                <p class="text-xs text-green-600 mt-1 font-semibold italic">Gereja / Pos PI</p>
            </div>
            <div class="p-4 rounded-xl bg-green-50 text-green-600"><i class="fas fa-church fa-2x"></i></div>
        </div>

        {{-- Kartu Pegawai --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border-b-4 border-orange-500 flex items-center justify-between transition hover:shadow-lg">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pegawai</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['pendeta'] ?? 0) }}</p>
                <p class="text-xs text-orange-600 mt-1 font-semibold italic">Organik & Kontrak</p>
            </div>
            <div class="p-4 rounded-xl bg-orange-50 text-orange-600"><i class="fas fa-user-tie fa-2x"></i></div>
        </div>

        {{-- Kartu Aset --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border-b-4 border-purple-600 flex items-center justify-between transition hover:shadow-lg">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Inventaris Aset</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['aset'] ?? 0) }}</p>
                <p class="text-xs text-purple-600 mt-1 font-semibold italic">Harta Milik Gereja</p>
            </div>
            <div class="p-4 rounded-xl bg-purple-50 text-purple-600"><i class="fas fa-boxes fa-2x"></i></div>
        </div>
    </div>

    {{-- 2. GRID WIDGET (PETA & KEUANGAN) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        {{-- WIDGET PETA PELAYANAN (IFRAME) --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">
                    <i class="fas fa-map text-blue-600 mr-1"></i> Peta Pelayanan
                </h3>
                <span class="text-[10px] text-gray-400 italic">Klik tombol cetak di dalam peta</span>
            </div>
            
            {{-- KOTAK PETA (ISOLASI) --}}
            <div class="w-full overflow-hidden rounded-lg border bg-gray-100" style="height: 300px;">
                <iframe src="{{ route('admin.dashboard.peta_widget') }}" 
                        width="100%" 
                        height="100%" 
                        frameborder="0" 
                        scrolling="no"
                        title="Peta Pelayanan">
                </iframe>
            </div>
        </div>

        {{-- WIDGET KEUANGAN & AKSES --}}
        <div class="lg:col-span-2 space-y-6">
             {{-- Ringkasan Keuangan --}}
             <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line text-primary mr-2"></i> Rencana vs Realisasi APB ({{ date('Y') }})
                    </h3>
                    <a href="{{ route('admin.perbendaharaan.anggaran.index') }}" class="text-xs font-bold text-blue-600 hover:underline">DETAIL</a>
                </div>
                <div class="p-6 grid grid-cols-2 gap-6">
                    {{-- Progress Pendapatan --}}
                    <div>
                        @php 
                            $target = $stats['keuangan_target'] ?? 0;
                            $realisasi = $stats['keuangan_realisasi'] ?? 0;
                            $persen = ($target > 0) ? ($realisasi / $target * 100) : 0;
                        @endphp
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-xs font-bold text-gray-500 uppercase">Realisasi Pendapatan</span>
                            <span class="text-xs font-extrabold text-green-600">{{ round($persen, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ min($persen, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500">Target: Rp {{ number_format($target) }}</p>
                    </div>

                    {{-- Saldo Kas --}}
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 text-center">
                        <span class="text-xs font-bold text-blue-400 uppercase tracking-widest block mb-1">Saldo Kas Riil</span>
                        <p class="text-2xl font-black text-blue-800">Rp {{ number_format(($stats['saldo_kas'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                </div>
             </div>

             {{-- Akses Cepat --}}
             <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.anggota-jemaat.create') }}" class="flex items-center p-4 bg-white border rounded-xl hover:shadow-md transition group">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3"><i class="fas fa-user-plus"></i></div>
                    <div><span class="text-sm font-bold text-gray-700">Sensus Baru</span></div>
                </a>
                <a href="{{ route('admin.perbendaharaan.transaksi.create') }}" class="flex items-center p-4 bg-white border rounded-xl hover:shadow-md transition group">
                    <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3"><i class="fas fa-cash-register"></i></div>
                    <div><span class="text-sm font-bold text-gray-700">Input Kas</span></div>
                </a>
             </div>
        </div>
    </div>

    {{-- FOOTER INFO --}}
    <div class="mt-8 bg-gray-900 rounded-2xl p-6 text-white flex flex-col md:flex-row items-center justify-between shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-church fa-6x"></i></div>
        <div class="z-10 text-center md:text-left">
            <h4 class="text-lg font-black tracking-tight">SINODE GPI PAPUA</h4>
            <p class="text-blue-300 text-xs mt-1">Sistem Informasi Manajemen Terintegrasi | Versi 1.5.0</p>
        </div>
    </div>
@endsection