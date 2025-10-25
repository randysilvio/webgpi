@extends('admin.layout')

@section('title', 'Manajemen Pendeta')
@section('header-title', 'Daftar Pendeta GPI Papua')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl font-semibold text-gray-800">Data Pendeta</h2>
        
        {{-- Tombol Aksi (Import, Export, Tambah) --}}
        <div class="flex flex-wrap gap-2">
            {{-- Tombol Import (Nanti sesuaikan hak aksesnya) --}}
            {{-- @hasanyrole('Super Admin|Admin Bidang 3') --}}
            <a href="{{ route('admin.pendeta.import-form') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import
            </a>
            {{-- @endhasanyrole --}}
            
            {{-- Tombol Export (Nanti sesuaikan hak aksesnya) --}}
            {{-- @hasanyrole('Super Admin|Admin Bidang 3') --}}
            <a href="{{ route('admin.pendeta.export') }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export
            </a>
            {{-- @endhasanyrole --}}

            {{-- Tombol Tambah (Hanya Super Admin/Bidang 3) --}}
            {{-- @hasanyrole('Super Admin|Admin Bidang 3') --}}
            <a href="{{ route('admin.pendeta.create') }}" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Pendeta
            </a>
            {{-- @endhasanyrole --}}
        </div>
    </div>

    {{-- Form Pencarian --}}
    <form method="GET" action="{{ route('admin.pendeta.index') }}" class="mb-4">
       <div class="flex items-center">
           <input type="text" name="search" placeholder="Cari Nama/NIPG Pendeta..." value="{{ request('search') }}"
                  class="flex-grow px-4 py-2 border border-gray-300 rounded-l-md focus:ring-primary focus:border-primary text-sm shadow-sm">
           <button type="submit" class="bg-primary text-white px-4 py-2 rounded-r-md hover:bg-blue-700 transition duration-150 ease-in-out shadow-sm">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
           </button>
            @if(request('search'))
           <a href="{{ route('admin.pendeta.index') }}" class="ml-2 text-sm text-gray-600 hover:text-primary underline">Reset</a>
           @endif
       </div>
   </form>

    <div class="overflow-x-auto relative shadow-md sm:rounded-lg border border-gray-200">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Lengkap</th>
                    <th scope="col" class="px-6 py-3">NIPG</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Penempatan</th>
                    <th scope="col" class="px-6 py-3">Jabatan</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendetaData as $pendeta)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <a href="{{ route('admin.pendeta.show', $pendeta->id) }}" class="text-primary hover:underline" title="Lihat Detail">
                                {{ $pendeta->nama_lengkap }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $pendeta->nipg }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             @php
                                $statusClass = match($pendeta->status_kepegawaian) {
                                    'Aktif' => 'bg-green-100 text-green-800',
                                    'Vikaris' => 'bg-blue-100 text-blue-800',
                                    'Emeritus' => 'bg-gray-200 text-gray-700',
                                    'Tugas Belajar' => 'bg-yellow-100 text-yellow-800',
                                    'Izin Belajar' => 'bg-yellow-100 text-yellow-800',
                                    'Dikaryakan' => 'bg-red-100 text-red-800',
                                    'Non-Aktif' => 'bg-red-100 text-red-800',
                                    'Lainnya' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $pendeta->status_kepegawaian }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                            {{ $pendeta->jemaatPenempatan->nama_jemaat ?? ($pendeta->klasisPenempatan->nama_klasis ?? 'Sinode/Lainnya') }}
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $pendeta->jabatan_saat_ini ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            {{-- Tombol Edit (Hanya Super Admin/Bidang 3) --}}
                            {{-- @hasanyrole('Super Admin|Admin Bidang 3') --}}
                            <a href="{{ route('admin.pendeta.edit', $pendeta->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 font-medium" title="Edit">Edit</a>
                            {{-- @endhasanyrole --}}

                            {{-- Tombol Hapus (Hanya Super Admin/Bidang 3) --}}
                            {{-- @hasanyrole('Super Admin|Admin Bidang 3') --}}
                            <form action="{{ route('admin.pendeta.destroy', $pendeta->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Pendeta {{ $pendeta->nama_lengkap }}? Akun user terkait juga akan terdampak (tergantung konfigurasi relasi).');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium" title="Hapus">Hapus</button>
                            </form>
                            {{-- @endhasanyrole --}}
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b">
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                            Tidak ada data pendeta yang ditemukan
                            @if(request('search'))
                                untuk pencarian "{{ request('search') }}"
                            @endif
                            .
                            {{-- @hasanyrole('Super Admin|Admin Bidang 3') --}}
                            <a href="{{ route('admin.pendeta.create') }}" class="text-primary hover:underline ml-2">Tambah Baru?</a>
                            {{-- @endhasanyrole --}}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-6">
        {{ $pendetaData->appends(request()->query())->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection