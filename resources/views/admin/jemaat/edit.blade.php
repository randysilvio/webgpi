@extends('layouts.app')

@section('title', 'Modifikasi Arsip Jemaat')

@section('content')
<div class="mb-6 flex items-center justify-between border-b-2 border-gray-800 pb-4">
    <div>
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Modifikasi Buku Jemaat: {{ $jemaat->nama_jemaat }}</h2>
        <p class="text-xs text-gray-600 mt-1">Sistem Pembaruan Data Registrasi Wilayah Jemaat.</p>
    </div>
    <a href="{{ route('admin.jemaat.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
    </a>
</div>

<form action="{{ route('admin.jemaat.update', $jemaat->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="space-y-6 max-w-5xl mx-auto">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- PANEL KIRI: INFO UTAMA --}}
            <div class="bg-white border border-gray-300 p-5 rounded shadow-sm h-full">
                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-church mr-2 text-blue-800"></i> I. Identitas & Legalitas Jemaat</h4>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nama Jemaat Resmi <span class="text-red-600">*</span></label>
                        <input type="text" name="nama_jemaat" value="{{ old('nama_jemaat', $jemaat->nama_jemaat) }}" required 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 uppercase">
                        @error('nama_jemaat') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Kode Identitas Jemaat</label>
                        <input type="text" name="kode_jemaat" value="{{ old('kode_jemaat', $jemaat->kode_jemaat) }}" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 font-mono">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Otoritas Wilayah Klasis <span class="text-red-600">*</span></label>
                        <select name="klasis_id" class="w-full border border-gray-300 rounded text-sm shadow-sm font-bold bg-gray-100 text-gray-600 pointer-events-none" 
                            {{ !Auth::user()->hasRole(['Super Admin', 'Admin Bidang 3']) ? 'readonly' : '' }}>
                            @foreach($klasisOptions as $id => $nama)
                                <option value="{{ $id }}" {{ $jemaat->klasis_id == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                        @if(!Auth::user()->hasRole(['Super Admin', 'Admin Bidang 3']))
                            <input type="hidden" name="klasis_id" value="{{ $jemaat->klasis_id }}">
                            <p class="text-[9px] text-red-600 italic mt-1 font-bold">Pemindahan Klasis hanya dapat dilakukan oleh Admin Sinode.</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Status Kelembagaan <span class="text-red-600">*</span></label>
                            <select name="status_jemaat" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                                <option value="Mandiri" {{ $jemaat->status_jemaat == 'Mandiri' ? 'selected' : '' }}>Gereja Mandiri</option>
                                <option value="Bakal Jemaat" {{ $jemaat->status_jemaat == 'Bakal Jemaat' ? 'selected' : '' }}>Bakal Jemaat</option>
                                <option value="Pos Pelayanan" {{ $jemaat->status_jemaat == 'Pos Pelayanan' ? 'selected' : '' }}>Pos Pelayanan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Jenis Pelayanan <span class="text-red-600">*</span></label>
                            <select name="jenis_jemaat" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                                <option value="Umum" {{ $jemaat->jenis_jemaat == 'Umum' ? 'selected' : '' }}>Umum / Terbuka</option>
                                <option value="Kategorial" {{ $jemaat->jenis_jemaat == 'Kategorial' ? 'selected' : '' }}>Kategorial Khusus</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-3">
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Alamat Domisili Gereja</label>
                        <textarea name="alamat_gereja" rows="3" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">{{ old('alamat_gereja', $jemaat->alamat_gereja) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- PANEL KANAN: KONTAK & MEDIA --}}
            <div class="bg-white border border-gray-300 p-5 rounded shadow-sm h-full flex flex-col">
                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-address-book mr-2 text-blue-800"></i> II. Kontak & Media Dokumentasi</h4>
                
                <div class="space-y-4 flex-grow">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tanggal Pendirian / Est</label>
                        <input type="date" name="tanggal_berdiri" value="{{ old('tanggal_berdiri', $jemaat->tanggal_berdiri?->format('Y-m-d')) }}" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Telepon Sekretariat</label>
                        <input type="text" name="telepon_kantor" value="{{ old('telepon_kantor', $jemaat->telepon_kantor) }}" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Surat Elektronik (Email)</label>
                        <input type="email" name="email_jemaat" value="{{ old('email_jemaat', $jemaat->email_jemaat) }}" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 p-4 rounded mt-4 text-center">
                    <label class="block text-[10px] font-bold text-blue-800 uppercase mb-3 border-b border-blue-200 pb-2">Foto Fisik Gedung</label>
                    
                    @if($jemaat->foto_gereja_path)
                        <div class="mb-3 inline-block border-2 border-white shadow-md rounded overflow-hidden">
                            <img src="{{ Storage::url($jemaat->foto_gereja_path) }}" class="h-24 w-auto object-cover">
                        </div>
                    @endif
                    
                    <input type="file" name="foto_gereja_path" accept="image/*" class="w-full text-[10px] text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-gray-800 file:text-white hover:file:bg-gray-900 cursor-pointer bg-white border border-blue-300 p-1 rounded shadow-sm">
                </div>
            </div>

        </div>

        <div class="flex justify-end pt-2 pb-10">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Perbarui Data Jemaat
            </button>
        </div>
    </div>
</form>
@endsection