@extends('layouts.app')

@section('header-title', 'Overview & Statistik')

@section('content')

{{-- 1. KPI CARDS (Corporate Style) --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    {{-- Card Klasis --}}
    @if(isset($stats['klasis']))
    <div class="bg-white rounded border border-slate-200 p-5 shadow-sm">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Klasis</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-1">{{ number_format($stats['klasis']) }}</h3>
            </div>
            <span class="text-slate-300 bg-slate-50 p-2 rounded"><i class="fas fa-map text-lg"></i></span>
        </div>
    </div>
    @endif

    {{-- Card Jemaat --}}
    @if(isset($stats['jemaat']))
    <div class="bg-white rounded border border-slate-200 p-5 shadow-sm">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Jemaat</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-1">{{ number_format($stats['jemaat']) }}</h3>
            </div>
            <span class="text-slate-300 bg-slate-50 p-2 rounded"><i class="fas fa-church text-lg"></i></span>
        </div>
    </div>
    @endif

    {{-- Card Anggota --}}
    @if(isset($stats['anggota']))
    <div class="bg-white rounded border border-slate-200 p-5 shadow-sm">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Jiwa</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-1">{{ number_format($stats['anggota']) }}</h3>
            </div>
            <span class="text-slate-300 bg-slate-50 p-2 rounded"><i class="fas fa-users text-lg"></i></span>
        </div>
    </div>
    @endif

    {{-- Card Pegawai --}}
    @if(isset($stats['pendeta']))
    <div class="bg-white rounded border border-slate-200 p-5 shadow-sm">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pegawai Organik</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-1">{{ number_format($stats['pendeta']) }}</h3>
            </div>
            <span class="text-slate-300 bg-slate-50 p-2 rounded"><i class="fas fa-user-tie text-lg"></i></span>
        </div>
    </div>
    @endif
</div>

{{-- 2. QUICK ACCESS / PINTASAN (Outline Style) --}}
<div class="mb-8">
    <div class="flex flex-wrap gap-3">
        
        {{-- JEMAAT / KLASIS --}}
        @can('create anggota jemaat')
        <a href="{{ route('admin.anggota-jemaat.create') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 rounded hover:bg-slate-50 text-sm font-medium transition shadow-sm flex items-center">
            <i class="fas fa-plus mr-2 text-slate-400"></i> Data Jemaat Baru
        </a>
        @endcan

        {{-- KEUANGAN --}}
        @can('manage keuangan')
        <a href="{{ route('admin.perbendaharaan.transaksi.create') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 rounded hover:bg-slate-50 text-sm font-medium transition shadow-sm flex items-center">
            <i class="fas fa-file-invoice-dollar mr-2 text-slate-400"></i> Input Transaksi Kas
        </a>
        @endcan

        {{-- KEPEGAWAIAN --}}
        @can('manage pegawai')
        <a href="{{ route('admin.kepegawaian.pegawai.create') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 rounded hover:bg-slate-50 text-sm font-medium transition shadow-sm flex items-center">
            <i class="fas fa-user-plus mr-2 text-slate-400"></i> Tambah Pegawai
        </a>
        <a href="{{ route('admin.mutasi.index') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 rounded hover:bg-slate-50 text-sm font-medium transition shadow-sm flex items-center">
            <i class="fas fa-exchange-alt mr-2 text-slate-400"></i> Mutasi
        </a>
        @endcan

        {{-- E-OFFICE --}}
        <a href="{{ route('admin.e-office.surat-masuk.create') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 rounded hover:bg-slate-50 text-sm font-medium transition shadow-sm flex items-center">
            <i class="fas fa-envelope mr-2 text-slate-400"></i> Catat Surat Masuk
        </a>
    </div>
</div>

{{-- 3. WIDGETS --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Peta --}}
    <div class="lg:col-span-2 bg-white rounded border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wide">Peta Sebaran Pelayanan</h3>
            <a href="{{ route('admin.dashboard.peta_widget') }}" class="text-xs text-blue-600 hover:underline">Mode Penuh</a>
        </div>
        <div class="h-80 w-full relative bg-slate-100">
            <iframe src="{{ route('admin.dashboard.peta_widget') }}" class="w-full h-full border-0 absolute inset-0"></iframe>
        </div>
    </div>

    {{-- Pensiun --}}
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wide">Jadwal Pensiun (1 Thn)</h3>
        </div>
        <div class="p-0">
            @if(isset($pensiunAkanDatang) && $pensiunAkanDatang->count() > 0)
                <ul class="divide-y divide-slate-100">
                    @foreach($pensiunAkanDatang as $p)
                    <li class="px-5 py-3 hover:bg-slate-50 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-bold text-slate-700">{{ $p->nama_lengkap ?? $p->nama }}</p>
                                <p class="text-xs text-slate-500">{{ $p->jabatan ?? 'Pegawai' }}</p>
                            </div>
                            <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded border border-red-100">
                                {{ \Carbon\Carbon::parse($p->tanggal_pensiun)->format('M Y') }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-12 text-slate-400 text-sm">
                    <i class="fas fa-check-circle mb-2 text-slate-300 text-2xl block"></i>
                    Tidak ada pensiun dalam waktu dekat.
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL POPUP (Formal) --}}
@if(isset($activePopup) && $activePopup)
<div id="info-popup" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/80 backdrop-blur-sm transition-opacity duration-300 p-4">
    <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg shadow-2xl transition-all scale-95 opacity-0" id="popup-content">
        
        <button onclick="closePopup()" class="absolute top-2 right-2 z-50 flex h-8 w-8 items-center justify-center rounded bg-white/20 text-white hover:bg-white/40 transition backdrop-blur-md">
            <i class="fas fa-times"></i>
        </button>

        <div class="flex justify-center bg-transparent">
             <img src="{{ asset('storage/' . $activePopup->gambar_path) }}" alt="Info Popup" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-xl">
        </div>
        
        <div class="bg-white/90 backdrop-blur-sm p-3 absolute bottom-4 left-1/2 transform -translate-x-1/2 rounded-full px-6 shadow-lg border border-white/50">
            <p class="text-xs font-medium text-slate-700 uppercase tracking-wide">{{ $activePopup->judul }}</p>
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