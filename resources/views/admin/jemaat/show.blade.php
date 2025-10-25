@extends('admin.layout')

@section('title', 'Detail Jemaat: ' . $jemaat->nama_jemaat)
@section('header-title', 'Detail Jemaat')

@section('content')
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">{{ $jemaat->nama_jemaat }}</h2>
            <p class="text-sm text-gray-500">Klasis: {{ $jemaat->klasis->nama_klasis ?? '-' }} | Status: {{ $jemaat->status_jemaat }}</p>
        </div>
        <div>
             @canany(['edit jemaat', 'manage jemaat'])
              @hasanyrole('Super Admin|Admin Bidang 3|Admin Klasis|Admin Jemaat')
             <a href="{{ route('admin.jemaat.edit', $jemaat->id) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out">
                 Edit Jemaat
             </a>
              @endhasanyrole
             @endcanany
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t pt-4">
        {{-- Info Dasar --}}
        <div class="md:col-span-1 space-y-3 text-sm">
            <p><strong class="font-medium text-gray-700 w-28 inline-block">Kode:</strong> {{ $jemaat->kode_jemaat ?: '-' }}</p>
            <p><strong class="font-medium text-gray-700 w-28 inline-block">Jenis:</strong> {{ $jemaat->jenis_jemaat }}</p>
            <p><strong class="font-medium text-gray-700 w-28 inline-block">Tgl Berdiri:</strong> {{ optional($jemaat->tanggal_berdiri)->isoFormat('D MMMM YYYY') ?: '-' }}</p>
            <p><strong class="font-medium text-gray-700 w-28 inline-block">Alamat:</strong></p>
            <p class="pl-4 whitespace-pre-line">{{ $jemaat->alamat_gereja ?: '-' }}</p>
            <p><strong class="font-medium text-gray-700 w-28 inline-block">Telepon:</strong> {{ $jemaat->telepon_kantor ?: '-' }}</p>
            <p><strong class="font-medium text-gray-700 w-28 inline-block">Email:</strong> {{ $jemaat->email_jemaat ?: '-' }}</p>
        </div>

        {{-- Statistik & Kepemimpinan --}}
        <div class="md:col-span-1 space-y-3 text-sm border-l md:pl-6">
            <p><strong class="font-medium text-gray-700 w-32 inline-block">Ketua Majelis:</strong> {{ $jemaat->nama_ketua_majelis ?: '-' }}</p>
             <p><strong class="font-medium text-gray-700 w-32 inline-block">Sekretaris:</strong> {{ $jemaat->nama_sekretaris_majelis ?: '-' }}</p>
             <p><strong class="font-medium text-gray-700 w-32 inline-block">Periode:</strong> {{ $jemaat->periode_majelis ?: '-' }}</p>
             <hr class="my-3">
             <p><strong class="font-medium text-gray-700 w-32 inline-block">Jumlah KK:</strong> {{ $jemaat->jumlah_kk ?? 0 }}</p>
             <p><strong class="font-medium text-gray-700 w-32 inline-block">Total Jiwa:</strong> {{ $jemaat->jumlah_total_jiwa ?? 0 }}</p>
             <p><strong class="font-medium text-gray-700 w-32 inline-block">Data per:</strong> {{ optional($jemaat->tanggal_update_statistik)->isoFormat('D MMMM YYYY') ?: '-' }}</p>
        </div>

        {{-- Foto --}}
        <div class="md:col-span-1 border-l md:pl-6">
             <strong class="font-medium text-gray-700 text-sm block mb-2">Foto Gedung Gereja:</strong>
             @if ($jemaat->foto_gereja_path && Storage::disk('public')->exists($jemaat->foto_gereja_path))
                <img src="{{ Storage::url($jemaat->foto_gereja_path) }}" alt="Foto {{ $jemaat->nama_jemaat }}" class="rounded-md shadow border max-w-full h-auto">
            @else
                <p class="text-sm text-gray-500 italic">Tidak ada foto.</p>
            @endif
        </div>
    </div>
</div>

{{-- Daftar Anggota Jemaat (Placeholder/Next Step) --}}
<div class="bg-white shadow rounded-lg p-6 mt-6">
     <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Anggota Jemaat</h3>
     <p class="text-sm text-gray-500">Fitur daftar anggota jemaat akan ditampilkan di sini.</p>
     {{-- Tabel anggota jemaat bisa ditampilkan di sini nanti --}}
</div>

 <div class="mt-6">
    <a href="{{ route('admin.jemaat.index') }}" class="text-primary hover:underline">&larr; Kembali ke Daftar Jemaat</a>
</div>

@endsection