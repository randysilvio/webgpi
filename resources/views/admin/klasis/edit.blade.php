@extends('layouts.app')

@section('title', 'Modifikasi Klasis')

@section('content')
<div class="mb-6 flex items-center justify-between border-b-2 border-gray-800 pb-4">
    <div>
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Modifikasi Klasis: {{ $klasis->nama_klasis }}</h2>
        <p class="text-xs text-gray-600 mt-1">Sistem pembaruan data wilayah administratif.</p>
    </div>
    <a href="{{ route('admin.klasis.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>

<form action="{{ route('admin.klasis.update', $klasis->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="space-y-6 max-w-5xl mx-auto">
        
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-list-ol mr-2 text-blue-800"></i> I. Informasi Klasifikasi</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form-input label="Nama Klasis" name="nama_klasis" value="{{ $klasis->nama_klasis }}" required />
                <x-form-input label="Kode Klasis" name="kode_klasis" value="{{ $klasis->kode_klasis }}" required />
                <x-form-input label="Pusat Klasis" name="pusat_klasis" value="{{ $klasis->pusat_klasis }}" />
                <x-form-input label="Nomor SK" name="nomor_sk_pembentukan" value="{{ $klasis->nomor_sk_pembentukan }}" />
                <x-form-input type="date" label="Tanggal Pembentukan" name="tanggal_pembentukan" value="{{ $klasis->tanggal_pembentukan?->format('Y-m-d') }}" />
                <x-form-input label="Klasis Induk" name="klasis_induk" value="{{ $klasis->klasis_induk }}" />
            </div>
        </div>

        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-address-book mr-2 text-blue-800"></i> II. Kontak & Lokasi</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-form-input label="Telepon Kantor" name="telepon_kantor" value="{{ $klasis->telepon_kantor }}" />
                <x-form-input type="email" label="Email Resmi" name="email_klasis" value="{{ $klasis->email_klasis }}" />
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Alamat Kantor</label>
                    <textarea name="alamat_kantor" rows="2" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">{{ $klasis->alamat_kantor }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm border-l-4 border-l-blue-800">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-map mr-2 text-blue-800"></i> III. Konfigurasi Peta</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-form-input label="Latitude" name="latitude" value="{{ $klasis->latitude }}" />
                <x-form-input label="Longitude" name="longitude" value="{{ $klasis->longitude }}" />
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Warna Peta</label>
                    <input type="color" name="warna_peta" value="{{ $klasis->warna_peta ?? '#3B82F6' }}" class="w-full h-9 p-1 border border-gray-300 rounded shadow-sm">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Foto Kantor Baru (Opsional)</label>
                @if($klasis->foto_kantor_path)
                    <div class="mb-2"><img src="{{ Storage::url($klasis->foto_kantor_path) }}" class="h-16 w-auto border border-gray-300"></div>
                @endif
                <input type="file" name="foto_kantor_path" class="block w-full text-[10px] text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-gray-800 file:text-white cursor-pointer">
            </div>
        </div>

        <div class="flex justify-end pt-2 pb-10">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Perbarui Data
            </button>
        </div>
    </div>
</form>
@endsection