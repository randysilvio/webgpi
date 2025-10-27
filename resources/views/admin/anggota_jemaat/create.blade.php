@extends('admin.layout')

@section('title', 'Tambah Anggota Jemaat')
@section('header-title', 'Tambah Anggota Jemaat Baru')

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8 max-w-4xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir Anggota Jemaat Baru</h2>

    {{-- Tampilkan pesan sukses dari redirect 'save_and_add_another' --}}
    @if (session('success'))
        <div class="flash-message mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif
    {{-- Tampilkan pesan error umum --}}
     @if (session('error'))
        <div class="flash-message mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif


    <form action="{{ route('admin.anggota-jemaat.store') }}" method="POST">
        @csrf
        <div class="space-y-8">

            {{-- Data Keluarga --}}
            <section class="border rounded-lg p-6 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">A. Data Keluarga</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    {{-- Nomor KK --}}
                    <div>
                        <label for="nomor_kk" class="block text-sm font-medium text-gray-700 mb-1">Nomor Kartu Keluarga (KK)</label>
                        <input type="text" id="nomor_kk" name="nomor_kk" value="{{ old('nomor_kk', $prefillData['nomor_kk'] ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nomor_kk') border-red-500 @enderror">
                        @error('nomor_kk') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Status Dalam Keluarga --}}
                    <div>
                        <label for="status_dalam_keluarga" class="block text-sm font-medium text-gray-700 mb-1">Status Dalam Keluarga</label>
                        <select id="status_dalam_keluarga" name="status_dalam_keluarga"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('status_dalam_keluarga') border-red-500 @enderror">
                            <option value="">-- Pilih Status --</option>
                            <option value="Kepala Keluarga" {{ old('status_dalam_keluarga') == 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                            <option value="Istri" {{ old('status_dalam_keluarga') == 'Istri' ? 'selected' : '' }}>Istri</option>
                            <option value="Anak" {{ old('status_dalam_keluarga') == 'Anak' ? 'selected' : '' }}>Anak</option>
                            <option value="Famili Lain" {{ old('status_dalam_keluarga') == 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                        </select>
                        @error('status_dalam_keluarga') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Alamat --}}
                    <div class="md:col-span-2">
                        <label for="alamat_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap (Sesuai KK)</label>
                        <textarea id="alamat_lengkap" name="alamat_lengkap" rows="2"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('alamat_lengkap') border-red-500 @enderror">{{ old('alamat_lengkap', $prefillData['alamat_lengkap'] ?? '') }}</textarea>
                        @error('alamat_lengkap') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Jemaat Asal (Gereja) --}}
                    <div class="md:col-span-2">
                        <label for="jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Jemaat Asal <span class="text-red-600">*</span></label>
                        <select id="jemaat_id" name="jemaat_id" required
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('jemaat_id') border-red-500 @enderror">
                            <option value="">-- Pilih Jemaat --</option>
                            @forelse ($jemaatOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('jemaat_id', $prefillData['jemaat_id'] ?? '') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @empty
                                 <option value="" disabled>Tidak ada jemaat tersedia dalam scope Anda.</option>
                            @endforelse
                        </select>
                        @error('jemaat_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- Data Pribadi Anggota --}}
            <section class="border rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">B. Data Pribadi Anggota</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                    {{-- Nama Lengkap --}}
                    <div class="lg:col-span-2">
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nama_lengkap') border-red-500 @enderror">
                        @error('nama_lengkap') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- NIK --}}
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk Kependudukan (NIK)</label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik') }}" pattern="[0-9]*" maxlength="16"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nik') border-red-500 @enderror" placeholder="16 digit angka (jika ada)">
                        @error('nik') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Tempat Lahir --}}
                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('tempat_lahir') border-red-500 @enderror">
                        @error('tempat_lahir') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Tanggal Lahir --}}
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('tanggal_lahir') border-red-500 @enderror">
                        @error('tanggal_lahir') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Jenis Kelamin --}}
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('jenis_kelamin') border-red-500 @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Golongan Darah --}}
                    <div>
                        <label for="golongan_darah" class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                        <select id="golongan_darah" name="golongan_darah"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('golongan_darah') border-red-500 @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>B</option>
                            <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>AB</option>
                            <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>O</option>
                            <option value="Tidak Tahu" {{ old('golongan_darah') == 'Tidak Tahu' ? 'selected' : '' }}>Tidak Tahu</option>
                        </select>
                        @error('golongan_darah') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Status Pernikahan --}}
                    <div>
                        <label for="status_pernikahan" class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                        <select id="status_pernikahan" name="status_pernikahan"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('status_pernikahan') border-red-500 @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="Belum Menikah" {{ old('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                            <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                            <option value="Cerai Hidup" {{ old('status_pernikahan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ old('status_pernikahan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                        </select>
                        @error('status_pernikahan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Telepon --}}
                    <div>
                        <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon/HP</label>
                        <input type="tel" id="telepon" name="telepon" value="{{ old('telepon') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('telepon') border-red-500 @enderror">
                        @error('telepon') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('email') border-red-500 @enderror">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Nama Ayah --}}
                     <div>
                        <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
                        <input type="text" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nama_ayah') border-red-500 @enderror">
                        @error('nama_ayah') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Nama Ibu --}}
                    <div>
                        <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
                        <input type="text" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nama_ibu') border-red-500 @enderror">
                        @error('nama_ibu') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Nomor Buku Induk --}}
                    <div>
                        <label for="nomor_buku_induk" class="block text-sm font-medium text-gray-700 mb-1">Nomor Buku Induk Jemaat</label>
                        <input type="text" id="nomor_buku_induk" name="nomor_buku_induk" value="{{ old('nomor_buku_induk') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nomor_buku_induk') border-red-500 @enderror">
                        @error('nomor_buku_induk') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

             {{-- Data Gerejawi --}}
            <section class="border rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">C. Data Gerejawi</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4">
                     {{-- Tgl Baptis & Tempat --}}
                     <div>
                        <label for="tanggal_baptis" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Baptis</label>
                        <input type="date" id="tanggal_baptis" name="tanggal_baptis" value="{{ old('tanggal_baptis') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('tanggal_baptis') border-red-500 @enderror">
                        @error('tanggal_baptis') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     <div>
                        <label for="tempat_baptis" class="block text-sm font-medium text-gray-700 mb-1">Tempat Baptis</label>
                        <input type="text" id="tempat_baptis" name="tempat_baptis" value="{{ old('tempat_baptis') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('tempat_baptis') border-red-500 @enderror">
                        @error('tempat_baptis') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Tgl Sidi & Tempat --}}
                      <div>
                        <label for="tanggal_sidi" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sidi</label>
                        <input type="date" id="tanggal_sidi" name="tanggal_sidi" value="{{ old('tanggal_sidi') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('tanggal_sidi') border-red-500 @enderror">
                        @error('tanggal_sidi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     <div>
                        <label for="tempat_sidi" class="block text-sm font-medium text-gray-700 mb-1">Tempat Sidi</label>
                        <input type="text" id="tempat_sidi" name="tempat_sidi" value="{{ old('tempat_sidi') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('tempat_sidi') border-red-500 @enderror">
                        @error('tempat_sidi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Keanggotaan --}}
                    <div>
                        <label for="tanggal_masuk_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk Jemaat Ini</label>
                        <input type="date" id="tanggal_masuk_jemaat" name="tanggal_masuk_jemaat" value="{{ old('tanggal_masuk_jemaat') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('tanggal_masuk_jemaat') border-red-500 @enderror">
                        @error('tanggal_masuk_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     <div>
                        <label for="status_keanggotaan" class="block text-sm font-medium text-gray-700 mb-1">Status Keanggotaan <span class="text-red-600">*</span></label>
                        <select id="status_keanggotaan" name="status_keanggotaan" required
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('status_keanggotaan') border-red-500 @enderror">
                            <option value="Aktif" {{ old('status_keanggotaan', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Tidak Aktif" {{ old('status_keanggotaan') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="Pindah" {{ old('status_keanggotaan') == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                            <option value="Meninggal" {{ old('status_keanggotaan') == 'Meninggal' ? 'selected' : '' }}>Meninggal</option>
                        </select>
                        @error('status_keanggotaan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Pindahan --}}
                     <div>
                        <label for="asal_gereja_sebelumnya" class="block text-sm font-medium text-gray-700 mb-1">Asal Gereja Sebelumnya</label>
                        <input type="text" id="asal_gereja_sebelumnya" name="asal_gereja_sebelumnya" value="{{ old('asal_gereja_sebelumnya') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('asal_gereja_sebelumnya') border-red-500 @enderror" placeholder="(Jika pindahan)">
                        @error('asal_gereja_sebelumnya') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     <div>
                        <label for="nomor_atestasi" class="block text-sm font-medium text-gray-700 mb-1">Nomor Atestasi Masuk</label>
                        <input type="text" id="nomor_atestasi" name="nomor_atestasi" value="{{ old('nomor_atestasi') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nomor_atestasi') border-red-500 @enderror" placeholder="(Jika pindahan)">
                        @error('nomor_atestasi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Pelayanan --}}
                     <div class="lg:col-span-1">
                        <label for="sektor_pelayanan" class="block text-sm font-medium text-gray-700 mb-1">Sektor Pelayanan</label>
                        {{-- Isi otomatis dari $prefillData jika ada --}}
                        <input type="text" id="sektor_pelayanan" name="sektor_pelayanan" value="{{ old('sektor_pelayanan', $prefillData['sektor_pelayanan'] ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('sektor_pelayanan') border-red-500 @enderror">
                        @error('sektor_pelayanan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     <div class="lg:col-span-1">
                        <label for="unit_pelayanan" class="block text-sm font-medium text-gray-700 mb-1">Unit Pelayanan</label>
                         {{-- Isi otomatis dari $prefillData jika ada --}}
                        <input type="text" id="unit_pelayanan" name="unit_pelayanan" value="{{ old('unit_pelayanan', $prefillData['unit_pelayanan'] ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('unit_pelayanan') border-red-500 @enderror">
                        @error('unit_pelayanan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Tambahkan field Jabatan Pelayan, Wadah, Keterlibatan Lain --}}
                    <div>
                        <label for="jabatan_pelayan_khusus" class="block text-sm font-medium text-gray-700 mb-1">Jabatan Pelayan Khusus</label>
                        <input type="text" id="jabatan_pelayan_khusus" name="jabatan_pelayan_khusus" value="{{ old('jabatan_pelayan_khusus') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Mis: Penatua, Diaken">
                    </div>
                    <div>
                        <label for="wadah_kategorial" class="block text-sm font-medium text-gray-700 mb-1">Wadah Kategorial</label>
                        <input type="text" id="wadah_kategorial" name="wadah_kategorial" value="{{ old('wadah_kategorial') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Mis: PAR, PP, Perwata, dll">
                    </div>
                 </div>
            </section>

             {{-- Data Sensus Ekonomi --}}
            <section class="border rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">D. Data Sensus Ekonomi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                    {{-- Pendidikan --}}
                    <div>
                        <label for="pendidikan_terakhir" class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                        <select id="pendidikan_terakhir" name="pendidikan_terakhir"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('pendidikan_terakhir') border-red-500 @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="Tidak Sekolah" {{ old('pendidikan_terakhir') == 'Tidak Sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                            <option value="SD" {{ old('pendidikan_terakhir') == 'SD' ? 'selected' : '' }}>SD</option>
                            <option value="SMP" {{ old('pendidikan_terakhir') == 'SMP' ? 'selected' : '' }}>SMP</option>
                            <option value="SMA/SMK" {{ old('pendidikan_terakhir') == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                            <option value="Diploma" {{ old('pendidikan_terakhir') == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                            <option value="S1" {{ old('pendidikan_terakhir') == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('pendidikan_terakhir') == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('pendidikan_terakhir') == 'S3' ? 'selected' : '' }}>S3</option>
                        </select>
                        @error('pendidikan_terakhir') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Pekerjaan --}}
                    <div>
                        <label for="pekerjaan_utama" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Utama</label>
                        <input type="text" id="pekerjaan_utama" name="pekerjaan_utama" value="{{ old('pekerjaan_utama') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('pekerjaan_utama') border-red-500 @enderror">
                        @error('pekerjaan_utama') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Perkiraan Pendapatan --}}
                    <div>
                        <label for="perkiraan_pendapatan_keluarga" class="block text-sm font-medium text-gray-700 mb-1">Perkiraan Pendapatan Keluarga</label>
                        <select id="perkiraan_pendapatan_keluarga" name="perkiraan_pendapatan_keluarga"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('perkiraan_pendapatan_keluarga') border-red-500 @enderror">
                             <option value="">-- Pilih --</option>
                             <option value="< 1 Juta" {{ old('perkiraan_pendapatan_keluarga') == '< 1 Juta' ? 'selected' : '' }}>< 1 Juta</option>
                             <option value="1 - 3 Juta" {{ old('perkiraan_pendapatan_keluarga') == '1 - 3 Juta' ? 'selected' : '' }}>1 - 3 Juta</option>
                             <option value="3 - 5 Juta" {{ old('perkiraan_pendapatan_keluarga') == '3 - 5 Juta' ? 'selected' : '' }}>3 - 5 Juta</option>
                             <option value="5 - 10 Juta" {{ old('perkiraan_pendapatan_keluarga') == '5 - 10 Juta' ? 'selected' : '' }}>5 - 10 Juta</option>
                             <option value="> 10 Juta" {{ old('perkiraan_pendapatan_keluarga') == '> 10 Juta' ? 'selected' : '' }}>> 10 Juta</option>
                         </select>
                         @error('perkiraan_pendapatan_keluarga') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Status Rumah --}}
                     <div>
                        <label for="status_kepemilikan_rumah" class="block text-sm font-medium text-gray-700 mb-1">Status Kepemilikan Rumah</label>
                        <select id="status_kepemilikan_rumah" name="status_kepemilikan_rumah"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('status_kepemilikan_rumah') border-red-500 @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="Milik Sendiri" {{ old('status_kepemilikan_rumah') == 'Milik Sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                            <option value="Sewa/Kontrak" {{ old('status_kepemilikan_rumah') == 'Sewa/Kontrak' ? 'selected' : '' }}>Sewa/Kontrak</option>
                            <option value="Menumpang" {{ old('status_kepemilikan_rumah') == 'Menumpang' ? 'selected' : '' }}>Menumpang</option>
                        </select>
                        @error('status_kepemilikan_rumah') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Tambahkan field sensus lain jika ada di model --}}
                     <div>
                        <label for="sumber_penerangan" class="block text-sm font-medium text-gray-700 mb-1">Sumber Penerangan</label>
                        <input type="text" id="sumber_penerangan" name="sumber_penerangan" value="{{ old('sumber_penerangan') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                     <div>
                        <label for="sumber_air_minum" class="block text-sm font-medium text-gray-700 mb-1">Sumber Air Minum</label>
                        <input type="text" id="sumber_air_minum" name="sumber_air_minum" value="{{ old('sumber_air_minum') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>

                </div>
            </section>

             {{-- Catatan --}}
            <section class="border rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">E. Catatan Tambahan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                     {{-- Nama Kepala Keluarga (Otomatis?) --}}
                    <div>
                         <label for="nama_kepala_keluarga" class="block text-sm font-medium text-gray-700 mb-1">Nama Kepala Keluarga</label>
                        <input type="text" id="nama_kepala_keluarga" name="nama_kepala_keluarga" value="{{ old('nama_kepala_keluarga') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    {{-- Status Pekerjaan KK --}}
                    <div>
                         <label for="status_pekerjaan_kk" class="block text-sm font-medium text-gray-700 mb-1">Status Pekerjaan KK</label>
                        <input type="text" id="status_pekerjaan_kk" name="status_pekerjaan_kk" value="{{ old('status_pekerjaan_kk') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    {{-- Sektor Pekerjaan KK --}}
                    <div>
                         <label for="sektor_pekerjaan_kk" class="block text-sm font-medium text-gray-700 mb-1">Sektor Pekerjaan KK</label>
                        <input type="text" id="sektor_pekerjaan_kk" name="sektor_pekerjaan_kk" value="{{ old('sektor_pekerjaan_kk') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                     {{-- Keterlibatan Lain --}}
                     <div class="md:col-span-2">
                        <label for="keterlibatan_lain" class="block text-sm font-medium text-gray-700 mb-1">Keterlibatan Lain</label>
                        <textarea id="keterlibatan_lain" name="keterlibatan_lain" rows="2"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">{{ old('keterlibatan_lain') }}</textarea>
                    </div>
                    {{-- Catatan Umum --}}
                    <div class="md:col-span-2">
                         <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan Umum</label>
                        <textarea id="catatan" name="catatan" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm @error('catatan') border-red-500 @enderror">{{ old('catatan') }}</textarea>
                        @error('catatan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end items-center flex-wrap gap-3 border-t pt-6">
            <a href="{{ route('admin.anggota-jemaat.index') }}" class="text-gray-600 hover:text-gray-800 font-medium py-2 px-4 transition duration-150 ease-in-out">
                Batal
            </a>
            {{-- Tombol Simpan & Tambah Lagi --}}
            <button type="submit" name="save_and_add_another" value="1" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out order-last md:order-none">
                <i class="fas fa-plus mr-1"></i> Simpan & Tambah Anggota Lain
            </button>
            {{-- Tombol Simpan Biasa --}}
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out order-last">
                Simpan & Selesai
            </button>
        </div>
    </form>
</div>

@endsection

{{-- Tambahkan style jika belum ada di layout utama --}}
@push('styles')
<style>
    /* Styling dasar untuk pesan error */
    .flash-message { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
@endpush