@extends('layouts.app')

@section('title', 'Detail Aset')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="text-slate-500 hover:text-slate-800 text-xs font-bold uppercase tracking-wide flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.perbendaharaan.aset.edit', $aset->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-yellow-600 transition shadow-sm">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="md:flex">
            {{-- Kolom Gambar --}}
            <div class="md:w-1/3 bg-slate-100 flex items-center justify-center p-6 border-r border-slate-200">
                @if($aset->foto_aset_path)
                    <img src="{{ Storage::url($aset->foto_aset_path) }}" class="rounded-lg shadow-md max-h-64 object-cover w-full border border-slate-300">
                @else
                    <div class="text-center text-slate-400">
                        <i class="fas fa-image text-6xl mb-2"></i>
                        <p class="text-xs font-medium">Tidak ada foto</p>
                    </div>
                @endif
            </div>

            {{-- Kolom Detail --}}
            <div class="md:w-2/3 p-8">
                <h1 class="text-2xl font-bold text-slate-800 mb-1">{{ $aset->nama_aset }}</h1>
                <span class="inline-block bg-slate-100 text-slate-600 text-xs font-mono px-2 py-1 rounded border border-slate-200 mb-6">
                    {{ $aset->kode_aset }}
                </span>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 text-sm">
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kategori</span>
                        <span class="font-bold text-slate-700">{{ $aset->kategori }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kondisi</span>
                        @php $color = $aset->kondisi == 'Baik' ? 'green' : 'red'; @endphp
                        <span class="font-bold text-{{ $color }}-600">{{ $aset->kondisi }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Perolehan</span>
                        <span class="font-medium text-slate-700">{{ $aset->tanggal_perolehan ? $aset->tanggal_perolehan->isoFormat('D MMMM Y') : '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nilai Perolehan</span>
                        <span class="font-mono font-bold text-slate-800 text-lg">{{ $aset->format_nilai }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Lokasi Fisik</span>
                        <span class="font-medium text-slate-700"><i class="fas fa-map-marker-alt mr-1 text-red-500"></i> {{ $aset->lokasi_fisik ?? '-' }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Dokumen Legalitas</span>
                        @if($aset->file_dokumen_path)
                            <a href="{{ Storage::url($aset->file_dokumen_path) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 hover:underline font-bold">
                                <i class="fas fa-file-pdf mr-2"></i> Lihat Dokumen Pendukung
                            </a>
                        @else
                            <span class="text-slate-400 italic text-xs">Tidak ada dokumen terlampir.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection