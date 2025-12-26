@extends('admin.layout')

@section('title', 'Edit Mutasi Pendeta')
@section('header-title', 'Edit Riwayat Mutasi SK No: ' . $mutasi->nomor_sk)

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8 max-w-3xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir Edit Mutasi Pendeta</h2>

     @if (session('error'))
        <div class="flash-message mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif
     @if ($errors->any())
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm">
            {{-- ... (error list) ... --}}
        </div>
    @endif

    {{-- Ganti action ke route update --}}
    <form action="{{ route('admin.mutasi.update', $mutasi->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Method PUT untuk update --}}
        <div class="space-y-6">

            {{-- Informasi Dasar Mutasi --}}
            <section class="border rounded-lg p-6">
                 <h3 class="text-lg font-semibold text-gray-700 mb-4">1. Informasi SK Mutasi</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    {{-- Tanggal SK --}}
                    <div>
                        <label for="tanggal_sk" class="block text-sm font-medium text-gray-700 mb-1">Tanggal SK <span class="text-red-600">*</span></label>
                        {{-- Isi value dengan data $mutasi --}}
                        <input type="date" id="tanggal_sk" name="tanggal_sk" value="{{ old('tanggal_sk', $mutasi->tanggal_sk->format('Y-m-d')) }}" required
                               class="input-field @error('tanggal_sk') input-error @enderror">
                        @error('tanggal_sk') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                    {{-- Nomor SK --}}
                    <div>
                        <label for="nomor_sk" class="block text-sm font-medium text-gray-700 mb-1">Nomor SK <span class="text-red-600">*</span></label>
                        <input type="text" id="nomor_sk" name="nomor_sk" value="{{ old('nomor_sk', $mutasi->nomor_sk) }}" required
                               class="input-field @error('nomor_sk') input-error @enderror">
                        @error('nomor_sk') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                     {{-- Jenis Mutasi --}}
                    <div>
                        <label for="jenis_mutasi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Mutasi <span class="text-red-600">*</span></label>
                        <select id="jenis_mutasi" name="jenis_mutasi" required
                                class="input-field @error('jenis_mutasi') input-error @enderror">
                             <option value="">-- Pilih Jenis --</option>
                             <option value="Penempatan Awal" {{ old('jenis_mutasi', $mutasi->jenis_mutasi) == 'Penempatan Awal' ? 'selected' : '' }}>Penempatan Awal</option>
                             <option value="Pindah Tugas" {{ old('jenis_mutasi', $mutasi->jenis_mutasi) == 'Pindah Tugas' ? 'selected' : '' }}>Pindah Tugas</option>
                             {{-- ... (opsi lainnya) ... --}}
                        </select>
                        @error('jenis_mutasi') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                     {{-- Tanggal Efektif --}}
                    <div>
                        <label for="tanggal_efektif" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Efektif Mutasi</label>
                        <input type="date" id="tanggal_efektif" name="tanggal_efektif" value="{{ old('tanggal_efektif', optional($mutasi->tanggal_efektif)->format('Y-m-d')) }}"
                               class="input-field @error('tanggal_efektif') input-error @enderror">
                        @error('tanggal_efektif') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                 </div>
            </section>

            {{-- Detail Penempatan --}}
             <section class="border rounded-lg p-6">
                 <h3 class="text-lg font-semibold text-gray-700 mb-4">2. Detail Penempatan</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    {{-- Asal Klasis --}}
                    <div>
                        <label for="asal_klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Asal Klasis (Sebelumnya)</label>
                        <select id="asal_klasis_id" name="asal_klasis_id"
                                class="input-field @error('asal_klasis_id') input-error @enderror">
                            <option value="">-- Pilih Klasis Asal --</option>
                            @foreach ($klasisOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('asal_klasis_id', $mutasi->asal_klasis_id) == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                        @error('asal_klasis_id') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                    {{-- Asal Jemaat --}}
                    <div>
                        <label for="asal_jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Asal Jemaat (Sebelumnya)</label>
                        <select id="asal_jemaat_id" name="asal_jemaat_id"
                                class="input-field @error('asal_jemaat_id') input-error @enderror">
                            <option value="">-- Pilih Jemaat Asal --</option>
                             {{-- Load opsi jemaat asal berdasarkan $mutasi->asal_klasis_id --}}
                             @php $asalKlasisIdForEdit = old('asal_klasis_id', $mutasi->asal_klasis_id); @endphp
                             @if($asalKlasisIdForEdit)
                                @foreach(\App\Models\Jemaat::where('klasis_id', $asalKlasisIdForEdit)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id') as $id => $nama)
                                     <option value="{{ $id }}" {{ old('asal_jemaat_id', $mutasi->asal_jemaat_id) == $id ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                             @endif
                        </select>
                        @error('asal_jemaat_id') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                     {{-- Tujuan Klasis --}}
                    <div>
                        <label for="tujuan_klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Klasis (Baru)</label>
                        <select id="tujuan_klasis_id" name="tujuan_klasis_id"
                                class="input-field @error('tujuan_klasis_id') input-error @enderror">
                            <option value="">-- Pilih Klasis Tujuan --</option>
                            @foreach ($klasisOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('tujuan_klasis_id', $mutasi->tujuan_klasis_id) == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                         <p class="mt-1 text-xs text-gray-500 italic">Kosongkan jika Emeritus/Keluar/Meninggal.</p>
                        @error('tujuan_klasis_id') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                     {{-- Tujuan Jemaat --}}
                    <div>
                        <label for="tujuan_jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Jemaat (Baru)</label>
                        <select id="tujuan_jemaat_id" name="tujuan_jemaat_id"
                                class="input-field @error('tujuan_jemaat_id') input-error @enderror">
                            <option value="">-- Pilih Jemaat Tujuan --</option>
                            {{-- Load opsi jemaat tujuan berdasarkan $mutasi->tujuan_klasis_id --}}
                            @php $tujuanKlasisIdForEdit = old('tujuan_klasis_id', $mutasi->tujuan_klasis_id); @endphp
                             @if($tujuanKlasisIdForEdit)
                                @foreach(\App\Models\Jemaat::where('klasis_id', $tujuanKlasisIdForEdit)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id') as $id => $nama)
                                     <option value="{{ $id }}" {{ old('tujuan_jemaat_id', $mutasi->tujuan_jemaat_id) == $id ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                             @endif
                        </select>
                         <p class="mt-1 text-xs text-gray-500 italic">Pilih Klasis Tujuan terlebih dahulu.</p>
                        @error('tujuan_jemaat_id') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                     {{-- Keterangan --}}
                     <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                                  class="input-field @error('keterangan') input-error @enderror">{{ old('keterangan', $mutasi->keterangan) }}</textarea>
                        @error('keterangan') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                 </div>
            </section>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            {{-- Kembali ke detail mutasi atau pendeta --}}
            <a href="{{ route('admin.pendeta.show', $mutasi->pendeta_id) }}" class="btn-secondary"> Batal </a>
            <button type="submit" class="btn-primary"> Simpan Perubahan </button>
        </div>
    </form>
</div>

{{-- Style & Script (sama seperti create.blade.php) --}}
@push('styles') <style> /* ... */ </style> @endpush
@push('scripts') <script> /* ... (script dropdown dinamis) ... */ </script> @endpush

@endsection