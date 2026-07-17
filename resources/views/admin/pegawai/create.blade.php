@extends('layouts.app')

@section('title', 'Registrasi Personel Baru')

@section('content')
<div class="mb-6 flex items-center justify-between border-b-2 border-gray-800 pb-4">
    <div>
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Registrasi Personel Baru</h2>
        <p class="text-xs text-gray-600 mt-1">Formulir pendaftaran Buku Induk Kepegawaian (Pendeta & Staff).</p>
    </div>
    <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
    </a>
</div>

<form action="{{ route('admin.kepegawaian.pegawai.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="space-y-6">

        {{-- PANEL I: IDENTITAS DASAR --}}
        <div class="bg-white border border-gray-200 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">I. Informasi Identitas Diri</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Gelar Depan</label>
                    <input type="text" name="gelar_depan" value="{{ old('gelar_depan') }}" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50" placeholder="Pdt. / Dr.">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Nama Lengkap <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm">
                    @error('nama_lengkap') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Gelar Belakang</label>
                    <input type="text" name="gelar_belakang" value="{{ old('gelar_belakang') }}" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50" placeholder="S.Th. / M.Mis.">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">NIP / NIPG <span class="text-red-600">*</span></label>
                    <input type="text" name="nipg" value="{{ old('nipg') }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm" placeholder="Nomor Induk Pegawai Gereja">
                    <p class="text-[10px] text-gray-500 mt-1">NIPG akan menjadi Password Default (Awal) saat Login.</p>
                    @error('nipg') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">NIK KTP Nasional</label>
                    <input type="text" name="nik_ktp" value="{{ old('nik_ktp') }}" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Tanggal Lahir <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                    @error('tanggal_lahir') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">- Pilih Gender -</option>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Status Pernikahan</label>
                    <select name="status_pernikahan" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">- Pilih Status -</option>
                        <option value="Belum Menikah" {{ old('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                        <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                        <option value="Cerai Hidup" {{ old('status_pernikahan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                        <option value="Cerai Mati" {{ old('status_pernikahan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Golongan Darah</label>
                    <select name="golongan_darah" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">- Pilih Darah -</option>
                        @foreach(['A', 'B', 'AB', 'O'] as $goldar)
                            <option value="{{ $goldar }}" {{ old('golongan_darah') == $goldar ? 'selected' : '' }}>{{ $goldar }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- PANEL II: KEDINASAN --}}
        <div class="bg-white border border-gray-200 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">II. Penugasan & Kedinasan</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Jenis Pegawai <span class="text-red-600">*</span></label>
                    <select id="jenis_pegawai" name="jenis_pegawai" onchange="togglePendetaFields()" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" required>
                        <option value="">- Klasifikasi Pegawai -</option>
                        <option value="Pendeta" {{ old('jenis_pegawai') == 'Pendeta' ? 'selected' : '' }}>Pelayan Firman (Pendeta)</option>
                        <option value="Pengajar" {{ old('jenis_pegawai') == 'Pengajar' ? 'selected' : '' }}>Guru Agama / Pengajar</option>
                        <option value="Pegawai Kantor" {{ old('jenis_pegawai') == 'Pegawai Kantor' ? 'selected' : '' }}>Pegawai Kantor Sinode</option>
                        <option value="Staff Khusus" {{ old('jenis_pegawai') == 'Staff Khusus' ? 'selected' : '' }}>Staff Khusus / Honorer</option>
                    </select>
                    @error('jenis_pegawai') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Status Kepegawaian <span class="text-red-600">*</span></label>
                    <select name="status_kepegawaian" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" required>
                        <option value="Organik" {{ old('status_kepegawaian') == 'Organik' ? 'selected' : '' }}>Organik / Tetap</option>
                        <option value="Kontrak" {{ old('status_kepegawaian') == 'Kontrak' ? 'selected' : '' }}>Kontrak / Honorer</option>
                        <option value="Titipan" {{ old('status_kepegawaian') == 'Titipan' ? 'selected' : '' }}>Titipan (Instansi Lain)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Status Aktif Kedinasan <span class="text-red-600">*</span></label>
                    <select name="status_aktif" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" required>
                        <option value="Aktif" {{ old('status_aktif') == 'Aktif' ? 'selected' : '' }}>Berstatus Aktif</option>
                        <option value="Tugas Belajar" {{ old('status_aktif') == 'Tugas Belajar' ? 'selected' : '' }}>Tugas Belajar</option>
                        <option value="Emeritus" {{ old('status_aktif') == 'Emeritus' ? 'selected' : '' }}>Pensiun (Emeritus)</option>
                        <option value="Cuti" {{ old('status_aktif') == 'Cuti' ? 'selected' : '' }}>Cuti Diluar Tanggungan</option>
                        <option value="Diberhentikan" {{ old('status_aktif') == 'Diberhentikan' ? 'selected' : '' }}>Diberhentikan</option>
                    </select>
                </div>
            </div>

            <div id="field_pendeta" class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded hidden">
                <h5 class="text-xs font-bold text-gray-700 uppercase mb-4"><i class="fas fa-cross text-blue-800 mr-2"></i> Khusus Pendeta (Tahbisan)</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-2">Tanggal Tahbisan <span class="text-red-600">*</span></label>
                        <input type="date" name="tanggal_tahbisan" value="{{ old('tanggal_tahbisan') }}" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        @error('tanggal_tahbisan') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-2">Tempat Lokasi Tahbisan <span class="text-red-600">*</span></label>
                        <input type="text" name="tempat_tahbisan" value="{{ old('tempat_tahbisan') }}" placeholder="Contoh: Jemaat GKI Siloam..."
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        @error('tempat_tahbisan') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 border-t border-gray-200 pt-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Penempatan Klasis</label>
                    <select id="klasis_id" name="klasis_id" onchange="loadJemaat(this.value)" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">- Diperbantukan Sinode (Pusat) -</option>
                        @foreach(App\Models\Klasis::all() as $klasis)
                            <option value="{{ $klasis->id }}" {{ old('klasis_id') == $klasis->id ? 'selected' : '' }}>{{ $klasis->nama_klasis }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Penempatan Jemaat</label>
                    <select id="jemaat_id" name="jemaat_id" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">- Silakan Pilih Klasis Dulu -</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- PANEL III: KONTAK & MEDIA --}}
        <div class="bg-white border border-gray-200 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">III. Kontak & Foto Diri</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Email (Akses Sistem)</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                        @error('email') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Alamat Domisili Tetap</label>
                        <textarea name="alamat_domisili" rows="3" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">{{ old('alamat_domisili') }}</textarea>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Pas Foto Resmi / Profil</label>
                    <div class="bg-gray-50 border border-gray-300 border-dashed rounded p-4 text-center">
                        <input type="file" name="foto_diri" accept="image/*" class="w-full text-xs text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-gray-200 file:text-gray-800 cursor-pointer">
                        <p class="text-[10px] text-gray-500 mt-2">Maksimal 2MB (Rasio 3:4 atau Square).</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Daftarkan Personel
            </button>
        </div>

    </div>
</form>

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
        jemaatSelect.innerHTML = '<option value="">Memuat Pangkalan Data...</option>';
        if(!klasisId) {
            jemaatSelect.innerHTML = '<option value="">- Silakan Pilih Klasis Dulu -</option>';
            return;
        }
        fetch(`/api/jemaat-by-klasis/${klasisId}`)
            .then(res => res.json())
            .then(data => {
                jemaatSelect.innerHTML = '<option value="">-- Tentukan Jemaat --</option>';
                data.forEach(j => {
                    jemaatSelect.innerHTML += `<option value="${j.id}">${j.nama_jemaat}</option>`;
                });
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        togglePendetaFields();
        if(document.getElementById('klasis_id').value) {
            loadJemaat(document.getElementById('klasis_id').value);
        }
    });
</script>
@endpush
@endsection