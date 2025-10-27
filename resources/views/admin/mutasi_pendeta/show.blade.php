@extends('admin.layout')

@section('title', 'Detail Mutasi Pendeta')
@section('header-title', 'Detail Mutasi SK No: ' . $mutasi->nomor_sk)

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8 max-w-3xl mx-auto">
    <div class="flex justify-between items-start mb-6 border-b pb-3">
        <h2 class="text-xl font-semibold text-gray-800">Detail Riwayat Mutasi</h2>
        <div>
             {{-- Tombol Edit & Hapus (Jika diizinkan) --}}
            {{-- <a href="{{ route('admin.mutasi.edit', $mutasi->id) }}" class="btn-secondary text-sm">Edit</a> --}}
            {{-- <form action="{{ route('admin.mutasi.destroy', $mutasi->id) }}" method="POST" class="inline ml-2"> @csrf @method('DELETE') <button type="submit" class="btn-danger text-sm" onclick="return confirm('Yakin?')">Hapus</button> </form> --}}
        </div>
    </div>

    <div class="space-y-4">
        {{-- Informasi Pendeta --}}
        <div>
            <span class="text-sm font-medium text-gray-500 block">Pendeta:</span>
            <p class="text-lg font-semibold text-gray-800">
                 <a href="{{ route('admin.pendeta.show', $mutasi->pendeta_id) }}" class="text-blue-600 hover:underline">
                    {{ $mutasi->pendeta->nama_lengkap ?? 'N/A' }} (NIPG: {{ $mutasi->pendeta->nipg ?? 'N/A' }})
                 </a>
            </p>
        </div>
         <hr>
        {{-- Detail SK --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <span class="text-sm font-medium text-gray-500 block">Nomor SK:</span>
                <p class="text-gray-800">{{ $mutasi->nomor_sk }}</p>
            </div>
             <div>
                <span class="text-sm font-medium text-gray-500 block">Tanggal SK:</span>
                <p class="text-gray-800">{{ $mutasi->tanggal_sk->format('d F Y') }}</p>
            </div>
             <div>
                <span class="text-sm font-medium text-gray-500 block">Jenis Mutasi:</span>
                <p class="text-gray-800">{{ $mutasi->jenis_mutasi }}</p>
            </div>
        </div>
         <hr>
         {{-- Detail Penempatan --}}
         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                 <span class="text-sm font-medium text-gray-500 block">Asal Penempatan:</span>
                 <p class="text-gray-800">
                    Klasis: {{ $mutasi->asalKlasis->nama_klasis ?? '-' }} <br>
                    Jemaat: {{ $mutasi->asalJemaat->nama_jemaat ?? '-' }}
                 </p>
            </div>
             <div>
                 <span class="text-sm font-medium text-gray-500 block">Tujuan Penempatan:</span>
                 <p class="text-gray-800">
                    Klasis: {{ $mutasi->tujuanKlasis->nama_klasis ?? '-' }} <br>
                    Jemaat: {{ $mutasi->tujuanJemaat->nama_jemaat ?? '-' }}
                 </p>
            </div>
         </div>
          <hr>
         {{-- Info Lain --}}
         <div>
            <span class="text-sm font-medium text-gray-500 block">Tanggal Efektif:</span>
            <p class="text-gray-800">{{ optional($mutasi->tanggal_efektif)->format('d F Y') ?? '-' }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500 block">Keterangan:</span>
            <p class="text-gray-800 whitespace-pre-wrap">{{ $mutasi->keterangan ?? '-' }}</p>
        </div>
         <div>
            <span class="text-sm font-medium text-gray-500 block">Dicatat pada:</span>
            <p class="text-gray-800">{{ $mutasi->created_at->format('d F Y H:i') }}</p>
        </div>

    </div>

    {{-- Tombol Kembali --}}
    <div class="mt-8 flex justify-start border-t pt-6">
         {{-- Kembali ke detail Pendeta --}}
        <a href="{{ route('admin.pendeta.show', $mutasi->pendeta_id) }}" class="btn-secondary">
             &larr; Kembali ke Detail Pendeta
        </a>
        {{-- Atau kembali ke daftar mutasi jika ada --}}
        {{-- <a href="{{ route('admin.mutasi.index') }}" class="btn-secondary">
             &larr; Kembali ke Daftar Mutasi
        </a> --}}
    </div>

</div>
{{-- Style --}}
@push('styles') <style> /* ... (style dari create.blade.php) ... */ </style> @endpush
@endsection