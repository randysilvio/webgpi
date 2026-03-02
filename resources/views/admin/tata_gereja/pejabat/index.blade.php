@extends('layouts.app')

@section('title', 'Direktori Pejabat Gereja')

@section('content')
    <x-admin-index 
        title="Pejabat Gerejawi" 
        subtitle="Manajemen data Penatua dan Diaken (Majelis Jemaat)."
        create-route="{{ route('admin.tata-gereja.pejabat.create') }}"
        create-label="Lantik Pejabat"
        :pagination="$pejabats"
    >
        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.tata-gereja.pejabat.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                {{-- Filter Jabatan --}}
                <x-form-select name="jabatan" onchange="this.form.submit()">
                    <option value="">- Semua Jabatan -</option>
                    <option value="Penatua" {{ request('jabatan') == 'Penatua' ? 'selected' : '' }}>Penatua</option>
                    <option value="Diaken" {{ request('jabatan') == 'Diaken' ? 'selected' : '' }}>Diaken</option>
                </x-form-select>

                {{-- Filter Status --}}
                <x-form-select name="status" onchange="this.form.submit()">
                    <option value="">- Semua Status -</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Demisioner" {{ request('status') == 'Demisioner' ? 'selected' : '' }}>Demisioner</option>
                    <option value="Emeritus" {{ request('status') == 'Emeritus' ? 'selected' : '' }}>Emeritus</option>
                </x-form-select>

                {{-- Search --}}
                <div class="md:col-span-2 relative">
                    <x-form-input name="search" value="{{ request('search') }}" placeholder="Cari Nama Pejabat..." />
                    <button type="submit" class="absolute right-2 top-1.5 text-slate-400 hover:text-blue-600">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4">Nama Pejabat</th>
            <th class="px-6 py-4">Jabatan</th>
            <th class="px-6 py-4">Periode Pelayanan</th>
            <th class="px-6 py-4 text-center">Status</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse($pejabats as $p)
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">
                            {{ substr($p->anggotaJemaat->nama_lengkap ?? 'X', 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 text-sm">{{ $p->anggotaJemaat->nama_lengkap ?? 'Data Terhapus' }}</div>
                            <div class="text-[10px] text-slate-500 uppercase tracking-wide">{{ $p->anggotaJemaat->jemaat->nama_jemaat ?? '-' }}</div>
                        </div>
                    </div>
                </x-td>
                <x-td>
                    @php
                        $badgeColor = $p->jabatan == 'Penatua' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700';
                    @endphp
                    <span class="{{ $badgeColor }} px-2 py-1 rounded text-[10px] font-bold uppercase">
                        {{ $p->jabatan }}
                    </span>
                </x-td>
                <x-td>
                    <div class="text-xs font-medium text-slate-700">
                        {{ $p->periode_mulai }} <span class="text-slate-400 mx-1">&mdash;</span> {{ $p->periode_selesai }}
                    </div>
                    @if($p->no_sk_pelantikan)
                        <div class="text-[10px] text-slate-400 mt-0.5">SK: {{ $p->no_sk_pelantikan }}</div>
                    @endif
                </x-td>
                <x-td class="text-center">
                    @php
                        $statusColor = match($p->status_aktif) {
                            'Aktif' => 'bg-green-100 text-green-700',
                            'Demisioner' => 'bg-slate-200 text-slate-600',
                            'Emeritus' => 'bg-yellow-100 text-yellow-700',
                            default => 'bg-red-100 text-red-700'
                        };
                    @endphp
                    <span class="{{ $statusColor }} px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-wider">
                        {{ $p->status_aktif }}
                    </span>
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.tata-gereja.pejabat.edit', $p->id) }}" class="text-slate-400 hover:text-yellow-600 transition" title="Edit Data">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.tata-gereja.pejabat.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus data pejabat ini?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </x-td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic text-sm">
                    Belum ada data pejabat gerejawi.
                </td>
            </tr>
        @endforelse

    </x-admin-index>
@endsection