@extends('admin.layout')

@section('title', 'Manajemen Jemaat')
@section('header-title', 'Daftar Jemaat GPI Papua')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & ACTION --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-black text-gray-800 tracking-tight uppercase">Direktori Jemaat</h2>
            <p class="text-sm text-gray-500">Database unit pelayanan tingkat Jemaat.</p>
        </div>
        <div class="flex flex-wrap gap-2">
             @can('import jemaat')
             <a href="{{ route('admin.jemaat.import-form') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm text-xs uppercase tracking-wider flex items-center">
                <i class="fas fa-file-excel mr-2"></i> Import
            </a>
            @endcan
             @can('export jemaat')
             <a href="{{ route('admin.jemaat.export', request()->query()) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm text-xs uppercase tracking-wider flex items-center">
                 <i class="fas fa-download mr-2"></i> Export
             </a>
             @endcan
            @can('create jemaat')
            <a href="{{ route('admin.jemaat.create') }}" class="bg-primary hover:bg-blue-800 text-white font-bold py-2 px-4 rounded-lg shadow-sm text-xs uppercase tracking-wider flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Jemaat
            </a>
            @endcan
        </div>
    </div>

    {{-- 2. PANEL ANALISA SEDERHANA --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card Total Jemaat --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-primary flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Jemaat</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-primary">
                <i class="fas fa-church text-lg"></i>
            </div>
        </div>

        {{-- Card Mandiri --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jemaat Mandiri</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_mandiri ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center text-green-500">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
        </div>

        {{-- Card Pos Pelayanan/Bakal --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-yellow-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pos Pelayanan</p>
                <p class="text-2xl font-black text-gray-800 mt-1">
                    {{ number_format(($stats->total_bakal ?? 0) + ($stats->total_pos ?? 0)) }}
                </p>
                <p class="text-[10px] text-gray-400">Termasuk Bakal Jemaat</p>
            </div>
            <div class="w-10 h-10 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-500">
                <i class="fas fa-home text-lg"></i>
            </div>
        </div>

        {{-- Card Total Jiwa --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-purple-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Jiwa</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_jiwa ?? 0) }}</p>
                <p class="text-[10px] text-purple-500">Warga Jemaat</p>
            </div>
            <div class="w-10 h-10 bg-purple-50 rounded-full flex items-center justify-center text-purple-500">
                <i class="fas fa-users text-lg"></i>
            </div>
        </div>
    </div>

    {{-- 3. FILTER & TABEL DATA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- Toolbar Filter --}}
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <form method="GET" action="{{ route('admin.jemaat.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Filter Klasis --}}
                    @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']) && isset($klasisFilterOptions) && $klasisFilterOptions->count() > 0)
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Klasis</label>
                            <select name="klasis_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary" onchange="this.form.submit()">
                                <option value="">-- Semua Klasis --</option>
                                @foreach($klasisFilterOptions as $id => $nama)
                                    <option value="{{ $id }}" {{ $request->input('klasis_id') == $id ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Search --}}
                    <div class="{{ Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']) ? 'md:col-span-3' : 'md:col-span-4' }}">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Pencarian</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $request->input('search') }}" class="w-full pl-9 border-gray-300 rounded-lg text-sm focus:ring-primary" placeholder="Cari Nama atau Kode Jemaat...">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-gray-500 font-bold border-b">
                    <tr>
                        <th class="px-6 py-4 w-16 font-bold uppercase text-[10px] tracking-wider">ID</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Nama Jemaat</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Klasis</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center uppercase text-[10px] tracking-wider">Statistik</th>
                        <th class="px-6 py-4 text-center uppercase text-[10px] tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($jemaatData as $jemaat)
                        <tr class="hover:bg-blue-50/40 transition group">
                            <td class="px-6 py-4 font-mono text-gray-400 font-bold text-xs">
                                #{{ $jemaat->id }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.jemaat.show', $jemaat->id) }}" class="font-bold text-gray-900 hover:text-primary hover:underline text-sm block">
                                    {{ $jemaat->nama_jemaat }}
                                </a>
                                <div class="text-[10px] text-gray-500 mt-0.5">{{ $jemaat->kode_jemaat ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $jemaat->klasis->nama_klasis ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClass = match($jemaat->status_jemaat) {
                                        'Mandiri' => 'bg-green-100 text-green-800 border-green-200',
                                        'Bakal Jemaat' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'Pos Pelayanan' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        default => 'bg-gray-100 text-gray-800 border-gray-200'
                                    };
                                @endphp
                                <span class="px-2 py-1 inline-flex text-[10px] font-bold rounded border {{ $statusClass }}">
                                    {{ $jemaat->status_jemaat }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-4 text-xs">
                                    <div class="text-center">
                                        <span class="block font-bold text-gray-800">{{ $jemaat->jumlah_kk ?? 0 }}</span>
                                        <span class="text-[9px] text-gray-400 uppercase">KK</span>
                                    </div>
                                    <div class="w-px h-8 bg-gray-200"></div>
                                    <div class="text-center">
                                        <span class="block font-bold text-gray-800">{{ $jemaat->jumlah_total_jiwa ?? 0 }}</span>
                                        <span class="text-[9px] text-gray-400 uppercase">Jiwa</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    @can('edit jemaat')
                                    <a href="{{ route('admin.jemaat.edit', $jemaat->id) }}" class="p-2 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-500 hover:text-white transition shadow-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan

                                    @can('delete jemaat')
                                    <form action="{{ route('admin.jemaat.destroy', $jemaat->id) }}" method="POST" class="inline-block" onsubmit="return confirm('PERHATIAN:\nMenghapus Jemaat juga akan menghapus SEMUA data Anggota Jemaat di dalamnya!\n\nApakah Anda benar-benar yakin ingin menghapus Jemaat {{ $jemaat->nama_jemaat }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-50 text-red-600 rounded hover:bg-red-500 hover:text-white transition shadow-sm" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-church text-4xl mb-3 opacity-20"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider">Belum ada data jemaat</span>
                                    @can('create jemaat')
                                    <a href="{{ route('admin.jemaat.create') }}" class="mt-2 text-primary hover:underline text-sm">Tambah Baru?</a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($jemaatData->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $jemaatData->links() }}
            </div>
        @endif
    </div>
</div>
@endsection