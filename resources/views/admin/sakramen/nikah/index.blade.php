@extends('layouts.app')

@section('title', 'Buku Pernikahan')

@section('content')
    <x-admin-index 
        title="Register Pernikahan" 
        subtitle="Arsip pelayanan sakramen pemberkatan nikah kudus."
        create-route="{{ route('admin.sakramen.nikah.create') }}"
        create-label="Catat Nikah Baru"
        :pagination="$nikahs"
    >
        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.sakramen.nikah.index') }}" method="GET" class="flex gap-4">
                <x-form-input name="search" label="Pencarian" value="{{ request('search') }}" placeholder="No. Akta / Nama Mempelai..." />
                <div class="flex items-end pb-0.5">
                    <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-bold uppercase transition">
                        Cari
                    </button>
                </div>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4">No. Akta & Tanggal</th>
            <th class="px-6 py-4 text-center">Mempelai Pria</th>
            <th class="px-6 py-4 text-center"></th>
            <th class="px-6 py-4 text-center">Mempelai Wanita</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse($nikahs as $n)
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <span class="font-mono font-bold text-pink-600 bg-pink-50 px-2 py-1 rounded text-xs">
                        {{ $n->no_akta_nikah }}
                    </span>
                    <div class="text-[10px] text-slate-400 mt-1">
                        {{ \Carbon\Carbon::parse($n->tanggal_nikah)->isoFormat('D MMMM Y') }}
                    </div>
                </x-td>
                <x-td class="text-center">
                    <div class="font-bold text-slate-800 text-xs uppercase">{{ $n->suami->nama_lengkap ?? 'HAPUS' }}</div>
                    <div class="text-[10px] text-slate-500">{{ $n->suami->jemaat->nama_jemaat ?? '-' }}</div>
                </x-td>
                <x-td class="text-center">
                    <i class="fas fa-heart text-pink-400"></i>
                </x-td>
                <x-td class="text-center">
                    <div class="font-bold text-slate-800 text-xs uppercase">{{ $n->istri->nama_lengkap ?? 'HAPUS' }}</div>
                    <div class="text-[10px] text-slate-500">{{ $n->istri->jemaat->nama_jemaat ?? '-' }}</div>
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.sakramen.nikah.cetak', $n->id) }}" target="_blank" class="text-slate-400 hover:text-blue-600" title="Cetak Sertifikat">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="{{ route('admin.sakramen.nikah.edit', $n->id) }}" class="text-slate-400 hover:text-yellow-600" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.sakramen.nikah.destroy', $n->id) }}" method="POST" onsubmit="return confirm('Hapus arsip ini?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                </x-td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data pernikahan.</td>
            </tr>
        @endforelse

    </x-admin-index>
@endsection