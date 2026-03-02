@extends('layouts.app')

@section('title', 'Edit Sidi')

@section('content')
    <x-admin-form 
        title="Edit Data Sidi" 
        action="{{ route('admin.sakramen.sidi.update', $sidi->id) }}" 
        method="PUT"
        back-route="{{ route('admin.sakramen.sidi.index') }}"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2 bg-slate-50 p-3 rounded border">
                <span class="text-xs font-bold text-slate-500 uppercase">Nama Anggota:</span>
                <p class="font-bold text-slate-800">{{ $sidi->anggotaJemaat->nama_lengkap }}</p>
            </div>

            <x-form-input label="Nomor Akta Sidi" name="no_akta_sidi" value="{{ $sidi->no_akta_sidi }}" required />
            
            <x-form-input type="date" label="Tanggal Peneguhan" name="tanggal_sidi" value="{{ $sidi->tanggal_sidi }}" required />

            <div class="md:col-span-2">
                <x-form-input label="Tempat / Gedung Gereja" name="tempat_sidi" value="{{ $sidi->tempat_sidi }}" required />
            </div>

            <div class="md:col-span-2">
                <x-form-input label="Pendeta Pelayan" name="pendeta_pelayan" value="{{ $sidi->pendeta_pelayan }}" required />
            </div>
        </div>
    </x-admin-form>
@endsection