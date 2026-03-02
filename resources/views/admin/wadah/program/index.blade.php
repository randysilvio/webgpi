@extends('layouts.app')

@section('title', 'Program Kerja')

@section('content')
    <x-admin-index 
        title="Manajemen Program" 
        subtitle="Daftar rencana dan realisasi program kerja tahunan wadah kategorial."
        create-route="{{ route('admin.wadah.program.create') }}"
        create-label="Buat Program Baru"
        :pagination="$programs"
    >
        {{-- SLOT STATS --}}
        <x-slot name="stats">
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Program</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_program ?? 0) }}</p>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i class="fas fa-clipboard-list text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total RAB</p>
                    <p class="text-xl font-bold text-emerald-700 mt-1">Rp {{ number_format($stats->total_rab ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg"><i class="fas fa-coins text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sedang Berjalan</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_berjalan ?? 0) }}</p>
                </div>
                <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg"><i class="fas fa-running text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Selesai</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_selesai ?? 0) }}</p>
                </div>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg"><i class="fas fa-check-circle text-lg"></i></div>
            </div>
        </x-slot>

        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.wadah.program.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                
                <x-form-select name="tahun" onchange="this.form.submit()">
                    <option value="">- Semua Tahun -</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </x-form-select>

                <x-form-select name="jenis_wadah_id" onchange="this.form.submit()">
                    <option value="">- Semua Wadah -</option>
                    @foreach($jenisWadahs as $wadah)
                        <option value="{{ $wadah->id }}" {{ request('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>
                            {{ $wadah->nama_wadah }}
                        </option>
                    @endforeach
                </x-form-select>

                <x-form-select name="tingkat" onchange="this.form.submit()">
                    <option value="">- Semua Tingkat -</option>
                    <option value="sinode" {{ request('tingkat') == 'sinode' ? 'selected' : '' }}>Sinode</option>
                    <option value="klasis" {{ request('tingkat') == 'klasis' ? 'selected' : '' }}>Klasis</option>
                    <option value="jemaat" {{ request('tingkat') == 'jemaat' ? 'selected' : '' }}>Jemaat</option>
                </x-form-select>

                <div class="md:col-span-2 relative">
                    <x-form-input name="search" value="{{ request('search') }}" placeholder="Cari Nama Program..." />
                    <button type="submit" class="absolute right-3 top-2 text-slate-400 hover:text-blue-600">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4">Nama Program</th>
            <th class="px-6 py-4">Tahun & Wadah</th>
            <th class="px-6 py-4">Tingkat Struktur</th>
            <th class="px-6 py-4 text-center">Status</th>
            <th class="px-6 py-4 text-right">RAB (Estimasi)</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse($programs as $p)
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <div class="font-bold text-slate-800 text-sm">{{ $p->nama_program }}</div>
                    @if($p->parentProgram)
                        <div class="text-[10px] text-slate-500 mt-1 flex items-center bg-slate-100 px-2 py-0.5 rounded w-fit border border-slate-200">
                            <i class="fas fa-level-up-alt mr-1.5 text-slate-400"></i> 
                            Induk: {{ Str::limit($p->parentProgram->nama_program, 30) }}
                        </div>
                    @endif
                </x-td>
                <x-td>
                    <span class="block font-bold text-slate-700">{{ $p->tahun_program }}</span>
                    <span class="text-[10px] text-blue-600 font-bold uppercase">{{ $p->jenisWadah->nama_wadah }}</span>
                </x-td>
                <x-td>
                    <span class="font-bold uppercase text-xs text-slate-600 block">{{ $p->tingkat }}</span>
                    @if($p->tingkat == 'jemaat')
                        <div class="text-[10px] text-slate-500">{{ $p->jemaat->nama_jemaat ?? '-' }}</div>
                    @elseif($p->tingkat == 'klasis')
                        <div class="text-[10px] text-slate-500">{{ $p->klasis->nama_klasis ?? '-' }}</div>
                    @endif
                </x-td>
                <x-td class="text-center">
                    <span class="px-2 py-1 inline-flex text-[10px] font-bold uppercase rounded-full bg-{{ $p->status_color }}-100 text-{{ $p->status_color }}-700 border border-{{ $p->status_color }}-200">
                        {{ $p->status_label }}
                    </span>
                </x-td>
                <x-td class="text-right font-mono text-xs text-slate-700">
                    Rp {{ number_format($p->target_anggaran, 0, ',', '.') }}
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.wadah.program.edit', $p->id) }}" class="text-slate-400 hover:text-yellow-600 transition" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.wadah.program.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus program ini?');">
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
                <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada program kerja yang dibuat.</td>
            </tr>
        @endforelse

    </x-admin-index>
@endsection