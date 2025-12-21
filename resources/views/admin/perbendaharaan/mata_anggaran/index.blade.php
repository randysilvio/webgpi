@extends('admin.layout')

@section('title', 'Mata Anggaran')
@section('header-title', 'Daftar Mata Anggaran (COA)')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    {{-- Header & Tombol Tambah --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Kategori Pendapatan & Belanja</h2>
            <p class="text-sm text-gray-500 mt-1">Standar kode akun untuk pelaporan keuangan seragam (PP No. 5).</p>
        </div>
        @hasanyrole('Super Admin|Admin Sinode')
        <a href="{{ route('admin.perbendaharaan.mata-anggaran.create') }}" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-150 inline-flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Akun
        </a>
        @endhasanyrole
    </div>

    {{-- Form Filter & Pencarian --}}
    <form method="GET" action="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cari Kode atau Nama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cth: 1.1 atau Persembahan..." 
                       class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jenis Akun</label>
                <select name="jenis" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                    <option value="">Semua Jenis</option>
                    <option value="Pendapatan" {{ request('jenis') == 'Pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                    <option value="Belanja" {{ request('jenis') == 'Belanja' ? 'selected' : '' }}>Belanja</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-900 text-sm font-bold transition">
                    <i class="fas fa-search mr-2"></i> Cari
                </button>
            </div>
        </div>
    </form>

    {{-- Tabel Data --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Akun</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mata Anggaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelompok</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($mataAnggarans as $ma)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-blue-700">
                        {{ $ma->kode }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $ma->nama_mata_anggaran }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $ma->jenis == 'Pendapatan' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $ma->jenis }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $ma->kelompok ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.perbendaharaan.mata-anggaran.edit', $ma->id) }}" class="text-indigo-600 hover:text-indigo-900 p-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            @hasanyrole('Super Admin')
                            <form action="{{ route('admin.perbendaharaan.mata-anggaran.destroy', $ma->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan kode akun ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 p-1">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endhasanyrole
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i>
                            <p>Data Mata Anggaran belum tersedia atau tidak ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $mataAnggarans->links() }}
    </div>
</div>

{{-- Tips Info --}}
<div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded shadow-sm">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-700">
                <strong>Tips:</strong> Mata Anggaran (COA) ini akan menjadi referensi utama saat menyusun <strong>Rencana APB</strong> dan pencatatan <strong>Buku Kas Umum</strong>. Pastikan kode akun sesuai dengan ketentuan Sinode.
            </p>
        </div>
    </div>
</div>
@endsection