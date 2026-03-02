@extends('layouts.app')

@section('title', 'Edit Klasis')

@section('content')
    <x-admin-form 
        title="Edit Data: {{ $klasis->nama_klasis }}" 
        action="{{ route('admin.klasis.update', $klasis->id) }}" 
        method="PUT"
        back-route="{{ route('admin.klasis.index') }}"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Identitas --}}
            <div class="md:col-span-2">
                <h3 class="text-sm font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">I. Identitas Utama</h3>
            </div>

            <x-form-input label="Nama Klasis" name="nama_klasis" value="{{ $klasis->nama_klasis }}" required />
            <x-form-input label="Kode Klasis" name="kode_klasis" value="{{ $klasis->kode_klasis }}" required />
            
            <x-form-input label="Pusat Klasis" name="pusat_klasis" value="{{ $klasis->pusat_klasis }}" />
            <x-form-input label="Nomor SK" name="nomor_sk_pembentukan" value="{{ $klasis->nomor_sk_pembentukan }}" />
            
            <x-form-input type="date" label="Tanggal Pembentukan" name="tanggal_pembentukan" value="{{ $klasis->tanggal_pembentukan?->format('Y-m-d') }}" />
            <x-form-input label="Klasis Induk" name="klasis_induk" value="{{ $klasis->klasis_induk }}" />

            {{-- Kontak --}}
            <div class="md:col-span-2 mt-2">
                <h3 class="text-sm font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">II. Kontak & Wilayah</h3>
            </div>

            <x-form-input label="Telepon Kantor" name="telepon_kantor" value="{{ $klasis->telepon_kantor }}" />
            <x-form-input type="email" label="Email Resmi" name="email_klasis" value="{{ $klasis->email_klasis }}" />
            
            <div class="md:col-span-2">
                <x-form-input label="Alamat Kantor" name="alamat_kantor" value="{{ $klasis->alamat_kantor }}" />
            </div>

            {{-- Konfigurasi Peta --}}
            <div class="md:col-span-2 bg-blue-50 p-4 rounded border border-blue-100 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-3 text-xs font-bold text-blue-800 uppercase">Konfigurasi Peta Digital</div>
                <x-form-input label="Latitude" name="latitude" value="{{ $klasis->latitude }}" />
                <x-form-input label="Longitude" name="longitude" value="{{ $klasis->longitude }}" />
                <x-form-input label="Warna Peta" name="warna_peta" type="color" value="{{ $klasis->warna_peta ?? '#3B82F6' }}" class="h-10" />
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Foto Kantor</label>
                @if($klasis->foto_kantor_path)
                    <div class="mb-2"><img src="{{ Storage::url($klasis->foto_kantor_path) }}" class="h-16 rounded border"></div>
                @endif
                <input type="file" name="foto_kantor_path" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
            </div>
        </div>
    </x-admin-form>
@endsection