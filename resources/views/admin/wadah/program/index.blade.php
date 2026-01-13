@extends('admin.layout')

@section('title', 'Program Kerja')
@section('header-title', 'Program Kerja Wadah Kategorial')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & ACTION --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-black text-gray-800 tracking-tight uppercase">Manajemen Program</h2>
            <p class="text-sm text-gray-500">Daftar rencana dan realisasi program kerja tahunan wadah.</p>
        </div>
        <a href="{{ route('admin.wadah.program.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm flex items-center transition transform hover:-translate-y-0.5">
            <i class="fas fa-plus mr-2"></i> Buat Program Baru
        </a>
    </div>

    {{-- 2. PANEL ANALISA SEDERHANA --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card Total Program --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-blue-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Program</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_program ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-500">
                <i class="fas fa-clipboard-list text-lg"></i>
            </div>
        </div>

        {{-- Card Total RAB --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-emerald-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total RAB</p>
                <p class="text-lg font-black text-emerald-700 mt-1">Rp {{ number_format($stats->total_rab ?? 0, 0, ',', '.') }}</p>
                <p class="text-[10px] text-emerald-600">Estimasi Biaya</p>
            </div>
            <div class="w-10 h-10 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500">
                <i class="fas fa-coins text-lg"></i>
            </div>
        </div>

        {{-- Card Berjalan --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-yellow-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sedang Berjalan</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_berjalan ?? 0) }}</p>
                <p class="text-[10px] text-yellow-600">Proses Pelaksanaan</p>
            </div>
            <div class="w-10 h-10 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-500">
                <i class="fas fa-running text-lg"></i>
            </div>
        </div>

        {{-- Card Selesai --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Selesai</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_selesai ?? 0) }}</p>
                <p class="text-[10px] text-green-600">Terlaksana</p>
            </div>
            <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center text-green-500">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
        </div>
    </div>

    {{-- 3. FILTER & TABEL DATA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- Toolbar Filter --}}
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <form method="GET" action="{{ route('admin.wadah.program.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tahun</label>
                        <select name="tahun" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                            <option value="">- Semua -</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Wadah</label>
                        <select name="jenis_wadah_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                            <option value="">- Semua -</option>
                            @foreach($jenisWadahs as $wadah)
                                <option value="{{ $wadah->id }}" {{ request('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>
                                    {{ $wadah->nama_wadah }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tingkat</label>
                        <select name="tingkat" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                            <option value="">- Semua -</option>
                            <option value="sinode" {{ request('tingkat') == 'sinode' ? 'selected' : '' }}>Sinode</option>
                            <option value="klasis" {{ request('tingkat') == 'klasis' ? 'selected' : '' }}>Klasis</option>
                            <option value="jemaat" {{ request('tingkat') == 'jemaat' ? 'selected' : '' }}>Jemaat</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Cari Program</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-9 border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Nama Program...">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-bold border-b">
                    <tr>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Tahun & Wadah</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Nama Program</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Tingkat</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Status</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider text-right">Anggaran (Est)</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($programs as $p)
                        <tr class="hover:bg-blue-50/40 transition group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="block font-bold text-gray-900">{{ $p->tahun_program }}</span>
                                <span class="inline-flex mt-1 px-2 py-0.5 text-[10px] font-bold rounded bg-blue-100 text-blue-800 border border-blue-200">
                                    {{ $p->jenisWadah->nama_wadah }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">{{ $p->nama_program }}</div>
                                @if($p->parentProgram)
                                    <div class="text-[10px] text-gray-500 mt-1 flex items-center bg-gray-50 p-1 rounded w-fit">
                                        <i class="fas fa-level-up-alt mr-1 text-gray-400"></i> 
                                        Induk: {{ $p->parentProgram->nama_program }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold uppercase text-xs text-gray-600 block">{{ $p->tingkat }}</span>
                                @if($p->tingkat == 'jemaat')
                                    <span class="text-[10px] text-gray-500">{{ $p->jemaat->nama_jemaat ?? '-' }}</span>
                                @elseif($p->tingkat == 'klasis')
                                    <span class="text-[10px] text-gray-500">{{ $p->klasis->nama_klasis ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-[10px] font-bold uppercase rounded-full bg-{{ $p->status_color }}-100 text-{{ $p->status_color }}-800">
                                    {{ $p->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right font-mono text-xs text-gray-700">
                                Rp {{ number_format($p->target_anggaran, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('admin.wadah.program.edit', $p->id) }}" class="p-2 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-500 hover:text-white transition shadow-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.wadah.program.destroy', $p->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus program ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-50 text-red-600 rounded hover:bg-red-500 hover:text-white transition shadow-sm" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-clipboard-list text-4xl mb-3 opacity-20"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider">Belum ada program kerja</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-100">
                {{ $programs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection