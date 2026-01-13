@extends('admin.layout')

@section('title', 'Edit Anggaran')
@section('header-title', 'Edit Pos Anggaran')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg p-6">
        
        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800">Edit Data Pos Anggaran</h3>
            <a href="{{ route('admin.wadah.anggaran.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Batal & Kembali</a>
        </div>

        <form method="POST" action="{{ route('admin.wadah.anggaran.update', $anggaran->id) }}">
            @csrf @method('PUT')

            <div class="bg-blue-50 p-4 mb-6 rounded-md border border-blue-100 text-sm text-blue-800 flex items-start">
                <i class="fas fa-info-circle mt-0.5 mr-2"></i>
                <div>
                    <strong>Informasi Tetap:</strong><br>
                    Tahun: {{ $anggaran->tahun_anggaran }} <br>
                    Wadah: {{ $anggaran->jenisWadah->nama_wadah }} <br>
                    Tingkat: {{ ucfirst($anggaran->tingkat) }}
                </div>
            </div>

            <div class="mb-4">
                <label for="nama_pos_anggaran" class="block text-sm font-medium text-gray-700 mb-1">Nama Pos Anggaran</label>
                <input id="nama_pos_anggaran" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm font-bold" type="text" name="nama_pos_anggaran" value="{{ old('nama_pos_anggaran', $anggaran->nama_pos_anggaran)" required />
                @error('nama_pos_anggaran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="jenis_anggaran" class="block text-sm font-medium text-gray-700 mb-1">Jenis Anggaran</label>
                    <select id="jenis_anggaran" name="jenis_anggaran" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                        <option value="penerimaan" {{ $anggaran->jenis_anggaran == 'penerimaan' ? 'selected' : '' }}>Penerimaan</option>
                        <option value="pengeluaran" {{ $anggaran->jenis_anggaran == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <label for="jumlah_target" class="block text-sm font-medium text-gray-700 mb-1">Target Jumlah (Rp)</label>
                    <input id="jumlah_target" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" type="number" name="jumlah_target" value="{{ old('jumlah_target', $anggaran->jumlah_target) }}" required />
                </div>
            </div>

            <div class="mb-6">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="2" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm">{{ old('keterangan', $anggaran->keterangan) }}</textarea>
            </div>

            <div class="flex items-center justify-end border-t pt-4">
                <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow-sm transition duration-150">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection