@extends('layouts.app')

@section('title', 'Direktori Registrasi Jemaat')

@section('content')
<div class="space-y-6">

    {{-- HEADER & TOOLS --}}
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-blue-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Buku Register Jemaat</h2>
            <p class="text-xs text-gray-600 mt-1">Pangkalan data pendaftaran dan status gereja / unit jemaat wilayah.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            @can('import jemaat')
            <a href="{{ route('admin.jemaat.import-form') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded text-[10px] font-bold uppercase hover:bg-gray-100 transition flex items-center">
                <i class="fas fa-file-excel mr-2 text-green-700"></i> Import Data
            </a>
            @endcan
            @can('export jemaat')
            <a href="{{ route('admin.jemaat.export', request()->query()) }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded text-[10px] font-bold uppercase hover:bg-gray-100 transition flex items-center">
                <i class="fas fa-download mr-2 text-blue-800"></i> Export Data
            </a>
            @endcan
            <a href="{{ route('admin.jemaat.create') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase shadow-sm transition flex items-center">
                <i class="fas fa-plus mr-2"></i> Pendaftaran Baru
            </a>
        </div>
    </div>

    {{-- STATISTIK RINGKAS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Total Jemaat / Gereja</p>
            <p class="text-xl font-black text-gray-900 mt-1">{{ number_format($stats->total ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-b-4 border-b-green-700">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Status: Mandiri</p>
            <p class="text-xl font-black text-green-800 mt-1">{{ number_format($stats->total_mandiri ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-b-4 border-b-yellow-600">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Bakal Jemaat / Pos Pelayanan</p>
            <p class="text-xl font-black text-yellow-800 mt-1">{{ number_format(($stats->total_bakal ?? 0) + ($stats->total_pos ?? 0)) }}</p>
        </div>
        <div class="bg-gray-800 p-4 border border-gray-900 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Akumulasi Populasi Umat</p>
            <p class="text-xl font-black text-white mt-1">{{ number_format($stats->total_jiwa ?? 0) }}</p>
        </div>
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-gray-50 border border-gray-200 rounded p-4 shadow-sm flex flex-col md:flex-row gap-4">
        <form action="{{ route('admin.jemaat.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4">
            
            @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']) && isset($klasisFilterOptions))
            <div class="w-full md:w-64 flex-shrink-0">
                <select name="klasis_id" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" onchange="this.form.submit()">
                    <option value="">-- Wilayah Klasis (Semua) --</option>
                    @foreach($klasisFilterOptions as $id => $nama)
                        <option value="{{ $id }}" {{ request('klasis_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="pl-10 w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" 
                    placeholder="Pencarian Nama Jemaat atau Kode Registrasi...">
            </div>

            <div class="flex-shrink-0">
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white px-6 py-2.5 rounded text-[10px] font-bold uppercase transition">
                    Saring Data
                </button>
            </div>
        </form>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                        <th class="px-5 py-3 w-20 text-center">Ref ID</th>
                        <th class="px-5 py-3">Nama Jemaat & Kode</th>
                        <th class="px-5 py-3">Wilayah Klasis</th>
                        <th class="px-5 py-3 text-center">Klasifikasi Status</th>
                        <th class="px-5 py-3 text-center">Populasi Umat</th>
                        <th class="px-5 py-3 text-center w-24">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse ($jemaatData as $jemaat)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-5 py-4 text-center">
                                <span class="text-xs font-mono font-bold text-gray-400">#{{ $jemaat->id }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.jemaat.show', $jemaat->id) }}" class="font-bold text-gray-900 hover:text-blue-800 uppercase text-xs">
                                    {{ $jemaat->nama_jemaat }}
                                </a>
                                <div class="text-[9px] text-gray-500 font-mono font-bold mt-1 tracking-widest bg-gray-100 border border-gray-200 px-1.5 py-0.5 rounded inline-block">
                                    KODE: {{ $jemaat->kode_jemaat ?? 'KOSONG' }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-xs font-bold text-gray-700 uppercase"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> {{ $jemaat->klasis->nama_klasis ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @php
                                    $statusColor = match($jemaat->status_jemaat) {
                                        'Mandiri' => 'bg-green-100 text-green-800 border-green-300',
                                        'Bakal Jemaat' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                        default => 'bg-gray-100 text-gray-600 border-gray-300'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-widest rounded border {{ $statusColor }}">
                                    {{ $jemaat->status_jemaat }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-4 text-[10px] font-bold text-gray-600">
                                    <div class="text-center bg-gray-50 border border-gray-200 p-1.5 rounded w-14">
                                        <span class="block font-black text-gray-900 text-xs">{{ number_format($jemaat->real_kk > 0 ? $jemaat->real_kk : ($jemaat->jumlah_kk ?? 0)) }}</span>
                                        <span class="text-[8px] text-gray-400 uppercase tracking-widest mt-0.5 block">Keluarga</span>
                                    </div>
                                    <div class="text-center bg-blue-50 border border-blue-200 p-1.5 rounded w-14">
                                        <span class="block font-black text-blue-900 text-xs">{{ number_format($jemaat->real_jiwa > 0 ? $jemaat->real_jiwa : ($jemaat->jumlah_total_jiwa ?? 0)) }}</span>
                                        <span class="text-[8px] text-blue-400 uppercase tracking-widest mt-0.5 block">Jiwa</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.jemaat.show', $jemaat->id) }}" class="text-gray-400 hover:text-blue-800 transition" title="Buka Detail">
                                        <i class="fas fa-folder-open text-xs"></i>
                                    </a>
                                    @can('edit jemaat')
                                    <a href="{{ route('admin.jemaat.edit', $jemaat->id) }}" class="text-gray-400 hover:text-yellow-600 transition" title="Modifikasi Data">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    @endcan
                                    @can('delete jemaat')
                                    <form action="{{ route('admin.jemaat.destroy', $jemaat->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Memusnahkan Data Jemaat?\n\nSemua populasi anggota di dalam jemaat ini akan ikut terhapus. Lanjutkan?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-700 transition" title="Musnahkan Arsip">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500 text-xs italic">
                                <i class="fas fa-church text-3xl mb-3 block text-gray-300"></i>
                                Pangkalan Data Jemaat masih kosong.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($jemaatData->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">
                {{ $jemaatData->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection