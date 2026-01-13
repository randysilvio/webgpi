@extends('admin.layout')

@section('title', 'Tambah Anggota Jemaat')
@section('header-title', 'Tambah Anggota Jemaat Baru')

@section('content')
{{-- Library Tambahan: Select2 untuk Pencarian Dinamis --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="bg-white shadow rounded-lg p-6 md:p-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir Pendaftaran Anggota Jemaat</h2>

    {{-- Penampil Error Validasi: Kunci Utama Mengatasi Masalah "Hanya Refresh" --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <p class="font-bold text-sm uppercase">Gagal Menyimpan! Periksa Kembali Inputan Berikut:</p>
            </div>
            <ul class="text-xs list-disc ml-8 font-semibold uppercase tracking-tight">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Pesan Sukses (Terutama dari redirect save_and_add_another) --}}
    @if (session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm font-bold text-sm uppercase">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.anggota-jemaat.store') }}" method="POST">
        @csrf

        {{-- BAGIAN 1: DATA PRIBADI & KONTAK --}}
        <section class="mb-8 border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">1. Data Pribadi & Kontak</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                {{-- Nama Lengkap --}}
                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required class="form-input">
                </div>
                {{-- NIK --}}
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik') }}" class="form-input" maxlength="20">
                </div>
                {{-- No Buku Induk --}}
                <div>
                    <label for="nomor_buku_induk" class="block text-sm font-medium text-gray-700 mb-1">No. Buku Induk <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="text" id="nomor_buku_induk" name="nomor_buku_induk" value="{{ old('nomor_buku_induk') }}" class="form-input" maxlength="50">
                </div>
                {{-- Tempat Lahir --}}
                <div>
                    <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="form-input">
                </div>
                {{-- Tanggal Lahir --}}
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-input">
                </div>
                {{-- Jenis Kelamin --}}
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-select">
                        <option value="" selected disabled>-- Pilih --</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                {{-- Golongan Darah --}}
                <div>
                    <label for="golongan_darah" class="block text-sm font-medium text-gray-700 mb-1">Gol. Darah</label>
                    <select id="golongan_darah" name="golongan_darah" class="form-select">
                         <option value="" selected disabled>-- Pilih --</option>
                         <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>A</option>
                         <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>B</option>
                         <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>AB</option>
                         <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>O</option>
                         <option value="Tidak Tahu" {{ old('golongan_darah') == 'Tidak Tahu' ? 'selected' : '' }}>Tidak Tahu</option>
                    </select>
                </div>
                {{-- Status Pernikahan --}}
                <div>
                    <label for="status_pernikahan" class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                    <select id="status_pernikahan" name="status_pernikahan" class="form-select">
                        <option value="" selected disabled>-- Pilih --</option>
                        <option value="Belum Menikah" {{ old('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                        <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                        <option value="Cerai Hidup" {{ old('status_pernikahan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                        <option value="Cerai Mati" {{ old('status_pernikahan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                </div>
                {{-- No Telepon --}}
                <div>
                    <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon/HP</label>
                    <input type="tel" id="telepon" name="telepon" value="{{ old('telepon') }}" class="form-input" placeholder="08...">
                </div>
                {{-- Alamat (Mendukung Prefill) --}}
                <div class="md:col-span-2 lg:col-span-3">
                    <label for="alamat_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap (Domisili Sekarang)</label>
                    <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" class="form-textarea">{{ old('alamat_lengkap', $prefillData['alamat_lengkap'] ?? '') }}</textarea>
                </div>
                {{-- Email --}}
                <div class="md:col-span-1">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-input">
                </div>
            </div>
        </section>

        {{-- BAGIAN 2: DATA KEANGGOTAAN GEREJA --}}
        <section class="mb-8 border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">2. Data Keanggotaan Gereja</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                {{-- Jemaat (Mendukung Prefill) --}}
                <div>
                    <label for="jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Jemaat Terdaftar <span class="text-red-600">*</span></label>
                    <select id="jemaat_id" name="jemaat_id" required class="form-select">
                        <option value="" selected disabled>-- Pilih Jemaat --</option>
                        @foreach ($jemaatOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('jemaat_id', $prefillData['jemaat_id'] ?? '') == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Status Keanggotaan --}}
                <div>
                    <label for="status_keanggotaan" class="block text-sm font-medium text-gray-700 mb-1">Status Keanggotaan <span class="text-red-600">*</span></label>
                    <select id="status_keanggotaan" name="status_keanggotaan" required class="form-select">
                        <option value="Aktif" {{ old('status_keanggotaan') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ old('status_keanggotaan') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="Pindah" {{ old('status_keanggotaan') == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                        <option value="Meninggal" {{ old('status_keanggotaan') == 'Meninggal' ? 'selected' : '' }}>Meninggal</option>
                    </select>
                </div>
                {{-- Nomor KK (Mendukung Prefill) --}}
                <div>
                    <label for="nomor_kk" class="block text-sm font-medium text-gray-700 mb-1">Nomor Kartu Keluarga (KK)</label>
                    <input type="text" id="nomor_kk" name="nomor_kk" value="{{ old('nomor_kk', $prefillData['nomor_kk'] ?? '') }}" class="form-input">
                </div>
                {{-- Sektor & Unit (Mendukung Prefill) --}}
                <div>
                    <label for="sektor_pelayanan" class="block text-sm font-medium text-gray-700 mb-1">Sektor Pelayanan</label>
                    <input type="text" id="sektor_pelayanan" name="sektor_pelayanan" value="{{ old('sektor_pelayanan', $prefillData['sektor_pelayanan'] ?? '') }}" class="form-input">
                </div>
                 <div>
                    <label for="unit_pelayanan" class="block text-sm font-medium text-gray-700 mb-1">Unit Pelayanan</label>
                    <input type="text" id="unit_pelayanan" name="unit_pelayanan" value="{{ old('unit_pelayanan', $prefillData['unit_pelayanan'] ?? '') }}" class="form-input">
                </div>
                {{-- Tanggal Baptis --}}
                <div>
                    <label for="tanggal_baptis" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Baptis</label>
                    <input type="date" id="tanggal_baptis" name="tanggal_baptis" value="{{ old('tanggal_baptis') }}" class="form-input">
                </div>
                {{-- Tempat Baptis --}}
                <div>
                    <label for="tempat_baptis" class="block text-sm font-medium text-gray-700 mb-1">Tempat Baptis</label>
                    <input type="text" id="tempat_baptis" name="tempat_baptis" value="{{ old('tempat_baptis') }}" class="form-input" placeholder="Nama Gereja/Jemaat">
                </div>
                {{-- Tanggal Sidi --}}
                <div>
                    <label for="tanggal_sidi" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sidi</label>
                    <input type="date" id="tanggal_sidi" name="tanggal_sidi" value="{{ old('tanggal_sidi') }}" class="form-input">
                </div>
                 {{-- Tempat Sidi --}}
                <div>
                    <label for="tempat_sidi" class="block text-sm font-medium text-gray-700 mb-1">Tempat Sidi</label>
                    <input type="text" id="tempat_sidi" name="tempat_sidi" value="{{ old('tempat_sidi') }}" class="form-input" placeholder="Nama Gereja/Jemaat">
                </div>
            </div>
        </section>

        {{-- BAGIAN 3: HUBUNGAN KELUARGA (POHON KELUARGA) --}}
        <section class="mb-8 border-2 border-blue-100 rounded-lg p-6 bg-blue-50/30">
            <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                <i class="fas fa-sitemap mr-2"></i> 3. Hubungan Keluarga (Pohon Keluarga)
            </h3>
            <p class="text-[11px] text-blue-600 mb-4 italic italic">Cari nama Ayah/Ibu di database jemaat. Jika belum terdaftar, isi Nama Ayah/Ibu manual di kolom teks bawahnya.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Relasi Ayah --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Pilih Ayah Biologis (Dari Database)</label>
                        <select name="ayah_id" id="select-ayah" class="w-full"></select>
                    </div>
                    <div>
                        <label for="nama_ayah" class="block text-[10px] font-bold text-gray-400 uppercase">Input Nama Ayah (Manual)</label>
                        <input type="text" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah') }}" class="form-input bg-white">
                    </div>
                </div>

                {{-- Relasi Ibu --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Pilih Ibu Biologis (Dari Database)</label>
                        <select name="ibu_id" id="select-ibu" class="w-full"></select>
                    </div>
                    <div>
                        <label for="nama_ibu" class="block text-[10px] font-bold text-gray-400 uppercase">Input Nama Ibu (Manual)</label>
                        <input type="text" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu') }}" class="form-input bg-white">
                    </div>
                </div>
                
                {{-- Status dalam Keluarga --}}
                <div>
                    <label for="status_dalam_keluarga" class="block text-sm font-medium text-gray-700 mb-1">Status dalam Keluarga</label>
                    <select id="status_dalam_keluarga" name="status_dalam_keluarga" class="form-select">
                        <option value="" selected disabled>-- Pilih Status --</option>
                        <option value="Kepala Keluarga" {{ old('status_dalam_keluarga') == 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                        <option value="Istri" {{ old('status_dalam_keluarga') == 'Istri' ? 'selected' : '' }}>Istri</option>
                        <option value="Anak" {{ old('status_dalam_keluarga') == 'Anak' ? 'selected' : '' }}>Anak</option>
                        <option value="Famili Lain" {{ old('status_dalam_keluarga') == 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                    </select>
                </div>
            </div>
        </section>

         {{-- BAGIAN 4: SENSUS EKONOMI --}}
         <section class="mb-8 border rounded-lg p-6">
             <h3 class="text-lg font-semibold text-gray-700 mb-1">4. Gambaran Ekonomi Keluarga</h3>
             <p class="text-xs text-gray-500 mb-4 italic">Data statistik untuk perencanaan pelayanan diakonia.</p>
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                {{-- Status Pekerjaan --}}
                <div>
                    <label for="pekerjaan_utama" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Utama</label>
                    <input type="text" id="pekerjaan_utama" name="pekerjaan_utama" value="{{ old('pekerjaan_utama') }}" class="form-input">
                </div>
                 {{-- Status Kepemilikan Rumah --}}
                <div>
                    <label for="status_kepemilikan_rumah" class="block text-sm font-medium text-gray-700 mb-1">Status Rumah</label>
                    <select id="status_kepemilikan_rumah" name="status_kepemilikan_rumah" class="form-select">
                        <option value="" selected disabled>-- Pilih --</option>
                        <option value="Milik Sendiri" {{ old('status_kepemilikan_rumah') == 'Milik Sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                        <option value="Sewa/Kontrak" {{ old('status_kepemilikan_rumah') == 'Sewa/Kontrak' ? 'selected' : '' }}>Sewa/Kontrak</option>
                        <option value="Menumpang" {{ old('status_kepemilikan_rumah') == 'Menumpang' ? 'selected' : '' }}>Menumpang</option>
                    </select>
                </div>
                {{-- Perkiraan Pendapatan --}}
                <div>
                    <label for="perkiraan_pendapatan_keluarga" class="block text-sm font-medium text-gray-700 mb-1">Pendapatan Keluarga</label>
                    <select id="perkiraan_pendapatan_keluarga" name="perkiraan_pendapatan_keluarga" class="form-select">
                        <option value="" selected disabled>-- Pilih --</option>
                        <option value="Di bawah UMR" {{ old('perkiraan_pendapatan_keluarga') == 'Di bawah UMR' ? 'selected' : '' }}>Di bawah UMR</option>
                        <option value="UMR - 5 Juta" {{ old('perkiraan_pendapatan_keluarga') == 'UMR - 5 Juta' ? 'selected' : '' }}>UMR - Rp 5 Juta</option>
                        <option value="Di atas 5 Juta" {{ old('perkiraan_pendapatan_keluarga') == 'Di atas 5 Juta' ? 'selected' : '' }}>Di atas Rp 5 Juta</option>
                    </select>
                </div>
             </div>
         </section>

        {{-- BAGIAN 5: CATATAN INTERNAL --}}
        <section class="mb-8 border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">5. Catatan Tambahan</h3>
            <textarea id="catatan" name="catatan" rows="3" class="form-textarea" placeholder="Keterangan lain mengenai anggota jemaat ini...">{{ old('catatan') }}</textarea>
        </section>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end items-center flex-wrap gap-3 border-t pt-6">
            <a href="{{ route('admin.anggota-jemaat.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md transition duration-150 ease-in-out">
                Batal
            </a>
            {{-- Tombol Simpan & Tambah Lagi: Mengirim value agar Controller melakukan prefill --}}
            <button type="submit" name="save_and_add_another" value="1" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                <i class="fas fa-plus mr-1"></i> Simpan & Tambah Anggota Keluarga
            </button>
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                <i class="fas fa-save mr-1"></i> Simpan & Selesai
            </button>
        </div>
    </form>
</div>

{{-- Scripts JQuery & Select2 --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Fungsi Inisialisasi Select2 AJAX untuk Ayah/Ibu
    function initSelect2Member(elementId) {
        $(elementId).select2({
            placeholder: 'Cari Nama Lengkap atau NIK...',
            ajax: {
                // Menggunakan rute search yang ada di web.php
                url: "{{ route('admin.anggota-jemaat.search') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data }; },
                cache: true
            }
        });
    }

    initSelect2Member('#select-ayah');
    initSelect2Member('#select-ibu');
});
</script>

{{-- Helper CSS untuk form & Select2 --}}
@push('styles')
<style>
    .form-input, .form-select, .form-textarea {
        display: block; width: 100%; padding: 0.5rem 0.75rem; font-size: 0.875rem;
        border: 1px solid #D1D5DB; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: #1e40af; outline: 0; box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.2);
    }
    /* Styling khusus Select2 agar selaras dengan Tailwind */
    .select2-container .select2-selection--single {
        height: 38px !important; border: 1px solid #D1D5DB !important; border-radius: 0.375rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important; font-size: 0.875rem !important; color: #374151 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>
@endpush

@endsection