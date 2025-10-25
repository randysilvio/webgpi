@extends('admin.layout')

@section('title', 'Tambah Klasis Baru')
@section('header-title', 'Tambah Data Klasis Baru')

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir Klasis Baru</h2>

    <form action="{{ route('admin.klasis.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            {{-- Kolom Kiri --}}
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-700 mb-3">Informasi Utama</h3>
                {{-- Nama Klasis --}}
                <div>
                    <label for="nama_klasis" class="block text-sm font-medium text-gray-700 mb-1">Nama Klasis <span class="text-red-600">*</span></label>
                    <input type="text" id="nama_klasis" name="nama_klasis" value="{{ old('nama_klasis') }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nama_klasis') border-red-500 @enderror">
                    @error('nama_klasis') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                {{-- Kode Klasis --}}
                <div>
                    <label for="kode_klasis" class="block text-sm font-medium text-gray-700 mb-1">Kode Klasis <span class="italic text-gray-500">(Opsional, Unik)</span></label>
                    <input type="text" id="kode_klasis" name="kode_klasis" value="{{ old('kode_klasis') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('kode_klasis') border-red-500 @enderror" placeholder="Contoh: FAK">
                    @error('kode_klasis') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                {{-- Pusat Klasis --}}
                <div>
                    <label for="pusat_klasis" class="block text-sm font-medium text-gray-700 mb-1">Pusat Klasis</label>
                    <input type="text" id="pusat_klasis" name="pusat_klasis" value="{{ old('pusat_klasis') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Nama Kota/Kabupaten">
                </div>
                 {{-- Ketua MPK --}}
                <div>
                    <label for="ketua_mpk_pendeta_id" class="block text-sm font-medium text-gray-700 mb-1">Ketua MPK <span class="italic text-gray-500">(Opsional)</span></label>
                    <select id="ketua_mpk_pendeta_id" name="ketua_mpk_pendeta_id"
                            class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('ketua_mpk_pendeta_id') border-red-500 @enderror">
                        <option value="">-- Pilih Pendeta --</option>
                        @foreach ($pendetaOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('ketua_mpk_pendeta_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                    @error('ketua_mpk_pendeta_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                {{-- Alamat Kantor --}}
                <div>
                    <label for="alamat_kantor" class="block text-sm font-medium text-gray-700 mb-1">Alamat Kantor</label>
                    <textarea id="alamat_kantor" name="alamat_kantor" rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">{{ old('alamat_kantor') }}</textarea>
                </div>
                {{-- Tanggal Pembentukan --}}
                <div>
                    <label for="tanggal_pembentukan" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembentukan</label>
                    <input type="date" id="tanggal_pembentukan" name="tanggal_pembentukan" value="{{ old('tanggal_pembentukan') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    @error('tanggal_pembentukan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                {{-- Klasis Induk --}}
                <div>
                    <label for="klasis_induk" class="block text-sm font-medium text-gray-700 mb-1">Klasis Induk <span class="italic text-gray-500">(Jika Pemekaran)</span></label>
                    <input type="text" id="klasis_induk" name="klasis_induk" value="{{ old('klasis_induk') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>
            </div>

            {{-- Kolom Kanan --}}
            <div class="space-y-4">
                 <h3 class="text-lg font-medium text-gray-700 mb-3">Kontak & Lain-lain</h3>
                {{-- Telepon Kantor --}}
                <div>
                    <label for="telepon_kantor" class="block text-sm font-medium text-gray-700 mb-1">Telepon Kantor</label>
                    <input type="text" id="telepon_kantor" name="telepon_kantor" value="{{ old('telepon_kantor') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="09XX-XXXXXX">
                </div>
                {{-- Email Klasis --}}
                <div>
                    <label for="email_klasis" class="block text-sm font-medium text-gray-700 mb-1">Email Klasis <span class="italic text-gray-500">(Opsional, Unik)</span></label>
                    <input type="email" id="email_klasis" name="email_klasis" value="{{ old('email_klasis') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('email_klasis') border-red-500 @enderror" placeholder="klasis@email.com">
                    @error('email_klasis') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                {{-- Website Klasis --}}
                <div>
                    <label for="website_klasis" class="block text-sm font-medium text-gray-700 mb-1">Website Klasis <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="url" id="website_klasis" name="website_klasis" value="{{ old('website_klasis') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('website_klasis') border-red-500 @enderror" placeholder="https://">
                    @error('website_klasis') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                {{-- Wilayah Pelayanan --}}
                <div>
                    <label for="wilayah_pelayanan" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Wilayah Pelayanan</label>
                    <textarea id="wilayah_pelayanan" name="wilayah_pelayanan" rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">{{ old('wilayah_pelayanan') }}</textarea>
                </div>
                 {{-- Sejarah Singkat --}}
                <div>
                    <label for="sejarah_singkat" class="block text-sm font-medium text-gray-700 mb-1">Sejarah Singkat</label>
                    <textarea id="sejarah_singkat" name="sejarah_singkat" rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">{{ old('sejarah_singkat') }}</textarea>
                </div>
                {{-- Foto Kantor --}}
                <div class="pt-2">
                    <label for="foto_kantor_path" class="block text-sm font-medium text-gray-700 mb-1">Foto Kantor <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="file" id="foto_kantor_path" name="foto_kantor_path" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" onchange="previewImage(event, 'foto-preview')">
                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maks: 2MB.</p>
                    <img id="foto-preview" src="#" alt="Preview Foto Kantor" class="image-preview mt-2 hidden">
                    @error('foto_kantor_path') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            <a href="{{ route('admin.klasis.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                Batal
            </a>
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                Simpan Klasis
            </button>
        </div>
    </form>
</div>

{{-- Error message styling (jika belum ada di layout atau diperlukan) --}}
@push('styles')
<style>
    .error-message {
        margin-top: 0.25rem;
        font-size: 0.75rem; /* text-xs */
        color: #DC2626; /* text-red-600 */
    }
    input.border-red-500, select.border-red-500, textarea.border-red-500 {
         border-color: #EF4444 !important;
    }
    input.border-red-500:focus, select.border-red-500:focus, textarea.border-red-500:focus {
         box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
    }
</style>
@endpush

@endsection