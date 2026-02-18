@extends('layouts.app')

@section('title', 'Tambah Jemaat')

@section('content')
    <x-admin-form 
        title="Formulir Jemaat Baru" 
        action="{{ route('admin.jemaat.store') }}" 
        back-route="{{ route('admin.jemaat.index') }}"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Bagian Kiri: Info Utama --}}
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">I. Identitas Jemaat</h3>
                
                <x-form-input label="Nama Jemaat" name="nama_jemaat" required placeholder="Jemaat ..." />
                <x-form-input label="Kode Jemaat" name="kode_jemaat" placeholder="Kode Unik (Opsional)" />
                
                {{-- Klasis Select --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Klasis <span class="text-red-500">*</span></label>
                    <select name="klasis_id" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 text-slate-700" 
                        {{ Auth::user()->hasRole('Admin Klasis') && count($klasisOptions) == 1 ? 'readonly' : '' }}>
                        <option value="">-- Pilih Klasis --</option>
                        @foreach($klasisOptions as $id => $nama)
                            <option value="{{ $id }}" {{ (old('klasis_id') == $id) || (Auth::user()->hasRole('Admin Klasis') && count($klasisOptions) == 1) ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                    {{-- Hidden input trik jika disabled --}}
                    @if(Auth::user()->hasRole('Admin Klasis') && count($klasisOptions) == 1)
                        <input type="hidden" name="klasis_id" value="{{ $klasisOptions->keys()->first() }}">
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <x-form-select label="Status" name="status_jemaat" required>
                        <option value="Mandiri">Mandiri</option>
                        <option value="Bakal Jemaat">Bakal Jemaat</option>
                        <option value="Pos Pelayanan">Pos Pelayanan</option>
                    </x-form-select>
                    <x-form-select label="Jenis" name="jenis_jemaat" required>
                        <option value="Umum">Umum</option>
                        <option value="Kategorial">Kategorial</option>
                    </x-form-select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Alamat Lengkap</label>
                    <textarea name="alamat_gereja" rows="3" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 placeholder-slate-400">{{ old('alamat_gereja') }}</textarea>
                </div>
            </div>

            {{-- Bagian Kanan: Kontak & Media --}}
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">II. Kontak & Media</h3>

                <x-form-input type="date" label="Tanggal Berdiri" name="tanggal_berdiri" />
                <x-form-input label="Telepon Kantor" name="telepon_kantor" placeholder="09XX-XXXXXX" />
                <x-form-input type="email" label="Email Resmi" name="email_jemaat" placeholder="jemaat@gpipapua.org" />

                <div class="pt-2">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Foto Gedung Gereja</label>
                    <input type="file" name="foto_gereja_path" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                    <p class="mt-1 text-[10px] text-slate-400">Format: JPG, PNG. Maks: 2MB.</p>
                </div>
            </div>

        </div>
    </x-admin-form>
@endsection