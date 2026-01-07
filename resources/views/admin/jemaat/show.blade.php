@extends('admin.layout')

@section('title', 'Detail Jemaat: ' . $jemaat->nama_jemaat)
@section('header-title', 'Detail Data Jemaat')

@section('content')
<div class="bg-white shadow rounded-lg p-6 mb-6">
    {{-- HEADER --}}
    <div class="flex justify-between items-start mb-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">{{ $jemaat->nama_jemaat }}</h2>
            <p class="text-sm text-gray-500">
                {{-- Safe Access ke Klasis --}}
                Klasis: {{ $jemaat->klasis ? $jemaat->klasis->nama_klasis : 'Tanpa Klasis' }} 
                | Status: {{ $jemaat->status_jemaat ?? '-' }}
            </p>
        </div>
        <div>
             @if(auth()->check() && auth()->user()->hasAnyRole(['Super Admin', 'Admin Bidang 3', 'Admin Klasis', 'Admin Jemaat']))
                 <a href="{{ route('admin.jemaat.edit', $jemaat->id) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out">
                     Edit Jemaat
                 </a>
             @endif
        </div>
    </div>

    {{-- GRID INFORMASI --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t pt-4">
        
        {{-- KOLOM 1: Info Dasar --}}
        <div class="md:col-span-1 space-y-3 text-sm">
            <p><strong class="font-medium text-gray-700 w-28 inline-block">Kode:</strong> {{ $jemaat->kode_jemaat ?: '-' }}</p>
            <p><strong class="font-medium text-gray-700 w-28 inline-block">Jenis:</strong> {{ $jemaat->jenis_jemaat ?: '-' }}</p>
            
            <p>
                <strong class="font-medium text-gray-700 w-28 inline-block">Tgl Berdiri:</strong> 
                {{-- Safe Date Parsing --}}
                @if(!empty($jemaat->tanggal_berdiri))
                    {{ \Carbon\Carbon::parse($jemaat->tanggal_berdiri)->isoFormat('D MMMM YYYY') }}
                @else
                    -
                @endif
            </p>

            <p><strong class="font-medium text-gray-700 w-28 inline-block">Alamat:</strong></p>
            <p class="pl-4 whitespace-pre-line">{{ $jemaat->alamat_gereja ?: '-' }}</p>
            
            <p><strong class="font-medium text-gray-700 w-28 inline-block">Telepon:</strong> {{ $jemaat->telepon_kantor ?: '-' }}</p>
            <p><strong class="font-medium text-gray-700 w-28 inline-block">Email:</strong> {{ $jemaat->email_jemaat ?: '-' }}</p>
        </div>

        {{-- KOLOM 2: Statistik & Kepemimpinan --}}
        <div class="md:col-span-1 space-y-3 text-sm border-l md:pl-6">
            <p><strong class="font-medium text-gray-700 w-32 inline-block">Ketua Majelis:</strong> {{ $jemaat->nama_ketua_majelis ?: '-' }}</p>
             <p><strong class="font-medium text-gray-700 w-32 inline-block">Sekretaris:</strong> {{ $jemaat->nama_sekretaris_majelis ?: '-' }}</p>
             <p><strong class="font-medium text-gray-700 w-32 inline-block">Periode:</strong> {{ $jemaat->periode_majelis ?: '-' }}</p>
             <hr class="my-3">
             <p><strong class="font-medium text-gray-700 w-32 inline-block">Jumlah KK:</strong> {{ number_format($jemaat->jumlah_kk ?? 0) }}</p>
             <p><strong class="font-medium text-gray-700 w-32 inline-block">Total Jiwa:</strong> {{ number_format($jemaat->jumlah_total_jiwa ?? 0) }}</p>
             
             <p>
                 <strong class="font-medium text-gray-700 w-32 inline-block">Data per:</strong> 
                 @if(!empty($jemaat->updated_at))
                    {{ \Carbon\Carbon::parse($jemaat->updated_at)->isoFormat('D MMMM YYYY') }}
                 @else
                    -
                 @endif
             </p>
        </div>

        {{-- KOLOM 3: Foto --}}
        <div class="md:col-span-1 border-l md:pl-6">
             <strong class="font-medium text-gray-700 text-sm block mb-2">Foto Gedung Gereja:</strong>
             @if ($jemaat->foto_gereja_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($jemaat->foto_gereja_path))
                <img src="{{ \Illuminate\Support\Facades\Storage::url($jemaat->foto_gereja_path) }}" alt="Foto {{ $jemaat->nama_jemaat }}" class="rounded-md shadow border max-w-full h-auto">
            @else
                <div class="w-full h-40 bg-gray-50 border border-dashed border-gray-300 rounded flex items-center justify-center">
                    <p class="text-sm text-gray-400 italic">Tidak ada foto.</p>
                </div>
            @endif
        </div>
    </div>
    
    {{-- BAGIAN PENDETA (Data Tambahan) --}}
    <div class="mt-6 border-t pt-4">
        <h4 class="font-bold text-gray-700 mb-3">Pendeta Bertugas</h4>
        @if($jemaat->pendetaDitempatkan->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($jemaat->pendetaDitempatkan as $pendeta)
                <div class="flex items-center space-x-3 bg-gray-50 p-3 rounded border">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                        {{ substr($pendeta->nama_lengkap, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ $pendeta->nama_lengkap }}</p>
                        <p class="text-xs text-gray-500">NIP: {{ $pendeta->nip ?? '-' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 italic">Belum ada data pendeta yang ditempatkan.</p>
        @endif
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.jemaat.index') }}" class="text-primary hover:underline font-semibold">&larr; Kembali ke Daftar Jemaat</a>
</div>
@endsection