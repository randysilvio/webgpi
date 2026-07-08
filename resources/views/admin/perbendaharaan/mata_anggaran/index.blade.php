@extends('layouts.app')

@section('title', 'Daftar Mata Anggaran (COA)')

@section('content')
<div class="space-y-6">

    {{-- HEADER & TOOLS --}}
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-gray-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Mata Anggaran (COA)</h2>
            <p class="text-xs text-gray-600 mt-1">Daftar Kode Akun Standar untuk klasifikasi Pendapatan dan Belanja.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.perbendaharaan.mata-anggaran.create') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wide shadow-sm transition flex items-center justify-center">
                <i class="fas fa-plus-circle mr-2"></i> Registrasi Akun Baru
            </a>
        </div>
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-gray-50 border border-gray-200 rounded p-4 shadow-sm flex flex-col md:flex-row gap-4">
        <form action="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4">
            
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="pl-10 w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" 
                    placeholder="Pencarian Nomor Kode atau Uraian Mata Anggaran...">
            </div>

            <div class="w-full md:w-64 flex-shrink-0">
                <select name="jenis" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                    <option value="">-- Semua Klasifikasi --</option>
                    <option value="Pendapatan" {{ request('jenis') == 'Pendapatan' ? 'selected' : '' }}>Hanya Pendapatan</option>
                    <option value="Belanja" {{ request('jenis') == 'Belanja' ? 'selected' : '' }}>Hanya Belanja</option>
                </select>
            </div>

            <div class="flex-shrink-0">
                <button type="submit" class="w-full bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-6 py-2.5 rounded text-[10px] font-bold uppercase transition">
                    Cari Akun
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
                        <th class="px-6 py-3 w-32">Kode Registrasi</th>
                        <th class="px-6 py-3">Uraian Mata Anggaran</th>
                        <th class="px-6 py-3 text-center w-32">Klasifikasi</th>
                        <th class="px-6 py-3 w-40">Kategori Kelompok</th>
                        <th class="px-6 py-3 text-center w-24">Status</th>
                        <th class="px-6 py-3 text-center w-24">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($mataAnggarans as $ma)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-blue-800 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded text-[11px]">
                                    {{ $ma->kode }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 uppercase text-xs">{{ $ma->nama_mata_anggaran }}</div>
                                @if($ma->deskripsi)
                                    <div class="text-[9px] text-gray-500 font-medium mt-1 truncate max-w-sm uppercase tracking-wider">{{ $ma->deskripsi }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded text-[9px] font-bold uppercase {{ $ma->jenis == 'Pendapatan' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800' }}">
                                    {{ $ma->jenis }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] text-gray-600 font-bold uppercase tracking-widest">{{ $ma->kelompok ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($ma->is_active)
                                    <span class="text-[10px] font-bold text-green-700 bg-green-100 border border-green-300 px-2 py-0.5 rounded uppercase">Aktif</span>
                                @else
                                    <span class="text-[10px] font-bold text-gray-500 bg-gray-100 border border-gray-300 px-2 py-0.5 rounded uppercase">Pasif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.perbendaharaan.mata-anggaran.edit', $ma->id) }}" class="text-gray-400 hover:text-yellow-600 transition" title="Modifikasi Akun">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @hasanyrole('Super Admin')
                                    <form action="{{ route('admin.perbendaharaan.mata-anggaran.destroy', $ma->id) }}" method="POST" class="inline" onsubmit="return confirm('Perhatian: Aksi ini akan menonaktifkan akun. Data historis pada Buku Kas tidak akan terhapus, namun akun tidak dapat digunakan untuk jurnal baru. Lanjutkan?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-700 transition" title="Non-aktifkan Akun">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endhasanyrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">
                                <i class="fas fa-book text-3xl mb-3 block text-gray-300"></i>
                                Pangkalan data Mata Anggaran masih kosong.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($mataAnggarans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $mataAnggarans->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- INFO PANDUAN --}}
    <div class="mt-4 bg-gray-50 border border-gray-300 rounded p-4 flex items-start gap-3 shadow-sm border-l-4 border-l-blue-800">
        <i class="fas fa-info-circle text-blue-800 mt-0.5"></i>
        <div class="text-[10px] text-gray-600 leading-relaxed font-bold uppercase tracking-widest">
            <strong>Pedoman Penggunaan:</strong> Direktori Mata Anggaran (Chart of Accounts) merupakan fondasi utama bagi pencatatan <strong>Buku Kas Umum</strong> maupun penyusunan <strong>Rencana Anggaran Pendapatan dan Belanja (RAPB)</strong>. Pastikan kodifikasi akun telah divalidasi dan memenuhi standar akuntansi Sinode sebelum digunakan dalam transaksi.
        </div>
    </div>
</div>
@endsection