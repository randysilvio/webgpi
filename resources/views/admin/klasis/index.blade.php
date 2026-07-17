@extends('layouts.app')

@section('title', 'Direktori Klasis')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-gray-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Direktori Wilayah Klasis</h2>
            <p class="text-xs text-gray-600 mt-1">Daftar wilayah pelayanan tingkat Klasis se-Tanah Papua.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            @hasanyrole('Super Admin|Admin Bidang 3')
            <a href="{{ route('admin.klasis.import-form') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded text-[10px] font-bold uppercase hover:bg-gray-100 transition flex items-center">
                <i class="fas fa-file-excel mr-2 text-green-700"></i> Import
            </a>
            <a href="{{ route('admin.klasis.export') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded text-[10px] font-bold uppercase hover:bg-gray-100 transition flex items-center">
                <i class="fas fa-download mr-2 text-blue-800"></i> Export
            </a>
            <a href="{{ route('admin.klasis.create') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase shadow-sm transition flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Klasis
            </a>
            @endhasanyrole
        </div>
    </div>

    {{-- STATISTIK RINGKAS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Total Klasis</p>
            <p class="text-xl font-black text-gray-900 mt-1">{{ number_format($stats->total_klasis ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Total Jemaat</p>
            <p class="text-xl font-black text-gray-900 mt-1">{{ number_format($stats->total_jemaat ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-l-4 border-l-green-600">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Jemaat Mandiri</p>
            <p class="text-xl font-black text-green-700 mt-1">{{ number_format($stats->total_jemaat_mandiri ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-l-4 border-l-yellow-600">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Pos Pelayanan</p>
            <p class="text-xl font-black text-yellow-700 mt-1">{{ number_format($stats->total_jemaat_pos ?? 0) }}</p>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Nama Klasis & Ketua</th>
                        <th class="px-6 py-3">Pusat & Kontak</th>
                        <th class="px-6 py-3 text-center">Jemaat</th>
                        <th class="px-6 py-3 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($klasisData as $klasis)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-[10px] font-mono font-bold border border-gray-300">
                                    {{ $klasis->kode_klasis }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.klasis.show', $klasis->id) }}" class="font-bold text-gray-900 hover:text-blue-800 uppercase text-xs">{{ $klasis->nama_klasis }}</a>
                                <div class="text-[10px] text-gray-500 font-bold mt-1">
                                    <i class="fas fa-user-tie mr-1 text-gray-400"></i> {{ $klasis->ketuaMp->nama_lengkap ?? 'Ketua Belum Diatur' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                <div class="font-bold"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> {{ $klasis->pusat_klasis ?? '-' }}</div>
                                <div class="mt-1"><i class="fas fa-phone text-gray-400 mr-1"></i> {{ $klasis->telepon_kantor ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-blue-50 text-blue-800 border border-blue-200 px-3 py-1 rounded-full text-xs font-bold">{{ $klasis->jemaat_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.klasis.show', $klasis->id) }}" class="text-gray-400 hover:text-blue-800 transition"><i class="fas fa-folder-open text-xs"></i></a>
                                    @hasanyrole('Super Admin|Admin Bidang 3')
                                        <a href="{{ route('admin.klasis.edit', $klasis->id) }}" class="text-gray-400 hover:text-yellow-600 transition"><i class="fas fa-edit text-xs"></i></a>
                                        <form action="{{ route('admin.klasis.destroy', $klasis->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-700 transition"><i class="fas fa-trash-alt text-xs"></i></button>
                                        </form>
                                    @endhasanyrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 text-xs italic">Data Klasis tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection