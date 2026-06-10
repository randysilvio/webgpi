@extends('layouts.app')

@section('title', 'Manajemen Pengurus')

@section('content')
    <x-admin-index 
        title="Direktori Pengurus" 
        subtitle="Kelola data pengurus Wadah Kategorial di semua tingkatan (Sinode, Klasis, Jemaat)."
        create-route="{{ route('admin.wadah.pengurus.create') }}"
        create-label="Tambah Pengurus"
        :pagination="$pengurus"
    >
        {{-- SLOT STATS --}}
        <x-slot name="stats">
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Personil</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total ?? 0) }}</p>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i class="fas fa-users text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pengurus Jemaat</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->level_jemaat ?? 0) }}</p>
                </div>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg"><i class="fas fa-church text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Aktif</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_aktif ?? 0) }}</p>
                </div>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg"><i class="fas fa-user-check text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Non-Aktif</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_non_aktif ?? 0) }}</p>
                </div>
                <div class="p-2 bg-slate-100 text-slate-500 rounded-lg"><i class="fas fa-user-clock text-lg"></i></div>
            </div>
        </x-slot>

        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.wadah.pengurus.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                {{-- Filter Wadah --}}
                <x-form-select name="jenis_wadah_id" onchange="this.form.submit()">
                    <option value="">- Semua Wadah -</option>
                    @foreach($jenisWadahs as $wadah)
                        <option value="{{ $wadah->id }}" {{ request('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>
                            {{ $wadah->nama_wadah }}
                        </option>
                    @endforeach
                </x-form-select>

                {{-- Filter Tingkat --}}
                <x-form-select name="tingkat" onchange="this.form.submit()">
                    <option value="">- Semua Tingkat -</option>
                    <option value="sinode" {{ request('tingkat') == 'sinode' ? 'selected' : '' }}>Sinode</option>
                    <option value="klasis" {{ request('tingkat') == 'klasis' ? 'selected' : '' }}>Klasis</option>
                    <option value="jemaat" {{ request('tingkat') == 'jemaat' ? 'selected' : '' }}>Jemaat</option>
                </x-form-select>

                {{-- Filter Klasis (Jika Ada) --}}
                @if($klasisList->count() > 0)
                    <x-form-select name="klasis_id" onchange="this.form.submit()">
                        <option value="">- Semua Klasis -</option>
                        @foreach($klasisList as $klasis)
                            <option value="{{ $klasis->id }}" {{ request('klasis_id') == $klasis->id ? 'selected' : '' }}>
                                {{ $klasis->nama_klasis }}
                            </option>
                        @endforeach
                    </x-form-select>
                @endif

                {{-- Search --}}
                <div class="{{ $klasisList->count() > 0 ? '' : 'md:col-span-2' }} relative">
                    <x-form-input name="search" value="{{ request('search') }}" placeholder="Cari Nama / SK..." />
                    <button type="submit" class="absolute right-3 top-2 text-slate-400 hover:text-blue-600">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4">Nama Pengurus</th>
            <th class="px-6 py-4">Jabatan & Wadah</th>
            <th class="px-6 py-4">Lingkup Pelayanan</th>
            <th class="px-6 py-4">Periode</th>
            <th class="px-6 py-4 text-center">Status</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse($pengurus as $p)
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-slate-100 border border-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs">
                            {{ substr($p->anggotaJemaat->nama_lengkap ?? 'M', 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-800">
                                {{ $p->anggotaJemaat->nama_lengkap ?? 'Manual Input' }}
                            </div>
                            @if($p->nomor_sk)
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">SK: {{ $p->nomor_sk }}</div>
                            @endif
                        </div>
                    </div>
                </x-td>
                <x-td>
                    <div class="text-xs font-bold text-slate-700">{{ $p->jabatan }}</div>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase">
                        {{ $p->jenisWadah->nama_wadah }}
                    </span>
                </x-td>
                <x-td>
                    <div class="font-bold uppercase text-[10px] text-slate-500 tracking-wider">{{ $p->tingkat }}</div>
                    @if($p->tingkat == 'klasis')
                        <div class="text-xs text-slate-800 font-medium">{{ $p->klasis->nama_klasis ?? '-' }}</div>
                    @elseif($p->tingkat == 'jemaat')
                        <div class="text-xs text-slate-800 font-medium">{{ $p->jemaat->nama_jemaat ?? '-' }}</div>
                        <div class="text-[10px] text-slate-400">({{ $p->klasis->nama_klasis ?? '-' }})</div>
                    @endif
                </x-td>
                <x-td>
                    <div class="text-xs font-mono text-slate-600 bg-slate-50 px-2 py-1 rounded inline-block border border-slate-100">
                        {{ $p->periode_mulai->format('Y') }} - {{ $p->periode_selesai->format('Y') }}
                    </div>
                </x-td>
                <x-td class="text-center">
                    @if($p->is_active)
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-green-100 text-green-700">Aktif</span>
                    @else
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-500">Non-Aktif</span>
                    @endif
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.wadah.pengurus.edit', $p->id) }}" class="text-slate-400 hover:text-yellow-600 transition" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.wadah.pengurus.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data pengurus ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </x-td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">
                    Belum ada data pengurus yang sesuai filter.
                </td>
            </tr>
        @endforelse

    </x-admin-index>
@endsection