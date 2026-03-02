@extends('layouts.app')

@section('title', 'Edit Pegawai')
@section('header-title', 'Kepegawaian')

@section('content')
<div class="max-w-5xl mx-auto">
    
    <form action="{{ route('admin.kepegawaian.pegawai.update', $pegawai->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- CARD: INFORMASI DASAR --}}
        <div class="bg-white rounded shadow-sm border border-slate-200 mb-6 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-700 uppercase text-xs tracking-wider">Edit Identitas: {{ $pegawai->nama }}</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Nama & Gelar --}}
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Gelar Depan</label>
                        <input type="text" name="gelar_depan" class="w-full border-slate-300 rounded text-sm" value="{{ old('gelar_depan', $pegawai->gelar_depan) }}">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" class="w-full border-slate-300 rounded text-sm" value="{{ old('nama', $pegawai->nama) }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Gelar Belakang</label>
                        <input type="text" name="gelar_belakang" class="w-full border-slate-300 rounded text-sm" value="{{ old('gelar_belakang', $pegawai->gelar_belakang) }}">
                    </div>
                </div>

                {{-- NIK & NIP --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">NIK (KTP)</label>
                    <input type="text" name="nik" class="w-full border-slate-300 rounded text-sm" value="{{ old('nik', $pegawai->nik) }}">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">NIP / NIPG</label>
                    <input type="text" name="nip" class="w-full border-slate-300 rounded text-sm" value="{{ old('nip', $pegawai->nip) }}">
                </div>

                {{-- TTL & Gender --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Tempat, Tanggal Lahir</label>
                    <div class="flex gap-2">
                        <input type="text" name="tempat_lahir" class="w-1/2 border-slate-300 rounded text-sm" value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}">
                        <input type="date" name="tanggal_lahir" class="w-1/2 border-slate-300 rounded text-sm" value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir) }}">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="w-full border-slate-300 rounded text-sm">
                        <option value="L" {{ $pegawai->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $pegawai->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                {{-- Foto --}}
                <div class="md:col-span-2 flex items-center gap-4">
                    <div class="shrink-0">
                        @if($pegawai->foto_profil)
                            <img class="h-16 w-16 object-cover rounded-full border border-slate-200" src="{{ asset('storage/'.$pegawai->foto_profil) }}" alt="Foto Lama">
                        @endif
                    </div>
                    <div class="w-full">
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Ganti Foto</label>
                        <input type="file" name="foto_profil" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: STATUS & KONTAK --}}
        <div class="bg-white rounded shadow-sm border border-slate-200 mb-6 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-700 uppercase text-xs tracking-wider">Status & Kontak</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Jenis Kepegawaian</label>
                    <select name="jenis_pegawai" class="w-full border-slate-300 rounded text-sm bg-slate-50" readonly>
                        <option value="{{ $pegawai->jenis_pegawai }}">{{ $pegawai->jenis_pegawai }}</option>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">*Jenis pegawai tidak dapat diubah sembarangan.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Status Aktif</label>
                    <select name="status_aktif" class="w-full border-slate-300 rounded text-sm">
                        <option value="Aktif" {{ $pegawai->status_aktif == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Cuti" {{ $pegawai->status_aktif == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="Pensiun" {{ $pegawai->status_aktif == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Alamat Domisili</label>
                    <textarea name="alamat" rows="2" class="w-full border-slate-300 rounded text-sm">{{ old('alamat', $pegawai->alamat) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" class="w-full border-slate-300 rounded text-sm" value="{{ old('no_hp', $pegawai->no_hp) }}">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Email</label>
                    <input type="email" name="email" class="w-full border-slate-300 rounded text-sm" value="{{ old('email', $pegawai->email) }}">
                </div>

            </div>
        </div>

        {{-- FORM ACTIONS --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded hover:bg-slate-50 transition">
                Kembali
            </a>
            <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white text-sm font-bold uppercase tracking-wide rounded hover:bg-slate-900 transition shadow-lg">
                Update Data
            </button>
        </div>

    </form>
</div>
@endsection