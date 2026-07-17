@extends('layouts.app')

@section('title', 'Registrasi Dokumen Teologi & Liturgi')

@section('content')
    <x-admin-form 
        title="Formulir Unggah Dokumen Resmi Bidang 1" 
        action="{{ route('admin.bursa.store') }}" 
        back-route="{{ route('admin.bursa.index') }}"
        has-file="true"
    >
        <div class="space-y-6">
            <div class="bg-gray-50 border border-gray-200 p-4 rounded shadow-sm">
                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-2">Informasi Administratif</h4>
                <p class="text-xs text-gray-600">Dokumen yang diunggah akan masuk ke Pangkalan Data Sinode dan dilindungi oleh sistem otorisasi. Dokumen berbayar wajib diverifikasi sebelum dapat diunduh oleh para Pelayan Firman.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Judul Dokumen <span class="text-red-600">*</span></label>
                    <input type="text" name="judul_dokumen" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm"
                        placeholder="Misal: Liturgi Jumat Agung 2026">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Klasifikasi Kategori <span class="text-red-600">*</span></label>
                    <select name="kategori" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">-- Pilih Klasifikasi --</option>
                        <option value="Materi Khotbah">Materi Khotbah Mingguan</option>
                        <option value="Tata Ibadah & Liturgi">Tata Ibadah & Liturgi</option>
                        <option value="Surat Gembala">Surat Gembala</option>
                        <option value="Materi Katekisasi">Materi Katekisasi / Bina</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Biaya Penggantian (Rp) <span class="text-red-600">*</span></label>
                    <input type="number" name="harga_dokumen" required min="0" value="0"
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm"
                        placeholder="Isi 0 jika dokumen ini dibagikan gratis">
                    <p class="text-[10px] text-gray-500 mt-1">Biaya dibebankan pada kas jemaat pemohon.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Berkas Dokumen Utama (PDF/Word) <span class="text-red-600">*</span></label>
                    <input type="file" name="file_dokumen" accept=".pdf,.doc,.docx" required
                           class="block w-full text-xs text-gray-600 border border-gray-300 p-1.5 rounded bg-gray-50">
                    <p class="text-[10px] text-gray-500 mt-1">File ini akan dienkripsi dan disembunyikan dari publik.</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Uraian / Deskripsi Singkat <span class="text-red-600">*</span></label>
                <textarea name="deskripsi_singkat" rows="4" required 
                    class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm"></textarea>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Gambar Sampul Dokumen (Opsional)</label>
                <input type="file" name="cover_image" accept="image/*" class="block w-full md:w-1/2 text-xs text-gray-600 border border-gray-300 p-1.5 rounded bg-gray-50">
            </div>
        </div>
    </x-admin-form>
@endsection