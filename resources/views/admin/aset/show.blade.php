@extends('admin.layout')

@section('title', $aset->nama_aset)
@section('header-title', 'Detail Aset')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white shadow rounded-xl p-6 flex flex-col md:flex-row gap-8">
        <div class="flex-shrink-0">
            @if($aset->foto_aset_path)
                <img src="{{ Storage::url($aset->foto_aset_path) }}" class="w-64 h-64 object-cover rounded-lg border shadow-sm">
            @else
                <div class="w-64 h-64 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                    <i class="fas fa-image fa-4x"></i>
                </div>
            @endif
        </div>
        <div class="flex-grow">
            <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $aset->nama_aset }}</h2>
            <p class="text-sm font-mono text-blue-600 mb-4">{{ $aset->kode_aset }}</p>
            
            <div class="grid grid-cols-2 gap-y-4 text-sm">
                <div><span class="text-gray-500 block">Kategori</span><span class="font-bold">{{ $aset->kategori }}</span></div>
                <div><span class="text-gray-500 block">Kondisi</span><span class="font-bold">{{ $aset->kondisi }}</span></div>
                <div><span class="text-gray-500 block">Tgl Perolehan</span><span class="font-bold">{{ $aset->tanggal_perolehan ? $aset->tanggal_perolehan->format('d/m/Y') : '-' }}</span></div>
                <div><span class="text-gray-500 block">Nilai Perolehan</span><span class="font-bold text-green-600">{{ $aset->format_nilai }}</span></div>
            </div>

            <div class="mt-6 flex space-x-3">
                @if($aset->file_dokumen_path)
                    <a href="{{ Storage::url($aset->file_dokumen_path) }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium"><i class="fas fa-file-download mr-2"></i> Lihat Dokumen</a>
                @endif
                <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection