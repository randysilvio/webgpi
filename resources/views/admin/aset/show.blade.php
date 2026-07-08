@extends('layouts.app')

@section('title', 'Dokumen Tinjauan Aset')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    {{-- Header --}}
    <div class="flex items-center justify-between border-b-2 border-gray-800 pb-4 mb-6">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Dokumen Register Aset</h2>
            <p class="text-xs text-gray-600 mt-1">Tinjauan detail dokumen inventaris dan legalitas properti.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest hover:bg-gray-100 transition flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Indeks Aset
            </a>
            <a href="{{ route('admin.perbendaharaan.aset.edit', $aset->id) }}" class="bg-gray-800 text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest hover:bg-gray-900 transition flex items-center shadow-sm">
                <i class="fas fa-edit mr-2"></i> Modifikasi Dokumen
            </a>
        </div>
    </div>

    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden border-l-8 border-l-blue-800">
        <div class="md:flex">
            {{-- Kolom Gambar / Media --}}
            <div class="md:w-1/3 bg-gray-50 flex items-center justify-center p-6 border-r border-gray-200 shadow-inner">
                @if($aset->foto_aset_path)
                    <div class="border-2 border-white shadow-md rounded overflow-hidden">
                        <img src="{{ Storage::url($aset->foto_aset_path) }}" class="max-h-64 object-cover w-full">
                    </div>
                @else
                    <div class="text-center text-gray-400">
                        <i class="fas fa-camera-retro text-6xl mb-3"></i>
                        <p class="text-[10px] font-bold uppercase tracking-widest">Dokumentasi Visual Kosong</p>
                    </div>
                @endif
            </div>

            {{-- Kolom Detail Dokumen --}}
            <div class="md:w-2/3 p-8">
                <div class="mb-6 border-b border-gray-200 pb-4">
                    <h1 class="text-2xl font-black text-gray-900 uppercase tracking-widest mb-2">{{ $aset->nama_aset }}</h1>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-gray-100 text-gray-800 text-[10px] font-mono font-bold px-2 py-1 rounded border border-gray-300">
                            {{ $aset->kode_aset }}
                        </span>
                        <span class="bg-blue-50 text-blue-800 text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded border border-blue-200">
                            {{ $aset->kategori }}
                        </span>
                        @php $color = $aset->kondisi == 'Baik' ? 'green' : 'red'; @endphp
                        <span class="bg-{{ $color }}-50 text-{{ $color }}-800 text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded border border-{{ $color }}-200">
                            Status: {{ $aset->kondisi }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 text-sm">
                    <div>
                        <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Tanggal Akuisisi</span>
                        <span class="font-bold text-gray-800">{{ $aset->tanggal_perolehan ? $aset->tanggal_perolehan->isoFormat('D MMMM Y') : 'Tidak Terdata' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Nilai Kapital Perolehan</span>
                        <span class="font-mono font-black text-gray-900 text-lg">Rp {{ number_format($aset->nilai_perolehan, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Status Hak Kepemilikan</span>
                        <span class="font-bold text-gray-800 uppercase">{{ $aset->status_kepemilikan ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Identifikasi BPKB / Sertifikat</span>
                        <span class="font-mono text-xs font-bold text-gray-700 bg-gray-50 px-1 rounded">{{ $aset->nomor_dokumen ?? 'KOSONG' }}</span>
                    </div>
                    <div class="md:col-span-2 border-t border-gray-100 pt-4">
                        <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Lokasi Fisik Keberadaan</span>
                        <span class="font-medium text-gray-800 text-xs"><i class="fas fa-map-marker-alt mr-1 text-gray-400"></i> {{ $aset->lokasi_fisik ?? 'TIDAK DIPETAKAN' }}</span>
                    </div>
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded border border-gray-200">
                        <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2 border-b border-gray-200 pb-1">Salinan Elektronik Legalitas (PDF)</span>
                        @if($aset->file_dokumen_path)
                            <a href="{{ Storage::url($aset->file_dokumen_path) }}" target="_blank" class="inline-flex items-center text-[10px] text-blue-800 hover:text-blue-600 uppercase font-black tracking-widest transition">
                                <i class="fas fa-file-pdf mr-2 text-red-600 text-lg"></i> Unduh / Buka Dokumen Tersimpan
                            </a>
                        @else
                            <span class="text-gray-400 italic text-[10px] font-bold uppercase tracking-widest">Tidak Ada Lampiran Salinan Elektronik.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection