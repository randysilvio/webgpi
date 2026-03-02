@extends('layouts.app')

@section('title', 'Detail Klasis')
@section('header-title', 'Detail Data Wilayah')

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="bg-white rounded-t-lg shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row items-center md:items-start gap-6">
        <div class="h-24 w-24 rounded bg-slate-100 flex items-center justify-center border border-slate-200 overflow-hidden">
            @if($klasis->foto_kantor_path)
                <img src="{{ Storage::url($klasis->foto_kantor_path) }}" class="h-full w-full object-cover">
            @else
                <i class="fas fa-church text-4xl text-slate-300"></i>
            @endif
        </div>
        <div class="flex-1 text-center md:text-left">
            <div class="flex items-center justify-center md:justify-start gap-2 mb-1">
                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded border border-blue-200">{{ $klasis->kode_klasis }}</span>
                <h1 class="text-2xl font-bold text-slate-800">{{ $klasis->nama_klasis }}</h1>
            </div>
            <p class="text-slate-500 text-sm"><i class="fas fa-map-marker-alt mr-1"></i> {{ $klasis->pusat_klasis ?? 'Lokasi belum diset' }}</p>
            
            <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-3">
                <div class="px-3 py-1 bg-slate-50 rounded border border-slate-200 text-xs font-medium text-slate-600">
                    <span class="block text-[10px] uppercase text-slate-400">Total Jemaat</span>
                    <span class="text-lg font-bold text-slate-800">{{ $klasis->jemaat->count() }}</span>
                </div>
                <div class="px-3 py-1 bg-slate-50 rounded border border-slate-200 text-xs font-medium text-slate-600">
                    <span class="block text-[10px] uppercase text-slate-400">Est.</span>
                    <span class="text-lg font-bold text-slate-800">{{ $klasis->tanggal_pembentukan ? $klasis->tanggal_pembentukan->format('Y') : '-' }}</span>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.klasis.index') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-xs font-bold uppercase rounded hover:bg-slate-50">Kembali</a>
            @hasanyrole('Super Admin|Admin Bidang 3')
            <a href="{{ route('admin.klasis.edit', $klasis->id) }}" class="px-4 py-2 bg-yellow-500 text-white text-xs font-bold uppercase rounded hover:bg-yellow-600">Edit Data</a>
            @endhasanyrole
        </div>
    </div>

    {{-- Detail Grid --}}
    <div class="bg-white rounded-b-lg shadow-sm border-x border-b border-slate-200 p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        {{-- Kolom 1 --}}
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase border-b border-slate-100 pb-2">Informasi Umum</h3>
            <div class="text-sm">
                <span class="block text-slate-500 text-xs">Nomor SK Pembentukan</span>
                <span class="font-medium text-slate-800">{{ $klasis->nomor_sk_pembentukan ?? '-' }}</span>
            </div>
            <div class="text-sm">
                <span class="block text-slate-500 text-xs">Tanggal Pembentukan</span>
                <span class="font-medium text-slate-800">{{ $klasis->tanggal_pembentukan ? $klasis->tanggal_pembentukan->translatedFormat('d F Y') : '-' }}</span>
            </div>
            <div class="text-sm">
                <span class="block text-slate-500 text-xs">Klasis Induk</span>
                <span class="font-medium text-slate-800">{{ $klasis->klasis_induk ?? '-' }}</span>
            </div>
        </div>

        {{-- Kolom 2 --}}
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase border-b border-slate-100 pb-2">Kontak & Lokasi</h3>
            <div class="text-sm">
                <span class="block text-slate-500 text-xs">Alamat Kantor</span>
                <span class="font-medium text-slate-800">{{ $klasis->alamat_kantor ?? '-' }}</span>
            </div>
            <div class="text-sm">
                <span class="block text-slate-500 text-xs">Email / Telepon</span>
                <span class="font-medium text-slate-800">
                    {{ $klasis->email_klasis ?? '-' }} <br> 
                    {{ $klasis->telepon_kantor ?? '-' }}
                </span>
            </div>
            <div class="text-sm">
                <span class="block text-slate-500 text-xs">Koordinat</span>
                <span class="font-mono text-xs bg-slate-100 px-1 rounded">{{ $klasis->latitude ?? '0' }}, {{ $klasis->longitude ?? '0' }}</span>
            </div>
        </div>

        {{-- Kolom 3 (List Jemaat) --}}
        <div class="bg-slate-50 p-4 rounded border border-slate-100 h-64 overflow-y-auto">
            <h3 class="text-xs font-bold text-slate-500 uppercase mb-3">Daftar Jemaat</h3>
            @if($klasis->jemaat->count() > 0)
                <ul class="space-y-2">
                    @foreach($klasis->jemaat as $j)
                    <li class="bg-white p-2 rounded shadow-sm border border-slate-200 flex justify-between items-center">
                        <a href="{{ route('admin.jemaat.show', $j->id) }}" class="text-xs font-bold text-slate-700 hover:text-blue-600 truncate w-32">{{ $j->nama_jemaat }}</a>
                        <span class="text-[10px] px-1.5 py-0.5 rounded {{ $j->status_jemaat == 'Mandiri' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $j->status_jemaat }}</span>
                    </li>
                    @endforeach
                </ul>
            @else
                <p class="text-xs text-slate-400 italic text-center mt-10">Belum ada jemaat.</p>
            @endif
        </div>
    </div>
</div>
@endsection