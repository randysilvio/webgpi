{{-- resources/views/admin/anggota_jemaat/index.blade.php --}}
@extends('admin.layout')

@section('title', 'Manajemen Anggota Jemaat')
@section('header-title', 'Daftar Anggota Jemaat')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl font-semibold text-gray-800">Data Anggota Jemaat</h2>
        {{-- Tombol Aksi --}}
        <div class="flex flex-wrap gap-2">
            @can('import anggota jemaat')
            <a href="{{ route('admin.anggota-jemaat.import-form') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Data
            </a>
            @endcan
            @can('export anggota jemaat')
            <a href="{{ route('admin.anggota-jemaat.export', request()->query()) }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center"> {{-- <-- Tambah request query --}}
                 <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                 Export Data (.xlsx)
             </a>
            @endcan
            @can('create anggota jemaat')
            <a href="{{ route('admin.anggota-jemaat.create') }}" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Anggota
            </a>
            @endcan
        </div>
    </div>

    {{-- Form Filter dan Search --}}
    {{-- 👇👇👇 Form Filter ditambahkan/diperbarui 👇👇👇 --}}
    <form method="GET" action="{{ route('admin.anggota-jemaat.index') }}" class="mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            {{-- Filter Klasis (Hanya untuk Super Admin/Bidang 3) --}}
            @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']) && isset($klasisFilterOptions) && $klasisFilterOptions->count() > 0)
                <div>
                    <label for="klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Filter Klasis:</label>
                    <select name="klasis_id" id="klasis_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm" onchange="this.form.submit()"> {{-- Submit on change --}}
                        <option value="">-- Semua Klasis --</option>
                        @foreach($klasisFilterOptions as $id => $nama)
                            <option value="{{ $id }}" {{ $request->input('klasis_id') == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

             {{-- Filter Jemaat (Untuk Super Admin/Bidang 3/Klasis) --}}
             @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3', 'Admin Klasis']) && isset($jemaatFilterOptions) && $jemaatFilterOptions->count() > 0)
                <div>
                    <label for="jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Filter Jemaat:</label>
                    <select name="jemaat_id" id="jemaat_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
                        <option value="">-- Semua Jemaat {{ Auth::user()->hasRole('Admin Klasis') ? 'di Klasis Ini' : '' }} --</option>
                        @foreach($jemaatFilterOptions as $id => $nama)
                            <option value="{{ $id }}" {{ $request->input('jemaat_id') == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
             @endif

            {{-- Input Search --}}
            <div class="lg:col-span-{{ Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3', 'Admin Klasis']) ? '1' : '3' }}"> {{-- Lebarkan search jika filter tidak ada --}}
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Anggota:</label>
                <input type="text" name="search" id="search" placeholder="Nama, NIK, No. Induk..." value="{{ $request->input('search') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
            </div>

            {{-- Tombol Filter & Reset --}}
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 text-sm h-full"> {{-- Tambah h-full --}}
                    <svg class="w-4 h-4 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    Filter
                </button>
                 @if($request->filled('klasis_id') || $request->filled('jemaat_id') || $request->filled('search'))
                 <a href="{{ route('admin.anggota-jemaat.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 text-sm h-full"> {{-- Tambah h-full --}}
                     Reset
                 </a>
                 @endif
            </div>
        </div>
    </form>
    {{-- --- Akhir Form Filter --- --}}

    <div class="overflow-x-auto relative shadow-md sm:rounded-lg border border-gray-200">
        <table class="w-full text-sm text-left text-gray-500">
             <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Lengkap</th>
                    <th scope="col" class="px-6 py-3">NIK / No. Induk</th>
                    <th scope="col" class="px-6 py-3">Jemaat</th>
                     {{-- Tampilkan kolom Klasis jika user bisa lihat > 1 Klasis --}}
                     @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']))
                     <th scope="col" class="px-6 py-3">Klasis</th>
                     @endif
                    <th scope="col" class="px-6 py-3">Tgl Lahir</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($anggotaJemaatData as $anggota)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <a href="{{ route('admin.anggota-jemaat.show', $anggota->id) }}" class="text-primary hover:underline" title="Lihat Detail">
                                {{ $anggota->nama_lengkap }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $anggota->nik ?? $anggota->nomor_buku_induk ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $anggota->jemaat->nama_jemaat ?? '-' }}</td>
                         {{-- Tampilkan kolom Klasis jika user bisa lihat > 1 Klasis --}}
                         @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']))
                         <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">{{ $anggota->jemaat->klasis->nama_klasis ?? '-' }}</td>
                         @endif
                        <td class="px-6 py-4 whitespace-nowrap">{{ optional($anggota->tanggal_lahir)->isoFormat('DD MMM YYYY') ?? '-'}}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             @php /* Logika status class sama */ @endphp
                              @php
                                $statusClass = match($anggota->status_keanggotaan) {
                                    'Aktif' => 'bg-green-100 text-green-800',
                                    'Pindah', 'Tidak Aktif' => 'bg-yellow-100 text-yellow-800',
                                    'Meninggal' => 'bg-gray-200 text-gray-600',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                              @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $anggota->status_keanggotaan }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center space-x-2">
                             @can('edit anggota jemaat')
                            <a href="{{ route('admin.anggota-jemaat.edit', $anggota->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium inline-block" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            @endcan
                            @can('delete anggota jemaat')
                            <form action="{{ route('admin.anggota-jemaat.destroy', $anggota->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data anggota {{ $anggota->nama_lengkap }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b">
                        {{-- Sesuaikan colspan berdasarkan kolom klasis --}}
                        <td colspan="{{ Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']) ? '7' : '6' }}" class="px-6 py-10 text-center text-gray-500 italic">
                            Tidak ada data anggota jemaat yang ditemukan
                            @if(request('search')) untuk pencarian "{{ request('search') }}" @endif
                            @if(request('jemaat_id')) @php $namaJemaatFilter = $jemaatFilterOptions[request('jemaat_id')] ?? ''; @endphp @if($namaJemaatFilter) di Jemaat "{{ $namaJemaatFilter }}" @endif @endif
                            @if(request('klasis_id')) @php $namaKlasisFilter = $klasisFilterOptions[request('klasis_id')] ?? ''; @endphp @if($namaKlasisFilter) di Klasis "{{ $namaKlasisFilter }}" @endif @endif.
                            @can('create anggota jemaat')
                            <a href="{{ route('admin.anggota-jemaat.create') }}" class="text-primary hover:underline ml-2">Tambah Baru?</a>
                            @endcan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-6">
        {{ $anggotaJemaatData->appends(request()->query())->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection

{{-- Script untuk filter Jemaat dinamis berdasarkan Klasis --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const klasisSelect = document.getElementById('klasis_id');
        const jemaatSelect = document.getElementById('jemaat_id');
        const searchInput = document.getElementById('search'); // Ambil input search

        // Jika filter klasis berubah, submit form untuk memuat ulang jemaat & data
        if (klasisSelect) {
            klasisSelect.addEventListener('change', function() {
                // Kosongkan search saat ganti klasis agar tidak membingungkan
                if (searchInput) {
                    searchInput.value = '';
                }
                this.form.submit();
            });
        }
    });
</script>
@endpush