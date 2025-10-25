@extends('admin.layout')

@section('title', 'Tambah Pendeta Baru')
@section('header-title', 'Tambah Data Pendeta Baru')

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir Pendeta Baru</h2>

    <form action="{{ route('admin.pendeta.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- BAGIAN 1: DATA PRIBADI & KONTAK --}}
        <section class="mb-8 border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">1. Data Pribadi & Kontak</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                {{-- Nama Lengkap --}}
                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap & Gelar <span class="text-red-600">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nama_lengkap') border-red-500 @enderror">
                    @error('nama_lengkap') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                {{-- NIK --}}
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nik') border-red-500 @enderror" maxlength="20">
                    @error('nik') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                 {{-- NIPG --}}
                <div>
                    <label for="nipg" class="block text-sm font-medium text-gray-700 mb-1">NIPG <span class="text-red-600">*</span></label>
                    <input type="text" id="nipg" name="nipg" value="{{ old('nipg') }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nipg') border-red-500 @enderror" maxlength="50">
                    @error('nipg') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                 {{-- Tempat Lahir --}}
                <div>
                    <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir <span class="text-red-600">*</span></label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('tempat_lahir') border-red-500 @enderror">
                    @error('tempat_lahir') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                {{-- Tanggal Lahir --}}
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-600">*</span></label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('tanggal_lahir') border-red-500 @enderror">
                    @error('tanggal_lahir') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                {{-- Jenis Kelamin --}}
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-600">*</span></label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required
                            class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('jenis_kelamin') border-red-500 @enderror">
                        <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>-- Pilih --</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                     @error('jenis_kelamin') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                 {{-- Status Pernikahan --}}
                <div>
                    <label for="status_pernikahan" class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                    <select id="status_pernikahan" name="status_pernikahan"
                            class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        <option value="" disabled {{ old('status_pernikahan') ? '' : 'selected' }}>-- Pilih --</option>
                        <option value="Belum Menikah" {{ old('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                        <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                        <option value="Cerai Hidup" {{ old('status_pernikahan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                        <option value="Cerai Mati" {{ old('status_pernikahan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                </div>
                 {{-- Nama Pasangan --}}
                <div>
                    <label for="nama_pasangan" class="block text-sm font-medium text-gray-700 mb-1">Nama Suami/Istri</label>
                    <input type="text" id="nama_pasangan" name="nama_pasangan" value="{{ old('nama_pasangan') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>
                 {{-- No Telepon --}}
                <div>
                    <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon/HP Aktif</label>
                    <input type="tel" id="telepon" name="telepon" value="{{ old('telepon') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="08...">
                </div>
                 {{-- Alamat --}}
                <div class="md:col-span-2">
                    <label for="alamat_domisili" class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili</label>
                    <textarea id="alamat_domisili" name="alamat_domisili" rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">{{ old('alamat_domisili') }}</textarea>
                </div>
                 {{-- Email --}}
                 <div class="md:col-span-1">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="italic text-gray-500">(Untuk Akun Login)</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('email') border-red-500 @enderror" placeholder="pendeta@email.com">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                     <p class="mt-1 text-xs text-gray-500 italic">Jika kosong, NIPG@gpipapua.local akan digunakan.</p>
                </div>
            </div>
        </section>

        {{-- BAGIAN 2: DATA KEPENDETAAN & KEPEGAWAIAN --}}
        <section class="mb-8 border rounded-lg p-6">
             <h3 class="text-lg font-semibold text-gray-700 mb-4">2. Kependetaan & Kepegawaian</h3>
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                 {{-- Tanggal Tahbisan --}}
                <div>
                    <label for="tanggal_tahbisan" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tahbisan <span class="text-red-600">*</span></label>
                    <input type="date" id="tanggal_tahbisan" name="tanggal_tahbisan" value="{{ old('tanggal_tahbisan') }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('tanggal_tahbisan') border-red-500 @enderror">
                    @error('tanggal_tahbisan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                 {{-- Tempat Tahbisan --}}
                <div>
                    <label for="tempat_tahbisan" class="block text-sm font-medium text-gray-700 mb-1">Tempat Tahbisan <span class="text-red-600">*</span></label>
                    <input type="text" id="tempat_tahbisan" name="tempat_tahbisan" value="{{ old('tempat_tahbisan') }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('tempat_tahbisan') border-red-500 @enderror" placeholder="Nama Gereja/Kota">
                    @error('tempat_tahbisan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                 {{-- SK Kependetaan --}}
                <div>
                    <label for="nomor_sk_kependetaan" class="block text-sm font-medium text-gray-700 mb-1">Nomor SK Kependetaan</label>
                    <input type="text" id="nomor_sk_kependetaan" name="nomor_sk_kependetaan" value="{{ old('nomor_sk_kependetaan') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>
                 {{-- Status Kepegawaian --}}
                <div>
                    <label for="status_kepegawaian" class="block text-sm font-medium text-gray-700 mb-1">Status Kepegawaian <span class="text-red-600">*</span></label>
                    <select id="status_kepegawaian" name="status_kepegawaian" required
                            class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('status_kepegawaian') border-red-500 @enderror">
                        <option value="Aktif" {{ old('status_kepegawaian', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Vikaris" {{ old('status_kepegawaian') == 'Vikaris' ? 'selected' : '' }}>Vikaris</option>
                        <option value="Emeritus" {{ old('status_kepegawaian') == 'Emeritus' ? 'selected' : '' }}>Emeritus</option>
                        <option value="Tugas Belajar" {{ old('status_kepegawaian') == 'Tugas Belajar' ? 'selected' : '' }}>Tugas Belajar</option>
                        <option value="Izin Belajar" {{ old('status_kepegawaian') == 'Izin Belajar' ? 'selected' : '' }}>Izin Belajar</option>
                        <option value="Dikaryakan" {{ old('status_kepegawaian') == 'Dikaryakan' ? 'selected' : '' }}>Dikaryakan di Luar GPI</option>
                        <option value="Non-Aktif" {{ old('status_kepegawaian') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        <option value="Lainnya" {{ old('status_kepegawaian') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                     @error('status_kepegawaian') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                 {{-- Pendidikan Teologi --}}
                <div>
                    <label for="pendidikan_teologi_terakhir" class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Teologi Terakhir</label>
                    <input type="text" id="pendidikan_teologi_terakhir" name="pendidikan_teologi_terakhir" value="{{ old('pendidikan_teologi_terakhir') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="S.Th, M.Th, Dr., dll.">
                </div>
                 {{-- Institusi Pendidikan --}}
                <div>
                    <label for="institusi_pendidikan_teologi" class="block text-sm font-medium text-gray-700 mb-1">Asal Institusi Pendidikan</label>
                    <input type="text" id="institusi_pendidikan_teologi" name="institusi_pendidikan_teologi" value="{{ old('institusi_pendidikan_teologi') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>
                 {{-- Tanggal Masuk GPI --}}
                <div>
                    <label for="tanggal_mulai_masuk_gpi" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Masuk GPI</label>
                    <input type="date" id="tanggal_mulai_masuk_gpi" name="tanggal_mulai_masuk_gpi" value="{{ old('tanggal_mulai_masuk_gpi') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>
                 {{-- Golongan/Pangkat --}}
                 <div>
                    <label for="golongan_pangkat_terakhir" class="block text-sm font-medium text-gray-700 mb-1">Golongan/Pangkat Terakhir <span class="italic text-gray-500">(Jika Ada)</span></label>
                    <input type="text" id="golongan_pangkat_terakhir" name="golongan_pangkat_terakhir" value="{{ old('golongan_pangkat_terakhir') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>
             </div>
         </section>

         {{-- BAGIAN 3: PENEMPATAN & JABATAN --}}
        <section class="mb-8 border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">3. Penempatan & Jabatan Saat Ini</h3>
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                 {{-- Klasis Penempatan --}}
                <div>
                    <label for="klasis_penempatan_id" class="block text-sm font-medium text-gray-700 mb-1">Klasis Penempatan <span class="italic text-gray-500">(Jika di Jemaat/Klasis)</span></label>
                    <select id="klasis_penempatan_id" name="klasis_penempatan_id"
                            class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('klasis_penempatan_id') border-red-500 @enderror">
                        <option value="">-- Tidak Ada/Sinode/Lainnya --</option>
                         @foreach ($klasisOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('klasis_penempatan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                    @error('klasis_penempatan_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                 {{-- Jemaat Penempatan --}}
                 <div>
                    <label for="jemaat_penempatan_id" class="block text-sm font-medium text-gray-700 mb-1">Jemaat Penempatan <span class="italic text-gray-500">(Jika di Jemaat)</span></label>
                    <select id="jemaat_penempatan_id" name="jemaat_penempatan_id"
                            class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('jemaat_penempatan_id') border-red-500 @enderror">
                        <option value="">-- Tidak Ada/Bukan Jemaat --</option>
                         {{-- TODO: Opsi ini sebaiknya di-load dinamis pakai JS berdasarkan Klasis --}}
                         @foreach ($jemaatOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('jemaat_penempatan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                    @error('jemaat_penempatan_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-gray-500 italic">Pilih Klasis terlebih dahulu untuk memfilter Jemaat (fitur mendatang).</p>
                </div>
                 {{-- Jabatan Saat Ini --}}
                <div>
                    <label for="jabatan_saat_ini" class="block text-sm font-medium text-gray-700 mb-1">Jabatan Fungsional/Struktural</label>
                    <input type="text" id="jabatan_saat_ini" name="jabatan_saat_ini" value="{{ old('jabatan_saat_ini') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Pendeta Jemaat, Ketua Klasis, Dosen, dll.">
                </div>
                 {{-- Tanggal Mulai Jabatan --}}
                <div>
                    <label for="tanggal_mulai_jabatan_saat_ini" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Jabatan/Penempatan</label>
                    <input type="date" id="tanggal_mulai_jabatan_saat_ini" name="tanggal_mulai_jabatan_saat_ini" value="{{ old('tanggal_mulai_jabatan_saat_ini') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>
             </div>
        </section>

        {{-- BAGIAN 4: LAIN-LAIN --}}
        <section class="mb-8 border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">4. Lain-lain</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                 {{-- Foto Pendeta --}}
                <div>
                    <label for="foto_path" class="block text-sm font-medium text-gray-700 mb-1">Foto Diri <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="file" id="foto_path" name="foto_path" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" onchange="previewImage(event, 'foto-preview')">
                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maks: 2MB.</p>
                    <img id="foto-preview" src="#" alt="Preview Foto" class="image-preview mt-2 hidden">
                    @error('foto_path') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                 {{-- Catatan --}}
                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                    <textarea id="catatan" name="catatan" rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </section>


        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            <a href="{{ route('admin.pendeta.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                Batal
            </a>
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                Simpan Data Pendeta
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