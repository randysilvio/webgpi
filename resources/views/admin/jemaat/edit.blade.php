@extends('admin.layout')

@section('title', 'Edit Jemaat')
@section('header-title', 'Edit Data Jemaat: ' . $jemaat->nama_jemaat)

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8">
    <div class="flex justify-between items-center mb-6 border-b pb-3">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Jemaat</h2>
        <div class="text-xs bg-blue-50 text-blue-700 px-3 py-1 rounded-full font-medium">
            <i class="fas fa-info-circle mr-1"></i> Statistik KK & Jiwa dihitung otomatis dari data Anggota
        </div>
    </div>

    <form action="{{ route('admin.jemaat.update', $jemaat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            {{-- Kolom Kiri: Informasi Utama --}}
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-700 mb-3 border-b pb-1">Informasi Utama</h3>
                
                {{-- Nama Jemaat --}}
                <div>
                    <label for="nama_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Nama Jemaat <span class="text-red-600">*</span></label>
                    <input type="text" id="nama_jemaat" name="nama_jemaat" value="{{ old('nama_jemaat', $jemaat->nama_jemaat) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('nama_jemaat') border-red-500 @enderror">
                    @error('nama_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Kode Jemaat --}}
                <div>
                    <label for="kode_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Kode Jemaat <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="text" id="kode_jemaat" name="kode_jemaat" value="{{ old('kode_jemaat', $jemaat->kode_jemaat) }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('kode_jemaat') border-red-500 @enderror" placeholder="Contoh: FAK01">
                    @error('kode_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Klasis --}}
                <div>
                    <label for="klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Klasis <span class="text-red-600">*</span></label>
                    <select id="klasis_id" name="klasis_id" required {{ Auth::user()->hasRole(['Admin Klasis', 'Admin Jemaat']) ? 'disabled' : '' }}
                            class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm {{ Auth::user()->hasRole(['Admin Klasis', 'Admin Jemaat']) ? 'bg-gray-100 cursor-not-allowed' : '' }} @error('klasis_id') border-red-500 @enderror">
                        @foreach ($klasisOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('klasis_id', $jemaat->klasis_id) == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                    {{-- Hidden input untuk role terbatas --}}
                    @if(Auth::user()->hasRole(['Admin Klasis', 'Admin Jemaat']))
                        <input type="hidden" name="klasis_id" value="{{ $jemaat->klasis_id }}">
                        <p class="mt-1 text-xs text-gray-500 italic">Hanya Super Admin/Admin Bidang 3 yang dapat mengubah Klasis.</p>
                    @endif
                    @error('klasis_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Status & Jenis Jemaat (Grid 2 Kolom) --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="status_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                        <select id="status_jemaat" name="status_jemaat" required
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="Mandiri" {{ old('status_jemaat', $jemaat->status_jemaat) == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                            <option value="Bakal Jemaat" {{ old('status_jemaat', $jemaat->status_jemaat) == 'Bakal Jemaat' ? 'selected' : '' }}>Bakal Jemaat</option>
                            <option value="Pos Pelayanan" {{ old('status_jemaat', $jemaat->status_jemaat) == 'Pos Pelayanan' ? 'selected' : '' }}>Pos Pelayanan</option>
                        </select>
                    </div>
                    <div>
                        <label for="jenis_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Jenis <span class="text-red-600">*</span></label>
                        <select id="jenis_jemaat" name="jenis_jemaat" required
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="Umum" {{ old('jenis_jemaat', $jemaat->jenis_jemaat) == 'Umum' ? 'selected' : '' }}>Umum</option>
                            <option value="Kategorial" {{ old('jenis_jemaat', $jemaat->jenis_jemaat) == 'Kategorial' ? 'selected' : '' }}>Kategorial</option>
                        </select>
                    </div>
                </div>

                {{-- Alamat Gereja --}}
                <div>
                    <label for="alamat_gereja" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap Gedung Gereja</label>
                    <textarea id="alamat_gereja" name="alamat_gereja" rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">{{ old('alamat_gereja', $jemaat->alamat_gereja) }}</textarea>
                </div>
            </div>

            {{-- Kolom Kanan: Kontak & Media --}}
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-700 mb-3 border-b pb-1">Kontak & Media</h3>

                {{-- Tanggal Berdiri --}}
                <div>
                    <label for="tanggal_berdiri" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berdiri/Peresmian</label>
                    <input type="date" id="tanggal_berdiri" name="tanggal_berdiri" value="{{ old('tanggal_berdiri', optional($jemaat->tanggal_berdiri)->format('Y-m-d')) }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    @error('tanggal_berdiri') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Kontak --}}
                <div>
                    <label for="telepon_kantor" class="block text-sm font-medium text-gray-700 mb-1">Telepon/Kontak Kantor</label>
                    <input type="text" id="telepon_kantor" name="telepon_kantor" value="{{ old('telepon_kantor', $jemaat->telepon_kantor) }}" placeholder="09XX-XXXXXX"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>

                {{-- Email --}}
                <div>
                    <label for="email_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Email Resmi Jemaat</label>
                    <input type="email" id="email_jemaat" name="email_jemaat" value="{{ old('email_jemaat', $jemaat->email_jemaat) }}" placeholder="jemaat.contoh@email.com"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('email_jemaat') border-red-500 @enderror">
                     @error('email_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Foto Gereja --}}
                <div class="pt-2">
                    <label for="foto_gereja_path" class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto Gedung Gereja</label>
                    <input type="file" id="foto_gereja_path" name="foto_gereja_path" accept="image/*" 
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" 
                           onchange="previewImage(event, 'foto-preview')">
                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maks: 2MB. Biarkan kosong jika tidak mengubah.</p>

                    {{-- Preview Foto Lama --}}
                    @if ($jemaat->foto_gereja_path && Storage::disk('public')->exists($jemaat->foto_gereja_path))
                        <div class="mt-2">
                            <span class="text-xs text-gray-500 block mb-1">Foto Saat Ini:</span>
                            <img src="{{ Storage::url($jemaat->foto_gereja_path) }}" alt="Foto Saat Ini" class="h-24 w-auto rounded border shadow-sm">
                        </div>
                    @else
                        <span class="text-xs text-gray-500 italic mt-2 block">Belum ada foto.</span>
                    @endif

                    {{-- Preview Foto Baru --}}
                    <img id="foto-preview" src="#" alt="Preview Foto Baru" class="image-preview mt-2 hidden h-48 rounded shadow-sm border">
                    
                    @error('foto_gereja_path') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            <a href="{{ route('admin.jemaat.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                Batal
            </a>
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection