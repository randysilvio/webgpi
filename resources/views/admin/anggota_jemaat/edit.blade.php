@extends('admin.layout')

@section('title', 'Edit Anggota Jemaat')
@section('header-title', 'Edit Data: ' . $anggotaJemaat->nama_lengkap)

@section('content')
{{-- Library Tambahan: Select2 untuk Pencarian Dinamis --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
                {{-- Nomor KK & Status Keluarga --}}
                <div>
                    <label for="nomor_kk" class="block text-sm font-medium text-gray-700 mb-1">Nomor Kartu Keluarga (KK)</label>
                    <input type="text" id="nomor_kk" name="nomor_kk" value="{{ old('nomor_kk', $anggotaJemaat->nomor_kk) }}" class="form-input">
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
                {{-- Tanggal Masuk Jemaat --}}
                <div>
                    <label for="tanggal_masuk_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Tgl Masuk Jemaat Ini <span class="italic text-gray-500">(Jika Pindahan)</span></label>
                    <input type="date" id="tanggal_masuk_jemaat" name="tanggal_masuk_jemaat" value="{{ old('tanggal_masuk_jemaat', optional($anggotaJemaat->tanggal_masuk_jemaat)->format('Y-m-d')) }}" class="form-input">
                </div>
                {{-- Asal Gereja Sebelumnya --}}
                <div>
                    <label for="asal_gereja_sebelumnya" class="block text-sm font-medium text-gray-700 mb-1">Asal Gereja Sebelumnya</label>
                    <input type="text" id="asal_gereja_sebelumnya" name="asal_gereja_sebelumnya" value="{{ old('asal_gereja_sebelumnya', $anggotaJemaat->asal_gereja_sebelumnya) }}" class="form-input">
                </div>
                 {{-- Sektor & Unit --}}
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

        {{-- BAGIAN 3: HUBUNGAN KELUARGA (POHON KELUARGA) - UPDATE FASE 12 --}}
        <section class="mb-8 border-2 border-blue-100 rounded-lg p-6 bg-blue-50/30">
            <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                <i class="fas fa-sitemap mr-2"></i> 3. Hubungan Keluarga (Pohon Keluarga)
            </h3>
            <p class="text-[11px] text-blue-600 mb-4 italic italic">Pilih Ayah/Ibu dari database untuk membangun silsilah otomatis. Jika tidak ada, tetap isi nama manual di bawahnya.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Relasi Ayah --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Ayah Biologis (Cari dari Database Anggota)</label>
                        <select name="ayah_id" id="select-ayah" class="w-full"></select>
                    </div>
                    <div>
                        <label for="nama_ayah" class="block text-[10px] font-bold text-gray-400 uppercase">Nama Ayah (Manual / Jika Tidak Ada di Database)</label>
                        <input type="text" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah', $anggotaJemaat->nama_ayah) }}" class="form-input bg-white">
                    </div>
                </div>

                {{-- Relasi Ibu --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Ibu Biologis (Cari dari Database Anggota)</label>
                        <select name="ibu_id" id="select-ibu" class="w-full"></select>
                    </div>
                    <div>
                        <label for="nama_ibu" class="block text-[10px] font-bold text-gray-400 uppercase">Nama Ibu (Manual / Jika Tidak Ada di Database)</label>
                        <input type="text" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu', $anggotaJemaat->nama_ibu) }}" class="form-input bg-white">
                    </div>
                </div>
                
                {{-- Status dalam Keluarga --}}
                <div>
                    <label for="status_dalam_keluarga" class="block text-sm font-medium text-gray-700 mb-1">Status dalam Keluarga</label>
                    <select id="status_dalam_keluarga" name="status_dalam_keluarga" class="form-select">
                        <option value="" disabled {{ !old('status_dalam_keluarga', $anggotaJemaat->status_dalam_keluarga) ? 'selected' : '' }}>-- Pilih Status --</option>
                        <option value="Kepala Keluarga" {{ old('status_dalam_keluarga', $anggotaJemaat->status_dalam_keluarga) == 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                        <option value="Istri" {{ old('status_dalam_keluarga', $anggotaJemaat->status_dalam_keluarga) == 'Istri' ? 'selected' : '' }}>Istri</option>
                        <option value="Anak" {{ old('status_dalam_keluarga', $anggotaJemaat->status_dalam_keluarga) == 'Anak' ? 'selected' : '' }}>Anak</option>
                        <option value="Famili Lain" {{ old('status_dalam_keluarga', $anggotaJemaat->status_dalam_keluarga) == 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                    </select>
                </div>
            </div>
        </section>

         {{-- BAGIAN 4: SENSUS EKONOMI --}}
         <section class="mb-8 border rounded-lg p-6">
             <h3 class="text-lg font-semibold text-gray-700 mb-1">4. Gambaran Ekonomi Keluarga</h3>
             <p class="text-xs text-gray-500 mb-4 italic">Data ini digunakan untuk perencanaan program diakonia jemaat.</p>
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                 {{-- Status Pekerjaan KK --}}
                <div>
                    <label for="status_pekerjaan_kk" class="block text-sm font-medium text-gray-700 mb-1">Status Pekerjaan Utama KK</label>
                    <select id="status_pekerjaan_kk" name="status_pekerjaan_kk" class="form-select">
                        <option value="Bekerja (PNS/TNI/POLRI)" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Bekerja (PNS/TNI/POLRI)' ? 'selected' : '' }}>Bekerja (PNS/TNI/POLRI)</option>
                        <option value="Wiraswasta/Usaha Sendiri" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Wiraswasta/Usaha Sendiri' ? 'selected' : '' }}>Wiraswasta/Usaha Sendiri</option>
                        <option value="Petani/Nelayan/Peternak" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Petani/Nelayan/Peternak' ? 'selected' : '' }}>Petani/Nelayan/Peternak</option>
                        <option value="Pensiunan" {{ old('status_pekerjaan_kk', $anggotaJemaat->status_pekerjaan_kk) == 'Pensiunan' ? 'selected' : '' }}>Pensiunan</option>
                    </select>
                </div>
                 {{-- Status Kepemilikan Rumah --}}
                <div>
                    <label for="status_kepemilikan_rumah" class="block text-sm font-medium text-gray-700 mb-1">Status Kepemilikan Rumah</label>
                    <select id="status_kepemilikan_rumah" name="status_kepemilikan_rumah" class="form-select">
                        <option value="Milik Sendiri" {{ old('status_kepemilikan_rumah', $anggotaJemaat->status_kepemilikan_rumah) == 'Milik Sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                        <option value="Sewa/Kontrak" {{ old('status_kepemilikan_rumah', $anggotaJemaat->status_kepemilikan_rumah) == 'Sewa/Kontrak' ? 'selected' : '' }}>Sewa/Kontrak</option>
                        <option value="Menumpang" {{ old('status_kepemilikan_rumah', $anggotaJemaat->status_kepemilikan_rumah) == 'Menumpang' ? 'selected' : '' }}>Menumpang</option>
                    </select>
                </div>
                {{-- Perkiraan Pendapatan --}}
                <div>
                    <label for="perkiraan_pendapatan_keluarga" class="block text-sm font-medium text-gray-700 mb-1">Rentang Pendapatan Keluarga</label>
                    <select id="perkiraan_pendapatan_keluarga" name="perkiraan_pendapatan_keluarga" class="form-select">
                        <option value="Di bawah UMR" {{ old('perkiraan_pendapatan_keluarga', $anggotaJemaat->perkiraan_pendapatan_keluarga) == 'Di bawah UMR' ? 'selected' : '' }}>Di bawah UMR</option>
                        <option value="UMR - 5 Juta" {{ old('perkiraan_pendapatan_keluarga', $anggotaJemaat->perkiraan_pendapatan_keluarga) == 'UMR - 5 Juta' ? 'selected' : '' }}>UMR - Rp 5 Juta</option>
                        <option value="Di atas 5 Juta" {{ old('perkiraan_pendapatan_keluarga', $anggotaJemaat->perkiraan_pendapatan_keluarga) == 'Di atas 5 Juta' ? 'selected' : '' }}>Di atas Rp 5 Juta</option>
                    </select>
                </div>
             </div>
         </section>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            <a href="{{ route('admin.anggota-jemaat.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                Batal
            </a>
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- Scripts JQuery & Select2 --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Fungsi Inisialisasi Select2 AJAX
    function initSelect2Member(elementId, initialData) {
        $(elementId).select2({
            placeholder: 'Cari Nama Lengkap atau NIK...',
            ajax: {
                url: "{{ route('admin.anggota-jemaat.search') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data }; },
                cache: true
            }
        });

        // Jika ada data awal (saat edit), tampilkan di select2
        if (initialData) {
            var option = new Option(initialData.text, initialData.id, true, true);
            $(elementId).append(option).trigger('change');
        }
    }

    // Eksekusi Inisialisasi untuk Ayah
    @if($anggotaJemaat->ayah_id)
        initSelect2Member('#select-ayah', { 
            id: '{{ $anggotaJemaat->ayah_id }}', 
            text: '{{ $anggotaJemaat->ayah->nama_lengkap }} ({{ $anggotaJemaat->ayah->nik ?? "No NIK" }})' 
        });
    @else
        initSelect2Member('#select-ayah', null);
    @endif

    // Eksekusi Inisialisasi untuk Ibu
    @if($anggotaJemaat->ibu_id)
        initSelect2Member('#select-ibu', { 
            id: '{{ $anggotaJemaat->ibu_id }}', 
            text: '{{ $anggotaJemaat->ibu->nama_lengkap }} ({{ $anggotaJemaat->ibu->nik ?? "No NIK" }})' 
        });
    @else
        initSelect2Member('#select-ibu', null);
    @endif
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
    .error-message { margin-top: 0.25rem; font-size: 0.75rem; color: #DC2626; }
</style>
@endpush

@endsection