@extends('layouts.app')

@section('title', 'Rencana Anggaran (RAPB)')

@section('content')
    <x-admin-index 
        title="Anggaran Induk (RAPB)" 
        subtitle="Penyusunan dan monitoring Rencana Anggaran Pendapatan dan Belanja Jemaat."
        create-route="{{ route('admin.perbendaharaan.anggaran.create') }}"
        create-label="Susun RAPB Baru"
        :pagination="$anggarans"
    >
        {{-- SLOT STATS: Ringkasan Anggaran --}}
        <x-slot name="stats">
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Target Pendapatan</p>
                    <p class="text-xl font-bold text-emerald-600 mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                </div>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg"><i class="fas fa-wallet text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rencana Belanja</p>
                    <p class="text-xl font-bold text-red-600 mt-1">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</p>
                </div>
                <div class="p-2 bg-red-50 text-red-600 rounded-lg"><i class="fas fa-shopping-cart text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estimasi Surplus</p>
                    <p class="text-xl font-bold text-blue-600 mt-1">Rp {{ number_format($totalPendapatan - $totalBelanja, 0, ',', '.') }}</p>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i class="fas fa-chart-line text-lg"></i></div>
            </div>
        </x-slot>

        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.perbendaharaan.anggaran.index') }}" method="GET" class="flex items-center gap-4">
                <div class="w-48">
                    <x-form-select name="tahun" onchange="this.form.submit()">
                        @for($i = date('Y'); $i <= date('Y')+2; $i++)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>Tahun Anggaran {{ $i }}</option>
                        @endfor
                    </x-form-select>
                </div>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4">Kode & Mata Anggaran</th>
            <th class="px-6 py-4 text-center">Jenis</th>
            <th class="px-6 py-4 text-right">Target (Rp)</th>
            <th class="px-6 py-4 text-center">Status</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse($anggarans as $ang)
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <div class="font-bold text-slate-800 text-sm">{{ $ang->mataAnggaran->nama_mata_anggaran }}</div>
                    <div class="text-[10px] text-slate-500 font-mono bg-slate-100 px-1.5 py-0.5 rounded inline-block mt-1">
                        {{ $ang->mataAnggaran->kode }}
                    </div>
                </x-td>
                <x-td class="text-center">
                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $ang->mataAnggaran->jenis == 'Pendapatan' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ $ang->mataAnggaran->jenis }}
                    </span>
                </x-td>
                <x-td class="text-right font-mono font-bold text-slate-700">
                    {{ number_format($ang->jumlah_target, 0, ',', '.') }}
                </x-td>
                <x-td class="text-center">
                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-slate-100 text-slate-600 uppercase border border-slate-200">
                        {{ $ang->status_anggaran }}
                    </span>
                </x-td>
                <x-td class="text-center">
                    {{-- Contoh Aksi Edit Individual (Jika Diperlukan) --}}
                    <a href="#" class="text-slate-400 hover:text-blue-600 transition" title="Edit Item">
                        <i class="fas fa-edit"></i>
                    </a>
                </x-td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                    Belum ada anggaran yang disusun untuk tahun {{ $tahun }}.
                </td>
            </tr>
        @endforelse

    </x-admin-index>
@endsection