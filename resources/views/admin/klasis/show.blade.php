@extends('admin.layout')

@section('title', 'Detail Klasis: ' . $klasis->nama_klasis)
@section('header-title', 'Detail Data Klasis')

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8">
    {{-- Header Detail --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4">
        <div class="flex items-center space-x-4">
             @if ($klasis->foto_kantor_path && Storage::disk('public')->exists($klasis->foto_kantor_path))
                <img src="{{ Storage::url($klasis->foto_kantor_path) }}" alt="Foto {{ $klasis->nama_klasis }}" class="w-20 h-20 rounded-lg object-cover border border-gray-200 shadow-sm">
            @else
                 <div class="w-20 h-20 rounded-lg bg-gray-200 flex items-center justify-center text-gray-400 border">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6M9 11.25h6m-6 4.5h6M6.75 21v-2.25a2.25 2.25 0 0 1 2.25-2.25h6a2.25 2.25 0 0 1 2.25 2.25V21"></path></svg>
                </div>
            @endif
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">{{ $klasis->nama_klasis }}</h2>
                <p class="text-sm text-gray-500">
                    Pusat: {{ $klasis->pusat_klasis ?? '-' }} |
                    Kode: <span class="font-medium">{{ $klasis->kode_klasis ?? '-' }}</span>
                </p>
                 <p class="text-sm text-gray-500">
                    Ketua MPK: {{ $klasis->ketuaMp->nama_lengkap ?? '-' }}
                </p>
            </div>
        </div>
        <div class="mt-3 sm:mt-0 flex space-x-2">
            {{-- Tombol Edit (Sesuaikan hak akses nanti) --}}
            {{-- @hasanyrole('Super Admin|Admin Bidang 3') --}}
            <a href="{{ route('admin.klasis.edit', $klasis->id) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap">
                Edit Data
            </a>
            {{-- @endhasanyrole --}}
            <a href="{{ route('admin.klasis.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap">
                &larr; Kembali
            </a>
        </div>
    </div>

    {{-- Grid Detail Data --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6 text-sm mt-6">

        {{-- Kolom 1: Info Dasar & Kontak --}}
        <div class="space-y-3 bg-white shadow rounded-lg p-6 border">
            <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Info Dasar & Kontak</h3>
            <p><strong class="font-medium text-gray-600 w-32 inline-block">Tanggal Bentuk:</strong> {{ optional($klasis->tanggal_pembentukan)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-32 inline-block">Nomor SK:</strong> {{ $klasis->nomor_sk_pembentukan ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-32 inline-block">Klasis Induk:</strong> {{ $klasis->klasis_induk ?: '-' }}</p>
            <hr class="my-2">
            <p><strong class="font-medium text-gray-600 w-32 inline-block">Telepon:</strong> {{ $klasis->telepon_kantor ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-32 inline-block">Email:</strong> {{ $klasis->email_klasis ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-32 inline-block">Website:</strong> {{ $klasis->website_klasis ?: '-' }}</p>
            <p><strong class="font-medium text-gray-600 w-32 inline-block">Alamat Kantor:</strong></p>
            <p class="pl-4 whitespace-pre-line">{{ $klasis->alamat_kantor ?: '-' }}</p>
        </div>

        {{-- Kolom 2: Deskripsi & Sejarah --}}
        <div class="space-y-3 bg-white shadow rounded-lg p-6 border">
             <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Deskripsi & Sejarah</h3>
             <p><strong class="font-medium text-gray-600 block mb-1">Wilayah Pelayanan:</strong></p>
             <p class="whitespace-pre-line">{{ $klasis->wilayah_pelayanan ?: '-' }}</p>
             <hr class="my-2">
             <p><strong class="font-medium text-gray-600 block mb-1">Sejarah Singkat:</strong></p>
             <p class="whitespace-pre-line">{{ $klasis->sejarah_singkat ?: '-' }}</p>
        </div>

        {{-- Kolom 3: Daftar Jemaat --}}
        <div class="space-y-3 bg-white shadow rounded-lg p-6 border">
            <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Jemaat di Bawah Klasis ({{ $klasis->jemaat->count() }})</h3>
            @if($klasis->jemaat->isNotEmpty())
            <ul class="list-disc list-inside space-y-1 max-h-60 overflow-y-auto">
                @foreach ($klasis->jemaat as $jemaat)
                     <li>
                         <a href="{{ route('admin.jemaat.show', $jemaat->id) }}" class="text-primary hover:underline" title="Lihat Detail Jemaat">
                             {{ $jemaat->nama_jemaat }}
                         </a>
                         <span class="text-xs text-gray-500">({{ $jemaat->status_jemaat }})</span>
                     </li>
                @endforeach
            </ul>
            @else
             <p class="text-gray-500 italic">Belum ada data jemaat di klasis ini.</p>
            @endif
        </div>
    </div>
</div>
@endsection