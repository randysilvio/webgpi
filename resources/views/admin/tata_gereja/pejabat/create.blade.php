@extends('layouts.app')

@section('title', 'Tambah Pejabat')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <x-admin-form 
        title="Registrasi Pejabat Baru" 
        action="{{ route('admin.tata-gereja.pejabat.store') }}" 
        back-route="{{ route('admin.tata-gereja.pejabat.index') }}"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- PILIH ANGGOTA --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                    Nama Warga Jemaat <span class="text-red-500">*</span>
                </label>
                <select name="anggota_jemaat_id" required class="w-full border-slate-300 rounded text-sm select2">
                    <option value="">-- Cari Nama Anggota --</option>
                    @foreach($anggotas as $a)
                        <option value="{{ $a->id }}" {{ old('anggota_jemaat_id') == $a->id ? 'selected' : '' }}>
                            {{ $a->nama_lengkap }} ({{ $a->jemaat->nama_jemaat ?? 'Umum' }}) - {{ $a->umur }} Thn
                        </option>
                    @endforeach
                </select>
                @error('anggota_jemaat_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- JABATAN & STATUS --}}
            <x-form-select label="Jabatan" name="jabatan" required>
                <option value="Penatua">Penatua</option>
                <option value="Diaken">Diaken</option>
                <option value="Pengajar">Pengajar</option>
            </x-form-select>

            <x-form-select label="Status Keaktifan" name="status_aktif" required>
                <option value="Aktif">Aktif</option>
                <option value="Demisioner">Demisioner (Selesai)</option>
                <option value="Emeritus">Emeritus (Pensiun)</option>
                <option value="Non-Aktif">Non-Aktif / Diberhentikan</option>
            </x-form-select>

            {{-- PERIODE --}}
            <div class="grid grid-cols-2 gap-4">
                <x-form-input type="number" label="Periode Mulai" name="periode_mulai" placeholder="Tahun (2022)" required />
                <x-form-input type="number" label="Periode Selesai" name="periode_selesai" placeholder="Tahun (2027)" required />
            </div>

            <div class="md:col-span-1">
                <x-form-input label="Nomor SK Pelantikan" name="no_sk_pelantikan" placeholder="Contoh: 001/SK-MJ/2022" />
            </div>

        </div>
    </x-admin-form>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({ width: '100%', placeholder: "-- Pilih Anggota --" });
        });
    </script>
    @endpush
@endsection