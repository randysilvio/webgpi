@extends('admin.layout')

@section('title', 'Edit Data Pegawai')
@section('header-title', 'Edit Data Pegawai')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6 md:p-8">
    <div class="flex justify-between items-center mb-6 border-b pb-3">
        <h2 class="text-xl font-bold text-gray-800">Edit Profil: {{ $pegawai->nama_lengkap }}</h2>
        <a href="{{ route('admin.kepegawaian.pegawai.show', $pegawai->id) }}" class="text-sm text-gray-500 hover:text-gray-700">
            &larr; Kembali ke Profil
        </a>
    </div>

    <form action="{{ route('admin.kepegawaian.pegawai.update', $pegawai->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            
            {{-- Kolom Kiri: Identitas --}}
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-blue-600 uppercase border-b pb-1">Identitas Dasar</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $pegawai->nama_lengkap) }}" required class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIPG (Nomor Induk)</label>
                    <input type="text" name="nipg" value="{{ old('nipg', $pegawai->nipg) }}" required class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary bg-gray-100" readonly>
                    <p class="text-xs text-gray-500 mt-1">NIPG tidak dapat diubah sembarangan.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gelar Depan</label>
                        <input type="text" name="gelar_depan" value="{{ old('gelar_depan', $pegawai->gelar_depan) }}" class="w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gelar Belakang</label>
                        <input type="text" name="gelar_belakang" value="{{ old('gelar_belakang', $pegawai->gelar_belakang) }}" class="w-full border-gray-300 rounded-md">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email (Login)</label>
                    <input type="email" name="email" value="{{ old('email', $pegawai->email) }}" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                </div>
            </div>

            {{-- Kolom Kanan: Kepegawaian & Foto --}}
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-blue-600 uppercase border-b pb-1">Status & Foto</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pegawai</label>
                        <select name="jenis_pegawai" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                            @foreach(['Pendeta', 'Pengajar', 'Pegawai Kantor', 'Koster', 'Lainnya'] as $jenis)
                                <option value="{{ $jenis }}" {{ $pegawai->jenis_pegawai == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Kepegawaian</label>
                        <select name="status_kepegawaian" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                            @foreach(['Organik', 'Kontrak', 'Honorer'] as $status)
                                <option value="{{ $status }}" {{ $pegawai->status_kepegawaian == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Aktif</label>
                    <select name="status_aktif" class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary bg-yellow-50">
                        @foreach(['Aktif', 'Cuti', 'Tugas Belajar', 'Pensiun', 'Meninggal', 'Diberhentikan'] as $sa)
                            <option value="{{ $sa }}" {{ $pegawai->status_aktif == $sa ? 'selected' : '' }}>{{ $sa }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-start space-x-4 pt-2">
                    <div class="flex-shrink-0">
                        @if($pegawai->foto_diri)
                            <img src="{{ Storage::url($pegawai->foto_diri) }}" alt="Foto Lama" class="w-20 h-20 rounded object-cover border">
                        @else
                            <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center text-gray-400"><i class="fas fa-user"></i></div>
                        @endif
                        <p class="text-xs text-center mt-1 text-gray-500">Foto Saat Ini</p>
                    </div>
                    <div class="flex-grow">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto</label>
                        <input type="file" name="foto_diri" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti foto.</p>
                    </div>
                </div>
            </div>

            {{-- Baris Bawah: Lokasi & Pribadi --}}
            <div class="col-span-1 md:col-span-2 mt-4">
                <h3 class="text-sm font-bold text-blue-600 uppercase mb-3 border-b pb-1">Data Pribadi & Penempatan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}" class="w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('Y-m-d') : '') }}" class="w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">TMT Pegawai</label>
                        <input type="date" name="tmt_pegawai" value="{{ old('tmt_pegawai', $pegawai->tmt_pegawai ? $pegawai->tmt_pegawai->format('Y-m-d') : '') }}" class="w-full border-gray-300 rounded-md">
                    </div>
                    
                    {{-- Klasis & Jemaat --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Klasis</label>
                        <select name="klasis_id" id="klasis_id" class="w-full border-gray-300 rounded-md" onchange="loadJemaat(this.value)">
                            <option value="">-- Pilih Klasis --</option>
                            @foreach($klasisList as $k)
                                <option value="{{ $k->id }}" {{ $pegawai->klasis_id == $k->id ? 'selected' : '' }}>{{ $k->nama_klasis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jemaat</label>
                        <select name="jemaat_id" id="jemaat_id" class="w-full border-gray-300 rounded-md">
                            <option value="">-- Pilih Klasis Dulu --</option>
                            @foreach($jemaatList as $j)
                                <option value="{{ $j->id }}" {{ $pegawai->jemaat_id == $j->id ? 'selected' : '' }}>{{ $j->nama_jemaat }}</option>
                            @endforeach
                        </select>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $pegawai->no_hp) }}" class="w-full border-gray-300 rounded-md">
                    </div>
                </div>
                
                <div class="mt-4">
                     <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili</label>
                     <textarea name="alamat_domisili" rows="2" class="w-full border-gray-300 rounded-md">{{ old('alamat_domisili', $pegawai->alamat_domisili) }}</textarea>
                </div>
            </div>

        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 font-medium">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-bold shadow-lg">Simpan Perubahan</button>
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