@extends('layouts.app')

@section('title', 'Buku Inventaris Aktiva Tetap')

@section('content')
<div class="space-y-6">

    {{-- HEADER & TOOLS --}}
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-blue-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Register Inventaris & Aset</h2>
            <p class="text-xs text-gray-600 mt-1">Pangkalan data pencatatan, pemeliharaan, dan valuasi kekayaan organisasi.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.perbendaharaan.aset.create') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm transition flex items-center justify-center">
                <i class="fas fa-plus-circle mr-2"></i> Catat Inventaris Baru
            </a>
        </div>
    </div>

    {{-- STATISTIK RINGKAS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gray-800 p-4 border border-gray-900 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Akumulasi Nilai Aset</p>
            <p class="text-xl font-mono font-black text-white mt-1">Rp {{ number_format($totalNilaiAset ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Total Barang/Aset</p>
            <p class="text-xl font-black text-gray-900 mt-1">{{ number_format($totalAset ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-b-4 border-b-green-700">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Kondisi Baik</p>
            <p class="text-xl font-black text-green-800 mt-1">{{ number_format($asetBaik ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-b-4 border-b-red-700">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Kondisi Rusak</p>
            <p class="text-xl font-black text-red-800 mt-1">{{ number_format($asetRusak ?? 0) }}</p>
        </div>
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-gray-50 border border-gray-200 rounded p-4 shadow-sm flex flex-col md:flex-row gap-4">
        <form action="{{ route('admin.perbendaharaan.aset.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4">
            
            <div class="w-full md:w-48 flex-shrink-0">
                <select name="kategori" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                    <option value="">- Semua Kategori -</option>
                    @foreach(['Tanah', 'Gedung', 'Kendaraan', 'Peralatan Elektronik', 'Meubelair', 'Alat Musik', 'Lainnya'] as $cat)
                        <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ strtoupper($cat) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-48 flex-shrink-0">
                <select name="kondisi" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                    <option value="">- Semua Kondisi -</option>
                    <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Kondisi Baik</option>
                    <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>

            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="pl-10 w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" 
                    placeholder="Pencarian Nama Barang atau Kode Identitas...">
            </div>

            <div class="flex-shrink-0">
                <button type="submit" class="w-full bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-6 py-2.5 rounded text-[10px] font-bold uppercase transition">
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
                        <th class="px-6 py-3 w-48">Kode & Identitas Barang</th>
                        <th class="px-6 py-3">Otoritas Kepemilikan & Kategori</th>
                        <th class="px-6 py-3 text-center w-32">Kondisi Fisik</th>
                        <th class="px-6 py-3 text-right w-40">Nilai Historis (Rp)</th>
                        <th class="px-6 py-3 text-center w-28">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($asets as $aset)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 uppercase text-xs">{{ $aset->nama_aset }}</div>
                                <div class="text-[9px] text-gray-500 font-mono font-bold bg-gray-100 border border-gray-200 px-2 py-0.5 rounded inline-block mt-1 tracking-widest">
                                    {{ $aset->kode_aset }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="block text-xs font-bold text-gray-700 uppercase">
                                    <i class="fas fa-sitemap text-gray-400 mr-1 w-3 text-center"></i> {{ $aset->jemaat->nama_jemaat ?? ($aset->klasis->nama_klasis ?? 'Pusat Sinode') }}
                                </span>
                                <span class="inline-block mt-1 px-2 py-0.5 text-[9px] rounded bg-blue-50 text-blue-800 border border-blue-200 uppercase font-bold tracking-widest">
                                    {{ $aset->kategori }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $kondisiColor = match($aset->kondisi) {
                                        'Baik' => 'bg-green-100 text-green-800 border-green-300',
                                        'Rusak Ringan' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                        default => 'bg-red-100 text-red-800 border-red-300',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded text-[9px] font-bold uppercase border {{ $kondisiColor }}">
                                    {{ $aset->kondisi }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-black text-gray-800 text-sm">
                                {{ number_format($aset->nilai_perolehan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.perbendaharaan.aset.show', $aset->id) }}" class="text-gray-400 hover:text-blue-800 transition" title="Tinjau Detail Buku Aset">
                                        <i class="fas fa-folder-open"></i>
                                    </a>
                                    <a href="{{ route('admin.perbendaharaan.aset.edit', $aset->id) }}" class="text-gray-400 hover:text-yellow-600 transition" title="Modifikasi Arsip">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.perbendaharaan.aset.destroy', $aset->id) }}" method="POST" onsubmit="return confirm('Peringatan: Pemusnahan data aset ini bersifat permanen dari buku besar. Lanjutkan?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-700 transition" title="Hapus Aset">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm">
                                <i class="fas fa-boxes text-3xl mb-3 block text-gray-300"></i>
                                Buku Inventaris Aktiva Tetap masih kosong.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($asets->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $asets->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection