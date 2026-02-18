@extends('layouts.app')

@section('title', 'Tambah Anggota')

@section('content')
    {{-- SELECT2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <x-admin-form 
        title="Registrasi Anggota Baru" 
        action="{{ route('admin.anggota-jemaat.store') }}" 
        back-route="{{ route('admin.anggota-jemaat.index') }}"
    >
        {{-- SECTION 1: DATA PRIBADI --}}
        <div class="space-y-4 mb-8">
            <h3 class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">I. Data Pribadi & Kontak</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <x-form-input label="Nama Lengkap" name="nama_lengkap" required />
                <x-form-input label="NIK (KTP)" name="nik" />
                <x-form-input label="No. Buku Induk" name="nomor_buku_induk" />
                
                <x-form-input label="Tempat Lahir" name="tempat_lahir" />
                <x-form-input type="date" label="Tanggal Lahir" name="tanggal_lahir" />
                
                <x-form-select label="Jenis Kelamin" name="jenis_kelamin">
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </x-form-select>

                <x-form-select label="Gol. Darah" name="golongan_darah">
                    <option value="-">-</option>
                    <option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option>
                </x-form-select>

                {{-- UPDATE RENSTRA: Disabilitas --}}
                <x-form-select label="Disabilitas" name="disabilitas">
                    <option value="Tidak Ada">Tidak Ada</option>
                    <option value="Tuna Netra">Tuna Netra</option>
                    <option value="Tuna Daksa">Tuna Daksa</option>
                    <option value="Tuna Rungu/Wicara">Tuna Rungu/Wicara</option>
                    <option value="Lainnya">Lainnya</option>
                </x-form-select>

                <x-form-select label="Status Pernikahan" name="status_pernikahan">
                    <option value="Belum Menikah">Belum Menikah</option>
                    <option value="Menikah">Menikah</option>
                    <option value="Cerai Hidup">Cerai Hidup</option>
                    <option value="Cerai Mati">Cerai Mati</option>
                </x-form-select>

                <x-form-input label="No. HP / WA" name="telepon" />
                
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" rows="2" class="w-full border-slate-300 rounded text-sm">{{ $prefillData['alamat_lengkap'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        {{-- SECTION 2: KEANGGOTAAN GEREJA --}}
        <div class="space-y-4 mb-8">
            <h3 class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">II. Keanggotaan Gereja</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <x-form-select label="Jemaat" name="jemaat_id" required>
                    <option value="">-- Pilih Jemaat --</option>
                    @foreach ($jemaatOptions as $id => $nama)
                        <option value="{{ $id }}" {{ ($prefillData['jemaat_id'] ?? '') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </x-form-select>

                <x-form-select label="Status Keanggotaan" name="status_keanggotaan" required>
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                    <option value="Pindah">Pindah</option>
                    <option value="Meninggal">Meninggal</option>
                </x-form-select>

                <x-form-input label="Nomor KK" name="nomor_kk" value="{{ $prefillData['nomor_kk'] ?? '' }}" />
                <x-form-input label="Sektor Pelayanan" name="sektor_pelayanan" value="{{ $prefillData['sektor_pelayanan'] ?? '' }}" />
                <x-form-input label="Unit Pelayanan" name="unit_pelayanan" value="{{ $prefillData['unit_pelayanan'] ?? '' }}" />

                <x-form-input type="date" label="Tanggal Baptis" name="tanggal_baptis" />
                <x-form-input label="Tempat Baptis" name="tempat_baptis" />
                
                <x-form-input type="date" label="Tanggal Sidi" name="tanggal_sidi" />
                <x-form-input label="Tempat Sidi" name="tempat_sidi" />
            </div>
        </div>

        {{-- SECTION 3: HUBUNGAN KELUARGA --}}
        <div class="bg-blue-50 p-5 rounded border border-blue-100 mb-8">
            <h3 class="text-xs font-bold text-blue-800 border-b border-blue-200 pb-2 mb-4 uppercase tracking-wide">III. Pohon Keluarga</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ayah (Database)</label>
                    <select name="ayah_id" id="select-ayah" class="w-full"></select>
                    <input type="text" name="nama_ayah" class="w-full mt-2 border-slate-300 rounded text-xs" placeholder="Atau ketik nama manual...">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ibu (Database)</label>
                    <select name="ibu_id" id="select-ibu" class="w-full"></select>
                    <input type="text" name="nama_ibu" class="w-full mt-2 border-slate-300 rounded text-xs" placeholder="Atau ketik nama manual...">
                </div>
                <div class="md:col-span-2">
                    <x-form-select label="Status dalam Keluarga" name="status_dalam_keluarga">
                        <option value="Kepala Keluarga">Kepala Keluarga</option>
                        <option value="Istri">Istri</option>
                        <option value="Anak">Anak</option>
                        <option value="Famili Lain">Famili Lain</option>
                    </x-form-select>
                </div>
            </div>
        </div>

        {{-- SECTION 4: ANALISIS RENSTRA (UPDATE MINOR) --}}
        <div class="bg-slate-50 p-5 rounded border border-slate-200 mb-8">
            <h3 class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">IV. Analisis Kesejahteraan & Digital (Renstra)</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-4">
                <x-form-select label="Kondisi Rumah" name="kondisi_rumah">
                    <option value="Permanen">Permanen</option>
                    <option value="Semi-Permanen">Semi-Permanen</option>
                    <option value="Darurat/Kayu">Darurat/Kayu</option>
                </x-form-select>
                
                <x-form-select label="Status Rumah" name="status_kepemilikan_rumah">
                    <option value="Milik Sendiri">Milik Sendiri</option>
                    <option value="Sewa">Sewa/Kontrak</option>
                    <option value="Menumpang">Menumpang</option>
                    <option value="Dinas">Dinas</option>
                </x-form-select>

                <x-form-select label="Rentang Pengeluaran" name="rentang_pengeluaran">
                    <option value="< 1jt">< 1 Juta</option>
                    <option value="1jt - 3jt">1 - 3 Juta</option>
                    <option value="> 3jt">> 3 Juta</option>
                </x-form-select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-4">
                <x-form-input label="Pekerjaan Utama" name="pekerjaan_utama" />
                
                <x-form-select label="Smartphone" name="punya_smartphone">
                    <option value="0">Tidak Memiliki</option>
                    <option value="1">Ya, Memiliki</option>
                </x-form-select>

                <x-form-select label="Akses Internet" name="akses_internet">
                    <option value="0">Tidak Ada</option>
                    <option value="1">Ya, Ada (Wifi/Data)</option>
                </x-form-select>
            </div>

            {{-- Aset Ekonomi (Checkbox) --}}
            <div class="mt-4 p-3 bg-white rounded border border-slate-200">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-3">Potensi Ekonomi & Aset</label>
                <div class="flex flex-wrap gap-4">
                    @foreach(['Perkebunan', 'Peternakan', 'Perikanan', 'Kehutanan', 'Usaha Mikro', 'Transportasi'] as $aset)
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="aset_ekonomi[]" value="{{ $aset }}" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-slate-600">{{ $aset }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tombol Khusus --}}
        <div class="flex justify-end pt-6">
            <button type="submit" name="save_and_add_another" value="1" class="mr-3 px-4 py-2 bg-green-600 text-white text-xs font-bold uppercase rounded hover:bg-green-700">
                Simpan & Tambah Keluarga
            </button>
        </div>

    </x-admin-form>

    {{-- SCRIPTS SELECT2 --}}
    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            function initSelect2(id) {
                $(id).select2({
                    placeholder: 'Cari Nama...',
                    ajax: {
                        url: "{{ route('admin.anggota-jemaat.search') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) { return { q: params.term }; },
                        processResults: function (data) { return { results: data }; },
                        cache: true
                    }
                });
            }
            initSelect2('#select-ayah');
            initSelect2('#select-ibu');
        });
    </script>
    @endpush
    @push('styles')
    <style>.select2-container .select2-selection--single { height: 38px; border-color: #cbd5e1; }</style>
    @endpush
@endsection