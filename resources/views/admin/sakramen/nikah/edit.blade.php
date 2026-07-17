@extends('layouts.app')

@section('title', 'Edit Pernikahan')

@section('content')
    <x-admin-form 
        title="Edit Akta Nikah: {{ $nikah->no_akta_nikah }}" 
        action="{{ route('admin.sakramen.nikah.update', $nikah->id) }}" 
        method="PUT"
        back-route="{{ route('admin.sakramen.nikah.index') }}"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- INFO READONLY --}}
            <div class="md:col-span-2 bg-slate-50 p-4 rounded border border-slate-200">
                <div class="flex justify-between items-center text-sm">
                    <div><strong>Suami:</strong> {{ $nikah->suami->nama_lengkap }}</div>
                    <div><i class="fas fa-heart text-pink-400 mx-2"></i></div>
                    <div><strong>Istri:</strong> {{ $nikah->istri->nama_lengkap }}</div>
                </div>
                <p class="text-[10px] text-red-500 mt-2 italic">* Pasangan nikah tidak dapat diubah. Hapus data dan buat baru jika salah input pasangan.</p>
            </div>

            <x-form-input label="Nomor Akta Nikah" name="no_akta_nikah" value="{{ $nikah->no_akta_nikah }}" required />
            
            <x-form-input type="date" label="Tanggal Pemberkatan" name="tanggal_nikah" value="{{ \Carbon\Carbon::parse($nikah->tanggal_nikah)->format('Y-m-d') }}" required />

            <div class="md:col-span-2">
                <x-form-input label="Tempat / Gedung Gereja" name="tempat_nikah" value="{{ $nikah->tempat_nikah }}" required />
            </div>

            <div class="md:col-span-2">
                <x-form-input label="Pendeta Pelayan" name="pendeta_pelayan" value="{{ $nikah->pendeta_pelayan }}" required />
            </div>
        </div>
    </x-admin-form>
@endsection