@extends('admin.layout')

@section('title', 'Tambah Pegawai')
@section('header-title', 'Registrasi Pegawai Baru')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6 md:p-8">
    <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-3">Formulir Data Pegawai</h2>

    {{-- Tampilkan Error Validasi (Agar ketahuan jika gagal simpan) --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Terjadi Kesalahan!</strong>
            <ul class="mt-1 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.kepegawaian.pegawai.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Identitas --}}
            <div class="col-span-2">
                <h3 class="text-sm font-bold text-blue-600 uppercase mb-3">Identitas Utama</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" placeholder="Tanpa gelar">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIPG <span class="text-red-500">*</span></label>
                <input type="text" name="nipg" value="{{ old('nipg') }}" required class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" placeholder="Nomor Induk Pegawai Gereja">
                <p class="text-xs text-gray-500 mt-1">NIPG akan menjadi password default login.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gelar Depan</label>
                <input type="text" name="gelar_depan" value="{{ old('gelar_depan') }}" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" placeholder="Pdt, Dr, dll">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gelar Belakang</label>
                <input type="text" name="gelar_belakang" value="{{ old('gelar_belakang') }}" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" placeholder="S.Th, M.Si, dll">
            </div>

            {{-- Kepegawaian --}}
            <div class="col-span-2 mt-4">
                <h3 class="text-sm font-bold text-blue-600 uppercase mb-3">Status Kepegawaian</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pegawai <span class="text-red-500">*</span></label>
                <select name="jenis_pegawai" id="jenis_pegawai" required class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    <option value="">-- Pilih --</option>
                    <option value="Pendeta" {{ old('jenis_pegawai') == 'Pendeta' ? 'selected' : '' }}>Pendeta</option>
                    <option value="Pengajar" {{ old('jenis_pegawai') == 'Pengajar' ? 'selected' : '' }}>Pengajar</option>
                    <option value="Pegawai Kantor" {{ old('jenis_pegawai') == 'Pegawai Kantor' ? 'selected' : '' }}>Pegawai Kantor</option>
                    <option value="Koster" {{ old('jenis_pegawai') == 'Koster' ? 'selected' : '' }}>Koster / Tuagama</option>
                    <option value="Lainnya" {{ old('jenis_pegawai') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Kepegawaian <span class="text-red-500">*</span></label>
                <select name="status_kepegawaian" required class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    <option value="Organik" {{ old('status_kepegawaian') == 'Organik' ? 'selected' : '' }}>Organik (Tetap)</option>
                    <option value="Kontrak" {{ old('status_kepegawaian') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                    <option value="Honorer" {{ old('status_kepegawaian') == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">TMT Pegawai</label>
                <input type="date" name="tmt_pegawai" value="{{ old('tmt_pegawai') }}" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Diri</label>
                <input type="file" name="foto_diri" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            {{-- Tambahan Khusus Pendeta (Hidden by default) --}}
            <div id="field_pendeta" class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 hidden bg-blue-50 p-4 rounded-md border border-blue-100">
                <div class="col-span-2">
                    <h3 class="text-sm font-bold text-blue-600 uppercase">Data Tahbisan (Wajib untuk Pendeta)</h3>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tahbisan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_tahbisan" id="input_tgl_tahbisan" value="{{ old('tanggal_tahbisan') }}" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Tahbisan <span class="text-red-500">*</span></label>
                    <input type="text" name="tempat_tahbisan" id="input_tempat_tahbisan" value="{{ old('tempat_tahbisan') }}" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" placeholder="Nama Jemaat/Gedung Gereja">
                </div>
            </div>

            {{-- Penempatan --}}
            <div class="col-span-2 mt-4">
                <h3 class="text-sm font-bold text-blue-600 uppercase mb-3">Lokasi Penempatan Awal</h3>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Klasis</label>
                <select name="klasis_id" id="klasis_id" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" onchange="loadJemaat(this.value)">
                    <option value="">-- Pilih Klasis --</option>
                    @foreach($klasisList as $k)
                        <option value="{{ $k->id }}" {{ old('klasis_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_klasis }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jemaat</label>
                <select name="jemaat_id" id="jemaat_id" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    <option value="">-- Pilih Klasis Dulu --</option>
                </select>
            </div>

            {{-- Data Pribadi --}}
            <div class="col-span-2 mt-4">
                <h3 class="text-sm font-bold text-blue-600 uppercase mb-3">Data Pribadi Lainnya</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                <select name="status_pernikahan" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    <option value="Belum Menikah" {{ old('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                    <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                    <option value="Janda/Duda" {{ old('status_pernikahan') == 'Janda/Duda' ? 'selected' : '' }}>Janda/Duda</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email (Opsional)</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
            </div>
             <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili</label>
                <textarea name="alamat_domisili" rows="2" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">{{ old('alamat_domisili') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 font-medium">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md hover:bg-blue-800 font-bold shadow-lg">Simpan Data Pegawai</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Logic Dropdown Jemaat
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
                    // Cek jika ada old value untuk auto-select saat validasi gagal
                    let selected = '';
                    @if(old('jemaat_id'))
                        if(j.id == "{{ old('jemaat_id') }}") selected = 'selected';
                    @endif
                    
                    jemaatSelect.innerHTML += `<option value="${j.id}" ${selected}>${j.nama_jemaat}</option>`;
                });
            });
    }

    // Trigger load jemaat jika validasi gagal dan klasis sudah terpilih
    document.addEventListener("DOMContentLoaded", function() {
        let oldKlasis = "{{ old('klasis_id') }}";
        if(oldKlasis) {
            loadJemaat(oldKlasis);
        }
        
        // Jalankan toggle pendeta saat load
        togglePendetaFields();
    });

    // --- LOGIC TAMBAHAN KHUSUS PENDETA ---
    const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
    const fieldPendeta = document.getElementById('field_pendeta');
    const inputTgl = document.getElementById('input_tgl_tahbisan');
    const inputTempat = document.getElementById('input_tempat_tahbisan');

    function togglePendetaFields() {
        if (jenisPegawaiSelect.value === 'Pendeta') {
            fieldPendeta.classList.remove('hidden');
            inputTgl.required = true;
            inputTempat.required = true;
        } else {
            fieldPendeta.classList.add('hidden');
            inputTgl.required = false;
            inputTempat.required = false;
        }
    }

    jenisPegawaiSelect.addEventListener('change', togglePendetaFields);
</script>
@endpush
@endsection