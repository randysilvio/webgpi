@extends('admin.layout')

@section('title', 'Detail Pendeta: ' . $pendeta->nama_lengkap)
@section('header-title', 'Detail Data Pendeta')

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8">
    {{-- Header Detail --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4">
        <div class="flex items-center space-x-4">
             @if ($pendeta->foto_path && Storage::disk('public')->exists($pendeta->foto_path))
                <img src="{{ Storage::url($pendeta->foto_path) }}" alt="Foto {{ $pendeta->nama_lengkap }}" class="w-16 h-16 rounded-full object-cover border border-gray-200 shadow-sm">
            @else
                 <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center text-gray-600">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                </div>
            @endif
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">{{ $pendeta->nama_lengkap }}</h2>
                <p class="text-sm text-gray-500">
                    NIPG: {{ $pendeta->nipg }} |
                    Status: <span class="font-medium">{{ $pendeta->status_kepegawaian ?? '-' }}</span>
                </p>
                 <p class="text-sm text-gray-500">
                    Jabatan: {{ $pendeta->jabatan_saat_ini ?: '-' }}
                </p>
            </div>
        </div>
        <div class="mt-3 sm:mt-0 flex space-x-2">
            {{-- Tombol Edit (Sesuaikan hak akses nanti) --}}
            {{-- @hasanyrole('Super Admin|Admin Bidang 3') --}}
            <a href="{{ route('admin.pendeta.edit', $pendeta->id) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap">
                Edit Data
            </a>
            {{-- @endhasanyrole --}}
            <a href="{{ route('admin.pendeta.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap">
                &larr; Kembali
            </a>
        </div>
    </div>

    {{-- Grid Detail Data --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6 text-sm mt-6">

        {{-- Kolom 1: Data Pribadi --}}
        <div class="space-y-3 bg-white shadow rounded-lg p-6 border">
            <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Data Pribadi</h3>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">NIK:</strong> {{ $pendeta->nik ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Tempat Lahir:</strong> {{ $pendeta->tempat_lahir ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Tanggal Lahir:</strong> {{ optional($pendeta->tanggal_lahir)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Jenis Kelamin:</strong> {{ $pendeta->jenis_kelamin ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Status Nikah:</strong> {{ $pendeta->status_pernikahan ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-28 inline-block">Pasangan:</strong> {{ $pendeta->nama_pasangan ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Telepon:</strong> {{ $pendeta->telepon ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Email Akun:</strong> {{ $pendeta->email ?: ($pendeta->user->email ?? '-') }}</p>
            <p><strong class="font-medium text-gray-600 w-28 inline-block">Alamat:</strong></p>
            <p class="pl-4 whitespace-pre-line">{{ $pendeta->alamat_domisili ?: '-' }}</p>
        </div>

        {{-- Kolom 2: Kependetaan & Kepegawaian --}}
        <div class="space-y-3 bg-white shadow rounded-lg p-6 border">
             <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Kependetaan & Kepegawaian</h3>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">NIPG:</strong> {{ $pendeta->nipg }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tgl Tahbisan:</strong> {{ optional($pendeta->tanggal_tahbisan)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tempat Tahbisan:</strong> {{ $pendeta->tempat_tahbisan ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">No SK Pendeta:</strong> {{ $pendeta->nomor_sk_kependetaan ?: '-' }}</p>
             <hr class="my-2">
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Status Pegawai:</strong> {{ $pendeta->status_kepegawaian }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Tgl Masuk GPI:</strong> {{ optional($pendeta->tanggal_mulai_masuk_gpi)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Gol./Pangkat:</strong> {{ $pendeta->golongan_pangkat_terakhir ?: '-' }}</p>
             <hr class="my-2">
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Pend. Teologi:</strong> {{ $pendeta->pendidikan_teologi_terakhir ?: '-' }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Institusi:</strong> {{ $pendeta->institusi_pendidikan_teologi ?: '-' }}</p>
        </div>

        {{-- Kolom 3: Penempatan & Catatan --}}
        <div class="space-y-3 bg-white shadow rounded-lg p-6 border">
            <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Penempatan & Catatan</h3>
            <p><strong class="font-medium text-gray-600 w-36 inline-block">Klasis Penempatan:</strong> {{ $pendeta->klasisPenempatan->nama_klasis ?? '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-36 inline-block">Jemaat Penempatan:</strong> {{ $pendeta->jemaatPenempatan->nama_jemaat ?? '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-36 inline-block">Jabatan Saat Ini:</strong> {{ $pendeta->jabatan_saat_ini ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-36 inline-block">Tgl Mulai Jabatan:</strong> {{ optional($pendeta->tanggal_mulai_jabatan_saat_ini)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
             <hr class="my-2">
             <p><strong class="font-medium text-gray-600 w-36 inline-block">Catatan:</strong></p>
             <p class="whitespace-pre-line">{{ $pendeta->catatan ?: '-' }}</p>
             <hr class="my-2">
            {{-- TODO: Tampilkan Riwayat Mutasi di sini jika sudah ada --}}
            <p class="text-gray-400 italic">(Riwayat Mutasi akan ditampilkan di sini)</p>

        </div>
    </div>

</div>
@endsection