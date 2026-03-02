@extends('layouts.app')

@section('title', 'Buku Register Sidi')

@section('content')
    <x-admin-index 
        title="Buku Register Sidi" 
        subtitle="Arsip peneguhan sidi jemaat."
        create-route="{{ route('admin.sakramen.sidi.create') }}"
        create-label="Catat Sidi Baru"
        :pagination="$sidi"
    >
        <x-slot name="filters">
            <form action="{{ route('admin.sakramen.sidi.index') }}" method="GET" class="flex gap-4">
                <x-form-input name="search" label="Pencarian" value="{{ request('search') }}" placeholder="No. Akta / Nama..." />
                <div class="flex items-end pb-0.5">
                    <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-bold uppercase transition">
                        Cari
                    </button>
                </div>
            </form>
        </x-slot>

        <x-slot name="tableHead">
            <th class="px-6 py-4">No. Akta & Tanggal</th>
            <th class="px-6 py-4">Nama Anggota</th>
            <th class="px-6 py-4">Pelayanan</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        @forelse($sidi as $s)
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <span class="font-mono font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded text-xs">
                        {{ $s->no_akta_sidi }}
                    </span>
                    <div class="text-[10px] text-slate-400 mt-1">
                        {{ \Carbon\Carbon::parse($s->tanggal_sidi)->isoFormat('D MMM Y') }}
                    </div>
                </x-td>
                <x-td>
                    <a href="{{ route('admin.anggota-jemaat.show', $s->anggota_jemaat_id) }}" class="text-sm font-bold text-slate-800 hover:text-blue-600 hover:underline uppercase">
                        {{ $s->anggotaJemaat->nama_lengkap ?? 'Data Terhapus' }}
                    </a>
                    <div class="text-xs text-slate-500">
                        {{ $s->anggotaJemaat->jemaat->nama_jemaat ?? '-' }}
                    </div>
                </x-td>
                <x-td>
                    <div class="text-xs text-slate-700 font-medium">{{ $s->pendeta_pelayan }}</div>
                    <div class="text-[10px] text-slate-500 italic">{{ $s->tempat_sidi }}</div>
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.sakramen.sidi.cetak', $s->id) }}" target="_blank" class="text-slate-400 hover:text-blue-600" title="Cetak Sertifikat">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="{{ route('admin.sakramen.sidi.edit', $s->id) }}" class="text-slate-400 hover:text-yellow-600" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.sakramen.sidi.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus data sidi ini?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                </x-td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data sidi.</td>
            </tr>
        @endforelse

    </x-admin-index>
@endsection