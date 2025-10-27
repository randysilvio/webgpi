@extends('admin.layout')

@section('title', 'Manajemen Pendeta')
@section('header-title', 'Daftar Pendeta GPI Papua')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl font-semibold text-gray-800">Data Pendeta</h2>

        {{-- Tombol Aksi (Import, Export, Tambah) --}}
        <div class="flex flex-wrap gap-2">
            @can('import pendeta') {{-- <-- Sesuaikan permission --}}
            <a href="{{ route('admin.pendeta.import-form') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import
            </a>
            @endcan

            @can('export pendeta') {{-- <-- Sesuaikan permission --}}
            <a href="{{ route('admin.pendeta.export', request()->query()) }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center"> {{-- <-- Tambah request query --}}
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export
            </a>
            @endcan

            @can('manage pendeta') {{-- <-- Sesuaikan permission --}}
            <a href="{{ route('admin.pendeta.create') }}" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Pendeta
            </a>
            @endcan
        </div>
    </div>

    {{-- Form Filter dan Search --}}
    {{-- 👇👇👇 Form Filter ditambahkan/diperbarui 👇👇👇 --}}
    <form method="GET" action="{{ route('admin.pendeta.index') }}" class="mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            {{-- Filter Klasis --}}
            {{-- TODO: Tambahkan scoping filter klasis jika diperlukan (misal untuk Admin Wilayah) --}}
            <div>
                <label for="klasis_penempatan_id" class="block text-sm font-medium text-gray-700 mb-1">Filter Klasis:</label>
                <select name="klasis_penempatan_id" id="klasis_penempatan_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm" onchange="loadJemaatOptions()"> {{-- Panggil JS onchange --}}
                    <option value="">-- Semua Klasis --</option>
                    @foreach($klasisFilterOptions as $id => $nama)
                        <option value="{{ $id }}" {{ $request->input('klasis_penempatan_id') == $id ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Jemaat (Opsi dimuat dinamis atau dari controller) --}}
            <div>
                <label for="jemaat_penempatan_id" class="block text-sm font-medium text-gray-700 mb-1">Filter Jemaat:</label>
                <select name="jemaat_penempatan_id" id="jemaat_penempatan_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm" {{ $jemaatFilterOptions->isEmpty() && $request->filled('klasis_penempatan_id') ? '' : ($jemaatFilterOptions->isEmpty() ? 'disabled' : '') }}> {{-- Disable jika tidak ada opsi --}}
                    <option value="">-- Semua Jemaat {{ $request->filled('klasis_penempatan_id') ? 'di Klasis Dipilih' : '(Pilih Klasis Dahulu)' }} --</option>
                     @foreach($jemaatFilterOptions as $id => $nama)
                        <option value="{{ $id }}" {{ $request->input('jemaat_penempatan_id') == $id ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
                 {{-- Indikator loading (jika pakai AJAX nanti) --}}
                 {{-- <span id="jemaat-loading" class="text-xs text-gray-500 hidden">Memuat...</span> --}}
            </div>

            {{-- Filter Status Kepegawaian --}}
            <div>
                <label for="status_kepegawaian" class="block text-sm font-medium text-gray-700 mb-1">Filter Status:</label>
                <select name="status_kepegawaian" id="status_kepegawaian" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
                    <option value="">-- Semua Status --</option>
                     @foreach($statusOptions as $status)
                        <option value="{{ $status }}" {{ $request->input('status_kepegawaian') == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Input Search --}}
            <div class="lg:col-span-1"> {{-- Sesuaikan lebar --}}
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari:</label>
                <input type="text" name="search" id="search" placeholder="Nama / NIPG..." value="{{ $request->input('search') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
            </div>

            {{-- Tombol Filter & Reset --}}
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 text-sm h-full">
                    <svg class="w-4 h-4 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    Filter
                </button>
                 @if($request->hasAny(['klasis_penempatan_id', 'jemaat_penempatan_id', 'status_kepegawaian', 'search']))
                 <a href="{{ route('admin.pendeta.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 text-sm h-full">
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
                    <th scope="col" class="px-6 py-3">NIPG</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Penempatan</th> {{-- Kolom Jemaat/Klasis digabung --}}
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
                                    'Dikaryakan' => 'bg-orange-100 text-orange-800', // Warna beda
                                    'Non-Aktif' => 'bg-red-100 text-red-800',
                                    'Lainnya' => 'bg-purple-100 text-purple-800', // Warna beda
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $pendeta->status_kepegawaian }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                            {{-- Tampilkan Jemaat jika ada, jika tidak, tampilkan Klasis --}}
                            <span class="font-medium">{{ $pendeta->jemaatPenempatan->nama_jemaat ?? '-' }}</span><br>
                            <span class="text-gray-500">({{ $pendeta->klasisPenempatan->nama_klasis ?? 'Tidak Ada Klasis' }})</span>
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $pendeta->jabatan_saat_ini ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center space-x-2">
                             {{-- Tombol Edit --}}
                            @can('manage pendeta') {{-- <-- Sesuaikan permission --}}
                            <a href="{{ route('admin.pendeta.edit', $pendeta->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium inline-block" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            @endcan

                            {{-- Tombol Hapus --}}
                            @can('manage pendeta') {{-- <-- Sesuaikan permission --}}
                            <form action="{{ route('admin.pendeta.destroy', $pendeta->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Pendeta {{ $pendeta->nama_lengkap }}? Akun user terkait akan diupdate (pendeta_id menjadi null).');">
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
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                             Tidak ada data pendeta yang ditemukan
                             @if($request->hasAny(['klasis_penempatan_id', 'jemaat_penempatan_id', 'status_kepegawaian', 'search']))
                                 sesuai filter/pencarian.
                             @else
                                 .
                             @endif
                             @can('manage pendeta') {{-- <-- Sesuaikan permission --}}
                             <a href="{{ route('admin.pendeta.create') }}" class="text-primary hover:underline ml-2">Tambah Baru?</a>
                             @endcan
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

@push('scripts')
{{-- Script sederhana untuk load ulang halaman saat Klasis dipilih (opsi Jemaat akan terfilter di Controller) --}}
<script>
    function loadJemaatOptions() {
        // Submit form filter ketika klasis dipilih
        // Ini akan memicu controller untuk memfilter opsi jemaat
        document.querySelector('form[action="{{ route('admin.pendeta.index') }}"]').submit();
    }

    // Optional: Tambahkan sedikit delay atau indikator loading jika diperlukan
    // function loadJemaatOptions() {
    //     const form = document.querySelector('form[action="{{ route('admin.pendeta.index') }}"]');
    //     const jemaatSelect = document.getElementById('jemaat_penempatan_id');
    //     const loadingIndicator = document.getElementById('jemaat-loading');

    //     if (jemaatSelect) jemaatSelect.disabled = true; // Disable select jemaat
    //     if (loadingIndicator) loadingIndicator.style.display = 'inline'; // Tampilkan loading

    //     // Submit setelah delay singkat (opsional, agar user lihat loading)
    //     setTimeout(() => {
    //         form.submit();
    //     }, 100); // 100ms delay
    // }
</script>
@endpush