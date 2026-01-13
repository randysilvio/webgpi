@extends('admin.layout')

@section('title', 'Manajemen Klasis')
@section('header-title', 'Data Wilayah Pelayanan (Klasis)')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & ACTION --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-black text-gray-800 tracking-tight uppercase">Direktori Klasis</h2>
            <p class="text-sm text-gray-500">Daftar wilayah pelayanan tingkat Klasis se-Tanah Papua.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @hasanyrole('Super Admin|Admin Bidang 3')
             <a href="{{ route('admin.klasis.import-form') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm text-xs uppercase tracking-wider flex items-center">
                <i class="fas fa-file-excel mr-2"></i> Import
            </a>
             <a href="{{ route('admin.klasis.export') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm text-xs uppercase tracking-wider flex items-center">
                 <i class="fas fa-download mr-2"></i> Export
             </a>
            <a href="{{ route('admin.klasis.create') }}" class="bg-primary hover:bg-blue-800 text-white font-bold py-2 px-4 rounded-lg shadow-sm text-xs uppercase tracking-wider flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Klasis
            </a>
            @endhasanyrole
        </div>
    </div>

    {{-- 2. PANEL ANALISA (STYLE SAMAKAN DENGAN JEMAAT) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card Total Klasis --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-primary flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Klasis</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_klasis ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-primary">
                <i class="fas fa-map-marked-alt text-lg"></i>
            </div>
        </div>

        {{-- Card Total Jemaat (Pakai Style Indigo agar beda warna dikit tapi ukuran sama) --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-indigo-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Jemaat</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_jemaat ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-500">
                <i class="fas fa-church text-lg"></i>
            </div>
        </div>

        {{-- Card Jemaat Mandiri --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jemaat Mandiri</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_jemaat_mandiri ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center text-green-500">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
        </div>

        {{-- Card Jemaat Pos/Bakal --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-yellow-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pos Pelayanan</p>
                <p class="text-2xl font-black text-gray-800 mt-1">
                    {{ number_format($stats->total_jemaat_pos ?? 0) }}
                </p>
                <p class="text-[10px] text-gray-400">Termasuk Bakal Jemaat</p>
            </div>
            <div class="w-10 h-10 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-500">
                <i class="fas fa-home text-lg"></i>
            </div>
        </div>
    </div>

    {{-- 3. FILTER & TABEL DATA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- Toolbar Filter --}}
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <form method="GET" action="{{ route('admin.klasis.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Search --}}
                    <div class="md:col-span-4">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Pencarian</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $request->input('search') }}" class="w-full pl-9 border-gray-300 rounded-lg text-sm focus:ring-primary" placeholder="Cari Nama Klasis, Kode, atau Kota...">
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
                        <th class="px-6 py-4 w-20 font-bold uppercase text-[10px] tracking-wider">Kode</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Klasis & Ketua</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Pusat & Kontak</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider hidden md:table-cell">Wilayah</th>
                        <th class="px-6 py-4 text-center uppercase text-[10px] tracking-wider">Jemaat</th>
                        <th class="px-6 py-4 text-center uppercase text-[10px] tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($klasisData as $klasis)
                        <tr class="hover:bg-blue-50/40 transition group">
                            {{-- Kode --}}
                            <td class="px-6 py-4">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-mono font-bold">
                                    {{ $klasis->kode_klasis }}
                                </span>
                            </td>
                            
                            {{-- Nama & Ketua --}}
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.klasis.show', $klasis->id) }}" class="font-bold text-gray-900 hover:text-primary hover:underline text-sm block">
                                    {{ $klasis->nama_klasis }}
                                </a>
                                <div class="flex items-center text-xs text-gray-500 mt-1">
                                    <i class="fas fa-user-tie mr-1.5 w-3 text-center"></i>
                                    @if($klasis->ketuaMp)
                                        <span class="text-gray-700">{{ $klasis->ketuaMp->nama_lengkap }}</span>
                                    @else
                                        <span class="text-red-400 italic">Belum ada Ketua</span>
                                    @endif
                                </div>
                                @if($klasis->tanggal_pembentukan)
                                <div class="flex items-center text-[10px] text-gray-400 mt-0.5">
                                    <i class="fas fa-calendar-alt mr-1.5 w-3 text-center"></i>
                                    <span>Est. {{ $klasis->tanggal_pembentukan->format('Y') }}</span>
                                </div>
                                @endif
                            </td>

                            {{-- Pusat & Kontak --}}
                            <td class="px-6 py-4 text-gray-600">
                                <div class="font-medium text-xs mb-1 flex items-center">
                                    <i class="fas fa-map-pin mr-1.5 w-3 text-center text-red-400"></i>
                                    {{ $klasis->pusat_klasis ?? '-' }}
                                </div>
                                @if($klasis->telepon_kantor)
                                <div class="text-[11px] flex items-center">
                                    <i class="fas fa-phone mr-1.5 w-3 text-center text-green-400"></i>
                                    {{ $klasis->telepon_kantor }}
                                </div>
                                @endif
                                @if($klasis->email_klasis)
                                <div class="text-[11px] flex items-center truncate max-w-[150px]" title="{{ $klasis->email_klasis }}">
                                    <i class="fas fa-envelope mr-1.5 w-3 text-center text-blue-400"></i>
                                    {{ Str::limit($klasis->email_klasis, 20) }}
                                </div>
                                @endif
                            </td>

                            {{-- Wilayah (Hidden di mobile) --}}
                            <td class="px-6 py-4 hidden md:table-cell">
                                <p class="text-xs text-gray-500 line-clamp-2" title="{{ $klasis->wilayah_pelayanan }}">
                                    {{ $klasis->wilayah_pelayanan ?? '-' }}
                                </p>
                            </td>

                            {{-- Jumlah Jemaat --}}
                            <td class="px-6 py-4 text-center">
                                <span class="bg-indigo-50 text-indigo-700 px-2.5 py-0.5 rounded-full text-xs font-bold border border-indigo-100">
                                    {{ $klasis->jemaat_count }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex justify-center items-center space-x-2">
                                    <a href="{{ route('admin.klasis.show', $klasis->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-500 hover:text-white transition shadow-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @hasanyrole('Super Admin|Admin Bidang 3')
                                    <a href="{{ route('admin.klasis.edit', $klasis->id) }}" class="p-2 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-500 hover:text-white transition shadow-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.klasis.destroy', $klasis->id) }}" method="POST" class="inline-block" onsubmit="return confirm('PERHATIAN: Menghapus Klasis ini?\n\nSyarat: Klasis harus kosong (tidak ada Jemaat).\n\nLanjutkan hapus {{ $klasis->nama_klasis }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-50 text-red-600 rounded hover:bg-red-500 hover:text-white transition shadow-sm" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endhasanyrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-map-signs text-4xl mb-3 opacity-20"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider">Belum ada data Klasis</span>
                                    @hasanyrole('Super Admin|Admin Bidang 3')
                                    <a href="{{ route('admin.klasis.create') }}" class="mt-2 text-primary hover:underline text-sm">Tambah Baru?</a>
                                    @endhasanyrole
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($klasisData->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $klasisData->links() }}
            </div>
        @endif
    </div>
</div>
@endsection