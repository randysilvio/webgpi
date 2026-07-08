@extends('layouts.app')

@section('title', 'Pusat Kendali Administrasi')
@section('header-title', 'Tinjauan Eksekutif & Statistik')

@section('content')

{{-- 1. KARTU INDIKATOR KINERJA UTAMA (KPI) --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    {{-- Card Klasis --}}
    @if(isset($stats['klasis']))
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm border-l-4 border-l-blue-800">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Teritorial Klasis</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">{{ number_format($stats['klasis']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                <i class="fas fa-map text-lg"></i>
            </div>
        </div>
    </div>
    @endif

    {{-- Card Jemaat --}}
    @if(isset($stats['jemaat']))
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm border-l-4 border-l-gray-800">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Institusi Jemaat</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">{{ number_format($stats['jemaat']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                <i class="fas fa-church text-lg"></i>
            </div>
        </div>
    </div>
    @endif

    {{-- Card Anggota --}}
    @if(isset($stats['anggota']))
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm border-l-4 border-l-green-700">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Akumulasi Populasi Umat</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">{{ number_format($stats['anggota']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                <i class="fas fa-users text-lg"></i>
            </div>
        </div>
    </div>
    @endif

    {{-- Card Pegawai --}}
    @if(isset($stats['pendeta']))
    <div class="bg-gray-800 rounded border border-gray-900 p-5 shadow-sm relative overflow-hidden">
        <div class="flex justify-between items-start relative z-10">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Personel Aktif</p>
                <h3 class="text-2xl font-black text-white mt-1">{{ number_format($stats['pendeta']) }}</h3>
            </div>
        </div>
        <i class="fas fa-user-tie text-5xl text-gray-700 absolute -right-2 -bottom-2 opacity-50"></i>
    </div>
    @endif
</div>

{{-- 2. PINTASAN AKSI CEPAT (QUICK ACTIONS) --}}
<div class="bg-gray-50 border border-gray-200 p-4 rounded mb-8 shadow-sm">
    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-3"><i class="fas fa-bolt mr-1"></i> Panel Aksi Cepat Administratif</p>
    <div class="flex flex-wrap gap-2">
        @can('create anggota jemaat')
        <a href="{{ route('admin.anggota-jemaat.create') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded text-[11px] font-bold uppercase tracking-wide hover:bg-gray-100 hover:text-blue-800 transition shadow-sm flex items-center">
            <i class="fas fa-user-plus mr-2 text-gray-400"></i> Register Umat Baru
        </a>
        @endcan

        @can('manage keuangan')
        <a href="{{ route('admin.perbendaharaan.transaksi.create') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded text-[11px] font-bold uppercase tracking-wide hover:bg-gray-100 hover:text-green-700 transition shadow-sm flex items-center">
            <i class="fas fa-file-invoice-dollar mr-2 text-gray-400"></i> Catat Transaksi Kas
        </a>
        @endcan

        @can('manage pegawai')
        <a href="{{ route('admin.kepegawaian.pegawai.create') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded text-[11px] font-bold uppercase tracking-wide hover:bg-gray-100 hover:text-blue-800 transition shadow-sm flex items-center">
            <i class="fas fa-id-card mr-2 text-gray-400"></i> Registrasi Pegawai
        </a>
        <a href="{{ route('admin.mutasi.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded text-[11px] font-bold uppercase tracking-wide hover:bg-gray-100 hover:text-blue-800 transition shadow-sm flex items-center">
            <i class="fas fa-exchange-alt mr-2 text-gray-400"></i> Proses Mutasi SK
        </a>
        @endcan

        <a href="{{ route('admin.e-office.surat-masuk.create') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded text-[11px] font-bold uppercase tracking-wide hover:bg-gray-100 hover:text-orange-700 transition shadow-sm flex items-center">
            <i class="fas fa-envelope-open-text mr-2 text-gray-400"></i> Agenda Surat Masuk
        </a>
    </div>
</div>

{{-- 3. AREA PANEL UTAMA (PETA & PERINGATAN) --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Panel Peta Sebaran --}}
    <div class="lg:col-span-2 bg-white rounded border border-gray-300 shadow-sm overflow-hidden flex flex-col">
        <div class="px-5 py-3 border-b border-gray-200 bg-gray-100 flex justify-between items-center">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest"><i class="fas fa-globe-asia mr-2 text-gray-500"></i> Peta Demografi & Sebaran Pelayanan</h3>
            <a href="{{ route('admin.dashboard.peta_widget') }}" target="_blank" class="text-[10px] font-bold text-gray-500 hover:text-blue-800 bg-white border border-gray-300 px-2 py-1 rounded transition uppercase">Mode Penuh <i class="fas fa-external-link-alt ml-1"></i></a>
        </div>
        <div class="h-80 w-full relative bg-gray-50 flex-grow">
            <iframe src="{{ route('admin.dashboard.peta_widget') }}" class="w-full h-full border-0 absolute inset-0"></iframe>
        </div>
    </div>

    {{-- Panel Peringatan Pensiun --}}
    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden flex flex-col">
        <div class="px-5 py-3 border-b border-gray-200 bg-gray-100">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest"><i class="fas fa-user-clock mr-2 text-red-700"></i> Peringatan Batas Pensiun (1 Thn)</h3>
        </div>
        <div class="p-0 overflow-y-auto flex-grow max-h-80">
            @if(isset($pensiunAkanDatang) && $pensiunAkanDatang->count() > 0)
                <ul class="divide-y divide-gray-100">
                    @foreach($pensiunAkanDatang as $p)
                    <li class="px-5 py-4 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold text-gray-900 uppercase">{{ $p->nama_lengkap ?? $p->nama }}</p>
                                <p class="text-[10px] text-gray-500 font-mono mt-0.5">{{ $p->jabatan ?? 'Pegawai Organik' }}</p>
                            </div>
                            <span class="text-[9px] font-bold text-red-800 bg-red-50 px-2 py-1 rounded border border-red-200 uppercase tracking-widest">
                                B.A.T: {{ \Carbon\Carbon::parse($p->tanggal_pensiun)->format('M Y') }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-12 text-gray-500 text-sm">
                    <i class="fas fa-check-square mb-3 text-gray-300 text-3xl block"></i>
                    <p class="text-xs font-bold uppercase tracking-widest">Aman Terkendali</p>
                    <p class="text-[10px] mt-1">Tidak ada personel yang akan pensiun dalam waktu dekat.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL POPUP (INFORMASI PUBLIK / IKLAN) --}}
@if(isset($activePopup) && $activePopup)
<div id="info-popup" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-gray-900/80 backdrop-blur-sm transition-opacity duration-300 p-4">
    <div class="relative w-full max-w-4xl transform overflow-hidden rounded bg-transparent shadow-2xl transition-all scale-95 opacity-0" id="popup-content">
        
        <button onclick="closePopup()" class="absolute top-2 right-2 z-50 flex h-8 w-8 items-center justify-center rounded border border-white text-white hover:bg-white hover:text-black transition">
            <i class="fas fa-times"></i>
        </button>

        <div class="flex justify-center bg-transparent">
             <img src="{{ asset('storage/' . $activePopup->gambar_path) }}" alt="Info Popup" class="max-w-full max-h-[85vh] object-contain rounded border border-gray-600 shadow-xl">
        </div>
        
        <div class="bg-gray-900 border border-gray-700 p-2 absolute bottom-4 left-1/2 transform -translate-x-1/2 rounded px-6 shadow-lg">
            <p class="text-[10px] font-bold text-white uppercase tracking-widest"><i class="fas fa-info-circle mr-2 text-blue-400"></i> {{ $activePopup->judul }}</p>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const popupId = "{{ $activePopup->id }}";
        const hasSeen = sessionStorage.getItem("seen_popup_" + popupId);
        
        if (!hasSeen) {
            const popup = document.getElementById('info-popup');
            const content = document.getElementById('popup-content');
            
            popup.classList.remove('hidden');
            popup.classList.add('flex');
            
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 100);

            sessionStorage.setItem("seen_popup_" + popupId, "true");
        }
    });

    function closePopup() {
        const popup = document.getElementById('info-popup');
        const content = document.getElementById('popup-content');

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            popup.classList.add('hidden');
            popup.classList.remove('flex');
        }, 300);
    }

    document.getElementById('info-popup').addEventListener('click', function(e) {
        if (e.target === this) { closePopup(); }
    });
</script>
@endif

@endsection