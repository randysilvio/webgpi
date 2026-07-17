@extends('layouts.app')

@section('title', 'Edit Pejabat')

@section('content')
    <x-admin-form 
        title="Edit Data Pejabat" 
        action="{{ route('admin.tata-gereja.pejabat.update', $pejabat->id) }}" 
        method="PUT"
        back-route="{{ route('admin.tata-gereja.pejabat.index') }}"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- INFO READONLY --}}
            <div class="md:col-span-2 bg-slate-50 p-4 rounded border border-slate-200 flex items-center gap-4">
                <div class="h-10 w-10 rounded bg-white border flex items-center justify-center font-bold text-slate-500">
                    {{ substr($pejabat->anggotaJemaat->nama_lengkap, 0, 1) }}
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400">Nama Pejabat</label>
                    <div class="font-bold text-slate-800">{{ $pejabat->anggotaJemaat->nama_lengkap }}</div>
                </div>
            </div>

            {{-- JABATAN & STATUS --}}
            <x-form-select label="Jabatan" name="jabatan" required>
                @foreach(['Penatua', 'Diaken', 'Pengajar'] as $j)
                    <option value="{{ $j }}" {{ $pejabat->jabatan == $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </x-form-select>

            <x-form-select label="Status Keaktifan" name="status_aktif" required>
                @foreach(['Aktif', 'Demisioner', 'Emeritus', 'Non-Aktif'] as $s)
                    <option value="{{ $s }}" {{ $pejabat->status_aktif == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </x-form-select>

            {{-- PERIODE --}}
            <div class="grid grid-cols-2 gap-4">
                <x-form-input type="number" label="Periode Mulai" name="periode_mulai" value="{{ $pejabat->periode_mulai }}" required />
                <x-form-input type="number" label="Periode Selesai" name="periode_selesai" value="{{ $pejabat->periode_selesai }}" required />
            </div>

            <div class="md:col-span-1">
                <x-form-input label="Nomor SK Pelantikan" name="no_sk_pelantikan" value="{{ $pejabat->no_sk_pelantikan }}" />
            </div>

        </div>
    </x-admin-form>
@endsection