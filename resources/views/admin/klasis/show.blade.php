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
                    <i class="fas fa-church text-3xl"></i>
                </div>
            @endif
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $klasis->nama_klasis }}</h2>
                <div class="flex items-center text-sm text-gray-500 mt-1">
                    <span class="bg-primary/10 text-primary px-2 py-0.5 rounded text-xs font-bold mr-2">{{ $klasis->kode_klasis }}</span>
                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $klasis->pusat_klasis ?? 'Lokasi belum diset' }}
                </div>
            </div>
        </div>
        
        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="{{ route('admin.klasis.index') }}" class="bg-gray-100 text-gray-600 hover:bg-gray-200 px-4 py-2 rounded-md text-sm font-medium transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            @hasanyrole('Super Admin|Admin Bidang 3')
            <a href="{{ route('admin.klasis.edit', $klasis->id) }}" class="bg-yellow-500 text-white hover:bg-yellow-600 px-4 py-2 rounded-md text-sm font-medium transition shadow-sm">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            @endhasanyrole
        </div>
    </div>

    {{-- Grid Informasi --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Kolom 1: Informasi Dasar --}}
        <div class="space-y-4">
            <h3 class="text-base font-semibold text-gray-700 border-b pb-2">Informasi Organisasi</h3>
            
            <div class="text-sm">
                <span class="block text-gray-500 text-xs uppercase font-bold">Ketua MPK</span>
                <span class="text-gray-900 font-medium">
                    {{ $klasis->ketuaMp->nama_lengkap ?? '-' }}
                </span>
            </div>
            
            <div class="text-sm">
                <span class="block text-gray-500 text-xs uppercase font-bold">Tanggal Pembentukan</span>
                <span class="text-gray-900">
                    {{ $klasis->tanggal_pembentukan ? $klasis->tanggal_pembentukan->translatedFormat('d F Y') : '-' }}
                </span>
            </div>

            <div class="text-sm">
                <span class="block text-gray-500 text-xs uppercase font-bold">Nomor SK</span>
                <span class="text-gray-900">{{ $klasis->nomor_sk_pembentukan ?? '-' }}</span>
            </div>

            <div class="text-sm">
                <span class="block text-gray-500 text-xs uppercase font-bold">Klasis Induk</span>
                <span class="text-gray-900">{{ $klasis->klasis_induk ?? '-' }}</span>
            </div>
        </div>

        {{-- Kolom 2: Kontak & Deskripsi --}}
        <div class="space-y-4">
            <h3 class="text-base font-semibold text-gray-700 border-b pb-2">Kontak & Wilayah</h3>

            <div class="text-sm">
                <span class="block text-gray-500 text-xs uppercase font-bold">Alamat Kantor</span>
                <span class="text-gray-900 whitespace-pre-line">{{ $klasis->alamat_kantor ?? '-' }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="text-sm">
                    <span class="block text-gray-500 text-xs uppercase font-bold">Telepon</span>
                    <span class="text-gray-900">{{ $klasis->telepon_kantor ?? '-' }}</span>
                </div>
                <div class="text-sm">
                    <span class="block text-gray-500 text-xs uppercase font-bold">Email</span>
                    <span class="text-gray-900 break-all">{{ $klasis->email_klasis ?? '-' }}</span>
                </div>
            </div>

            <div class="text-sm">
                <span class="block text-gray-500 text-xs uppercase font-bold">Wilayah Pelayanan</span>
                <p class="text-gray-900 mt-1 text-justify text-xs leading-relaxed">
                    {{ $klasis->wilayah_pelayanan ?? 'Belum ada deskripsi wilayah.' }}
                </p>
            </div>
        </div>

        {{-- Kolom 3: Statistik Jemaat --}}
        <div class="space-y-4 bg-gray-50 p-4 rounded-lg border">
            <h3 class="text-base font-semibold text-gray-700 border-b pb-2">Statistik Jemaat</h3>
            
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Total Jemaat</span>
                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded-full">{{ $klasis->jemaat->count() }}</span>
            </div>

            <div class="h-64 overflow-y-auto pr-2 custom-scrollbar">
                @if($klasis->jemaat->isEmpty())
                    <p class="text-xs text-gray-400 italic text-center mt-4">Belum ada jemaat terdaftar.</p>
                @else
                    <ul class="space-y-2">
                        @foreach($klasis->jemaat as $jemaat)
                            <li class="bg-white p-2 rounded shadow-sm border flex justify-between items-center">
                                <a href="{{ route('admin.jemaat.show', $jemaat->id) }}" class="text-xs font-medium text-gray-700 hover:text-primary truncate max-w-[150px]">
                                    {{ $jemaat->nama_jemaat }}
                                </a>
                                <span class="text-[10px] {{ $jemaat->status_jemaat == 'Mandiri' ? 'text-green-600 bg-green-50' : 'text-yellow-600 bg-yellow-50' }} px-1.5 py-0.5 rounded border">
                                    {{ $jemaat->status_jemaat }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Sejarah Singkat (Full Width) --}}
    @if($klasis->sejarah_singkat)
    <div class="mt-8 border-t pt-6">
        <h3 class="text-base font-semibold text-gray-700 mb-2">Sejarah Singkat</h3>
        <div class="prose max-w-none text-sm text-gray-600 text-justify">
            {!! nl2br(e($klasis->sejarah_singkat)) !!}
        </div>
    </div>
    @endif
</div>
@endsection