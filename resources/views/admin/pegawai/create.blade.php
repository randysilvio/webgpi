@extends('admin.layout')

@section('title', 'Tambah Pegawai')
@section('header-title', 'Registrasi Pegawai Baru')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6 md:p-8">
    <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-3">Formulir Data Pegawai</h2>

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
                <select name="jenis_pegawai" required class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    <option value="">-- Pilih --</option>
                    <option value="Pendeta">Pendeta</option>
                    <option value="Pengajar">Pengajar</option>
                    <option value="Pegawai Kantor">Pegawai Kantor</option>
                    <option value="Koster">Koster / Tuagama</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Kepegawaian <span class="text-red-500">*</span></label>
                <select name="status_kepegawaian" required class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    <option value="Organik">Organik (Tetap)</option>
                    <option value="Kontrak">Kontrak</option>
                    <option value="Honorer">Honorer</option>
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

            {{-- Penempatan --}}
            <div class="col-span-2 mt-4">
                <h3 class="text-sm font-bold text-blue-600 uppercase mb-3">Lokasi Penempatan Awal</h3>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Klasis</label>
                <select name="klasis_id" id="klasis_id" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" onchange="loadJemaat(this.value)">
                    <option value="">-- Pilih Klasis --</option>
                    @foreach($klasisList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_klasis }}</option>
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
                {{-- PERBAIKAN DISINI: Value diubah menjadi 'L' dan 'P' sesuai Database --}}
                <select name="jenis_kelamin" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                <select name="status_pernikahan" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    <option value="Belum Menikah">Belum Menikah</option>
                    <option value="Menikah">Menikah</option>
                    <option value="Janda/Duda">Janda/Duda</option>
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
</script>
@endpush
@endsection