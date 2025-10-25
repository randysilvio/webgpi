@extends('admin.layout')

@section('title', 'Edit Jemaat')
@section('header-title', 'Edit Data Jemaat: ' . $jemaat->nama_jemaat)

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir Edit Jemaat</h2>

    <form action="{{ route('admin.jemaat.update', $jemaat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') {{-- Method untuk update --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            {{-- Kolom Kiri --}}
            <div class="space-y-4">
                 <h3 class="text-lg font-medium text-gray-700 mb-3">Informasi Utama</h3>
                {{-- Nama Jemaat --}}
                <div>
                    <label for="nama_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Nama Jemaat <span class="text-red-600">*</span></label>
                    <input type="text" id="nama_jemaat" name="nama_jemaat" value="{{ old('nama_jemaat', $jemaat->nama_jemaat) }}" required
                           class="w-full px-4 py-2 border @error('nama_jemaat') border-red-500 @else border-gray-300 @enderror rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    @error('nama_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Kode Jemaat --}}
                <div>
                    <label for="kode_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Kode Jemaat <span class="italic text-gray-500">(Opsional, jika ada)</span></label>
                    <input type="text" id="kode_jemaat" name="kode_jemaat" value="{{ old('kode_jemaat', $jemaat->kode_jemaat) }}"
                           class="w-full px-4 py-2 border @error('kode_jemaat') border-red-500 @else border-gray-300 @enderror rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm" placeholder="Contoh: FAK01">
                    @error('kode_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Klasis --}}
                <div>
                    <label for="klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Klasis <span class="text-red-600">*</span></label>
                    <select id="klasis_id" name="klasis_id" required {{ Auth::user()->hasRole(['Admin Klasis', 'Admin Jemaat']) ? 'disabled' : '' }}
                            class="w-full px-4 py-2 border @error('klasis_id') border-red-500 @else border-gray-300 @enderror rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm bg-white disabled:bg-gray-100 disabled:text-gray-500 cursor-not-allowed">
                        {{-- <option value="" disabled>-- Pilih Klasis --</option> --}}
                        @foreach ($klasisOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('klasis_id', $jemaat->klasis_id) == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                    {{-- Hidden input jika disabled untuk memastikan value terkirim --}}
                     @if(Auth::user()->hasRole(['Admin Klasis', 'Admin Jemaat']))
                        <input type="hidden" name="klasis_id" value="{{ $jemaat->klasis_id }}">
                        <p class="mt-1 text-xs text-gray-500 italic">Hanya Super Admin atau Admin Bidang 3 yang dapat mengubah Klasis.</p>
                     @endif
                    @error('klasis_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Alamat Gereja --}}
                <div>
                    <label for="alamat_gereja" class="block text-sm font-medium text-gray-700 mb-1">Alamat Gereja</label>
                    <textarea id="alamat_gereja" name="alamat_gereja" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">{{ old('alamat_gereja', $jemaat->alamat_gereja) }}</textarea>
                </div>

                {{-- Tanggal Berdiri --}}
                <div>
                    <label for="tanggal_berdiri" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berdiri/Peresmian</label>
                    <input type="date" id="tanggal_berdiri" name="tanggal_berdiri" value="{{ old('tanggal_berdiri', optional($jemaat->tanggal_berdiri)->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    @error('tanggal_berdiri') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Status Jemaat --}}
                <div>
                    <label for="status_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Status Jemaat <span class="text-red-600">*</span></label>
                    <select id="status_jemaat" name="status_jemaat" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm bg-white">
                        <option value="Mandiri" {{ old('status_jemaat', $jemaat->status_jemaat) == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="Bakal Jemaat" {{ old('status_jemaat', $jemaat->status_jemaat) == 'Bakal Jemaat' ? 'selected' : '' }}>Bakal Jemaat</option>
                        <option value="Pos Pelayanan" {{ old('status_jemaat', $jemaat->status_jemaat) == 'Pos Pelayanan' ? 'selected' : '' }}>Pos Pelayanan</option>
                    </select>
                     @error('status_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                 {{-- Jenis Jemaat --}}
                <div>
                    <label for="jenis_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Jenis Jemaat <span class="text-red-600">*</span></label>
                    <select id="jenis_jemaat" name="jenis_jemaat" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm bg-white">
                        <option value="Umum" {{ old('jenis_jemaat', $jemaat->jenis_jemaat) == 'Umum' ? 'selected' : '' }}>Umum</option>
                        <option value="Kategorial" {{ old('jenis_jemaat', $jemaat->jenis_jemaat) == 'Kategorial' ? 'selected' : '' }}>Kategorial</option>
                    </select>
                     @error('jenis_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Kolom Kanan --}}
            <div class="space-y-4">
                 <h3 class="text-lg font-medium text-gray-700 mb-3">Data Statistik & Kontak</h3>

                {{-- Jumlah KK --}}
                <div>
                    <label for="jumlah_kk" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kepala Keluarga (KK)</label>
                    <input type="number" id="jumlah_kk" name="jumlah_kk" value="{{ old('jumlah_kk', $jemaat->jumlah_kk ?? 0) }}" min="0"
                           class="w-full px-4 py-2 border @error('jumlah_kk') border-red-500 @else border-gray-300 @enderror rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    @error('jumlah_kk') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                 {{-- Jumlah Jiwa --}}
                <div>
                    <label for="jumlah_total_jiwa" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Total Jiwa</label>
                    <input type="number" id="jumlah_total_jiwa" name="jumlah_total_jiwa" value="{{ old('jumlah_total_jiwa', $jemaat->jumlah_total_jiwa ?? 0) }}" min="0"
                           class="w-full px-4 py-2 border @error('jumlah_total_jiwa') border-red-500 @else border-gray-300 @enderror rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                     @error('jumlah_total_jiwa') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                 {{-- Tanggal Update Statistik --}}
                <div>
                    <label for="tanggal_update_statistik" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Update Statistik</label>
                    <input type="date" id="tanggal_update_statistik" name="tanggal_update_statistik" value="{{ old('tanggal_update_statistik', optional($jemaat->tanggal_update_statistik)->format('Y-m-d') ?? date('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                     @error('tanggal_update_statistik') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                 <div>
                    <label for="telepon_kantor" class="block text-sm font-medium text-gray-700 mb-1">Telepon Kantor/Kontak</label>
                    <input type="text" id="telepon_kantor" name="telepon_kantor" value="{{ old('telepon_kantor', $jemaat->telepon_kantor) }}" placeholder="09XX-XXXXXX"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                </div>

                <div>
                    <label for="email_jemaat" class="block text-sm font-medium text-gray-700 mb-1">Email Jemaat <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="email" id="email_jemaat" name="email_jemaat" value="{{ old('email_jemaat', $jemaat->email_jemaat) }}" placeholder="jemaat.contoh@email.com"
                           class="w-full px-4 py-2 border @error('email_jemaat') border-red-500 @else border-gray-300 @enderror rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm">
                    @error('email_jemaat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Foto Gereja --}}
                <div class="pt-2">
                    <label for="foto_gereja_path" class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto Gedung Gereja <span class="italic text-gray-500">(Opsional)</span></label>
                    <input type="file" id="foto_gereja_path" name="foto_gereja_path" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" onchange="previewImage(event, 'foto-preview')">
                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maks: 2MB. Biarkan kosong jika tidak ingin mengubah.</p>
                     @if ($jemaat->foto_gereja_path && Storage::disk('public')->exists($jemaat->foto_gereja_path))
                        <img src="{{ Storage::url($jemaat->foto_gereja_path) }}" alt="Foto Saat Ini" class="image-preview mt-2 block">
                    @else
                         <span class="text-xs text-gray-500 italic mt-2 block">Belum ada foto.</span>
                    @endif
                    <img id="foto-preview" src="#" alt="Preview Foto Gereja Baru" class="image-preview mt-2 hidden">
                    @error('foto_gereja_path') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Tambahkan field edit lain sesuai kebutuhan --}}

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

@push('scripts')
{{-- Tambahkan skrip JS jika perlu, misal untuk filter dropdown jemaat berdasarkan klasis --}}
@endpush