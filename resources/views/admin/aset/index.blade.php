@extends('layouts.app')

@section('title', 'Inventaris Aset')

@section('content')
    <x-admin-index 
        title="Database Inventaris" 
        subtitle="Manajemen aset bergerak, tidak bergerak, dan inventaris gereja."
        create-route="{{ route('admin.perbendaharaan.aset.create') }}"
        create-label="Catat Aset Baru"
        :pagination="$asets"
    >
        {{-- SLOT STATS --}}
        <x-slot name="stats">
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Aset</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_item ?? 0) }}</p>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i class="fas fa-boxes text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Nilai (Perolehan)</p>
                    <p class="text-xl font-bold text-emerald-600 mt-1">Rp {{ number_format($stats->total_nilai ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg"><i class="fas fa-coins text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kondisi Baik</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_baik ?? 0) }}</p>
                </div>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg"><i class="fas fa-check-circle text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Perlu Perbaikan</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_rusak ?? 0) }}</p>
                </div>
                <div class="p-2 bg-red-50 text-red-600 rounded-lg"><i class="fas fa-wrench text-lg"></i></div>
            </div>
        </x-slot>

        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.perbendaharaan.aset.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                {{-- Filter Kategori --}}
                <x-form-select name="kategori" onchange="this.form.submit()">
                    <option value="">- Semua Kategori -</option>
                    @foreach(['Tanah', 'Gedung', 'Kendaraan', 'Peralatan Elektronik', 'Meubelair', 'Alat Musik', 'Lainnya'] as $cat)
                        <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </x-form-select>

                {{-- Filter Kondisi --}}
                <x-form-select name="kondisi" onchange="this.form.submit()">
                    <option value="">- Semua Kondisi -</option>
                    <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                </x-form-select>

                {{-- Search --}}
                <div class="md:col-span-2 relative">
                    <x-form-input name="search" value="{{ request('search') }}" placeholder="Cari Nama / Kode Aset..." />
                    <button type="submit" class="absolute right-3 top-2 text-slate-400 hover:text-blue-600">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4">Aset / Kode</th>
            <th class="px-6 py-4">Lokasi & Kategori</th>
            <th class="px-6 py-4">Kondisi</th>
            <th class="px-6 py-4 text-right">Nilai Perolehan</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse($asets as $aset)
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                            <i class="fas fa-cube text-sm"></i>
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 text-sm">{{ $aset->nama_aset }}</div>
                            <div class="text-[10px] text-slate-500 font-mono mt-0.5">{{ $aset->kode_aset }}</div>
                        </div>
                    </div>
                </x-td>
                <x-td>
                    <span class="block text-xs font-bold text-slate-700">{{ $aset->jemaat->nama_jemaat ?? ($aset->klasis->nama_klasis ?? 'Sinode') }}</span>
                    <span class="inline-flex mt-1 px-2 py-0.5 text-[10px] rounded bg-slate-100 text-slate-600 border border-slate-200 uppercase font-bold">
                        {{ $aset->kategori }}
                    </span>
                </x-td>
                <x-td>
                    @php
                        $color = match($aset->kondisi) {
                            'Baik' => 'green',
                            'Rusak Ringan' => 'yellow',
                            default => 'red',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-{{ $color }}-100 text-{{ $color }}-700 border border-{{ $color }}-200 uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500 mr-1.5"></span>
                        {{ $aset->kondisi }}
                    </span>
                </x-td>
                <x-td class="text-right font-mono text-xs font-bold text-slate-700">
                    {{ $aset->format_nilai }}
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.perbendaharaan.aset.show', $aset->id) }}" class="text-blue-500 hover:text-blue-700" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.perbendaharaan.aset.edit', $aset->id) }}" class="text-slate-400 hover:text-yellow-600" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.perbendaharaan.aset.destroy', $aset->id) }}" method="POST" onsubmit="return confirm('Hapus aset ini? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </x-td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data aset yang terdaftar.</td>
            </tr>
        @endforelse

    </x-admin-index>
@endsection