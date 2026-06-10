@extends('layouts.app')

@section('title', 'Edit Mata Anggaran')

@section('content')
    <x-admin-form 
        title="Edit Kode Akun: {{ $mataAnggaran->kode }}" 
        action="{{ route('admin.perbendaharaan.mata-anggaran.update', $mataAnggaran->id) }}" 
        method="PUT"
        back-route="{{ route('admin.perbendaharaan.mata-anggaran.index') }}"
    >
        <div class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kode Akun --}}
                <x-form-input label="Kode Akun" name="kode" value="{{ $mataAnggaran->kode }}" required />

                {{-- Nama Akun --}}
                <x-form-input label="Nama Mata Anggaran" name="nama_mata_anggaran" value="{{ $mataAnggaran->nama_mata_anggaran }}" required />
            </div>

            {{-- Jenis & Kelompok --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-4 rounded border border-slate-200">
                <x-form-select label="Jenis Akun" name="jenis" required>
                    <option value="Pendapatan" {{ $mataAnggaran->jenis == 'Pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                    <option value="Belanja" {{ $mataAnggaran->jenis == 'Belanja' ? 'selected' : '' }}>Belanja</option>
                </x-form-select>

                <x-form-input label="Kelompok" name="kelompok" value="{{ $mataAnggaran->kelompok }}" />
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Deskripsi Tambahan</label>
                <textarea name="deskripsi" rows="3" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">{{ old('deskripsi', $mataAnggaran->deskripsi) }}</textarea>
            </div>

            {{-- Status Aktif --}}
            <div class="flex items-center space-x-3 border-t border-slate-100 pt-4">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ $mataAnggaran->is_active ? 'checked' : '' }} class="rounded text-slate-800 focus:ring-slate-500 w-4 h-4 border-slate-300">
                <label for="is_active" class="text-sm font-bold text-slate-700 cursor-pointer select-none">
                    Akun Aktif <span class="text-slate-400 font-normal">(Muncul di pilihan saat menyusun anggaran)</span>
                </label>
            </div>

        </div>
    </x-admin-form>
@endsection