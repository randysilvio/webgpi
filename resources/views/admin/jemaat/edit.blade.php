@extends('layouts.app')

@section('title', 'Edit Jemaat')

@section('content')
    <x-admin-form 
        title="Edit Data: {{ $jemaat->nama_jemaat }}" 
        action="{{ route('admin.jemaat.update', $jemaat->id) }}" 
        method="PUT"
        back-route="{{ route('admin.jemaat.index') }}"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Info Utama --}}
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">I. Identitas Jemaat</h3>
                
                <x-form-input label="Nama Jemaat" name="nama_jemaat" value="{{ $jemaat->nama_jemaat }}" required />
                <x-form-input label="Kode Jemaat" name="kode_jemaat" value="{{ $jemaat->kode_jemaat }}" />
                
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Klasis</label>
                    <select name="klasis_id" class="w-full border-slate-300 rounded text-sm bg-slate-50 text-slate-500 cursor-not-allowed" 
                        {{ !Auth::user()->hasRole(['Super Admin', 'Admin Bidang 3']) ? 'disabled' : '' }}>
                        @foreach($klasisOptions as $id => $nama)
                            <option value="{{ $id }}" {{ $jemaat->klasis_id == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                    {{-- Hidden input jika user biasa agar data terkirim --}}
                    @if(!Auth::user()->hasRole(['Super Admin', 'Admin Bidang 3']))
                        <input type="hidden" name="klasis_id" value="{{ $jemaat->klasis_id }}">
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <x-form-select label="Status" name="status_jemaat" required>
                        <option value="Mandiri" {{ $jemaat->status_jemaat == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="Bakal Jemaat" {{ $jemaat->status_jemaat == 'Bakal Jemaat' ? 'selected' : '' }}>Bakal Jemaat</option>
                        <option value="Pos Pelayanan" {{ $jemaat->status_jemaat == 'Pos Pelayanan' ? 'selected' : '' }}>Pos Pelayanan</option>
                    </x-form-select>
                    <x-form-select label="Jenis" name="jenis_jemaat" required>
                        <option value="Umum" {{ $jemaat->jenis_jemaat == 'Umum' ? 'selected' : '' }}>Umum</option>
                        <option value="Kategorial" {{ $jemaat->jenis_jemaat == 'Kategorial' ? 'selected' : '' }}>Kategorial</option>
                    </x-form-select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Alamat Lengkap</label>
                    <textarea name="alamat_gereja" rows="3" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500">{{ $jemaat->alamat_gereja }}</textarea>
                </div>
            </div>

            {{-- Kontak & Media --}}
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">II. Kontak & Media</h3>

                <x-form-input type="date" label="Tanggal Berdiri" name="tanggal_berdiri" value="{{ $jemaat->tanggal_berdiri?->format('Y-m-d') }}" />
                <x-form-input label="Telepon Kantor" name="telepon_kantor" value="{{ $jemaat->telepon_kantor }}" />
                <x-form-input type="email" label="Email Resmi" name="email_jemaat" value="{{ $jemaat->email_jemaat }}" />

                <div class="pt-2">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Foto Gedung</label>
                    @if($jemaat->foto_gereja_path)
                        <img src="{{ Storage::url($jemaat->foto_gereja_path) }}" class="h-20 rounded mb-2 border shadow-sm">
                    @endif
                    <input type="file" name="foto_gereja_path" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                </div>
            </div>

        </div>
    </x-admin-form>
@endsection