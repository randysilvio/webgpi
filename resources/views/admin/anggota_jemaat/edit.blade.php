@extends('admin.layout')

@section('title', 'Edit Anggota Jemaat')
@section('header-title', 'Edit Data: ' . $anggotaJemaat->nama_lengkap)

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir Edit Anggota Jemaat</h2>

    <form action="{{ route('admin.anggota-jemaat.update', $anggotaJemaat->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Method untuk update --}}

        {{-- BAGIAN 1: DATA PRIBADI & KONTAK --}}
        <section class="mb-8 border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">1. Data Pribadi & Kontak</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                {{-- Nama Lengkap --}}
                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $anggotaJemaat->nama_lengkap) }}" required class="form-input">
                    @error('nama_lengkap') <p class="error-message">{{ $message }}</p> @enderror
                </div>
                {{-- NIK --}}
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik', $anggotaJemaat->nik) }}" class="form-input" maxlength="20">
                    @error('nik') <p class="error-message">{{ $message }}</p> @enderror
                </div>
                 {{-- No Buku Induk --}}
                <div>
                    <label for="nomor_buku_induk" class="block text-sm font-medium text-gray-700 mb-1">No. Buku Induk <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="text" id="nomor_buku_induk" name="nomor_buku_induk" value="{{ old('nomor_buku_induk', $anggotaJemaat->nomor_buku_induk) }}" class="form-input" maxlength="50">
                    @error('nomor_buku_induk') <p class="error-message">{{ $message }}</p> @enderror
                </div>
                 {{-- Tempat Lahir --}}
                <div>
                    <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $anggotaJemaat->tempat_lahir) }}" class="form-input">
                </div>
                {{-- Tanggal Lahir --}}
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($anggotaJemaat->tanggal_lahir)->format('Y-m-d')) }}" class="form-input">
                    @error('tanggal_lahir') <p class="error-message">{{ $message }}</p> @enderror
                </div>
                {{-- Jenis Kelamin --}}
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-select">
                        <option value="" disabled {{ !old('jenis_kelamin', $anggotaJemaat->jenis_kelamin) ? 'selected' : '' }}>-- Pilih --</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin', $anggotaJemaat->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $anggotaJemaat->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                {{-- Golongan Darah --}}
                <div>
                    <label for="golongan_darah" class="block text-sm font-medium text-gray-700 mb-1">Gol. Darah</label>
                    <select id="golongan_darah" name="golongan_darah" class="form-select">
                         <option value="" disabled {{ !old('golongan_darah', $anggotaJemaat->golongan_darah) ? 'selected' : '' }}>-- Pilih --</option>
                         <option value="A" {{ old('golongan_darah', $anggotaJemaat->golongan_darah) == 'A' ? 'selected' : '' }}>A</option>
                         <option value="B" {{ old('golongan_darah', $anggotaJemaat->golongan_darah) == 'B' ? 'selected' : '' }}>B</option>
                         <option value="AB" {{ old('golongan_darah', $anggotaJemaat->golongan_darah) == 'AB' ? 'selected' : '' }}>AB</option>
                         <option value="O" {{ old('golongan_darah', $anggotaJemaat->golongan_darah) == 'O' ? 'selected' : '' }}>O</option>
                         <option value="Tidak Tahu" {{ old('golongan_darah', $anggotaJemaat->golongan_darah) == 'Tidak Tahu' ? 'selected' : '' }}>Tidak Tahu</option>
                    </select>
                </div>
                 {{-- Status Pernikahan --}}
                <div>
                    <label for="status_pernikahan" class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                    <select id="status_pernikahan" name="status_pernikahan" class="form-select">
                        <option value="" disabled {{ !old('status_pernikahan', $anggotaJemaat->status_pernikahan) ? 'selected' : '' }}>-- Pilih --</option>
                        <option value="Belum Menikah" {{ old('status_pernikahan', $anggotaJemaat->status_pernikahan) == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                        <option value="Menikah" {{ old('status_pernikahan', $anggotaJemaat->status_pernikahan) == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                        <option value="Cerai Hidup" {{ old('status_pernikahan', $anggotaJemaat->status_pernikahan) == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                        <option value="Cerai Mati" {{ old('status_pernikahan', $anggotaJemaat->status_pernikahan) == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                </div>
                 {{-- No Telepon --}}
                <div>
                    <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon/HP</label>
                    <input type="tel" id="telepon" name="telepon" value="{{ old('telepon', $anggotaJemaat->telepon) }}" class="form-input" placeholder="08...">
                </div>
                 {{-- Alamat --}}
                <div class="md:col-span-2 lg:col-span-3">
                    <label for="alamat_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" class="form-textarea">{{ old('alamat_lengkap', $anggotaJemaat->alamat_lengkap) }}</textarea>
                </div>
                 {{-- Email --}}
                 <div class="md:col-span-1">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $anggotaJemaat->email) }}" class="form-input">
                    @error('email') <p class="error-message">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- BAGIAN 2: DATA KEANGGOTAAN GEREJA --}}
        <section class="mb-8 border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">2. Data Keanggotaan Gereja</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                {{-- Jemaat --}}
                <div>
                    <label for="jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Jemaat Terdaftar <span class="text-red-600">*</span></label>
                    <select id="jemaat_id" name="jemaat_id" required {{ Auth::check() && Auth::user()->hasRole(['Admin Jemaat', 'Admin Klasis']) ? 'disabled' : '' }}
                            class="form-select {{ Auth::check() && Auth::user()->hasRole(['Admin Jemaat', 'Admin Klasis']) ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                        <option value="" disabled>-- Pilih Jemaat --</option>
                        @foreach ($jemaatOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('jemaat_id', $anggotaJemaat->jemaat_id) == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                    {{-- Hidden input jika disabled --}}
                     @if(Auth::check() && Auth::user()->hasRole(['Admin Jemaat', 'Admin Klasis']))
                        <input type="hidden" name="jemaat_id" value="{{ $anggotaJemaat->jemaat_id }}">
                        <p class="mt-1 text-xs text-gray-500 italic">Hanya Super Admin atau Admin Bidang 3 yang dapat memindahkan Jemaat.</p>
                     @endif
                    @error('jemaat_id') <p class="error-message">{{ $message }}</p> @enderror
                </div>
                 {{-- Status Keanggotaan --}}
                <div>
                    <label for="status_keanggotaan" class="block text-sm font-medium text-gray-700 mb-1">Status Keanggotaan <span class="text-red-600">*</span></label>
                    <select id="status_keanggotaan" name="status_keanggotaan" required class="form-select">
                        <option value="Aktif" {{ old('status_keanggotaan', $anggotaJemaat->status_keanggotaan) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ old('status_keanggotaan', $anggotaJemaat->status_keanggotaan) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="Pindah" {{ old('status_keanggotaan', $anggotaJemaat->status_keanggotaan) == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                        <option value="Meninggal" {{ old('status_keanggotaan', $anggotaJemaat->status_keanggotaan) == 'Meninggal' ? 'selected' : '' }}>Meninggal</option>
                    </select>
                    @error('status_keanggotaan') <p class="error-message">{{ $message }}</p> @enderror
                </div>
                {{-- Tanggal Baptis --}}
                <div>
                    <label for="tanggal_baptis" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Baptis</label>
                    <input type="date" id="tanggal_baptis" name="tanggal_baptis" value="{{ old('tanggal_baptis', optional($anggotaJemaat->tanggal_baptis)->format('Y-m-d')) }}" class="form-input">
                </div>
                {{-- Tempat Baptis --}}
                <div>
                    <label for="tempat_baptis" class="block text-sm font-medium text-gray-700 mb-1">Tempat Baptis</label>
                    <input type="text" id="tempat_baptis" name="tempat_baptis" value="{{ old('tempat_baptis', $anggotaJemaat->tempat_baptis) }}" class="form-input" placeholder="Nama Gereja/Jemaat">
                </div>
                {{-- Tanggal Sidi --}}
                <div>
                    <label for="tanggal_sidi" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sidi</label>
                    <input type="date" id="tanggal_sidi" name="tanggal_sidi" value="{{ old('tanggal_sidi', optional($anggotaJemaat->tanggal_sidi)->format('Y-m-d')) }}" class="form-input">
                </div>
                 {{-- Tempat Sidi --}}
                <div>
                    <label for="tempat_sidi" class="block text-sm font-medium text-gray-700 mb-1">Tempat Sidi</label>
                    <input type="text" id="tempat_sidi" name="tempat_sidi" value="{{ old('tempat_sidi', $anggotaJemaat->tempat_sidi) }}" class="form-input" placeholder="Nama Gereja/Jemaat">
                </div>
                {{-- Tanggal Masuk Jemaat (Jika Pindahan) --}}
                <div>
                    <label for="tanggal_masuk_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Tgl Masuk Jemaat Ini <span class="italic text-gray-500">(Jika Pindahan)</span></label>
                    <input type="date" id="tanggal_masuk_jemaat" name="tanggal_masuk_jemaat" value="{{ old('tanggal_masuk_jemaat', optional($anggotaJemaat->tanggal_masuk_jemaat)->format('Y-m-d')) }}" class="form-input">
                </div>
                {{-- Asal Gereja Sebelumnya --}}
                <div>
                    <label for="asal_gereja_sebelumnya" class="block text-sm font-medium text-gray-700 mb-1">Asal Gereja Sebelumnya <span class="italic text-gray-500">(Jika Pindahan)</span></label>
                    <input type="text" id="asal_gereja_sebelumnya" name="asal_gereja_sebelumnya" value="{{ old('asal_gereja_sebelumnya', $anggotaJemaat->asal_gereja_sebelumnya) }}" class="form-input">
                </div>
                 {{-- No Atestasi --}}
                <div>
                    <label for="nomor_atestasi" class="block text-sm font-medium text-gray-700 mb-1">No. Atestasi <span class="italic text-gray-500">(Jika Pindahan)</span></label>
                    <input type="text" id="nomor_atestasi" name="nomor_atestasi" value="{{ old('nomor_atestasi', $anggotaJemaat->nomor_atestasi) }}" class="form-input">
                </div>
                {{-- Sektor & Unit (Opsional) --}}
                 <div>
                    <label for="sektor_pelayanan" class="block text-sm font-medium text-gray-700 mb-1">Sektor Pelayanan</label>
                    <input type="text" id="sektor_pelayanan" name="sektor_pelayanan" value="{{ old('sektor_pelayanan', $anggotaJemaat->sektor_pelayanan) }}" class="form-input">
                </div>
                 <div>
                    <label for="unit_pelayanan" class="block text-sm font-medium text-gray-700 mb-1">Unit Pelayanan</label>
                    <input type="text" id="unit_pelayanan" name="unit_pelayanan" value="{{ old('unit_pelayanan', $anggotaJemaat->unit_pelayanan) }}" class="form-input">
                </div>
            </div>
        </section>

         {{-- BAGIAN 3: SENSUS EKONOMI (SEDERHANA) --}}
         <section class="mb-8 border rounded-lg p-6">
             <h3 class="text-lg font-semibold text-gray-700 mb-1">3. Gambaran Ekonomi Keluarga</h3>
             <p class="text-xs text-gray-500 mb-4 italic">Data ini bersifat rahasia dan digunakan secara anonim untuk perencanaan program diakonia.</p>
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                 {{-- Status Pekerjaan KK --}}
                <div>
                    <label for="status_pekerjaan_kk" class="block text-sm font-medium text-gray-700 mb-1">Status Pekerjaan Utama Kepala Keluarga</label>
                    <select id="status_pekerjaan_kk" name="status_pekerjaan_kk" class="form-select">
                        <option value="" disabled {{ !old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) ? 'selected' : '' }}>-- Pilih Status --</option>
                        <option value="Bekerja (PNS/TNI/POLRI)" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Bekerja (PNS/TNI/POLRI)' ? 'selected' : '' }}>Bekerja (PNS/TNI/POLRI)</option>
                        <option value="Bekerja (Swasta/BUMN/Honorer)" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Bekerja (Swasta/BUMN/Honorer)' ? 'selected' : '' }}>Bekerja (Swasta/BUMN/Honorer)</option>
                        <option value="Wiraswasta/Usaha Sendiri" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Wiraswasta/Usaha Sendiri' ? 'selected' : '' }}>Wiraswasta/Usaha Sendiri</option>
                        <option value="Petani/Nelayan/Peternak" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Petani/Nelayan/Peternak' ? 'selected' : '' }}>Petani/Nelayan/Peternak</option>
                        <option value="Buruh Harian Lepas" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Buruh Harian Lepas' ? 'selected' : '' }}>Buruh Harian Lepas</option>
                        <option value="Tidak Bekerja" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja (Termasuk IRT)</option>
                        <option value="Pelajar/Mahasiswa" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Pelajar/Mahasiswa' ? 'selected' : '' }}>Pelajar/Mahasiswa</option>
                        <option value="Pensiunan" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Pensiunan' ? 'selected' : '' }}>Pensiunan</option>
                        <option value="Lainnya" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                 {{-- Status Kepemilikan Rumah --}}
                <div>
                    <label for="status_kepemilikan_rumah" class="block text-sm font-medium text-gray-700 mb-1">Status Kepemilikan Rumah</label>
                    <select id="status_kepemilikan_rumah" name="status_kepemilikan_rumah" class="form-select">
                        <option value="" disabled {{ !old('status_kepemilikan_rumah', $anggotaJemaat->status_kepemilikan_rumah) ? 'selected' : '' }}>-- Pilih Status --</option>
                        <option value="Milik Sendiri" {{ old('status_kepemilikan_rumah', $anggotaJemaat->status_kepemilikan_rumah) == 'Milik Sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                        <option value="Sewa/Kontrak" {{ old('status_kepemilikan_rumah', $anggotaJemaat->status_kepemilikan_rumah) == 'Sewa/Kontrak' ? 'selected' : '' }}>Sewa/Kontrak</option>
                        <option value="Menumpang" {{ old('status_kepemilikan_rumah', $anggotaJemaat->status_kepemilikan_rumah) == 'Menumpang' ? 'selected' : '' }}>Menumpang</option>
                        <option value="Rumah Dinas" {{ old('status_kepemilikan_rumah', $anggotaJemaat->status_kepemilikan_rumah) == 'Rumah Dinas' ? 'selected' : '' }}>Rumah Dinas</option>
                        <option value="Bebas Sewa" {{ old('status_kepemilikan_rumah', $anggotaJemaat->status_kepemilikan_rumah) == 'Bebas Sewa' ? 'selected' : '' }}>Bebas Sewa</option>
                        <option value="Lainnya" {{ old('status_kepemilikan_rumah', $anggotaJemaat->status_kepemilikan_rumah) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                {{-- Perkiraan Pendapatan (Opsional) --}}
                <div>
                    <label for="perkiraan_pendapatan_keluarga" class="block text-sm font-medium text-gray-700 mb-1">Perkiraan Pendapatan Keluarga/Bulan <span class="italic text-gray-500">(Opsional)</span></label>
                    <select id="perkiraan_pendapatan_keluarga" name="perkiraan_pendapatan_keluarga" class="form-select">
                        <option value="" {{ !old('perkiraan_pendapatan_keluarga', $anggotaJemaat->perkiraan_pendapatan_keluarga) ? 'selected' : '' }}>-- Pilih Rentang --</option>
                        <option value="Di bawah UMR" {{ old('perkiraan_pendapatan_keluarga', $anggotaJemaat->perkiraan_pendapatan_keluarga) == 'Di bawah UMR' ? 'selected' : '' }}>Di bawah UMR</option>
                        <option value="UMR - 5 Juta" {{ old('perkiraan_pendapatan_keluarga', $anggotaJemaat->perkiraan_pendapatan_keluarga) == 'UMR - 5 Juta' ? 'selected' : '' }}>UMR - Rp 5 Juta</option>
                        <option value="5 - 10 Juta" {{ old('perkiraan_pendapatan_keluarga', $anggotaJemaat->perkiraan_pendapatan_keluarga) == '5 - 10 Juta' ? 'selected' : '' }}>Rp 5 Juta - Rp 10 Juta</option>
                        <option value="Di atas 10 Juta" {{ old('perkiraan_pendapatan_keluarga', $anggotaJemaat->perkiraan_pendapatan_keluarga) == 'Di atas 10 Juta' ? 'selected' : '' }}>Di atas Rp 10 Juta</option>
                        <option value="Tidak menjawab" {{ old('perkiraan_pendapatan_keluarga', $anggotaJemaat->perkiraan_pendapatan_keluarga) == 'Tidak menjawab' ? 'selected' : '' }}>Tidak ingin menjawab</option>
                    </select>
                </div>
             </div>
         </section>

         {{-- BAGIAN 4: LAIN-LAIN (jika ada) --}}
         {{-- ... (Tambahkan section lain jika perlu, misal Keterlibatan Pelayanan, dll.) ... --}}


        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            <a href="{{ route('admin.anggota-jemaat.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                Batal
            </a>
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- Helper CSS untuk form (sama seperti di create.blade.php) --}}
@push('styles')
<style>
    .form-input, .form-select, .form-textarea {
        display: block;
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem; /* text-sm */
        line-height: 1.25rem;
        border: 1px solid #D1D5DB; /* border-gray-300 */
        border-radius: 0.375rem; /* rounded-md */
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: #1e40af; /* primary color */
        outline: 0;
        box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.2); /* ring-primary focus:ring-2 */
    }
    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
        -webkit-appearance: none;
           -moz-appearance: none;
                appearance: none;
    }
     select:disabled, select[readonly] {
         background-color: #F3F4F6; /* bg-gray-100 */
         cursor: not-allowed;
         color: #6B7280; /* text-gray-500 */
         /* Hapus background image arrow jika disabled */
         background-image: none;
     }
    .error-message {
        margin-top: 0.25rem;
        font-size: 0.75rem; /* text-xs */
        color: #DC2626; /* text-red-600 */
    }
    input.border-red-500, select.border-red-500, textarea.border-red-500 {
        border-color: #EF4444; /* border-red-500 */
    }
    input.border-red-500:focus, select.border-red-500:focus, textarea.border-red-500:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
    }
</style>
@endpush

@endsection