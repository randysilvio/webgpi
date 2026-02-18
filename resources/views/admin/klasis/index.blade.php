@extends('layouts.app')

@section('title', 'Direktori Klasis')

@section('content')
    <x-admin-index 
        title="Direktori Klasis" 
        subtitle="Daftar wilayah pelayanan tingkat Klasis se-Tanah Papua."
        create-route="{{ route('admin.klasis.create') }}"
        create-label="Tambah Klasis"
        :pagination="$klasisData"
    >
        {{-- SLOT ACTIONS: Tombol Tambahan (Export/Import) --}}
        <x-slot name="actions">
            @hasanyrole('Super Admin|Admin Bidang 3')
                <a href="{{ route('admin.klasis.import-form') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 text-xs font-bold uppercase tracking-wide rounded shadow-sm transition">
                    <i class="fas fa-file-excel mr-2 text-green-600"></i> Import
                </a>
                <a href="{{ route('admin.klasis.export') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 text-xs font-bold uppercase tracking-wide rounded shadow-sm transition">
                    <i class="fas fa-download mr-2 text-blue-600"></i> Export
                </a>
            @endhasanyrole
        </x-slot>

        {{-- SLOT STATS: Statistik Ringkas --}}
        <x-slot name="stats">
            {{-- Card Total Klasis --}}
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Klasis</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_klasis ?? 0) }}</h3>
                </div>
                <div class="p-2 bg-slate-50 rounded text-slate-400"><i class="fas fa-map-marked-alt text-lg"></i></div>
            </div>
            {{-- Card Total Jemaat --}}
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Jemaat</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_jemaat ?? 0) }}</h3>
                </div>
                <div class="p-2 bg-slate-50 rounded text-slate-400"><i class="fas fa-church text-lg"></i></div>
            </div>
            {{-- Card Jemaat Mandiri --}}
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jemaat Mandiri</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_jemaat_mandiri ?? 0) }}</h3>
                </div>
                <div class="p-2 bg-green-50 rounded text-green-600 border border-green-100"><i class="fas fa-check text-lg"></i></div>
            </div>
            {{-- Card Jemaat Pos/Bakal --}}
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pos Pelayanan</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_jemaat_pos ?? 0) }}</h3>
                </div>
                <div class="p-2 bg-yellow-50 rounded text-yellow-600 border border-yellow-100"><i class="fas fa-home text-lg"></i></div>
            </div>
        </x-slot>

        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.klasis.index') }}" method="GET" class="flex gap-4">
                <div class="relative w-full md:w-1/3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="pl-10 w-full border-slate-300 rounded text-sm focus:ring-slate-500 placeholder-slate-400 text-slate-600" 
                        placeholder="Cari Nama, Kode, atau Kota...">
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-bold uppercase transition">
                    Cari
                </button>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4 w-20">Kode</th>
            <th class="px-6 py-4">Klasis & Ketua</th>
            <th class="px-6 py-4">Pusat & Kontak</th>
            <th class="px-6 py-4 hidden md:table-cell">Wilayah</th>
            <th class="px-6 py-4 text-center">Jemaat</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA (ISI TABEL) --}}
        @forelse($klasisData as $klasis)
            <tr class="hover:bg-slate-50 transition">
                {{-- Kode --}}
                <x-td class="align-top">
                    <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-[10px] font-mono font-bold border border-slate-200">
                        {{ $klasis->kode_klasis }}
                    </span>
                </x-td>
                
                {{-- Nama & Ketua --}}
                <x-td class="align-top">
                    <a href="{{ route('admin.klasis.show', $klasis->id) }}" class="text-sm font-bold text-slate-800 hover:text-blue-600 hover:underline transition">
                        {{ $klasis->nama_klasis }}
                    </a>
                    <div class="flex items-center text-xs text-slate-500 mt-1">
                        <i class="fas fa-user-tie mr-1.5 w-3 text-center text-slate-400"></i>
                        <span class="{{ $klasis->ketuaMp ? 'text-slate-600' : 'text-slate-400 italic' }}">
                            {{ $klasis->ketuaMp->nama_lengkap ?? 'Belum ada Ketua' }}
                        </span>
                    </div>
                    @if($klasis->tanggal_pembentukan)
                        <div class="flex items-center text-[10px] text-slate-400 mt-1">
                            <i class="fas fa-calendar-alt mr-1.5 w-3 text-center"></i>
                            <span>Est. {{ $klasis->tanggal_pembentukan->format('Y') }}</span>
                        </div>
                    @endif
                </x-td>

                {{-- Pusat & Kontak --}}
                <x-td class="align-top text-slate-600">
                    <div class="font-medium text-xs mb-1 flex items-center">
                        <i class="fas fa-map-pin mr-1.5 w-3 text-center text-slate-400"></i>
                        {{ $klasis->pusat_klasis ?? '-' }}
                    </div>
                    @if($klasis->telepon_kantor)
                        <div class="text-[11px] flex items-center">
                            <i class="fas fa-phone mr-1.5 w-3 text-center text-slate-400"></i>
                            {{ $klasis->telepon_kantor }}
                        </div>
                    @endif
                    @if($klasis->email_klasis)
                        <div class="text-[11px] flex items-center truncate max-w-[150px]" title="{{ $klasis->email_klasis }}">
                            <i class="fas fa-envelope mr-1.5 w-3 text-center text-slate-400"></i>
                            {{ Str::limit($klasis->email_klasis, 20) }}
                        </div>
                    @endif
                </x-td>

                {{-- Wilayah --}}
                <x-td class="align-top hidden md:table-cell">
                    <p class="text-xs text-slate-500 line-clamp-2" title="{{ $klasis->wilayah_pelayanan }}">
                        {{ $klasis->wilayah_pelayanan ?? '-' }}
                    </p>
                </x-td>

                {{-- Jumlah Jemaat --}}
                <x-td class="align-top text-center">
                    <span class="bg-blue-50 text-blue-700 px-2.5 py-0.5 rounded-full text-xs font-bold border border-blue-100">
                        {{ $klasis->jemaat_count }}
                    </span>
                </x-td>

                {{-- Aksi --}}
                <x-td class="align-top text-center whitespace-nowrap">
                    <div class="flex justify-center items-center space-x-3">
                        <a href="{{ route('admin.klasis.show', $klasis->id) }}" class="text-slate-400 hover:text-blue-600 transition" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>

                        @hasanyrole('Super Admin|Admin Bidang 3')
                            <a href="{{ route('admin.klasis.edit', $klasis->id) }}" class="text-slate-400 hover:text-yellow-600 transition" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>

                            <form action="{{ route('admin.klasis.destroy', $klasis->id) }}" method="POST" class="inline-block" onsubmit="return confirm('PERHATIAN: Menghapus Klasis ini?\n\nSyarat: Klasis harus kosong (tidak ada Jemaat).\n\nLanjutkan hapus {{ $klasis->nama_klasis }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @endhasanyrole
                    </div>
                </x-td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                    <i class="fas fa-map-signs text-3xl mb-2 opacity-30"></i><br>
                    Belum ada data Klasis.
                    @hasanyrole('Super Admin|Admin Bidang 3')
                        <a href="{{ route('admin.klasis.create') }}" class="block mt-2 text-blue-600 hover:underline font-bold text-xs">Tambah Baru?</a>
                    @endhasanyrole
                </td>
            </tr>
        @endforelse

    </x-admin-index>
@endsection