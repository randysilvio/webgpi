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
                Status: <span class="font-medium">{{ $anggotaJemaat->status_keanggotaan ?? '-' }}</span>
            </p>
        </div>
        <div class="mt-3 sm:mt-0 flex space-x-2">
            {{-- Tombol Edit (Sesuaikan hak akses nanti) --}}
            {{-- @can('edit anggota jemaat', $anggotaJemaat) --}}
            <a href="{{ route('admin.anggota-jemaat.edit', $anggotaJemaat->id) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap">
                Edit Data
            </a>
            {{-- @endcan --}}
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
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Tanggal Lahir:</strong> {{ optional($anggotaJemaat->tanggal_lahir)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Jenis Kelamin:</strong> {{ $anggotaJemaat->jenis_kelamin ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Gol. Darah:</strong> {{ $anggotaJemaat->golongan_darah ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Status Nikah:</strong> {{ $anggotaJemaat->status_pernikahan ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Nama Ayah:</strong> {{ $anggotaJemaat->nama_ayah ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Nama Ibu:</strong> {{ $anggotaJemaat->nama_ibu ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Pendidikan:</strong> {{ $anggotaJemaat->pendidikan_terakhir ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Pekerjaan:</strong> {{ $anggotaJemaat->pekerjaan_utama ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Telepon:</strong> {{ $anggotaJemaat->telepon ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Email:</strong> {{ $anggotaJemaat->email ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Alamat:</strong></p>
            <p class="pl-4 whitespace-pre-line">{{ $anggotaJemaat->alamat_lengkap ?: '-' }}</p>
        </div>

        {{-- Kolom 2: Data Keanggotaan --}}
        <div class="space-y-3 md:border-l md:pl-6">
             <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Data Keanggotaan</h3>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">No. Buku Induk:</strong> {{ $anggotaJemaat->nomor_buku_induk ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Jemaat:</strong> {{ $anggotaJemaat->jemaat->nama_jemaat ?? '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Klasis:</strong> {{ $anggotaJemaat->jemaat->klasis->nama_klasis ?? '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Sektor:</strong> {{ $anggotaJemaat->sektor_pelayanan ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Unit:</strong> {{ $anggotaJemaat->unit_pelayanan ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Status Anggota:</strong> {{ $anggotaJemaat->status_keanggotaan }}</p>
             <hr class="my-2">
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tgl Baptis:</strong> {{ optional($anggotaJemaat->tanggal_baptis)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tempat Baptis:</strong> {{ $anggotaJemaat->tempat_baptis ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tgl Sidi:</strong> {{ optional($anggotaJemaat->tanggal_sidi)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tempat Sidi:</strong> {{ $anggotaJemaat->tempat_sidi ?: '-' }}</p>
              <hr class="my-2">
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tgl Masuk Jemaat:</strong> {{ optional($anggotaJemaat->tanggal_masuk_jemaat)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Asal Gereja:</strong> {{ $anggotaJemaat->asal_gereja_sebelumnya ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">No. Atestasi:</strong> {{ $anggotaJemaat->nomor_atestasi ?: '-' }}</p>
        </div>

        {{-- Kolom 3: Keterlibatan & Ekonomi --}}
        <div class="space-y-3 md:border-l md:pl-6">
            <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Keterlibatan & Ekonomi</h3>
            <p><strong class="font-medium text-gray-600 w-36 inline-block">Jabatan Pelayan Khusus:</strong> {{ $anggotaJemaat->jabatan_pelayan_khusus ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-36 inline-block">Wadah Kategorial:</strong> {{ $anggotaJemaat->wadah_kategorial ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-36 inline-block">Keterlibatan Lain:</strong></p>
            <p class="pl-4 whitespace-pre-line">{{ $anggotaJemaat->keterlibatan_lain ?: '-' }}</p>
            <hr class="my-2">
            <p><strong class="font-medium text-gray-600 w-36 inline-block">Status dlm Keluarga:</strong> {{ $anggotaJemaat->status_dalam_keluarga ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-36 inline-block">Kepala Keluarga:</strong> {{ $anggotaJemaat->nama_kepala_keluarga ?: '-' }}</p>
             <hr class="my-2">
             <p><strong class="font-medium text-gray-600 w-36 inline-block">Pekerjaan KK:</strong> {{ $anggotaJemaat->status_pekerjaan_kk ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-36 inline-block">Kepemilikan Rumah:</strong> {{ $anggotaJemaat->status_kepemilikan_rumah ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-36 inline-block">Pendapatan Keluarga:</strong> {{ $anggotaJemaat->perkiraan_pendapatan_keluarga ?: '-' }}</p>
             {{-- <p><strong class="font-medium text-gray-600 w-36 inline-block">Sumber Penerangan:</strong> {{ $anggotaJemaat->sumber_penerangan ?: '-' }}</p> --}}
             {{-- <p><strong class="font-medium text-gray-600 w-36 inline-block">Sumber Air Minum:</strong> {{ $anggotaJemaat->sumber_air_minum ?: '-' }}</p> --}}
        </div>

    </div>

    {{-- Catatan --}}
    @if($anggotaJemaat->catatan)
    <div class="mt-6 border-t pt-4">
        <h3 class="text-base font-semibold text-gray-700 mb-2">Catatan Tambahan</h3>
        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $anggotaJemaat->catatan }}</p>
    </div>
    @endif

</div>
@endsection