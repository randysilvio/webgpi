@extends('admin.layout')

@section('title', 'Catat Aset Baru')
@section('header-title', 'Registrasi Inventaris Aset')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6 md:p-8">
    <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-3">Formulir Inventaris</h2>

    <form action="{{ route('admin.perbendaharaan.aset.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="col-span-2"><h3 class="text-sm font-bold text-blue-600 uppercase mb-3">Informasi Utama</h3></div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Aset <span class="text-red-500">*</span></label>
                <input type="text" name="nama_aset" required class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" placeholder="Contoh: Gedung Gereja Utama">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Aset (Opsional)</label>
                <input type="text" name="kode_aset" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" placeholder="Kosongkan untuk auto-generate">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                <select name="kategori" required class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    <option value="Tanah">Tanah</option>
                    <option value="Gedung">Gedung</option>
                    <option value="Kendaraan">Kendaraan</option>
                    <option value="Peralatan Elektronik">Peralatan Elektronik</option>
                    <option value="Meubelair">Meubelair</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi <span class="text-red-500">*</span></label>
                <select name="kondisi" required class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>
            </div>

            <div class="col-span-2 mt-4"><h3 class="text-sm font-bold text-blue-600 uppercase mb-3">Nilai & Legalitas</h3></div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Perolehan</label>
                <input type="date" name="tanggal_perolehan" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Perolehan (Rupiah)</label>
                <input type="number" name="nilai_perolehan" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" placeholder="Cth: 500000000">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Kepemilikan</label>
                <select name="status_kepemilikan" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    <option value="Milik Sendiri">Milik Sendiri</option>
                    <option value="Sewa">Sewa</option>
                    <option value="Pinjam Pakai">Pinjam Pakai</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. Sertifikat / BPKB</label>
                <input type="text" name="nomor_dokumen" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
            </div>

            <div class="col-span-2 mt-4"><h3 class="text-sm font-bold text-blue-600 uppercase mb-3">Lokasi & Berkas</h3></div>
            
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Fisik</label>
                <input type="text" name="lokasi_fisik" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" placeholder="Cth: Ruang Kantor Jemaat">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Scan Dokumen (PDF/JPG)</label>
                <input type="file" name="file_dokumen" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Aset</label>
                <input type="file" name="foto_aset" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" onchange="previewImage(event, 'foto-preview')">
                <img id="foto-preview" src="#" alt="Preview" class="mt-2 h-32 hidden rounded border shadow-sm">
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t">
            <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-medium">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md font-bold shadow-lg">Simpan Inventaris</button>
        </div>
    </form>
</div>
@endsection