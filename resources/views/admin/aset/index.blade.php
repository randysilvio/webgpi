@extends('admin.layout')

@section('title', 'Inventaris Aset')
@section('header-title', 'Inventaris Harta Milik Gereja')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Inventaris</h2>
        <a href="{{ route('admin.perbendaharaan.aset.create') }}" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out inline-flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Aset
        </a>
    </div>

    {{-- Filter & Pencarian --}}
    <form method="GET" action="{{ route('admin.perbendaharaan.aset.index') }}" class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Cari Nama / Kode</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cth: Gedung..." class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                <select name="kategori" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                    <option value="">Semua Kategori</option>
                    @foreach(['Tanah', 'Gedung', 'Kendaraan', 'Peralatan Elektronik', 'Meubelair', 'Lainnya'] as $cat)
                        <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Kondisi</label>
                <select name="kondisi" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-900 text-sm font-medium transition">Filter</button>
                <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 text-sm font-medium transition">Reset</a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aset / Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Perolehan</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($asets as $aset)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">{{ $aset->nama_aset }}</div>
                            <div class="text-xs text-gray-500">{{ $aset->kode_aset }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $aset->kategori }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $color = $aset->kondisi == 'Baik' ? 'green' : ($aset->kondisi == 'Rusak Ringan' ? 'yellow' : 'red');
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">{{ $aset->kondisi }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $aset->format_nilai }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.perbendaharaan.aset.show', $aset->id) }}" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-eye"></i></a>
                            <form action="{{ route('admin.perbendaharaan.aset.destroy', $aset->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus aset ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data aset yang dicatat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $asets->links() }}</div>
</div>
@endsection