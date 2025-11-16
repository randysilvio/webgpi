@extends('admin.layout')

@section('title', 'Manajemen Jemaat')
@section('header-title', 'Daftar Jemaat GPI Papua')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl font-semibold text-gray-800">Data Jemaat</h2>
        {{-- Tombol Aksi --}}
        <div class="flex flex-wrap gap-2">
             {{-- Tombol Import --}}
             @can('import jemaat')
             <a href="{{ route('admin.jemaat.import-form') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import
            </a>
            @endcan
             {{-- Tombol Export --}}
             @can('export jemaat')
             <a href="{{ route('admin.jemaat.export', request()->query()) }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                 <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                 Export
             </a>
             @endcan
            {{-- Tombol Tambah --}}
            @can('create jemaat')
            <a href="{{ route('admin.jemaat.create') }}" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Jemaat
            </a>
            @endcan
        </div>
    </div>

    {{-- Form Filter dan Search --}}
    <form method="GET" action="{{ route('admin.jemaat.index') }}" class="mb-6">
        <div class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-2 md:space-y-0">
            {{-- Filter Klasis --}}
            @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']) && isset($klasisFilterOptions) && $klasisFilterOptions->count() > 0)
                <div class="flex-grow">
                    <label for="klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Filter Klasis:</label>
                    <select name="klasis_id" id="klasis_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
                        <option value="">-- Semua Klasis --</option>
                        @foreach($klasisFilterOptions as $id => $nama)
                            <option value="{{ $id }}" {{ $request->input('klasis_id') == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Input Search --}}
            <div class="flex-grow">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Jemaat:</label>
                <input type="text" name="search" id="search" placeholder="Nama atau Kode Jemaat..." value="{{ $request->input('search') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
            </div>

            {{-- Tombol Filter & Reset --}}
            <div class="flex-shrink-0 flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 text-sm">
                    <svg class="w-4 h-4 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    Filter
                </button>
                 @if($request->filled('klasis_id') || $request->filled('search'))
                 <a href="{{ route('admin.jemaat.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 text-sm">
                     Reset
                 </a>
                 @endif
            </div>
        </div>
    </form>

    <div class="overflow-x-auto relative shadow-md sm:rounded-lg border border-gray-200">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    {{-- KOLOM ID DITAMBAHKAN --}}
                    <th scope="col" class="px-6 py-3 w-16 font-bold">ID</th>
                    
                    <th scope="col" class="px-6 py-3">Nama Jemaat</th>
                    <th scope="col" class="px-6 py-3">Klasis</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3 text-center">Jml KK</th>
                    <th scope="col" class="px-6 py-3 text-center">Jml Jiwa</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jemaatData as $jemaat)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        {{-- TAMPILKAN ID --}}
                        <td class="px-6 py-4 font-mono text-gray-500 font-bold">
                            #{{ $jemaat->id }}
                        </td>

                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <a href="{{ route('admin.jemaat.show', $jemaat->id) }}" class="text-primary hover:underline" title="Lihat Detail">{{ $jemaat->nama_jemaat }}</a>
                             <div class="text-xs text-gray-500">{{ $jemaat->kode_jemaat ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $jemaat->klasis->nama_klasis ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = match($jemaat->status_jemaat) {
                                    'Mandiri' => 'bg-green-100 text-green-800',
                                    'Bakal Jemaat' => 'bg-yellow-100 text-yellow-800',
                                    'Pos Pelayanan' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $jemaat->status_jemaat }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ $jemaat->jumlah_kk ?? 0 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ $jemaat->jumlah_total_jiwa ?? 0 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center space-x-2">
                            {{-- Tombol Edit --}}
                            @can('edit jemaat')
                            <a href="{{ route('admin.jemaat.edit', $jemaat->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium inline-block" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            @endcan

                            {{-- Tombol Hapus --}}
                            @can('delete jemaat')
                            <form action="{{ route('admin.jemaat.destroy', $jemaat->id) }}" method="POST" class="inline-block" onsubmit="return confirm('PERHATIAN:\nMenghapus Jemaat juga akan menghapus SEMUA data Anggota Jemaat di dalamnya!\n\nApakah Anda benar-benar yakin ingin menghapus Jemaat {{ $jemaat->nama_jemaat }}?');">
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
                            Tidak ada data jemaat yang ditemukan.
                            @can('create jemaat')
                            <a href="{{ route('admin.jemaat.create') }}" class="text-primary hover:underline ml-2">Tambah Baru?</a>
                            @endcan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-6">
        {{ $jemaatData->appends(request()->query())->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection