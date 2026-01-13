@extends('admin.layout')

@section('title', 'Edit Mata Anggaran')
@section('header-title', 'Ubah Kode Akun')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6 md:p-8">
    <div class="flex justify-between items-center mb-6 border-b pb-3">
        <h2 class="text-xl font-bold text-gray-800">Ubah Akun: {{ $mataAnggaran->kode }}</h2>
        <a href="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
    </div>

    <form action="{{ route('admin.perbendaharaan.mata-anggaran.update', $mataAnggaran->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Akun <span class="text-red-500">*</span></label>
                <input type="text" name="kode" value="{{ old('kode', $mataAnggaran->kode) }}" required 
                       class="w-full border-gray-300 rounded-md focus:ring-primary">
                @error('kode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Anggaran <span class="text-red-500">*</span></label>
                <input type="text" name="nama_mata_anggaran" value="{{ old('nama_mata_anggaran', $mataAnggaran->nama_mata_anggaran) }}" required 
                       class="w-full border-gray-300 rounded-md focus:ring-primary">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Akun</label>
                    <select name="jenis" required class="w-full border-gray-300 rounded-md focus:ring-primary">
                        <option value="Pendapatan" {{ $mataAnggaran->jenis == 'Pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                        <option value="Belanja" {{ $mataAnggaran->jenis == 'Belanja' ? 'selected' : '' }}>Belanja</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelompok</label>
                    <input type="text" name="kelompok" value="{{ old('kelompok', $mataAnggaran->kelompok) }}" 
                           class="w-full border-gray-300 rounded-md">
                </div>
            </div>

            <div class="flex items-center space-x-2 py-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ $mataAnggaran->is_active ? 'checked' : '' }} class="rounded text-primary focus:ring-primary">
                <label for="is_active" class="text-sm font-medium text-gray-700">Akun Aktif (Muncul di Form RAPB)</label>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3 border-t pt-6">
            <a href="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-medium hover:bg-gray-300">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-bold shadow-lg hover:bg-blue-700 transition">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection