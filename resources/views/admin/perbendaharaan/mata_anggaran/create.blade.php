@extends('layouts.app')

@section('title', 'Tambah Mata Anggaran')

@section('content')
    <x-admin-form 
        title="Buat Kode Akun Baru" 
        action="{{ route('admin.perbendaharaan.mata-anggaran.store') }}" 
        back-route="{{ route('admin.perbendaharaan.mata-anggaran.index') }}"
    >
        <div class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kode Akun --}}
                <x-form-input label="Kode Akun" name="kode" required placeholder="Contoh: 1.1.01" />

                {{-- Nama Akun --}}
                <x-form-input label="Nama Mata Anggaran" name="nama_mata_anggaran" required placeholder="Contoh: Persembahan Syukur" />
            </div>

            {{-- Jenis & Kelompok --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-4 rounded border border-slate-200">
                <x-form-select label="Jenis Akun" name="jenis" required>
                    <option value="Pendapatan">Pendapatan</option>
                    <option value="Belanja">Belanja</option>
                </x-form-select>

                <x-form-input label="Kelompok (Opsional)" name="kelompok" placeholder="Contoh: Rutin / Pelayanan / Pembangunan" />
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Deskripsi Tambahan</label>
                <textarea name="deskripsi" rows="3" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 placeholder-slate-400">{{ old('deskripsi') }}</textarea>
                <p class="text-[10px] text-slate-400 mt-1">Penjelasan singkat mengenai penggunaan akun ini.</p>
            </div>

        </div>
    </x-admin-form>
@endsection