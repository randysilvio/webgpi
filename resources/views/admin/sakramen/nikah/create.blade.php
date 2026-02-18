@extends('layouts.app')

@section('title', 'Input Pernikahan')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <x-admin-form 
        title="Registrasi Pemberkatan Nikah" 
        action="{{ route('admin.sakramen.nikah.store') }}" 
        back-route="{{ route('admin.sakramen.nikah.index') }}"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- DATA MEMPELAI --}}
            <div class="md:col-span-2 bg-pink-50 p-4 rounded border border-pink-100 mb-2">
                <h4 class="text-xs font-bold text-pink-800 uppercase mb-3 border-b border-pink-200 pb-2">Data Mempelai</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-blue-600 uppercase mb-1">Mempelai Pria</label>
                        <select name="suami_id" required class="w-full border-slate-300 rounded text-sm select2">
                            <option value="">-- Cari Nama --</option>
                            @foreach($pria as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }} ({{ $p->jemaat->nama_jemaat ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-pink-600 uppercase mb-1">Mempelai Wanita</label>
                        <select name="istri_id" required class="w-full border-slate-300 rounded text-sm select2">
                            <option value="">-- Cari Nama --</option>
                            @foreach($wanita as $w)
                                <option value="{{ $w->id }}">{{ $w->nama_lengkap }} ({{ $w->jemaat->nama_jemaat ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- DATA ADMINISTRASI --}}
            <x-form-input label="Nomor Akta Nikah" name="no_akta_nikah" required placeholder="Contoh: 001/NIK/2026" />
            <x-form-input type="date" label="Tanggal Pemberkatan" name="tanggal_nikah" required />

            <div class="md:col-span-2">
                <x-form-input label="Tempat / Gedung Gereja" name="tempat_nikah" required />
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pendeta Pelayan</label>
                <select name="pendeta_pelayan" required class="w-full border-slate-300 rounded text-sm select2">
                    <option value="">-- Pilih Pendeta --</option>
                    @foreach($pendetas as $pd)
                        <option value="{{ $pd->nama_lengkap }}">{{ $pd->nama_lengkap }}</option>
                    @endforeach
                </select>
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