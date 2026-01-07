@extends('admin.layout')

@section('title', 'Inventaris Aset')
@section('header-title', 'Inventaris Harta Milik Gereja')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & ACTION --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-black text-gray-800 tracking-tight uppercase">Database Inventaris</h2>
            <p class="text-sm text-gray-500">Kelola aset bergerak, tidak bergerak, dan inventaris lainnya.</p>
        </div>
        <a href="{{ route('admin.perbendaharaan.aset.create') }}" class="bg-primary hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg text-sm font-bold uppercase tracking-wider shadow-lg transition transform hover:-translate-y-0.5 flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Catat Aset Baru
        </a>
    </div>

    {{-- 2. PANEL ANALISA SEDERHANA --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card Total Item --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-blue-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Item</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_item ?? 0) }}</p>
                <p class="text-[10px] text-gray-400">Unit Inventaris</p>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-500">
                <i class="fas fa-boxes text-lg"></i>
            </div>
        </div>

        {{-- Card Total Nilai --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-emerald-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Estimasi Nilai Aset</p>
                <p class="text-lg font-black text-emerald-700 mt-1">Rp {{ number_format($stats->total_nilai ?? 0, 0, ',', '.') }}</p>
                <p class="text-[10px] text-gray-400">Nilai Perolehan</p>
            </div>
            <div class="w-10 h-10 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500">
                <i class="fas fa-coins text-lg"></i>
            </div>
        </div>

        {{-- Card Kondisi Baik --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kondisi Baik</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_baik ?? 0) }}</p>
                <p class="text-[10px] text-green-600">Layak Pakai</p>
            </div>
            <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center text-green-500">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
        </div>

        {{-- Card Kondisi Rusak --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-red-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Perlu Perbaikan</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_rusak ?? 0) }}</p>
                <p class="text-[10px] text-red-500">Rusak Ringan/Berat</p>
            </div>
            <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center text-red-500">
                <i class="fas fa-wrench text-lg"></i>
            </div>
        </div>
    </div>

    {{-- 3. TABEL DATA & FILTER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- Toolbar Filter --}}
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <form method="GET" action="{{ route('admin.perbendaharaan.aset.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Search --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Cari Aset</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Kode Aset..." class="w-full pl-9 border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        </div>
                    </div>

                    {{-- Filter Kategori --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Kategori</label>
                        <select name="kategori" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary" onchange="this.form.submit()">
                            <option value="">- Semua Kategori -</option>
                            @foreach(['Tanah', 'Gedung', 'Kendaraan', 'Peralatan Elektronik', 'Meubelair', 'Alat Musik', 'Lainnya'] as $cat)
                                <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Kondisi --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Kondisi</label>
                        <select name="kondisi" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary" onchange="this.form.submit()">
                            <option value="">- Semua Kondisi -</option>
                            <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>

                    {{-- Tombol Reset --}}
                    <div class="flex items-end">
                        <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="w-full bg-white border border-gray-300 text-gray-600 py-2 rounded-lg text-sm font-bold hover:bg-gray-100 transition text-center">
                            Reset Filter
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-gray-500 font-bold border-b">
                    <tr>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Aset / Kode</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Lokasi & Kategori</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Kondisi</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider text-right">Nilai Perolehan</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($asets as $aset)
                        <tr class="hover:bg-blue-50/40 transition group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded bg-indigo-50 text-indigo-600 flex items-center justify-center mr-3 border border-indigo-100">
                                        <i class="fas fa-cube text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $aset->nama_aset }}</div>
                                        <div class="text-[10px] text-gray-500 font-mono mt-0.5">{{ $aset->kode_aset }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="block text-xs font-bold text-gray-700">{{ $aset->jemaat->nama_jemaat ?? ($aset->klasis->nama_klasis ?? 'Sinode') }}</span>
                                <span class="inline-flex mt-1 px-2 py-0.5 text-[10px] rounded bg-gray-100 text-gray-600 border border-gray-200">
                                    {{ $aset->kategori }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $color = match($aset->kondisi) {
                                        'Baik' => 'green',
                                        'Rusak Ringan' => 'yellow',
                                        default => 'red',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-{{ $color }}-100 text-{{ $color }}-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500 mr-1.5"></span>
                                    {{ $aset->kondisi }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-xs text-gray-700">
                                {{ $aset->format_nilai }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('admin.perbendaharaan.aset.show', $aset->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-500 hover:text-white transition shadow-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.perbendaharaan.aset.destroy', $aset->id) }}" method="POST" onsubmit="return confirm('Hapus aset ini? Tindakan ini tidak dapat dibatalkan.')">
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
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-box-open text-4xl mb-3 opacity-20"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider">Belum ada data aset</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($asets->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $asets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection