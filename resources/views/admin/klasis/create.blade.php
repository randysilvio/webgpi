@extends('layouts.app')

@section('title', 'Tambah Klasis')

@section('content')
    <x-admin-form 
        title="Formulir Klasis Baru" 
        action="{{ route('admin.klasis.store') }}" 
        back-route="{{ route('admin.klasis.index') }}"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Identitas --}}
            <div class="md:col-span-2">
                <h3 class="text-sm font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">I. Identitas Utama</h3>
            </div>

            <x-form-input label="Nama Klasis" name="nama_klasis" required placeholder="Contoh: Klasis Fakfak" />
            <x-form-input label="Kode Klasis" name="kode_klasis" required placeholder="K-01" />
            
            <x-form-input label="Pusat Klasis" name="pusat_klasis" placeholder="Kota/Wilayah Pusat" />
            <x-form-input label="Nomor SK" name="nomor_sk_pembentukan" placeholder="No. SK Pembentukan" />
            
            <x-form-input type="date" label="Tanggal Pembentukan" name="tanggal_pembentukan" />
            <x-form-input label="Klasis Induk" name="klasis_induk" placeholder="Jika hasil pemekaran" />

            {{-- Kontak --}}
            <div class="md:col-span-2 mt-2">
                <h3 class="text-sm font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">II. Kontak & Wilayah</h3>
            </div>

            <x-form-input label="Telepon Kantor" name="telepon_kantor" />
            <x-form-input type="email" label="Email Resmi" name="email_klasis" />
            
            <div class="md:col-span-2">
                <x-form-input label="Alamat Kantor" name="alamat_kantor" />
            </div>

            {{-- Konfigurasi Peta --}}
            <div class="md:col-span-2 bg-blue-50 p-4 rounded border border-blue-100 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-3 text-xs font-bold text-blue-800 uppercase">Konfigurasi Peta Digital</div>
                <x-form-input label="Latitude" name="latitude" placeholder="-2.5489" />
                <x-form-input label="Longitude" name="longitude" placeholder="140.718" />
                <x-form-input label="Warna Peta" name="warna_peta" type="color" value="#3B82F6" class="h-10" />
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Foto Kantor</label>
                <input type="file" name="foto_kantor_path" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
            </div>
        </div>
    </x-admin-form>
@endsection