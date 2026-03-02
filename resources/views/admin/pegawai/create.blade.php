@extends('layouts.app')

@section('title', 'Registrasi Pegawai')
@section('header-title', 'Kepegawaian')

@section('content')
<div class="max-w-5xl mx-auto">
    
    <form action="{{ route('admin.kepegawaian.pegawai.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- CARD: INFORMASI DASAR --}}
        <div class="bg-white rounded shadow-sm border border-slate-200 mb-6 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-700 uppercase text-xs tracking-wider">I. Identitas & Profil</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Nama & Gelar --}}
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Gelar Depan</label>
                        <input type="text" name="gelar_depan" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" value="{{ old('gelar_depan') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" value="{{ old('nama') }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Gelar Belakang</label>
                        <input type="text" name="gelar_belakang" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" value="{{ old('gelar_belakang') }}">
                    </div>
                </div>

                {{-- NIK & NIP --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">NIK (KTP)</label>
                    <input type="text" name="nik" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" value="{{ old('nik') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">NIP / NIPG</label>
                    <input type="text" name="nip" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" value="{{ old('nip') }}">
                </div>

                {{-- TTL & Gender --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Tempat, Tanggal Lahir</label>
                    <div class="flex gap-2">
                        <input type="text" name="tempat_lahir" class="w-1/2 border-slate-300 rounded text-sm" placeholder="Kota" value="{{ old('tempat_lahir') }}">
                        <input type="date" name="tanggal_lahir" class="w-1/2 border-slate-300 rounded text-sm" value="{{ old('tanggal_lahir') }}">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                {{-- Foto --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Foto Profil (Opsional)</label>
                    <input type="file" name="foto_profil" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                </div>
            </div>
        </div>

        {{-- CARD: STATUS KEPEGAWAIAN --}}
        <div class="bg-white rounded shadow-sm border border-slate-200 mb-6 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-700 uppercase text-xs tracking-wider">II. Status & Penempatan</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Jenis & Status --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Jenis Kepegawaian <span class="text-red-500">*</span></label>
                    <select name="jenis_pegawai" id="jenis_pegawai" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" onchange="togglePendetaFields()">
                        <option value="Pendeta">Pendeta Organik</option>
                        <option value="Pegawai Kantor">Pegawai Kantor</option>
                        <option value="Pengajar">Tenaga Pengajar</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Status Aktif</label>
                    <select name="status_aktif" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                        <option value="Aktif">Aktif</option>
                        <option value="Cuti">Cuti / Non-Aktif</option>
                        <option value="Pensiun">Pensiun</option>
                    </select>
                </div>

                {{-- Field Khusus Pendeta --}}
                <div id="field_pendeta" class="md:col-span-2 bg-slate-50 p-4 rounded border border-slate-200">
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-3">Data Tahbisan (Khusus Pendeta)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase text-slate-500 mb-1">Tanggal Tahbisan</label>
                            <input type="date" name="tanggal_tahbisan" id="input_tgl_tahbisan" class="w-full border-slate-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase text-slate-500 mb-1">Tempat Tahbisan</label>
                            <input type="text" name="tempat_tahbisan" id="input_tempat_tahbisan" class="w-full border-slate-300 rounded text-sm">
                        </div>
                    </div>
                </div>

                {{-- Lokasi Tugas --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Klasis (Opsional)</label>
                    <select name="klasis_id" id="klasis_id" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" onchange="loadJemaat(this.value)">
                        <option value="">-- Pilih Klasis --</option>
                        @foreach(App\Models\Klasis::all() as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_klasis }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Jemaat (Opsional)</label>
                    <select name="jemaat_id" id="jemaat_id" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                        <option value="">-- Pilih Jemaat --</option>
                    </select>
                </div>

            </div>
        </div>

        {{-- FORM ACTIONS --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white text-sm font-bold uppercase tracking-wide rounded hover:bg-slate-900 transition shadow-lg">
                Simpan Data
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    function togglePendetaFields() {
        const jenis = document.getElementById('jenis_pegawai').value;
        const field = document.getElementById('field_pendeta');
        if (jenis === 'Pendeta') {
            field.classList.remove('hidden');
        } else {
            field.classList.add('hidden');
        }
    }

    function loadJemaat(klasisId) {
        const jemaatSelect = document.getElementById('jemaat_id');
        jemaatSelect.innerHTML = '<option value="">Memuat...</option>';
        
        if(!klasisId) {
            jemaatSelect.innerHTML = '<option value="">-- Pilih Klasis Dulu --</option>';
            return;
        }

        fetch(`/api/jemaat-by-klasis/${klasisId}`)
            .then(response => response.json())
            .then(data => {
                jemaatSelect.innerHTML = '<option value="">-- Pilih Jemaat --</option>';
                data.forEach(j => {
                    jemaatSelect.innerHTML += `<option value="${j.id}">${j.nama_jemaat}</option>`;
                });
            });
    }

    document.addEventListener("DOMContentLoaded", function() {
        togglePendetaFields();
    });
</script>
@endpush
@endsection