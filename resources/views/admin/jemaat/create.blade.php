@extends('admin.layout')

@section('title', 'Tambah Jemaat Baru')
@section('header-title', 'Tambah Data Jemaat Baru')

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir Jemaat Baru</h2>

    {{-- Tampilkan error validasi umum jika ada --}}
    @if ($errors->any() && !$errors->hasAny(['nama_jemaat', 'klasis_id', 'status_jemaat', 'jenis_jemaat' /* ... field spesifik lainnya ... */]))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            <p class="font-bold">Terjadi Kesalahan:</p>
            <ul class="mt-1 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.jemaat.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            {{-- Kolom Kiri --}}
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-700 mb-3">Informasi Utama</h3>
                {{-- Nama Jemaat --}}
                <div>
                    <label for="nama_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Nama Jemaat <span class="text-red-600">*</span></label>
                    <input type="text" id="nama_jemaat" name="nama_jemaat" value="{{ old('nama_jemaat') }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nama_jemaat') border-red-500 @enderror">
                    @error('nama_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Kode Jemaat --}}
                <div>
                    <label for="kode_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Kode Jemaat <span class="italic text-gray-500">(Opsional, Unik)</span></label>
                    <input type="text" id="kode_jemaat" name="kode_jemaat" value="{{ old('kode_jemaat') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('kode_jemaat') border-red-500 @enderror" placeholder="Contoh: FAK01">
                    @error('kode_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Klasis --}}
                <div>
                    <label for="klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Klasis <span class="text-red-600">*</span></label>
                    <select id="klasis_id" name="klasis_id" required {{ Auth::check() && Auth::user()->hasRole('Admin Klasis') && count($klasisOptions) == 1 ? 'disabled' : '' }}
                            class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm {{ Auth::check() && Auth::user()->hasRole('Admin Klasis') && count($klasisOptions) == 1 ? 'bg-gray-100 cursor-not-allowed' : '' }} @error('klasis_id') border-red-500 @enderror">
                        <option value="" disabled {{ !old('klasis_id') ? 'selected' : '' }}>-- Pilih Klasis --</option>
                        {{-- Variabel yang benar adalah $klasisOptions --}}
                        @if(isset($klasisOptions) && $klasisOptions->isNotEmpty())
                            @foreach ($klasisOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('klasis_id', (Auth::check() && Auth::user()->hasRole('Admin Klasis') && count($klasisOptions) == 1 ? $klasisOptions->keys()->first() : null)) == $id ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        @else
                             <option value="" disabled>-- Data Klasis belum tersedia --</option>
                        @endif
                    </select>
                    {{-- Hidden input jika disabled untuk Admin Klasis --}}
                     @if(Auth::check() && Auth::user()->hasRole('Admin Klasis') && count($klasisOptions) == 1)
                        <input type="hidden" name="klasis_id" value="{{ $klasisOptions->keys()->first() }}">
                     @endif
                    @error('klasis_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Alamat Gereja --}}
                <div>
                    <label for="alamat_gereja" class="block text-sm font-medium text-gray-700 mb-1">Alamat Gereja</label>
                    <textarea id="alamat_gereja" name="alamat_gereja" rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Jl. Contoh No. 1, Kampung ABC, Distrik XYZ...">{{ old('alamat_gereja') }}</textarea>
                </div>

                {{-- Tanggal Berdiri --}}
                <div>
                    <label for="tanggal_berdiri" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berdiri/Peresmian</label>
                    <input type="date" id="tanggal_berdiri" name="tanggal_berdiri" value="{{ old('tanggal_berdiri') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    @error('tanggal_berdiri') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Status Jemaat --}}
                <div>
                    <label for="status_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Status Jemaat <span class="text-red-600">*</span></label>
                    <select id="status_jemaat" name="status_jemaat" required
                            class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('status_jemaat') border-red-500 @enderror">
                        <option value="Mandiri" {{ old('status_jemaat', 'Mandiri') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="Bakal Jemaat" {{ old('status_jemaat') == 'Bakal Jemaat' ? 'selected' : '' }}>Bakal Jemaat</option>
                        <option value="Pos Pelayanan" {{ old('status_jemaat') == 'Pos Pelayanan' ? 'selected' : '' }}>Pos Pelayanan</option>
                    </select>
                    @error('status_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                 {{-- Jenis Jemaat --}}
                <div>
                    <label for="jenis_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Jenis Jemaat <span class="text-red-600">*</span></label>
                    <select id="jenis_jemaat" name="jenis_jemaat" required
                            class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('jenis_jemaat') border-red-500 @enderror">
                        <option value="Umum" {{ old('jenis_jemaat', 'Umum') == 'Umum' ? 'selected' : '' }}>Umum</option>
                        <option value="Kategorial" {{ old('jenis_jemaat') == 'Kategorial' ? 'selected' : '' }}>Kategorial</option>
                    </select>
                    @error('jenis_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Kolom Kanan --}}
            <div class="space-y-4 mt-6 md:mt-0">
                <h3 class="text-lg font-medium text-gray-700 mb-3">Data Statistik & Kontak</h3>

                {{-- Jumlah KK --}}
                <div>
                    <label for="jumlah_kk" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kepala Keluarga (KK)</label>
                    <input type="number" id="jumlah_kk" name="jumlah_kk" value="{{ old('jumlah_kk', 0) }}" min="0"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('jumlah_kk') border-red-500 @enderror">
                    @error('jumlah_kk') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                 {{-- Jumlah Jiwa --}}
                <div>
                    <label for="jumlah_total_jiwa" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Total Jiwa</label>
                    <input type="number" id="jumlah_total_jiwa" name="jumlah_total_jiwa" value="{{ old('jumlah_total_jiwa', 0) }}" min="0"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('jumlah_total_jiwa') border-red-500 @enderror">
                     @error('jumlah_total_jiwa') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal Update Statistik --}}
                <div>
                    <label for="tanggal_update_statistik" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Update Statistik</label>
                    <input type="date" id="tanggal_update_statistik" name="tanggal_update_statistik" value="{{ old('tanggal_update_statistik', date('Y-m-d')) }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                     @error('tanggal_update_statistik') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                 <div>
                    <label for="telepon_kantor" class="block text-sm font-medium text-gray-700 mb-1">Telepon Kantor/Kontak</label>
                    <input type="text" id="telepon_kantor" name="telepon_kantor" value="{{ old('telepon_kantor') }}" placeholder="09XX-XXXXXX"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>

                <div>
                    <label for="email_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Email Jemaat <span class="italic text-gray-500">(Opsional, Unik)</span></label>
                    <input type="email" id="email_jemaat" name="email_jemaat" value="{{ old('email_jemaat') }}" placeholder="jemaat.contoh@email.com"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('email_jemaat') border-red-500 @enderror">
                     @error('email_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                 {{-- Foto Gereja --}}
                <div class="pt-2">
                    <label for="foto_gereja_path" class="block text-sm font-medium text-gray-700 mb-1">Foto Gedung Gereja <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="file" id="foto_gereja_path" name="foto_gereja_path" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" onchange="previewImage(event, 'foto-preview')">
                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maks: 2MB.</p>
                    <img id="foto-preview" src="#" alt="Preview Foto Gereja" class="image-preview mt-2 hidden">
                    @error('foto_gereja_path') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Tambahkan field lain sesuai kebutuhan --}}
                {{-- Misal: Jemaat Induk, Sejarah, SK Pendirian, Kepemimpinan, Sarana, dll. --}}

            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            <a href="{{ route('admin.jemaat.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                Batal
            </a>
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                Simpan Jemaat
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