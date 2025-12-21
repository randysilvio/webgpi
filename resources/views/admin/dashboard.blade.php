@extends('admin.layout')

@section('title', 'Dashboard Utama')
@section('header-title', 'Ringkasan Eksekutif')

@section('content')
    {{-- Header Sambutan & Waktu --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h2>
            <p class="text-gray-600 mt-1">
                Akses Level:
                @forelse (Auth::user()->getRoleNames() as $role)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 mr-1 uppercase">
                        {{ $role }}
                    </span>
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

    {{-- 1. GRID STATISTIK UTAMA (4 KOLOM) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Statistik Anggota --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border-b-4 border-blue-600 flex items-center justify-between transition hover:shadow-lg">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Anggota</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['anggota'] ?? 0) }}</p>
                <p class="text-xs text-blue-600 mt-1 font-semibold italic">Jiwa Aktif</p>
            </div>
            <div class="p-4 rounded-xl bg-blue-50 text-blue-600"><i class="fas fa-users fa-2x"></i></div>
        </div>

        {{-- Statistik Jemaat --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border-b-4 border-green-600 flex items-center justify-between transition hover:shadow-lg">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Jemaat</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['jemaat'] ?? 0) }}</p>
                <p class="text-xs text-green-600 mt-1 font-semibold italic">Gereja / Pos PI</p>
            </div>
            <div class="p-4 rounded-xl bg-green-50 text-green-600"><i class="fas fa-church fa-2x"></i></div>
        </div>

        {{-- Statistik Pegawai --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border-b-4 border-orange-500 flex items-center justify-between transition hover:shadow-lg">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pegawai</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['pendeta'] ?? 0) }}</p>
                <p class="text-xs text-orange-600 mt-1 font-semibold italic">Organik & Kontrak</p>
            </div>
            <div class="p-4 rounded-xl bg-orange-50 text-orange-600"><i class="fas fa-user-tie fa-2x"></i></div>
        </div>

        {{-- Statistik Aset (FASE 7 Baru) --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border-b-4 border-purple-600 flex items-center justify-between transition hover:shadow-lg">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Inventaris Aset</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['aset'] ?? 0) }}</p>
                <p class="text-xs text-purple-600 mt-1 font-semibold italic">Harta Milik Gereja</p>
            </div>
            <div class="p-4 rounded-xl bg-purple-50 text-purple-600"><i class="fas fa-boxes fa-2x"></i></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- 2. KOLOM KIRI: RINGKASAN KEUANGAN & GRAFIK --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Panel Keuangan Induk (RAPB Fase 7) --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line text-primary mr-2"></i> Rencana vs Realisasi APB ({{ date('Y') }})
                    </h3>
                    <a href="{{ route('admin.perbendaharaan.anggaran.index') }}" class="text-xs font-bold text-blue-600 hover:underline">LIHAT DETAIL RAPB</a>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Progress Pendapatan --}}
                        <div>
                            @php $p_pendapatan = ($stats['keuangan_target'] > 0) ? ($stats['keuangan_realisasi'] / $stats['keuangan_target'] * 100) : 0; @endphp
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-sm font-bold text-gray-600 uppercase">Target Pendapatan</span>
                                <span class="text-sm font-extrabold text-green-600">{{ round($p_pendapatan, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                                <div class="bg-green-500 h-4 rounded-full transition-all duration-1000" style="width: {{ min($p_pendapatan, 100) }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Target: Rp {{ number_format($stats['keuangan_target'] ?? 0, 0, ',', '.') }}</span>
                                <span class="font-bold">Rp {{ number_format($stats['keuangan_realisasi'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Info Saldo Kas --}}
                        <div class="bg-blue-900 rounded-xl p-6 text-white shadow-inner flex flex-col justify-center">
                            <p class="text-xs font-bold text-blue-300 uppercase tracking-widest mb-2">Saldo Kas Riil (BKU)</p>
                            <p class="text-3xl font-black">Rp {{ number_format(($stats['keuangan_realisasi'] ?? 0), 0, ',', '.') }}</p>
                            <div class="mt-4 pt-4 border-t border-blue-800">
                                <a href="{{ route('admin.perbendaharaan.transaksi.index') }}" class="text-xs font-bold text-blue-200 hover:text-white transition">KELOLA BUKU KAS &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik Arus Kas (Menggunakan Chart.js) --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6">Tren Arus Kas Bulanan</h3>
                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                     <p class="text-gray-400 italic text-sm"><i class="fas fa-chart-area mr-1"></i> Data grafik akan muncul setelah terdapat transaksi bulanan.</p>
                </div>
            </div>
        </div>

        {{-- 3. KOLOM KANAN: PERINGATAN & AKSI CEPAT --}}
        <div class="space-y-8">
            
            {{-- WIDGET 1: PERINGATAN PENSIUN (FASE 6) --}}
            @if(isset($pensiunAkanDatang) && $pensiunAkanDatang->isNotEmpty())
            <div class="bg-red-50 rounded-xl shadow-sm p-5 border border-red-200">
                <h3 class="text-md font-bold text-red-800 mb-4 flex items-center">
                    <i class="fas fa-clock mr-2"></i> Peringatan Pensiun (< 1 Thn)
                </h3>
                <div class="space-y-3">
                    @foreach($pensiunAkanDatang as $p)
                        <a href="{{ route('admin.kepegawaian.pegawai.show', $p->id) }}" class="flex justify-between items-start bg-white p-3 rounded-lg border border-red-100 hover:shadow-md transition group">
                            <div>
                                <span class="text-sm font-bold text-gray-800 group-hover:text-blue-600 block">{{ $p->nama_lengkap }}</span>
                                <span class="text-[10px] text-gray-500 uppercase font-bold">{{ $p->jenis_pegawai }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-black text-red-600 block uppercase">{{ \Carbon\Carbon::parse($p->tanggal_pensiun)->diffForHumans() }}</span>
                                <span class="text-[9px] text-gray-400">{{ \Carbon\Carbon::parse($p->tanggal_pensiun)->format('d/m/Y') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- WIDGET 2: AKSI CEPAT --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-800 text-white">
                    <h3 class="text-md font-bold flex items-center"><i class="fas fa-bolt text-yellow-400 mr-2"></i> Akses Cepat</h3>
                </div>
                <div class="p-4 space-y-3">
                    <a href="{{ route('admin.anggota-jemaat.create') }}" class="flex items-center p-3 rounded-lg border border-gray-100 hover:bg-blue-50 hover:border-blue-300 transition group">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-4 group-hover:bg-blue-600 group-hover:text-white"><i class="fas fa-user-plus"></i></div>
                        <div><p class="text-sm font-bold text-gray-800">Sensus Baru</p><p class="text-[10px] text-gray-500">Input anggota jemaat</p></div>
                    </a>

                    <a href="{{ route('admin.perbendaharaan.transaksi.create') }}" class="flex items-center p-3 rounded-lg border border-gray-100 hover:bg-green-50 hover:border-green-300 transition group">
                        <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-4 group-hover:bg-green-600 group-hover:text-white"><i class="fas fa-hand-holding-usd"></i></div>
                        <div><p class="text-sm font-bold text-gray-800">Catat Kas (BKU)</p><p class="text-[10px] text-gray-500">Input transaksi harian</p></div>
                    </a>

                    <a href="{{ route('admin.perbendaharaan.aset.create') }}" class="flex items-center p-3 rounded-lg border border-gray-100 hover:bg-orange-50 hover:border-orange-300 transition group">
                        <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center mr-4 group-hover:bg-orange-600 group-hover:text-white"><i class="fas fa-plus-square"></i></div>
                        <div><p class="text-sm font-bold text-gray-800">Tambah Aset</p><p class="text-[10px] text-gray-500">Registrasi inventaris</p></div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- INFO SISTEM --}}
    <div class="mt-12 bg-gray-900 rounded-2xl p-8 text-white flex flex-col md:flex-row items-center justify-between shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-church fa-8x"></i></div>
        <div class="z-10 text-center md:text-left">
            <h4 class="text-xl font-black tracking-tight">SINODE GPI PAPUA</h4>
            <p class="text-blue-300 text-sm mt-1 font-medium">Sistem Informasi Manajemen Terintegrasi | Versi 1.5.0 (Treasury Edition)</p>
        </div>
        <div class="flex space-x-4 mt-6 md:mt-0 z-10">
            <a href="{{ route('home') }}" target="_blank" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-600 rounded-xl text-sm font-bold transition shadow-lg">Lihat Website</a>
            <button class="px-5 py-2.5 bg-white text-gray-900 hover:bg-gray-100 rounded-xl text-sm font-bold transition shadow-lg">Bantuan Teknis</button>
        </div>
    </div>
@endsection