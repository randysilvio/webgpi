@extends('layouts.app')

@section('title', 'Buku Baptisan Kudus')

@section('content')
    <x-admin-index 
        title="Buku Register Baptisan" 
        subtitle="Arsip digital penerima sakramen baptisan kudus."
        create-route="{{ route('admin.sakramen.baptis.create') }}"
        create-label="Catat Baptisan Baru"
        :pagination="$baptis"
    >
        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.sakramen.baptis.index') }}" method="GET" class="flex gap-4">
                <x-form-input name="search" label="Cari Data" value="{{ request('search') }}" placeholder="No. Akta / Nama Anak..." />
                <div class="flex items-end pb-0.5">
                    <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-bold uppercase transition">
                        Cari
                    </button>
                </div>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4">Nomor Akta</th>
            <th class="px-6 py-4">Nama Anggota</th>
            <th class="px-6 py-4">Pelayanan</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse($baptis as $b)
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded text-xs">
                        {{ $b->no_akta_baptis }}
                    </span>
                    <div class="text-[10px] text-slate-400 mt-1">
                        Tgl: {{ \Carbon\Carbon::parse($b->tanggal_baptis)->isoFormat('D MMM Y') }}
                    </div>
                </x-td>
                <x-td>
                    <a href="{{ route('admin.anggota-jemaat.show', $b->anggota_jemaat_id) }}" class="text-sm font-bold text-slate-800 hover:text-blue-600 hover:underline uppercase">
                        {{ $b->anggotaJemaat->nama_lengkap ?? 'Data Terhapus' }}
                    </a>
                    <div class="text-xs text-slate-500">
                        {{ $b->anggotaJemaat->jemaat->nama_jemaat ?? '-' }}
                    </div>
                </x-td>
                <x-td>
                    <div class="text-xs text-slate-700 font-medium">{{ $b->pendeta_pelayan }}</div>
                    <div class="text-[10px] text-slate-500 italic">{{ $b->tempat_baptis }}</div>
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        {{-- Tombol Cetak --}}
                        <a href="{{ route('admin.sakramen.baptis.cetak', $b->id) }}" target="_blank" class="text-slate-400 hover:text-blue-600 transition" title="Cetak Surat Baptis">
                            <i class="fas fa-print"></i>
                        </a>
                        {{-- Tombol Edit (Opsional jika ada) --}}
                        @if(Route::has('admin.sakramen.baptis.edit'))
                        <a href="{{ route('admin.sakramen.baptis.edit', $b->id) }}" class="text-slate-400 hover:text-yellow-600 transition">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                    </div>
                </x-td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">
                    Belum ada data baptisan yang tercatat.
                </td>
            </tr>
        @endforelse

    </x-admin-index>
@endsection