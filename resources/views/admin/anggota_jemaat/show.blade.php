@extends('admin.layout')

@section('title', 'Detail Anggota: ' . $anggotaJemaat->nama_lengkap)
@section('header-title', 'Detail Anggota Jemaat')

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8">
    {{-- Header Detail --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">{{ $anggotaJemaat->nama_lengkap }}</h2>
            <p class="text-sm text-gray-500">
                Jemaat: {{ $anggotaJemaat->jemaat->nama_jemaat ?? '-' }} |
                Status: <span class="font-medium px-2 py-0.5 rounded {{ $anggotaJemaat->status_keanggotaan == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $anggotaJemaat->status_keanggotaan ?? '-' }}</span>
            </p>
        </div>
        <div class="mt-3 sm:mt-0 flex space-x-2">
            {{-- Tombol Edit --}}
            @can('edit anggota jemaat')
            <a href="{{ route('admin.anggota-jemaat.edit', $anggotaJemaat->id) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap">
                Edit Data
            </a>
            @endcan
            <a href="{{ route('admin.anggota-jemaat.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap">
                &larr; Kembali
            </a>
        </div>
    </div>

    {{-- Grid Detail Data --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6 text-sm">

        {{-- Kolom 1: Data Pribadi --}}
        <div class="space-y-3">
            <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Data Pribadi</h3>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">NIK:</strong> {{ $anggotaJemaat->nik ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Tempat Lahir:</strong> {{ $anggotaJemaat->tempat_lahir ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Tanggal Lahir:</strong> {{ optional($anggotaJemaat->tanggal_lahir)->isoFormat('D MMMM Y') ?: '-' }} <span class="text-gray-400 text-xs">({{ $anggotaJemaat->tanggal_lahir ? \Carbon\Carbon::parse($anggotaJemaat->tanggal_lahir)->age : '-' }} Thn)</span></p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Jenis Kelamin:</strong> {{ $anggotaJemaat->jenis_kelamin ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Gol. Darah:</strong> {{ $anggotaJemaat->golongan_darah ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Status Nikah:</strong> {{ $anggotaJemaat->status_pernikahan ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Nama Ayah:</strong> {{ $anggotaJemaat->nama_ayah ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Nama Ibu:</strong> {{ $anggotaJemaat->nama_ibu ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Pendidikan:</strong> {{ $anggotaJemaat->pendidikan_terakhir ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Pekerjaan:</strong> {{ $anggotaJemaat->pekerjaan_utama ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Telepon:</strong> {{ $anggotaJemaat->telepon ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Email:</strong> {{ $anggotaJemaat->email ?: '-' }}</p>
            <div>
                <strong class="font-medium text-gray-600 block mb-1">Alamat:</strong>
                <p class="pl-0 text-gray-800">{{ $anggotaJemaat->alamat_lengkap ?: '-' }}</p>
            </div>
        </div>

        {{-- Kolom 2: Data Keanggotaan & Keluarga --}}
        <div class="space-y-3 md:border-l md:pl-6">
             <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Data Keanggotaan</h3>
             
             {{-- TAMPILKAN NOMOR KK DISINI --}}
             <div class="bg-blue-50 p-3 rounded border border-blue-100 mb-3">
                 <p class="mb-1"><strong class="font-medium text-gray-600 w-32 inline-block">Nomor KK:</strong> <span class="font-bold text-gray-900 text-lg">{{ $anggotaJemaat->nomor_kk ?: '-' }}</span></p>
                 <p><strong class="font-medium text-gray-600 w-32 inline-block">Status Keluarga:</strong> {{ $anggotaJemaat->status_dalam_keluarga ?: '-' }}</p>
             </div>
             
             <p><strong class="font-medium text-gray-600 w-32 inline-block">No. Buku Induk:</strong> {{ $anggotaJemaat->nomor_buku_induk ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Jemaat:</strong> {{ $anggotaJemaat->jemaat->nama_jemaat ?? '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Klasis:</strong> {{ $anggotaJemaat->jemaat->klasis->nama_klasis ?? '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Sektor:</strong> {{ $anggotaJemaat->sektor_pelayanan ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Unit:</strong> {{ $anggotaJemaat->unit_pelayanan ?: '-' }}</p>
             
             <hr class="my-2">
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tgl Baptis:</strong> {{ optional($anggotaJemaat->tanggal_baptis)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tempat Baptis:</strong> {{ $anggotaJemaat->tempat_baptis ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tgl Sidi:</strong> {{ optional($anggotaJemaat->tanggal_sidi)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tempat Sidi:</strong> {{ $anggotaJemaat->tempat_sidi ?: '-' }}</p>
              <hr class="my-2">
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tgl Masuk:</strong> {{ optional($anggotaJemaat->tanggal_masuk_jemaat)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Asal Gereja:</strong> {{ $anggotaJemaat->asal_gereja_sebelumnya ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">No. Atestasi:</strong> {{ $anggotaJemaat->nomor_atestasi ?: '-' }}</p>
        </div>

        {{-- Kolom 3: Keterlibatan & Ekonomi --}}
        <div class="space-y-3 md:border-l md:pl-6">
            <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Keterlibatan & Ekonomi</h3>
            <p><strong class="font-medium text-gray-600 w-36 inline-block">Pelayan Khusus:</strong> {{ $anggotaJemaat->jabatan_pelayan_khusus ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-36 inline-block">Wadah Kategorial:</strong> {{ $anggotaJemaat->wadah_kategorial ?: '-' }}</p>
            
            @if($anggotaJemaat->keterlibatan_lain)
            <div class="mt-1">
                <strong class="font-medium text-gray-600 block">Keterlibatan Lain:</strong>
                <p class="whitespace-pre-line text-gray-800 bg-gray-50 p-2 rounded text-xs">{{ $anggotaJemaat->keterlibatan_lain }}</p>
            </div>
            @endif

            <hr class="my-2">
            <p><strong class="font-medium text-gray-600 w-36 inline-block">Kepala Keluarga:</strong> {{ $anggotaJemaat->nama_kepala_keluarga ?: '-' }}</p>
             <hr class="my-2">
             <p><strong class="font-medium text-gray-600 w-36 inline-block">Pekerjaan KK:</strong> {{ $anggotaJemaat->status_pekerjaan_kk ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-36 inline-block">Kepemilikan Rumah:</strong> {{ $anggotaJemaat->status_kepemilikan_rumah ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-36 inline-block">Pendapatan:</strong> {{ $anggotaJemaat->perkiraan_pendapatan_keluarga ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-36 inline-block">Penerangan:</strong> {{ $anggotaJemaat->sumber_penerangan ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-36 inline-block">Air Minum:</strong> {{ $anggotaJemaat->sumber_air_minum ?: '-' }}</p>
        </div>

    </div>

    {{-- Catatan --}}
    @if($anggotaJemaat->catatan)
    <div class="mt-6 border-t pt-4">
        <h3 class="text-base font-semibold text-gray-700 mb-2">Catatan Tambahan</h3>
        <p class="text-sm text-gray-600 whitespace-pre-line bg-yellow-50 p-3 rounded border border-yellow-100">{{ $anggotaJemaat->catatan }}</p>
    </div>
    @endif

    {{-- DAFTAR ANGGOTA KELUARGA (BARU) --}}
    @if(isset($anggotaKeluargaLain) && $anggotaJemaat->nomor_kk)
    <div class="mt-8">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Anggota Keluarga (Satu KK)
            </h3>
            {{-- Tombol Tambah Anggota Keluarga Baru ke KK ini --}}
            <a href="{{ route('admin.anggota-jemaat.create', ['nomor_kk' => $anggotaJemaat->nomor_kk, 'jemaat_id' => $anggotaJemaat->jemaat_id, 'alamat' => $anggotaJemaat->alamat_lengkap, 'sektor' => $anggotaJemaat->sektor_pelayanan, 'unit' => $anggotaJemaat->unit_pelayanan]) }}" 
               class="text-sm bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded font-medium transition border border-green-200">
               + Tambah Anggota Keluarga
            </a>
        </div>
        
        <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Keluarga</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">L/P</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usia</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Tampilkan Diri Sendiri (Sedang Dilihat) --}}
                    <tr class="bg-blue-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            {{ $anggotaJemaat->nama_lengkap }} (Ini)
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $anggotaJemaat->status_dalam_keluarga }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $anggotaJemaat->jenis_kelamin }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $anggotaJemaat->tanggal_lahir ? \Carbon\Carbon::parse($anggotaJemaat->tanggal_lahir)->age : '-' }} Thn
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <span class="text-gray-400 cursor-default text-xs">Sedang Dilihat</span>
                        </td>
                    </tr>

                    {{-- Tampilkan Anggota Keluarga Lain --}}
                    @forelse($anggotaKeluargaLain as $keluarga)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <a href="{{ route('admin.anggota-jemaat.show', $keluarga->id) }}" class="font-medium text-blue-600 hover:underline">
                                {{ $keluarga->nama_lengkap }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $keluarga->status_dalam_keluarga }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $keluarga->jenis_kelamin }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $keluarga->tanggal_lahir ? \Carbon\Carbon::parse($keluarga->tanggal_lahir)->age : '-' }} Thn
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.anggota-jemaat.show', $keluarga->id) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                        </td>
                    </tr>
                    @empty
                        {{-- Kosong (hanya diri sendiri) --}}
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection