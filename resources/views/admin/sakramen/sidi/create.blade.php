@extends('layouts.app')

@section('title', 'Input Sidi')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <x-admin-form 
        title="Registrasi Peneguhan Sidi" 
        action="{{ route('admin.sakramen.sidi.store') }}" 
        back-route="{{ route('admin.sakramen.sidi.index') }}"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                    Pilih Peserta Sidi <span class="text-red-500">*</span>
                </label>
                <select name="anggota_jemaat_id" required class="w-full border-slate-300 rounded text-sm select2">
                    <option value="">-- Cari Nama Anggota --</option>
                    @foreach($anggotaTanpaSidi as $a)
                        <option value="{{ $a->id }}">{{ $a->nama_lengkap }} ({{ $a->jemaat->nama_jemaat ?? 'Umum' }}) - {{ $a->umur }} Thn</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1 italic">Hanya menampilkan anggota yang belum sidi.</p>
            </div>

            <x-form-input label="Nomor Akta Sidi" name="no_akta_sidi" required placeholder="Contoh: 001/SIDI/2026" />
            
            <x-form-input type="date" label="Tanggal Peneguhan" name="tanggal_sidi" required />

            <div class="md:col-span-2">
                <x-form-input label="Tempat / Gedung Gereja" name="tempat_sidi" required placeholder="Nama Gereja" />
            </div>

            <div class="md:col-span-2">
                <x-form-input label="Pendeta Pelayan" name="pendeta_pelayan" required />
            </div>
        </div>
    </x-admin-form>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({ width: '100%' });
        });
    </script>
    @endpush
@endsection