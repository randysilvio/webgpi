@extends('layouts.app')

@section('title', 'Direktori Anggota Jemaat')

@section('content')
    <x-admin-index 
        title="Direktori Anggota" 
        subtitle="Database seluruh warga jemaat (Anggota Sidi, Baptis, dan Anak)."
        create-route="{{ route('admin.anggota-jemaat.create') }}"
        create-label="Tambah Anggota"
        :pagination="$anggotaJemaatData"
    >
        {{-- SLOT ACTIONS: Import/Export --}}
        <x-slot name="actions">
            @can('import anggota jemaat')
            <a href="{{ route('admin.anggota-jemaat.import-form') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 text-xs font-bold uppercase tracking-wide rounded shadow-sm transition">
                <i class="fas fa-file-excel mr-2 text-green-600"></i> Import
            </a>
            @endcan
            @can('export anggota jemaat')
            <a href="{{ route('admin.anggota-jemaat.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 text-xs font-bold uppercase tracking-wide rounded shadow-sm transition">
                <i class="fas fa-download mr-2 text-blue-600"></i> Export
            </a>
            @endcan
        </x-slot>

        {{-- SLOT STATS --}}
        <x-slot name="stats">
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex justify-between">
                <div><p class="text-[10px] font-bold text-slate-400 uppercase">Total Anggota</p><h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats->total ?? 0) }}</h3></div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded"><i class="fas fa-users text-lg"></i></div>
            </div>
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex justify-between">
                <div><p class="text-[10px] font-bold text-slate-400 uppercase">Laki-laki</p><h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats->total_laki ?? 0) }}</h3></div>
                <div class="p-2 bg-cyan-50 text-cyan-600 rounded"><i class="fas fa-male text-lg"></i></div>
            </div>
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex justify-between">
                <div><p class="text-[10px] font-bold text-slate-400 uppercase">Perempuan</p><h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats->total_perempuan ?? 0) }}</h3></div>
                <div class="p-2 bg-pink-50 text-pink-600 rounded"><i class="fas fa-female text-lg"></i></div>
            </div>
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex justify-between">
                <div><p class="text-[10px] font-bold text-slate-400 uppercase">Kepala Keluarga</p><h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats->total_kk ?? 0) }}</h3></div>
                <div class="p-2 bg-purple-50 text-purple-600 rounded"><i class="fas fa-house-user text-lg"></i></div>
            </div>
        </x-slot>

        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.anggota-jemaat.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Filter Klasis & Jemaat (Khusus Admin Atas) --}}
                @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3', 'Admin Klasis']))
                    <x-form-select name="klasis_id" onchange="this.form.submit()">
                        <option value="">- Semua Klasis -</option>
                        @foreach($klasisFilterOptions ?? [] as $id => $nama)
                            <option value="{{ $id }}" {{ request('klasis_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </x-form-select>
                    
                    <x-form-select name="jemaat_id" onchange="this.form.submit()">
                        <option value="">- Semua Jemaat -</option>
                        @foreach($jemaatFilterOptions ?? [] as $id => $nama)
                            <option value="{{ $id }}" {{ request('jemaat_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </x-form-select>
                @endif

                <x-form-select name="status_keanggotaan" onchange="this.form.submit()">
                    <option value="">- Semua Status -</option>
                    <option value="Aktif" {{ request('status_keanggotaan') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Pindah" {{ request('status_keanggotaan') == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                    <option value="Meninggal" {{ request('status_keanggotaan') == 'Meninggal' ? 'selected' : '' }}>Meninggal</option>
                    <option value="Tidak Aktif" {{ request('status_keanggotaan') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </x-form-select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-9 border-slate-300 rounded text-sm focus:ring-slate-500" placeholder="Cari Nama / NIK...">
                    <i class="fas fa-search absolute left-3 top-2.5 text-slate-400"></i>
                </div>
                
                <button type="submit" class="hidden">Cari</button>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4">Profil Anggota</th>
            <th class="px-6 py-4">No. Induk / NIK</th>
            <th class="px-6 py-4">Jemaat</th>
            <th class="px-6 py-4">Usia & Gender</th>
            <th class="px-6 py-4 text-center">Status</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse ($anggotaJemaatData as $anggota)
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs mr-3 border border-slate-200">
                            {{ substr($anggota->nama_lengkap, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-800">
                                <a href="{{ route('admin.anggota-jemaat.show', $anggota->id) }}" class="hover:text-blue-600 hover:underline">
                                    {{ $anggota->nama_lengkap }}
                                </a>
                            </div>
                            <div class="text-[10px] text-slate-500 uppercase">{{ $anggota->status_dalam_keluarga ?? '-' }}</div>
                        </div>
                    </div>
                </x-td>
                <x-td>
                    <div class="font-mono text-xs text-slate-600">{{ $anggota->nomor_buku_induk ?? '-' }}</div>
                    <div class="text-[10px] text-slate-400">{{ $anggota->nik }}</div>
                </x-td>
                <x-td>
                    <div class="text-slate-800 font-medium text-xs">{{ $anggota->jemaat->nama_jemaat ?? '-' }}</div>
                    @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']))
                        <div class="text-[10px] text-slate-500">{{ $anggota->jemaat->klasis->nama_klasis ?? '-' }}</div>
                    @endif
                </x-td>
                <x-td>
                    <div class="text-xs text-slate-700">{{ $anggota->tanggal_lahir ? \Carbon\Carbon::parse($anggota->tanggal_lahir)->age . ' Thn' : '-' }}</div>
                    <div class="text-[10px] {{ $anggota->jenis_kelamin == 'Laki-laki' ? 'text-blue-500' : 'text-pink-500' }}">
                        {{ $anggota->jenis_kelamin == 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}
                    </div>
                </x-td>
                <x-td class="text-center">
                    @php
                        $statusColor = match($anggota->status_keanggotaan) {
                            'Aktif' => 'bg-green-100 text-green-800',
                            'Pindah' => 'bg-yellow-100 text-yellow-800',
                            'Meninggal' => 'bg-slate-200 text-slate-600',
                            default => 'bg-red-100 text-red-800'
                        };
                    @endphp
                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $statusColor }}">
                        {{ $anggota->status_keanggotaan }}
                    </span>
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center space-x-2">
                        @can('edit anggota jemaat')
                        <a href="{{ route('admin.anggota-jemaat.edit', $anggota->id) }}" class="text-slate-400 hover:text-yellow-600"><i class="fas fa-edit"></i></a>
                        @endcan
                        @can('delete anggota jemaat')
                        <form action="{{ route('admin.anggota-jemaat.destroy', $anggota->id) }}" method="POST" onsubmit="return confirm('Hapus data anggota ini?');" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                        </form>
                        @endcan
                    </div>
                </x-td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Data tidak ditemukan.</td></tr>
        @endforelse

        {{-- SLOT PAGINATION (Jika komponen x-admin-index mendukung slot ini) --}}
        <x-slot name="pagination">
            <div class="mt-4">
                {{ $anggotaJemaatData->links() }}
            </div>
        </x-slot>

    </x-admin-index>

    {{-- FALLBACK MANUAL PAGINATION (Berjaga-jaga jika komponen x-admin-index menolak render slot) --}}
    @if ($anggotaJemaatData->hasPages())
        <div class="mt-4 px-6 mb-6">
            {{ $anggotaJemaatData->links() }}
        </div>
    @endif
@endsection