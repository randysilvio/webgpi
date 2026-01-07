@extends('admin.layout')

@section('title', 'Manajemen Pengurus')
@section('header-title', 'Manajemen Pengurus Wadah Kategorial')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & ACTION --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-black text-gray-800 tracking-tight uppercase">Direktori Pengurus</h2>
            <p class="text-sm text-gray-500">Kelola data pengurus Wadah Kategorial di semua tingkatan.</p>
        </div>
        <a href="{{ route('admin.wadah.pengurus.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm flex items-center transition transform hover:-translate-y-0.5">
            <i class="fas fa-plus mr-2"></i> Tambah Pengurus
        </a>
    </div>

    {{-- 2. PANEL ANALISA SEDERHANA --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card Total Pengurus --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-blue-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Personil</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-500">
                <i class="fas fa-users text-lg"></i>
            </div>
        </div>

        {{-- Card Level Jemaat --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-purple-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pengurus Jemaat</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->level_jemaat ?? 0) }}</p>
                <p class="text-[10px] text-purple-500">Tingkat Lokal</p>
            </div>
            <div class="w-10 h-10 bg-purple-50 rounded-full flex items-center justify-center text-purple-500">
                <i class="fas fa-church text-lg"></i>
            </div>
        </div>

        {{-- Card Aktif --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Aktif</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_aktif ?? 0) }}</p>
                <p class="text-[10px] text-gray-400">Periode Berjalan</p>
            </div>
            <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center text-green-500">
                <i class="fas fa-user-check text-lg"></i>
            </div>
        </div>

        {{-- Card Demisioner --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-gray-400 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Non-Aktif</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_non_aktif ?? 0) }}</p>
                <p class="text-[10px] text-gray-400">Demisioner / Selesai</p>
            </div>
            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">
                <i class="fas fa-user-clock text-lg"></i>
            </div>
        </div>
    </div>

    {{-- 3. FILTER & TABEL DATA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- Toolbar Filter --}}
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <form method="GET" action="{{ route('admin.wadah.pengurus.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Filter Wadah --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Wadah</label>
                        <select name="jenis_wadah_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                            <option value="">- Semua Wadah -</option>
                            @foreach($jenisWadahs as $wadah)
                                <option value="{{ $wadah->id }}" {{ request('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>
                                    {{ $wadah->nama_wadah }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Tingkat --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tingkat</label>
                        <select name="tingkat" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                            <option value="">- Semua Tingkat -</option>
                            <option value="sinode" {{ request('tingkat') == 'sinode' ? 'selected' : '' }}>Sinode</option>
                            <option value="klasis" {{ request('tingkat') == 'klasis' ? 'selected' : '' }}>Klasis</option>
                            <option value="jemaat" {{ request('tingkat') == 'jemaat' ? 'selected' : '' }}>Jemaat</option>
                        </select>
                    </div>

                    {{-- Filter Klasis --}}
                    @if($klasisList->count() > 0)
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Klasis</label>
                        <select name="klasis_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                            <option value="">- Semua Klasis -</option>
                            @foreach($klasisList as $klasis)
                                <option value="{{ $klasis->id }}" {{ request('klasis_id') == $klasis->id ? 'selected' : '' }}>
                                    {{ $klasis->nama_klasis }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Search --}}
                    <div class="{{ $klasisList->count() > 0 ? '' : 'md:col-span-2' }}">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Pencarian</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-9 border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Cari Nama / Jabatan / SK...">
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
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Nama Pengurus</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Jabatan & Wadah</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Lingkup Pelayanan</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Periode</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Status</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($pengurus as $p)
                        <tr class="hover:bg-blue-50/40 transition group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs mr-3">
                                        {{ substr($p->anggotaJemaat->nama_lengkap ?? 'M', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ $p->anggotaJemaat->nama_lengkap ?? 'Manual Input' }}
                                        </div>
                                        @if($p->nomor_sk)
                                            <div class="text-[10px] text-gray-500 font-mono mt-0.5">SK: {{ $p->nomor_sk }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-800 font-semibold">{{ $p->jabatan }}</div>
                                <span class="px-2 py-0.5 mt-1 inline-flex text-[10px] font-bold rounded bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $p->jenisWadah->nama_wadah }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold uppercase text-xs text-gray-600">{{ $p->tingkat }}</div>
                                @if($p->tingkat == 'klasis')
                                    <div class="text-xs text-gray-500">{{ $p->klasis->nama_klasis ?? '-' }}</div>
                                @elseif($p->tingkat == 'jemaat')
                                    <div class="text-xs text-gray-900 font-medium">{{ $p->jemaat->nama_jemaat ?? '-' }}</div>
                                    <div class="text-[10px] text-gray-400">({{ $p->klasis->nama_klasis ?? '-' }})</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600 font-mono">
                                {{ $p->periode_mulai->format('Y') }} - {{ $p->periode_selesai->format('Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($p->is_active)
                                    <span class="px-2 py-1 inline-flex text-[10px] font-bold uppercase rounded bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-[10px] font-bold uppercase rounded bg-gray-100 text-gray-500">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('admin.wadah.pengurus.edit', $p->id) }}" class="p-2 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-500 hover:text-white transition shadow-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.wadah.pengurus.destroy', $p->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data pengurus ini?');">
                                        @csrf
                                        @method('DELETE')
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
                                    <i class="fas fa-users-slash text-4xl mb-3 opacity-20"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider">Belum ada data pengurus</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($pengurus->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $pengurus->links() }}
            </div>
        @endif
    </div>
</div>
@endsection