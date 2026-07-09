@extends('layouts.app')

@section('title', 'Dokumen Program Kerja Kategorial')

@section('content')
<div class="space-y-6">

    {{-- HEADER & TOOLS --}}
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-blue-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Matriks Program Kategorial</h2>
            <p class="text-xs text-gray-600 mt-1">Sistem Perencanaan, Realisasi, dan Pemantauan Program Kerja Wadah Pelayanan.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.wadah.program.create') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm transition flex items-center justify-center">
                <i class="fas fa-plus-circle mr-2"></i> Rancang Program Baru
            </a>
        </div>
    </div>

    {{-- STATISTIK RINGKAS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Total Agenda Program</p>
            <p class="text-xl font-black text-gray-900 mt-1">{{ number_format($stats->total_program ?? 0) }}</p>
        </div>
        <div class="bg-gray-800 p-4 border border-gray-900 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Estimasi Total RAB</p>
            <p class="text-xl font-mono font-black text-white mt-1">Rp {{ number_format($stats->total_rab ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-b-4 border-b-blue-800">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Fase Eksekusi (Berjalan)</p>
            <p class="text-xl font-black text-blue-900 mt-1">{{ number_format($stats->total_berjalan ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-b-4 border-b-green-700">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Agenda Selesai</p>
            <p class="text-xl font-black text-green-800 mt-1">{{ number_format($stats->total_selesai ?? 0) }}</p>
        </div>
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-gray-50 border border-gray-200 rounded p-4 shadow-sm flex flex-col md:flex-row gap-4">
        <form action="{{ route('admin.wadah.program.index') }}" method="GET" class="w-full grid grid-cols-1 md:grid-cols-5 gap-4">
            
            <select name="tahun" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                <option value="">- Tahun Pelaksanaan -</option>
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>T.A. {{ $y }}</option>
                @endforeach
            </select>

            <select name="jenis_wadah_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                <option value="">- Spesifikasi Wadah -</option>
                @foreach($jenisWadahs as $wadah)
                    <option value="{{ $wadah->id }}" {{ request('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>
                        {{ $wadah->nama_wadah }}
                    </option>
                @endforeach
            </select>

            <select name="tingkat" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                <option value="">- Hierarki Pelaksanaan -</option>
                <option value="sinode" {{ request('tingkat') == 'sinode' ? 'selected' : '' }}>PUSAT SINODE</option>
                <option value="klasis" {{ request('tingkat') == 'klasis' ? 'selected' : '' }}>REGIONAL KLASIS</option>
                <option value="jemaat" {{ request('tingkat') == 'jemaat' ? 'selected' : '' }}>LOKAL JEMAAT</option>
            </select>

            <div class="relative md:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="pl-10 w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" 
                    placeholder="Pencarian Nama Program atau Kegiatan...">
            </div>

            <button type="submit" class="hidden">Saring</button>
        </form>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                        <th class="px-5 py-3 w-1/4">Identifikasi Program</th>
                        <th class="px-5 py-3">Organisasi Pelaksana</th>
                        <th class="px-5 py-3">Yurisdiksi (Wilayah)</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">RAB Proyeksi</th>
                        <th class="px-5 py-3 text-center w-24">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($programs as $p)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-900 uppercase text-xs mb-1.5 leading-snug">{{ $p->nama_program }}</div>
                                @if($p->parentProgram)
                                    <div class="text-[9px] text-gray-500 font-bold tracking-widest bg-gray-100 px-2 py-1 rounded inline-flex items-center border border-gray-200">
                                        <i class="fas fa-link mr-1.5 text-blue-800"></i> INDUK: {{ Str::limit(strtoupper($p->parentProgram->nama_program), 30) }}
                                    </div>
                                @else
                                    <div class="text-[8px] text-gray-400 font-bold uppercase tracking-widest italic">Program Mandiri</div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="block font-mono font-black text-gray-800 mb-1">T.A. {{ $p->tahun_program }}</span>
                                <span class="text-[9px] text-blue-800 font-bold uppercase tracking-widest bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 inline-block">
                                    {{ $p->jenisWadah->nama_wadah }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-black uppercase text-[10px] text-gray-600 tracking-widest mb-1">{{ $p->tingkat }}</div>
                                @if($p->tingkat == 'klasis')
                                    <div class="text-[10px] text-gray-900 font-bold uppercase"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> {{ $p->klasis->nama_klasis ?? '-' }}</div>
                                @elseif($p->tingkat == 'jemaat')
                                    <div class="text-[10px] text-gray-900 font-bold uppercase"><i class="fas fa-church text-gray-400 mr-1"></i> {{ $p->jemaat->nama_jemaat ?? '-' }}</div>
                                    <div class="text-[8px] text-gray-400 uppercase tracking-widest">({{ $p->klasis->nama_klasis ?? '-' }})</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @php
                                    $statusConfig = match($p->status_pelaksanaan) {
                                        1 => ['bg-blue-100', 'text-blue-800', 'border-blue-300'],
                                        2 => ['bg-green-100', 'text-green-800', 'border-green-300'],
                                        3 => ['bg-yellow-100', 'text-yellow-800', 'border-yellow-300'],
                                        4 => ['bg-red-100', 'text-red-800', 'border-red-300'],
                                        default => ['bg-gray-100', 'text-gray-600', 'border-gray-300'],
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border {{ $statusConfig[0] }} {{ $statusConfig[1] }} {{ $statusConfig[2] }}">
                                    {{ $p->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <span class="font-mono text-xs font-black text-gray-800">Rp {{ number_format($p->target_anggaran, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.wadah.program.edit', $p->id) }}" class="text-gray-400 hover:text-yellow-600 transition" title="Modifikasi Dokumen">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <form action="{{ route('admin.wadah.program.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Peringatan: Pemusnahan data ini akan menghapus arsip rencana program. Lanjutkan?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-700 transition" title="Batalkan/Hapus Program">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500 text-sm">
                                <i class="fas fa-clipboard-list text-3xl mb-3 block text-gray-300"></i>
                                Pangkalan data program kerja wadah belum tersedia atau filter tidak sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($programs->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">
                {{ $programs->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection