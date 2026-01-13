@extends('admin.layout')

@section('title', 'Tambah Mata Anggaran')
@section('header-title', 'Tambah Kode Akun Baru')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6 md:p-8">
    <div class="flex justify-between items-center mb-6 border-b pb-3">
        <h2 class="text-xl font-bold text-gray-800">Formulir Mata Anggaran</h2>
        <a href="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
    </div>

    <form action="{{ route('admin.perbendaharaan.mata-anggaran.store') }}" method="POST">
        @csrf
        <div class="space-y-5">
            {{-- Kode Akun --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Akun <span class="text-red-500">*</span></label>
                <input type="text" name="kode" value="{{ old('kode') }}" required 
                       class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" placeholder="Cth: 1.5 atau 2.6">
                @error('kode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Nama Mata Anggaran --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Anggaran <span class="text-red-500">*</span></label>
                <input type="text" name="nama_mata_anggaran" value="{{ old('nama_mata_anggaran') }}" required 
                       class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" placeholder="Cth: Persembahan Syukur Laut">
                @error('nama_mata_anggaran') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Jenis & Kelompok --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Akun <span class="text-red-500">*</span></label>
                    <select name="jenis" required class="w-full border-gray-300 rounded-md focus:ring-primary">
                        <option value="Pendapatan" {{ old('jenis') == 'Pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                        <option value="Belanja" {{ old('jenis') == 'Belanja' ? 'selected' : '' }}>Belanja</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelompok</label>
                    <input type="text" name="kelompok" value="{{ old('kelompok') }}" 
                           class="w-full border-gray-300 rounded-md focus:ring-primary" placeholder="Cth: Rutin / Pelayanan">
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" rows="3" class="w-full border-gray-300 rounded-md focus:ring-primary">{{ old('deskripsi') }}</textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3 border-t pt-6">
            <a href="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-medium hover:bg-gray-300">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md font-bold shadow-lg hover:bg-blue-800 transition">Simpan Akun</button>
        </div>
    </form>
</div>
@endsection