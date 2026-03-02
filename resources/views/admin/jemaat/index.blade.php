@extends('layouts.app')

@section('title', 'Direktori Jemaat')

@section('content')
    <x-admin-index 
        title="Direktori Jemaat" 
        subtitle="Database unit pelayanan tingkat Jemaat di seluruh wilayah."
        create-route="{{ route('admin.jemaat.create') }}"
        create-label="Jemaat Baru"
        :pagination="$jemaatData"
    >
        {{-- SLOT ACTIONS: Import/Export --}}
        <x-slot name="actions">
            @can('import jemaat')
            <a href="{{ route('admin.jemaat.import-form') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 text-xs font-bold uppercase tracking-wide rounded shadow-sm transition">
                <i class="fas fa-file-excel mr-2 text-green-600"></i> Import
            </a>
            @endcan
            @can('export jemaat')
            <a href="{{ route('admin.jemaat.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 text-xs font-bold uppercase tracking-wide rounded shadow-sm transition">
                <i class="fas fa-download mr-2 text-blue-600"></i> Export
            </a>
            @endcan
        </x-slot>

        {{-- SLOT STATS --}}
        <x-slot name="stats">
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex justify-between">
                <div><p class="text-[10px] font-bold text-slate-400 uppercase">Total Jemaat</p><h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats->total ?? 0) }}</h3></div>
                <div class="p-2 bg-slate-50 rounded text-slate-400"><i class="fas fa-church text-lg"></i></div>
            </div>
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex justify-between">
                <div><p class="text-[10px] font-bold text-slate-400 uppercase">Mandiri</p><h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats->total_mandiri ?? 0) }}</h3></div>
                <div class="p-2 bg-green-50 text-green-600 rounded"><i class="fas fa-check-circle text-lg"></i></div>
            </div>
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex justify-between">
                <div><p class="text-[10px] font-bold text-slate-400 uppercase">Pos/Bakal</p><h3 class="text-2xl font-bold text-slate-800">{{ number_format(($stats->total_bakal ?? 0) + ($stats->total_pos ?? 0)) }}</h3></div>
                <div class="p-2 bg-yellow-50 text-yellow-600 rounded"><i class="fas fa-home text-lg"></i></div>
            </div>
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex justify-between">
                <div><p class="text-[10px] font-bold text-slate-400 uppercase">Total Jiwa</p><h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats->total_jiwa ?? 0) }}</h3></div>
                <div class="p-2 bg-purple-50 text-purple-600 rounded"><i class="fas fa-users text-lg"></i></div>
            </div>
        </x-slot>

        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.jemaat.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']) && isset($klasisFilterOptions))
                <div>
                    <x-form-select name="klasis_id" label="Filter Klasis">
                        <option value="">-- Semua Klasis --</option>
                        @foreach($klasisFilterOptions as $id => $nama)
                            <option value="{{ $id }}" {{ request('klasis_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </x-form-select>
                </div>
                @endif
                <div class="{{ Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']) ? 'md:col-span-2' : 'md:col-span-3' }}">
                    <x-form-input name="search" label="Pencarian" value="{{ request('search') }}" placeholder="Cari Nama atau Kode Jemaat..." />
                </div>
                <div class="flex items-end pb-0.5">
                    <button type="submit" class="w-full px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-bold uppercase transition">Filter Data</button>
                </div>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4 w-16">ID</th>
            <th class="px-6 py-4">Jemaat</th>
            <th class="px-6 py-4">Klasis</th>
            <th class="px-6 py-4 text-center">Status</th>
            <th class="px-6 py-4 text-center">Statistik</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse ($jemaatData as $jemaat)
            <tr class="hover:bg-slate-50 transition">
                <x-td class="font-mono text-xs text-slate-400 font-bold">#{{ $jemaat->id }}</x-td>
                <x-td>
                    <a href="{{ route('admin.jemaat.show', $jemaat->id) }}" class="text-sm font-bold text-slate-800 hover:text-blue-600 hover:underline">
                        {{ $jemaat->nama_jemaat }}
                    </a>
                    <div class="text-[10px] text-slate-500 mt-0.5">{{ $jemaat->kode_jemaat ?? '-' }}</div>
                </x-td>
                <x-td class="text-slate-600">{{ $jemaat->klasis->nama_klasis ?? '-' }}</x-td>
                <x-td class="text-center">
                    @php
                        $statusColor = match($jemaat->status_jemaat) {
                            'Mandiri' => 'bg-green-100 text-green-700',
                            'Bakal Jemaat' => 'bg-yellow-100 text-yellow-700',
                            default => 'bg-slate-100 text-slate-600'
                        };
                    @endphp
                    <span class="px-2 py-1 text-[10px] font-bold rounded border border-transparent {{ $statusColor }}">
                        {{ $jemaat->status_jemaat }}
                    </span>
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-4 text-xs">
                        <div class="text-center"><span class="block font-bold">{{ $jemaat->jumlah_kk ?? 0 }}</span><span class="text-[9px] text-slate-400 uppercase">KK</span></div>
                        <div class="w-px h-8 bg-slate-200"></div>
                        <div class="text-center"><span class="block font-bold">{{ $jemaat->jumlah_total_jiwa ?? 0 }}</span><span class="text-[9px] text-slate-400 uppercase">Jiwa</span></div>
                    </div>
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        @can('edit jemaat')
                        <a href="{{ route('admin.jemaat.edit', $jemaat->id) }}" class="text-slate-400 hover:text-yellow-600"><i class="fas fa-edit"></i></a>
                        @endcan
                        @can('delete jemaat')
                        <form action="{{ route('admin.jemaat.destroy', $jemaat->id) }}" method="POST" onsubmit="return confirm('Hapus Jemaat ini? Data anggota juga akan terhapus!');" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                        </form>
                        @endcan
                    </div>
                </x-td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data jemaat.</td></tr>
        @endforelse

    </x-admin-index>
@endsection