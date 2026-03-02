@extends('layouts.app')

@section('title', 'Detail Jemaat')
@section('header-title', 'Detail Data Wilayah')

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Header Card --}}
    <div class="bg-white rounded-t-lg shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row items-center md:items-start gap-6">
        <div class="h-24 w-24 rounded bg-slate-100 flex items-center justify-center border border-slate-200 overflow-hidden shrink-0">
            @if($jemaat->foto_gereja_path)
                <img src="{{ Storage::url($jemaat->foto_gereja_path) }}" class="h-full w-full object-cover">
            @else
                <i class="fas fa-church text-4xl text-slate-300"></i>
            @endif
        </div>
        <div class="flex-1 text-center md:text-left">
            <h1 class="text-2xl font-bold text-slate-800 mb-1">{{ $jemaat->nama_jemaat }}</h1>
            <p class="text-slate-500 text-sm mb-3">
                <i class="fas fa-map-marker-alt mr-1"></i> {{ $jemaat->klasis->nama_klasis ?? 'Tanpa Klasis' }} 
                <span class="mx-2 text-slate-300">|</span> 
                Status: <strong>{{ $jemaat->status_jemaat }}</strong>
            </p>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-3">
                <div class="px-4 py-2 bg-slate-50 rounded border border-slate-200 text-center">
                    <span class="block text-[10px] uppercase text-slate-400 font-bold">Total KK</span>
                    <span class="text-xl font-bold text-slate-800">{{ number_format($jemaat->jumlah_kk ?? 0) }}</span>
                </div>
                <div class="px-4 py-2 bg-slate-50 rounded border border-slate-200 text-center">
                    <span class="block text-[10px] uppercase text-slate-400 font-bold">Total Jiwa</span>
                    <span class="text-xl font-bold text-slate-800">{{ number_format($jemaat->jumlah_total_jiwa ?? 0) }}</span>
                </div>
            </div>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('admin.jemaat.index') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-xs font-bold uppercase rounded hover:bg-slate-50 transition">Kembali</a>
            @can('edit jemaat')
            <a href="{{ route('admin.jemaat.edit', $jemaat->id) }}" class="px-4 py-2 bg-yellow-500 text-white text-xs font-bold uppercase rounded hover:bg-yellow-600 transition">Edit Data</a>
            @endcan
        </div>
    </div>

    {{-- Detail Grid --}}
    <div class="bg-white rounded-b-lg shadow-sm border-x border-b border-slate-200 p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        {{-- Info Dasar --}}
        <div class="space-y-4 text-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase border-b border-slate-100 pb-2">Informasi Umum</h3>
            <div class="grid grid-cols-3 gap-2">
                <span class="text-slate-500 text-xs font-medium">Kode</span>
                <span class="col-span-2 text-slate-800">{{ $jemaat->kode_jemaat ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="text-slate-500 text-xs font-medium">Tgl Berdiri</span>
                <span class="col-span-2 text-slate-800">{{ $jemaat->tanggal_berdiri ? $jemaat->tanggal_berdiri->isoFormat('D MMMM Y') : '-' }}</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="text-slate-500 text-xs font-medium">Alamat</span>
                <span class="col-span-2 text-slate-800 leading-snug">{{ $jemaat->alamat_gereja ?? '-' }}</span>
            </div>
        </div>

        {{-- Kontak --}}
        <div class="space-y-4 text-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase border-b border-slate-100 pb-2">Kontak</h3>
            <div class="grid grid-cols-3 gap-2">
                <span class="text-slate-500 text-xs font-medium">Telepon</span>
                <span class="col-span-2 text-slate-800">{{ $jemaat->telepon_kantor ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="text-slate-500 text-xs font-medium">Email</span>
                <span class="col-span-2 text-slate-800 truncate" title="{{ $jemaat->email_jemaat }}">{{ $jemaat->email_jemaat ?? '-' }}</span>
            </div>
        </div>

        {{-- Pendeta --}}
        <div class="bg-slate-50 p-4 rounded border border-slate-100 h-64 overflow-y-auto">
            <h3 class="text-xs font-bold text-slate-500 uppercase mb-3">Pendeta Bertugas</h3>
            @if($jemaat->pendetaDitempatkan->count() > 0)
                <ul class="space-y-2">
                    @foreach($jemaat->pendetaDitempatkan as $p)
                    <li class="bg-white p-2 rounded shadow-sm border border-slate-200 flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold">
                            {{ substr($p->nama_lengkap, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700">{{ $p->nama_lengkap }}</p>
                            <p class="text-[10px] text-slate-400">{{ $p->nip ?? 'Non-Organik' }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @else
                <p class="text-xs text-slate-400 italic text-center mt-10">Belum ada pendeta tercatat.</p>
            @endif
        </div>
    </div>
</div>
@endsection