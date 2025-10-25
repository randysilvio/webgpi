@extends('admin.layout')

    @section('title', 'Tambah Anggota Jemaat')
    @section('header-title', 'Tambah Data Anggota Jemaat Baru')

    @section('content')
    <div class="bg-white shadow rounded-lg p-6 md:p-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir Anggota Jemaat Baru</h2>

        <form action="{{ route('admin.anggota-jemaat.store') }}" method="POST">
            @csrf

            {{-- BAGIAN 1: DATA PRIBADI & KONTAK --}}
            <section class="mb-8 border rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">1. Data Pribadi & Kontak</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
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
                     {{-- No Buku Induk --}}
                    <div>
                        <label for="nomor_buku_induk" class="block text-sm font-medium text-gray-700 mb-1">No. Buku Induk <span class="italic text-gray-500">(Opsional)</span></label>
                        <input type="text" id="nomor_buku_induk" name="nomor_buku_induk" value="{{ old('nomor_buku_induk') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nomor_buku_induk') border-red-500 @enderror" maxlength="50">
                        @error('nomor_buku_induk') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Tempat Lahir --}}
                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    {{-- Tanggal Lahir --}}
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('tanggal_lahir') border-red-500 @enderror" placeholder="dd/mm/yyyy">
                        @error('tanggal_lahir') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- Jenis Kelamin --}}
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>-- Pilih --</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    {{-- Golongan Darah --}}
                    <div>
                        <label for="golongan_darah" class="block text-sm font-medium text-gray-700 mb-1">Gol. Darah</label>
                        <select id="golongan_darah" name="golongan_darah"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                             <option value="" disabled {{ old('golongan_darah') ? '' : 'selected' }}>-- Pilih --</option>
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
                        <select id="status_pernikahan" name="status_pernikahan"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="" disabled {{ old('status_pernikahan') ? '' : 'selected' }}>-- Pilih --</option>
                            <option value="Belum Menikah" {{ old('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                            <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                            <option value="Cerai Hidup" {{ old('status_pernikahan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ old('status_pernikahan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                        </select>
                    </div>
                     {{-- No Telepon --}}
                    <div>
                        <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon/HP</label>
                        <input type="tel" id="telepon" name="telepon" value="{{ old('telepon') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="08...">
                    </div>
                     {{-- Alamat --}}
                    <div class="md:col-span-2 lg:col-span-3">
                        <label for="alamat_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">{{ old('alamat_lengkap') }}</textarea>
                    </div>
                     {{-- Email --}}
                     <div class="md:col-span-1">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="italic text-gray-500">(Opsional)</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('email') border-red-500 @enderror">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
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
                        <select id="jemaat_id" name="jemaat_id" required {{ Auth::check() && Auth::user()->hasRole('Admin Jemaat') ? 'readonly' : '' }}
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm {{ Auth::check() && Auth::user()->hasRole('Admin Jemaat') ? 'bg-gray-100 cursor-not-allowed' : '' }} @error('jemaat_id') border-red-500 @enderror">
                            <option value="" disabled {{ !old('jemaat_id') ? 'selected' : '' }}>-- Pilih Jemaat --</option>
                            @foreach ($jemaatOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('jemaat_id', (Auth::check() && Auth::user()->hasRole('Admin Jemaat') ? Auth::user()->jemaat_id : null)) == $id ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('jemaat_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Status Keanggotaan --}}
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
                    {{-- Tanggal Baptis --}}
                    <div>
                        <label for="tanggal_baptis" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Baptis</label>
                        <input type="date" id="tanggal_baptis" name="tanggal_baptis" value="{{ old('tanggal_baptis') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    {{-- Tempat Baptis --}}
                    <div>
                        <label for="tempat_baptis" class="block text-sm font-medium text-gray-700 mb-1">Tempat Baptis</label>
                        <input type="text" id="tempat_baptis" name="tempat_baptis" value="{{ old('tempat_baptis') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Nama Gereja/Jemaat">
                    </div>
                    {{-- Tanggal Sidi --}}
                    <div>
                        <label for="tanggal_sidi" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sidi</label>
                        <input type="date" id="tanggal_sidi" name="tanggal_sidi" value="{{ old('tanggal_sidi') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                     {{-- Tempat Sidi --}}
                    <div>
                        <label for="tempat_sidi" class="block text-sm font-medium text-gray-700 mb-1">Tempat Sidi</label>
                        <input type="text" id="tempat_sidi" name="tempat_sidi" value="{{ old('tempat_sidi') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Nama Gereja/Jemaat">
                    </div>
                    {{-- Tanggal Masuk Jemaat (Jika Pindahan) --}}
                    <div>
                        <label for="tanggal_masuk_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Tgl Masuk Jemaat Ini <span class="italic text-gray-500">(Jika Pindahan)</span></label>
                        <input type="date" id="tanggal_masuk_jemaat" name="tanggal_masuk_jemaat" value="{{ old('tanggal_masuk_jemaat') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    {{-- Asal Gereja Sebelumnya --}}
                    <div>
                        <label for="asal_gereja_sebelumnya" class="block text-sm font-medium text-gray-700 mb-1">Asal Gereja Sebelumnya <span class="italic text-gray-500">(Jika Pindahan)</span></label>
                        <input type="text" id="asal_gereja_sebelumnya" name="asal_gereja_sebelumnya" value="{{ old('asal_gereja_sebelumnya') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                     {{-- No Atestasi --}}
                    <div>
                        <label for="nomor_atestasi" class="block text-sm font-medium text-gray-700 mb-1">No. Atestasi <span class="italic text-gray-500">(Jika Pindahan)</span></label>
                        <input type="text" id="nomor_atestasi" name="nomor_atestasi" value="{{ old('nomor_atestasi') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    {{-- Sektor & Unit (Opsional) --}}
                     <div>
                        <label for="sektor_pelayanan" class="block text-sm font-medium text-gray-700 mb-1">Sektor Pelayanan</label>
                        <input type="text" id="sektor_pelayanan" name="sektor_pelayanan" value="{{ old('sektor_pelayanan') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                     <div>
                        <label for="unit_pelayanan" class="block text-sm font-medium text-gray-700 mb-1">Unit Pelayanan</label>
                        <input type="text" id="unit_pelayanan" name="unit_pelayanan" value="{{ old('unit_pelayanan') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
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
                        <select id="status_pekerjaan_kk" name="status_pekerjaan_kk"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="" disabled selected>-- Pilih Status --</option>
                            <option value="Bekerja (PNS/TNI/POLRI)" {{ old('status_pekerjaan_kk') == 'Bekerja (PNS/TNI/POLRI)' ? 'selected' : '' }}>Bekerja (PNS/TNI/POLRI)</option>
                            <option value="Bekerja (Swasta/BUMN/Honorer)" {{ old('status_pekerjaan_kk') == 'Bekerja (Swasta/BUMN/Honorer)' ? 'selected' : '' }}>Bekerja (Swasta/BUMN/Honorer)</option>
                            <option value="Wiraswasta/Usaha Sendiri" {{ old('status_pekerjaan_kk') == 'Wiraswasta/Usaha Sendiri' ? 'selected' : '' }}>Wiraswasta/Usaha Sendiri</option>
                            <option value="Petani/Nelayan/Peternak" {{ old('status_pekerjaan_kk') == 'Petani/Nelayan/Peternak' ? 'selected' : '' }}>Petani/Nelayan/Peternak</option>
                            <option value="Buruh Harian Lepas" {{ old('status_pekerjaan_kk') == 'Buruh Harian Lepas' ? 'selected' : '' }}>Buruh Harian Lepas</option>
                            <option value="Tidak Bekerja" {{ old('status_pekerjaan_kk') == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja (Termasuk IRT)</option>
                            <option value="Pelajar/Mahasiswa" {{ old('status_pekerjaan_kk') == 'Pelajar/Mahasiswa' ? 'selected' : '' }}>Pelajar/Mahasiswa</option>
                            <option value="Pensiunan" {{ old('status_pekerjaan_kk') == 'Pensiunan' ? 'selected' : '' }}>Pensiunan</option>
                            <option value="Lainnya" {{ old('status_pekerjaan_kk') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                     {{-- Status Kepemilikan Rumah --}}
                    <div>
                        <label for="status_kepemilikan_rumah" class="block text-sm font-medium text-gray-700 mb-1">Status Kepemilikan Rumah</label>
                        <select id="status_kepemilikan_rumah" name="status_kepemilikan_rumah"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="" disabled selected>-- Pilih Status --</option>
                            <option value="Milik Sendiri" {{ old('status_kepemilikan_rumah') == 'Milik Sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                            <option value="Sewa/Kontrak" {{ old('status_kepemilikan_rumah') == 'Sewa/Kontrak' ? 'selected' : '' }}>Sewa/Kontrak</option>
                            <option value="Menumpang" {{ old('status_kepemilikan_rumah') == 'Menumpang' ? 'selected' : '' }}>Menumpang</option>
                            <option value="Rumah Dinas" {{ old('status_kepemilikan_rumah') == 'Rumah Dinas' ? 'selected' : '' }}>Rumah Dinas</option>
                            <option value="Bebas Sewa" {{ old('status_kepemilikan_rumah') == 'Bebas Sewa' ? 'selected' : '' }}>Bebas Sewa</option>
                            <option value="Lainnya" {{ old('status_kepemilikan_rumah') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    {{-- Perkiraan Pendapatan (Opsional) --}}
                    <div>
                        <label for="perkiraan_pendapatan_keluarga" class="block text-sm font-medium text-gray-700 mb-1">Perkiraan Pendapatan Keluarga/Bulan <span class="italic text-gray-500">(Opsional)</span></label>
                        <select id="perkiraan_pendapatan_keluarga" name="perkiraan_pendapatan_keluarga"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="" selected>-- Pilih Rentang --</option>
                            <option value="Di bawah UMR" {{ old('perkiraan_pendapatan_keluarga') == 'Di bawah UMR' ? 'selected' : '' }}>Di bawah UMR</option>
                            <option value="UMR - 5 Juta" {{ old('perkiraan_pendapatan_keluarga') == 'UMR - 5 Juta' ? 'selected' : '' }}>UMR - Rp 5 Juta</option>
                            <option value="5 - 10 Juta" {{ old('perkiraan_pendapatan_keluarga') == '5 - 10 Juta' ? 'selected' : '' }}>Rp 5 Juta - Rp 10 Juta</option>
                            <option value="Di atas 10 Juta" {{ old('perkiraan_pendapatan_keluarga') == 'Di atas 10 Juta' ? 'selected' : '' }}>Di atas Rp 10 Juta</option>
                            <option value="Tidak menjawab" {{ old('perkiraan_pendapatan_keluarga') == 'Tidak menjawab' ? 'selected' : '' }}>Tidak ingin menjawab</option>
                        </select>
                    </div>
                 </div>
             </section>

            {{-- BAGIAN 4: LAIN-LAIN (jika ada) --}}
            {{-- ... --}}

            {{-- Tombol Aksi --}}
            <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
                <a href="{{ route('admin.anggota-jemaat.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                    Batal
                </a>
                <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                    Simpan Anggota
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
        /* Style tambahan untuk border merah pada error */
        input.border-red-500, select.border-red-500, textarea.border-red-500 {
             border-color: #EF4444 !important;
        }
        input.border-red-500:focus, select.border-red-500:focus, textarea.border-red-500:focus {
             box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
        }
    </style>
    @endpush

@endsection