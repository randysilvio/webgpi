@extends('admin.layout')

@section('title', 'Manajemen Klasis')
@section('header-title', 'Daftar Klasis GPI Papua')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl font-semibold text-gray-800">Data Klasis</h2>
        {{-- Tombol Aksi --}}
        <div class="flex flex-wrap gap-2">
             {{-- Tombol Import --}}
             @can('import klasis')
             <a href="{{ route('admin.klasis.import-form') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import
            </a>
             @endcan

             {{-- Tombol Export --}}
             @can('export klasis')
             <a href="{{ route('admin.klasis.export', request()->query()) }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                 <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                 Export
             </a>
            @endcan

            {{-- Tombol Tambah --}}
            @can('manage klasis')
            <a href="{{ route('admin.klasis.create') }}" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Klasis
            </a>
            @endcan
        </div>
    </div>

    {{-- Form Pencarian --}}
     <form method="GET" action="{{ route('admin.klasis.index') }}" class="mb-6">
        <div class="flex items-center max-w-lg">
            <input type="text" name="search" placeholder="Cari Nama/Kode/Pusat Klasis/Ketua..." value="{{ request('search') }}"
                   class="flex-grow px-4 py-2 border border-gray-300 rounded-l-md focus:ring-primary focus:border-primary text-sm shadow-sm">
            <button type="submit" class="bg-primary text-white px-3 py-2 rounded-r-md hover:bg-blue-700 transition duration-150 ease-in-out shadow-sm -ml-px">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
             @if(request('search'))
            <a href="{{ route('admin.klasis.index') }}" class="ml-3 text-sm text-gray-600 hover:text-primary underline">Reset</a>
            @endif
        </div>
    </form>

    <div class="overflow-x-auto relative shadow-md sm:rounded-lg border border-gray-200">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    {{-- Penambahan Kolom ID --}}
                    <th scope="col" class="px-6 py-3 w-16">ID</th>
                    <th scope="col" class="px-6 py-3">Nama Klasis</th>
                    <th scope="col" class="px-6 py-3">Kode</th>
                    <th scope="col" class="px-6 py-3">Pusat</th>
                    <th scope="col" class="px-6 py-3">Ketua MPK</th>
                    <th scope="col" class="px-6 py-3 text-center">Jml Jemaat</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($klasisData as $klasis)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        {{-- Tampilkan ID --}}
                        <td class="px-6 py-4 font-mono text-gray-500 font-bold">
                            #{{ $klasis->id }}
                        </td>

                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <a href="{{ route('admin.klasis.show', $klasis->id) }}" class="text-primary hover:underline" title="Lihat Detail">
                                {{ $klasis->nama_klasis }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $klasis->kode_klasis ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $klasis->pusat_klasis ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $klasis->ketuaMp->nama_lengkap ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">
                                {{ $klasis->jemaat_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center space-x-2">
                            {{-- Tombol Edit --}}
                            @can('manage klasis')
                            <a href="{{ route('admin.klasis.edit', $klasis->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium inline-block" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            @endcan

                            {{-- Tombol Hapus --}}
                             @can('manage klasis')
                            <form action="{{ route('admin.klasis.destroy', $klasis->id) }}" method="POST" class="inline-block" onsubmit="return confirm('PERHATIAN:\nMenghapus Klasis juga akan menghapus SEMUA Jemaat di dalamnya!\n\nApakah Anda yakin ingin menghapus Klasis {{ $klasis->nama_klasis }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium" title="Hapus">
                                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b">
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500 italic">
                            Tidak ada data klasis yang ditemukan
                            @if(request('search'))
                                untuk pencarian "{{ request('search') }}"
                            @endif
                            .
                            @can('manage klasis')
                            <a href="{{ route('admin.klasis.create') }}" class="text-primary hover:underline ml-2">Tambah Baru?</a>
                            @endcan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-6">
        {{ $klasisData->appends(request()->query())->links() }} 
    </div>
</div>
@endsection