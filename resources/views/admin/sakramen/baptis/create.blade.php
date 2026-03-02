@extends('layouts.app')

@section('title', 'Input Baptisan')

@section('content')
    {{-- CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <x-admin-form 
        title="Registrasi Baptisan Baru" 
        action="{{ route('admin.sakramen.baptis.store') }}" 
        back-route="{{ route('admin.sakramen.baptis.index') }}"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- DATA ANGGOTA --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                    Pilih Anggota (Calon Baptis) <span class="text-red-500">*</span>
                </label>
                <select name="anggota_jemaat_id" required class="w-full border-slate-300 rounded text-sm select2">
                    <option value="">-- Cari Nama Anggota --</option>
                    @foreach($anggotaTanpaBaptis as $a)
                        <option value="{{ $a->id }}">{{ $a->nama_lengkap }} ({{ $a->jemaat->nama_jemaat ?? 'Umum' }}) - {{ $a->umur }} Thn</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1 italic">Hanya menampilkan anggota yang belum memiliki data baptis.</p>
            </div>

            {{-- DATA ADMINISTRASI --}}
            <x-form-input label="Nomor Akta Baptis" name="no_akta_baptis" required placeholder="Contoh: 001/BAP/2026" />
            
            <x-form-input type="date" label="Tanggal Pelayanan" name="tanggal_baptis" required />

            <div class="md:col-span-2">
                <x-form-input label="Tempat / Gedung Gereja" name="tempat_baptis" required placeholder="Nama Gereja tempat pembaptisan" />
            </div>

            <div class="md:col-span-2">
                <x-form-input label="Pendeta Pelayan" name="pendeta_pelayan" required placeholder="Nama Pendeta yang melayani" />
            </div>

        </div>
    </x-admin-form>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: "-- Pilih Anggota --"
            });
        });
    </script>
    @endpush
    @push('styles')
    <style>
        .select2-container .select2-selection--single { height: 38px; border-color: #cbd5e1; border-radius: 0.375rem; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 36px; padding-left: 12px; font-size: 0.875rem; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
    </style>
    @endpush
@endsection