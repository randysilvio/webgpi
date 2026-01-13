@extends('admin.layout')

@section('title', 'Edit Klasis')
@section('header-title', 'Edit Data: ' . $klasis->nama_klasis)

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir Edit Klasis</h2>

    <form action="{{ route('admin.klasis.update', $klasis->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            {{-- KOLOM KIRI: Identitas Utama --}}
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-700 mb-3 border-b pb-1">Identitas Klasis</h3>
                
                {{-- Nama & Kode --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_klasis" class="block text-sm font-medium text-gray-700 mb-1">Nama Klasis <span class="text-red-600">*</span></label>
                        <input type="text" id="nama_klasis" name="nama_klasis" value="{{ old('nama_klasis', $klasis->nama_klasis) }}" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    <div>
                        <label for="kode_klasis" class="block text-sm font-medium text-gray-700 mb-1">Kode Klasis <span class="text-red-600">*</span></label>
                        <input type="text" id="kode_klasis" name="kode_klasis" value="{{ old('kode_klasis', $klasis->kode_klasis) }}" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                </div>

                {{-- Pusat Klasis & SK --}}
                <div>
                    <label for="pusat_klasis" class="block text-sm font-medium text-gray-700 mb-1">Pusat Klasis (Kota/Wilayah)</label>
                    <input type="text" id="pusat_klasis" name="pusat_klasis" value="{{ old('pusat_klasis', $klasis->pusat_klasis) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                </div>
                
                <div>
                    <label for="nomor_sk_pembentukan" class="block text-sm font-medium text-gray-700 mb-1">Nomor SK Pembentukan</label>
                    <input type="text" id="nomor_sk_pembentukan" name="nomor_sk_pembentukan" value="{{ old('nomor_sk_pembentukan', $klasis->nomor_sk_pembentukan) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                </div>

                {{-- Tanggal & Induk --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_pembentukan" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembentukan</label>
                        <input type="date" id="tanggal_pembentukan" name="tanggal_pembentukan" value="{{ old('tanggal_pembentukan', $klasis->tanggal_pembentukan?->format('Y-m-d')) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    <div>
                        <label for="klasis_induk" class="block text-sm font-medium text-gray-700 mb-1">Klasis Induk (Jika ada)</label>
                        <input type="text" id="klasis_induk" name="klasis_induk" value="{{ old('klasis_induk', $klasis->klasis_induk) }}" placeholder="Asal pemekaran" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                </div>

                {{-- Ketua MPK --}}
                <div>
                    <label for="ketua_mpk_pendeta_id" class="block text-sm font-medium text-gray-700 mb-1">Ketua MPK (Pegawai/Pendeta)</label>
                    <select name="ketua_mpk_pendeta_id" id="ketua_mpk_pendeta_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                        <option value="">-- Pilih Ketua MPK --</option>
                        @foreach($pendetaOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('ketua_mpk_pendeta_id', $klasis->ketua_mpk_pendeta_id) == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- KOLOM KANAN: Kontak & Detail --}}
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-700 mb-3 border-b pb-1">Kontak & Wilayah</h3>

                {{-- Kontak --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="telepon_kantor" class="block text-sm font-medium text-gray-700 mb-1">Telepon Kantor</label>
                        <input type="text" id="telepon_kantor" name="telepon_kantor" value="{{ old('telepon_kantor', $klasis->telepon_kantor) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    <div>
                        <label for="email_klasis" class="block text-sm font-medium text-gray-700 mb-1">Email Resmi</label>
                        <input type="email" id="email_klasis" name="email_klasis" value="{{ old('email_klasis', $klasis->email_klasis) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="website_klasis" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" id="website_klasis" name="website_klasis" value="{{ old('website_klasis', $klasis->website_klasis) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                </div>

                {{-- SETUP PETA PELAYANAN (BARU) --}}
                <div class="bg-blue-50 p-4 rounded-md border border-blue-100">
                    <h4 class="text-xs font-bold text-blue-800 uppercase mb-3">Konfigurasi Peta Pelayanan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Latitude</label>
                            <input type="text" name="latitude" value="{{ old('latitude', $klasis->latitude) }}" placeholder="-2.5489" class="w-full border-gray-300 rounded text-sm focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Longitude</label>
                            <input type="text" name="longitude" value="{{ old('longitude', $klasis->longitude) }}" placeholder="140.718" class="w-full border-gray-300 rounded text-sm focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Kabupaten/Kota</label>
                            <input type="text" name="kabupaten_kota" value="{{ old('kabupaten_kota', $klasis->kabupaten_kota) }}" placeholder="JAYAPURA" class="w-full border-gray-300 rounded text-sm uppercase focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Warna Wilayah</label>
                            <input type="color" name="warna_peta" value="{{ old('warna_peta', $klasis->warna_peta ?? '#3B82F6') }}" class="w-full h-9 border-gray-300 rounded p-1 cursor-pointer">
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2 italic">* Koordinat kantor & nama kabupaten untuk visualisasi dashboard.</p>
                </div>

                {{-- Lokasi --}}
                <div>
                    <label for="alamat_kantor" class="block text-sm font-medium text-gray-700 mb-1">Alamat Kantor</label>
                    <textarea id="alamat_kantor" name="alamat_kantor" rows="2" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">{{ old('alamat_kantor', $klasis->alamat_kantor) }}</textarea>
                </div>

                {{-- Koordinat Legacy --}}
                <div>
                    <label for="koordinat_gps" class="block text-sm font-medium text-gray-700 mb-1">Koordinat GPS (Legacy)</label>
                    <input type="text" id="koordinat_gps" name="koordinat_gps" value="{{ old('koordinat_gps', $klasis->koordinat_gps) }}" placeholder="-2.548, 140.678" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                </div>

                {{-- Wilayah & Sejarah --}}
                <div>
                    <label for="wilayah_pelayanan" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Wilayah Pelayanan</label>
                    <textarea id="wilayah_pelayanan" name="wilayah_pelayanan" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">{{ old('wilayah_pelayanan', $klasis->wilayah_pelayanan) }}</textarea>
                </div>
                
                <div>
                    <label for="sejarah_singkat" class="block text-sm font-medium text-gray-700 mb-1">Sejarah Singkat</label>
                    <textarea id="sejarah_singkat" name="sejarah_singkat" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">{{ old('sejarah_singkat', $klasis->sejarah_singkat) }}</textarea>
                </div>

                {{-- Foto Kantor --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Kantor Klasis</label>
                    <div class="flex items-center space-x-4">
                        @if($klasis->foto_kantor_path)
                            <img src="{{ Storage::url($klasis->foto_kantor_path) }}" alt="Foto Lama" class="w-16 h-16 object-cover rounded border">
                        @endif
                        <input type="file" id="foto_kantor_path" name="foto_kantor_path" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-blue-700">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah foto.</p>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            <a href="{{ route('admin.klasis.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                Batal
            </a>
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection