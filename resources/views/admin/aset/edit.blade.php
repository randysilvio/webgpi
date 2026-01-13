@extends('admin.layout')

@section('title', 'Edit Aset')
@section('header-title', 'Edit Data Inventaris')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6 md:p-8">
    <div class="flex justify-between items-center mb-6 border-b pb-3">
        <h2 class="text-xl font-bold text-gray-800">Ubah Data: {{ $aset->nama_aset }}</h2>
        <a href="{{ route('admin.perbendaharaan.aset.show', $aset->id) }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
    </div>

    <form action="{{ route('admin.perbendaharaan.aset.update', $aset->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="col-span-2"><h3 class="text-sm font-bold text-blue-600 uppercase mb-3 border-b pb-1">Informasi Dasar</h3></div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Aset</label>
                <input type="text" name="nama_aset" value="{{ old('nama_aset', $aset->nama_aset) }}" required class="w-full border-gray-300 rounded-md focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Aset</label>
                <input type="text" name="kode_aset" value="{{ old('kode_aset', $aset->kode_aset) }}" class="w-full border-gray-300 rounded-md bg-gray-50" readonly>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="kategori" class="w-full border-gray-300 rounded-md">
                    @foreach(['Tanah', 'Gedung', 'Kendaraan', 'Peralatan Elektronik', 'Meubelair', 'Lainnya'] as $cat)
                        <option value="{{ $cat }}" {{ $aset->kategori == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
                <select name="kondisi" class="w-full border-gray-300 rounded-md">
                    @foreach(['Baik', 'Rusak Ringan', 'Rusak Berat'] as $kon)
                        <option value="{{ $kon }}" {{ $aset->kondisi == $kon ? 'selected' : '' }}>{{ $kon }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-2 mt-4"><h3 class="text-sm font-bold text-blue-600 uppercase mb-3 border-b pb-1">Legalitas & Lokasi</h3></div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. Sertifikat / BPKB</label>
                <input type="text" name="nomor_dokumen" value="{{ old('nomor_dokumen', $aset->nomor_dokumen) }}" class="w-full border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Perolehan</label>
                <input type="number" name="nilai_perolehan" value="{{ old('nilai_perolehan', (int)$aset->nilai_perolehan) }}" class="w-full border-gray-300 rounded-md">
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Fisik</label>
                <input type="text" name="lokasi_fisik" value="{{ old('lokasi_fisik', $aset->lokasi_fisik) }}" class="w-full border-gray-300 rounded-md">
            </div>

            <div class="pt-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Dokumen (PDF/JPG)</label>
                <input type="file" name="file_dokumen" class="block w-full text-xs text-gray-500">
                @if($aset->file_dokumen_path) <p class="text-xs text-green-600 mt-1">✓ Dokumen sudah ada</p> @endif
            </div>
            <div class="pt-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto</label>
                <input type="file" name="foto_aset" class="block w-full text-xs text-gray-500" onchange="previewImage(event, 'foto-preview-edit')">
                <img id="foto-preview-edit" src="{{ $aset->foto_aset_path ? Storage::url($aset->foto_aset_path) : '#' }}" class="mt-2 h-24 rounded border {{ $aset->foto_aset_path ? '' : 'hidden' }}">
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t">
            <a href="{{ route('admin.perbendaharaan.aset.show', $aset->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-bold shadow-lg">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection