@extends('layouts.app')

@section('title', 'Detail Mutasi')
@section('header-title', 'Detail SK Mutasi')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- BUTTON BACK --}}
    <div class="mb-4">
        <a href="{{ route('admin.mutasi.index') }}" class="text-sm text-slate-500 hover:text-slate-800 font-medium">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    {{-- MAIN CARD --}}
    <div class="bg-white rounded-lg shadow-lg border border-slate-200 overflow-hidden relative">
        
        {{-- Header Card --}}
        <div class="bg-slate-900 px-8 py-6 text-white flex justify-between items-start">
            <div>
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-1">Surat Keputusan</p>
                <h1 class="text-2xl font-bold font-mono tracking-tight">{{ $mutasi->nomor_sk }}</h1>
            </div>
            <div class="text-right">
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-1">Tanggal Efektif</p>
                <p class="text-lg font-bold">{{ \Carbon\Carbon::parse($mutasi->tanggal_efektif)->format('d F Y') }}</p>
            </div>
        </div>

        <div class="p-8">
            {{-- Profile Section --}}
            <div class="flex items-center mb-8 pb-8 border-b border-slate-100 border-dashed">
                <div class="h-14 w-14 rounded bg-slate-100 flex items-center justify-center text-slate-400 text-xl border border-slate-200">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-slate-800">{{ $mutasi->pegawai->nama_lengkap ?? 'Pegawai Tidak Ditemukan' }}</h3>
                    <p class="text-sm text-slate-500">NIP: {{ $mutasi->pegawai->nip ?? '-' }}</p>
                </div>
            </div>

            {{-- Movement Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 relative">
                {{-- Connector Line (Desktop) --}}
                <div class="hidden md:block absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-0">
                    <i class="fas fa-arrow-right text-slate-200 text-3xl"></i>
                </div>

                {{-- From --}}
                <div class="relative z-10 bg-slate-50 p-5 rounded border border-slate-100">
                    <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-2 block">Dari (Instansi Lama)</span>
                    <p class="text-base font-bold text-slate-700">{{ $mutasi->asal_instansi }}</p>
                    <p class="text-xs text-slate-500 mt-1">Jabatan Lama</p>
                </div>

                {{-- To --}}
                <div class="relative z-10 bg-blue-50 p-5 rounded border border-blue-100">
                    <span class="text-[10px] font-bold uppercase text-blue-400 tracking-wider mb-2 block">Ke (Instansi Baru)</span>
                    <p class="text-base font-bold text-blue-800">{{ $mutasi->tujuan_instansi }}</p>
                    <p class="text-xs text-blue-600 mt-1">Jabatan Baru</p>
                </div>
            </div>

            {{-- Details --}}
            <div class="grid grid-cols-2 gap-6 text-sm">
                <div>
                    <span class="block text-xs font-bold text-slate-400 uppercase">Jenis Mutasi</span>
                    <span class="block font-medium text-slate-700 mt-1">{{ $mutasi->jenis_mutasi ?? 'Rutin' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-slate-400 uppercase">Tanggal SK Ditetapkan</span>
                    <span class="block font-medium text-slate-700 mt-1">{{ \Carbon\Carbon::parse($mutasi->tanggal_sk)->format('d F Y') }}</span>
                </div>
                <div class="col-span-2">
                    <span class="block text-xs font-bold text-slate-400 uppercase">Keterangan / Catatan</span>
                    <p class="block text-slate-600 mt-1 leading-relaxed bg-slate-50 p-3 rounded text-xs border border-slate-100">
                        {{ $mutasi->keterangan ?? 'Tidak ada keterangan tambahan.' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="bg-slate-50 px-8 py-4 border-t border-slate-200 flex justify-between items-center">
            <span class="text-xs text-slate-400">ID Mutasi: #{{ $mutasi->id }}</span>
            
            <div class="flex gap-2">
                {{-- <button onclick="window.print()" class="px-3 py-1.5 bg-white border border-slate-300 text-slate-600 text-xs font-bold uppercase rounded hover:bg-slate-50">
                    Cetak
                </button> --}}
                @can('manage pendeta')
                <a href="{{ route('admin.mutasi.edit', $mutasi->id) }}" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-bold uppercase rounded hover:bg-slate-900">
                    Koreksi Data
                </a>
                @endcan
            </div>
        </div>

    </div>
</div>
@endsection