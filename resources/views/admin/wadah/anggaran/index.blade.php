@extends('layouts.app')

@section('title', 'Anggaran & Realisasi')

@section('content')
    <x-admin-index 
        title="Anggaran & Realisasi Wadah" 
        subtitle="Kelola Rencana Anggaran Belanja (RAB) dan monitoring realisasi keuangan."
        create-route="{{ route('admin.wadah.anggaran.create') }}"
        create-label="Buat Pos Anggaran"
        :pagination="$anggarans"
    >
        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.wadah.anggaran.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                
                {{-- Filter Tahun --}}
                <x-form-select name="tahun" onchange="this.form.submit()">
                    <option value="">- Semua Tahun -</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </x-form-select>

                {{-- Filter Wadah --}}
                <x-form-select name="wadah" onchange="this.form.submit()">
                    <option value="">- Semua Wadah -</option>
                    @foreach($jenisWadahs as $w)
                        <option value="{{ $w->id }}" {{ request('wadah') == $w->id ? 'selected' : '' }}>{{ $w->nama_wadah }}</option>
                    @endforeach
                </x-form-select>

                {{-- Filter Tingkat --}}
                <x-form-select name="tingkat" onchange="this.form.submit()">
                    <option value="">- Semua Tingkat -</option>
                    <option value="sinode" {{ request('tingkat') == 'sinode' ? 'selected' : '' }}>Sinode</option>
                    <option value="klasis" {{ request('tingkat') == 'klasis' ? 'selected' : '' }}>Klasis</option>
                    <option value="jemaat" {{ request('tingkat') == 'jemaat' ? 'selected' : '' }}>Jemaat</option>
                </x-form-select>

                {{-- Filter Jenis --}}
                <x-form-select name="jenis" onchange="this.form.submit()">
                    <option value="">- Jenis Anggaran -</option>
                    <option value="penerimaan" {{ request('jenis') == 'penerimaan' ? 'selected' : '' }}>Penerimaan</option>
                    <option value="pengeluaran" {{ request('jenis') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                </x-form-select>

                {{-- Tombol Filter (Optional jika onchange sudah ada, tapi bagus untuk UX) --}}
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 px-4 rounded text-sm transition shadow-sm flex items-center justify-center">
                    <i class="fas fa-filter mr-2"></i> Terapkan
                </button>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4">Detail Pos Anggaran</th>
            <th class="px-6 py-4 text-center">Jenis</th>
            <th class="px-6 py-4 text-right">Target (Rp)</th>
            <th class="px-6 py-4 text-right">Realisasi (Rp)</th>
            <th class="px-6 py-4 w-1/6">Capaian</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse($anggarans as $a)
            @php
                $persen = $a->jumlah_target > 0 ? ($a->jumlah_realisasi / $a->jumlah_target) * 100 : 0;
                // Logika Warna Progress Bar
                $color = $persen >= 100 ? 'bg-green-500' : ($persen >= 50 ? 'bg-yellow-500' : 'bg-red-500');
                $textColor = $persen >= 100 ? 'text-green-600' : ($persen >= 50 ? 'text-yellow-600' : 'text-red-600');
                
                // Jika Pengeluaran, logika warna dibalik (Over budget = Merah)
                if($a->jenis_anggaran == 'pengeluaran') {
                    $color = $persen > 100 ? 'bg-red-500' : ($persen >= 80 ? 'bg-yellow-500' : 'bg-green-500');
                    $textColor = $persen > 100 ? 'text-red-600' : ($persen >= 80 ? 'text-yellow-600' : 'text-green-600');
                }
            @endphp
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <div class="font-bold text-slate-800">{{ $a->nama_pos_anggaran }}</div>
                    <div class="text-[10px] text-slate-500 mt-1 uppercase tracking-wide">
                        {{ $a->tahun_anggaran }} <span class="mx-1">•</span> {{ $a->jenisWadah->nama_wadah }} <span class="mx-1">•</span> {{ strtoupper($a->tingkat) }}
                    </div>
                    @if($a->programKerja)
                        <div class="text-xs text-blue-600 mt-1 flex items-center">
                            <i class="fas fa-link mr-1"></i> {{ Str::limit($a->programKerja->nama_program, 30) }}
                        </div>
                    @endif
                </x-td>
                <x-td class="text-center">
                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $a->jenis_anggaran == 'penerimaan' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                        {{ ucfirst($a->jenis_anggaran) }}
                    </span>
                </x-td>
                <x-td class="text-right font-mono text-slate-600">
                    {{ number_format($a->jumlah_target, 0, ',', '.') }}
                </x-td>
                <x-td class="text-right font-mono font-bold text-slate-800">
                    {{ number_format($a->jumlah_realisasi, 0, ',', '.') }}
                </x-td>
                <x-td>
                    <div class="flex items-center">
                        <span class="mr-2 text-xs font-bold {{ $textColor }}">{{ round($persen) }}%</span>
                        <div class="w-full bg-slate-200 rounded-full h-1.5">
                            <div class="{{ $color }} h-1.5 rounded-full" style="width: {{ min($persen, 100) }}%"></div>
                        </div>
                    </div>
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.wadah.anggaran.show', $a->id) }}" class="text-blue-500 hover:text-blue-700" title="Detail & Transaksi">
                            <i class="fas fa-list-alt"></i>
                        </a>
                        <a href="{{ route('admin.wadah.anggaran.edit', $a->id) }}" class="text-slate-400 hover:text-yellow-600" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.wadah.anggaran.destroy', $a->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pos anggaran ini? Transaksi terkait juga akan terhapus.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </x-td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada pos anggaran yang dibuat.</td>
            </tr>
        @endforelse

    </x-admin-index>
@endsection