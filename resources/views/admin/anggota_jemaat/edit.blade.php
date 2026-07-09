@extends('layouts.app')

@section('title', 'Pembaruan Buku Induk Anggota')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between border-b-2 border-gray-800 pb-4">
    <div>
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Modifikasi Buku Induk Anggota</h2>
        <p class="text-xs text-gray-600 mt-1">Pembaruan data registrasi atas nama: <strong class="text-gray-900 uppercase">{{ $anggotaJemaat->nama_lengkap }}</strong></p>
    </div>
    <a href="{{ route('admin.anggota-jemaat.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center mt-3 md:mt-0">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
    </a>
</div>

<form action="{{ route('admin.anggota-jemaat.update', $anggotaJemaat->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="space-y-6 max-w-6xl mx-auto">
        
        {{-- SECTION 1: DATA PRIBADI --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-id-badge mr-2 text-blue-800"></i> I. Identitas Diri & Demografi</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nama Lengkap (Sesuai KTP) <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $anggotaJemaat->nama_lengkap) }}" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 uppercase">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomor Induk Kependudukan (NIK)</label>
                    <input type="text" name="nik" value="{{ old('nik', $anggotaJemaat->nik) }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white font-mono">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomor Register Buku Induk Jemaat</label>
                    <input type="text" name="nomor_buku_induk" value="{{ old('nomor_buku_induk', $anggotaJemaat->nomor_buku_induk) }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white font-mono">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tempat Kelahiran</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $anggotaJemaat->tempat_lahir) }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tanggal Kelahiran</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($anggotaJemaat->tanggal_lahir)->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Jenis Kelamin Sipil <span class="text-red-600">*</span></label>
                    <select name="jenis_kelamin" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="Laki-laki" {{ old('jenis_kelamin', $anggotaJemaat->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $anggotaJemaat->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Golongan Darah</label>
                    <select name="golongan_darah" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="-">- Kosong / Bebas -</option>
                        @foreach(['A','B','AB','O'] as $g)
                            <option value="{{ $g }}" {{ old('golongan_darah', $anggotaJemaat->golongan_darah) == $g ? 'selected' : '' }}>Tipe {{ $g }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Status Disabilitas Fisik/Mental</label>
                    <select name="disabilitas" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        @foreach(['Tidak Ada','Tuna Netra','Tuna Daksa','Tuna Rungu/Wicara','Lainnya'] as $d)
                            <option value="{{ $d }}" {{ old('disabilitas', $anggotaJemaat->disabilitas) == $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Status Perkawinan Sipil/Gereja</label>
                    <select name="status_pernikahan" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        @foreach(['Belum Menikah','Menikah','Cerai Hidup','Cerai Mati'] as $s)
                            <option value="{{ $s }}" {{ old('status_pernikahan', $anggotaJemaat->status_pernikahan) == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">No. Telepon / Ponsel Aktif</label>
                    <input type="text" name="telepon" value="{{ old('telepon', $anggotaJemaat->telepon) }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white font-mono">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Alamat Lengkap / Domisili Domestik</label>
                    <textarea name="alamat_lengkap" rows="2" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">{{ old('alamat_lengkap', $anggotaJemaat->alamat_lengkap) }}</textarea>
                </div>
            </div>
        </div>

        {{-- SECTION 2: KEANGGOTAAN GEREJA --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm border-t-4 border-t-blue-800">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-church mr-2 text-blue-800"></i> II. Administrasi Organisasi Jemaat</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Instansi Jemaat Terdaftar <span class="text-red-600">*</span></label>
                    <select name="jemaat_id" class="w-full border border-gray-300 rounded text-sm shadow-sm font-bold {{ !Auth::user()->hasRole('Super Admin') ? 'bg-gray-100 text-gray-500 cursor-not-allowed pointer-events-none' : 'bg-white' }}" {{ !Auth::user()->hasRole('Super Admin') ? 'readonly' : '' }}>
                        @foreach ($jemaatOptions as $id => $nama)
                            <option value="{{ $id }}" {{ $anggotaJemaat->jemaat_id == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                    @if(!Auth::user()->hasRole('Super Admin')) <input type="hidden" name="jemaat_id" value="{{ $anggotaJemaat->jemaat_id }}"> @endif
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Status Keanggotaan Aktif <span class="text-red-600">*</span></label>
                    <select name="status_keanggotaan" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="Aktif" {{ $anggotaJemaat->status_keanggotaan == 'Aktif' ? 'selected' : '' }}>Aktif Mengikuti</option>
                        <option value="Tidak Aktif" {{ $anggotaJemaat->status_keanggotaan == 'Tidak Aktif' ? 'selected' : '' }}>Pasif / Tidak Aktif</option>
                        <option value="Pindah" {{ $anggotaJemaat->status_keanggotaan == 'Pindah' ? 'selected' : '' }}>Pindah Wilayah</option>
                        <option value="Meninggal" {{ $anggotaJemaat->status_keanggotaan == 'Meninggal' ? 'selected' : '' }}>Meninggal Dunia</option>
                    </select>
                </div>

                <div class="bg-blue-50 border border-blue-200 px-3 py-2 rounded">
                    <label class="block text-[10px] font-bold text-blue-900 uppercase mb-1">Nomor Registrasi Kartu Keluarga (KK)</label>
                    <input type="text" name="nomor_kk" value="{{ old('nomor_kk', $anggotaJemaat->nomor_kk) }}" class="w-full border border-blue-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 font-mono shadow-sm bg-white">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Sektor Wilayah Pelayanan</label>
                    <input type="text" name="sektor_pelayanan" value="{{ old('sektor_pelayanan', $anggotaJemaat->sektor_pelayanan) }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Unit Pembinaan Khusus</label>
                    <input type="text" name="unit_pelayanan" value="{{ old('unit_pelayanan', $anggotaJemaat->unit_pelayanan) }}" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                </div>

                <div class="lg:col-span-3 border-t border-gray-200 pt-4 mt-2">
                    <h5 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4">Administrasi Sakramen Rekaman</h5>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div><label class="block text-[9px] font-bold text-gray-500 uppercase mb-1">Tgl Baptis Air</label><input type="date" name="tanggal_baptis" value="{{ old('tanggal_baptis', optional($anggotaJemaat->tanggal_baptis)->format('Y-m-d')) }}" class="w-full border-gray-300 rounded text-xs"></div>
                        <div><label class="block text-[9px] font-bold text-gray-500 uppercase mb-1">Lokasi Baptis</label><input type="text" name="tempat_baptis" value="{{ old('tempat_baptis', $anggotaJemaat->tempat_baptis) }}" class="w-full border-gray-300 rounded text-xs"></div>
                        <div><label class="block text-[9px] font-bold text-gray-500 uppercase mb-1">Tgl Peneguhan Sidi</label><input type="date" name="tanggal_sidi" value="{{ old('tanggal_sidi', optional($anggotaJemaat->tanggal_sidi)->format('Y-m-d')) }}" class="w-full border-gray-300 rounded text-xs"></div>
                        <div><label class="block text-[9px] font-bold text-gray-500 uppercase mb-1">Lokasi Sidi</label><input type="text" name="tempat_sidi" value="{{ old('tempat_sidi', $anggotaJemaat->tempat_sidi) }}" class="w-full border-gray-300 rounded text-xs"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: HUBUNGAN KELUARGA --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm border-t-4 border-t-green-700">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-sitemap mr-2 text-green-700"></i> III. Struktur Silsilah & Kartu Keluarga</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 border border-gray-200 rounded">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-2">Tautan Data Biologis Ayah</label>
                    <select name="ayah_id" id="select-ayah" class="w-full"></select>
                    <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $anggotaJemaat->nama_ayah) }}" class="w-full mt-3 border border-gray-300 rounded text-xs p-2 shadow-sm" placeholder="Atau ketik nama ayah secara manual jika tidak terdata...">
                </div>
                <div class="bg-gray-50 p-4 border border-gray-200 rounded">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-2">Tautan Data Biologis Ibu</label>
                    <select name="ibu_id" id="select-ibu" class="w-full"></select>
                    <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $anggotaJemaat->nama_ibu) }}" class="w-full mt-3 border border-gray-300 rounded text-xs p-2 shadow-sm" placeholder="Atau ketik nama ibu secara manual jika tidak terdata...">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-2">Kedudukan & Status Dalam Kartu Keluarga (KK) <span class="text-red-600">*</span></label>
                    <select name="status_dalam_keluarga" required class="w-full border border-gray-300 rounded text-sm focus:ring-green-700 focus:border-green-700 shadow-sm bg-white font-bold">
                        @foreach(['Kepala Keluarga','Istri','Anak','Famili Lain'] as $s)
                            <option value="{{ $s }}" {{ old('status_dalam_keluarga', $anggotaJemaat->status_dalam_keluarga) == $s ? 'selected' : '' }}>{{ strtoupper($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- SECTION 4: ANALISIS RENSTRA --}}
        <div class="bg-gray-100 border border-gray-300 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-300 pb-2 mb-4"><i class="fas fa-chart-pie mr-2 text-gray-600"></i> IV. Analisis Kesejahteraan & Kapabilitas Digital (Renstra)</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Status Konstruksi Rumah</label>
                    <select name="kondisi_rumah" class="w-full border border-gray-300 rounded text-sm shadow-sm bg-white">
                        @foreach(['Permanen','Semi-Permanen','Darurat/Kayu'] as $kr)
                            <option value="{{ $kr }}" {{ old('kondisi_rumah', $anggotaJemaat->kondisi_rumah) == $kr ? 'selected' : '' }}>{{ strtoupper($kr) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Legalitas Kepemilikan Rumah</label>
                    <select name="status_kepemilikan_rumah" class="w-full border border-gray-300 rounded text-sm shadow-sm bg-white">
                        @foreach(['Milik Sendiri','Sewa','Menumpang','Dinas'] as $sr)
                            <option value="{{ $sr }}" {{ old('status_kepemilikan_rumah', $anggotaJemaat->status_kepemilikan_rumah) == $sr ? 'selected' : '' }}>{{ strtoupper($sr) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Estimasi Pengeluaran Rutin</label>
                    <select name="rentang_pengeluaran" class="w-full border border-gray-300 rounded text-sm shadow-sm bg-white">
                        @foreach(['< 1jt','1jt - 3jt','> 3jt'] as $rp)
                            <option value="{{ $rp }}" {{ old('rentang_pengeluaran', $anggotaJemaat->rentang_pengeluaran) == $rp ? 'selected' : '' }}>{{ strtoupper($rp) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Pekerjaan / Mata Pencaharian</label>
                    <input type="text" name="pekerjaan_utama" value="{{ old('pekerjaan_utama', $anggotaJemaat->pekerjaan_utama) }}" class="w-full border border-gray-300 rounded text-sm shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Kepemilikan Perangkat Cerdas (Smartphone)</label>
                    <select name="punya_smartphone" class="w-full border border-gray-300 rounded text-sm shadow-sm bg-white">
                        <option value="0" {{ old('punya_smartphone', $anggotaJemaat->punya_smartphone) == 0 ? 'selected' : '' }}>TIDAK MEMILIKI</option>
                        <option value="1" {{ old('punya_smartphone', $anggotaJemaat->punya_smartphone) == 1 ? 'selected' : '' }}>YA, MEMILIKI</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Akses Jaringan Internet Domestik</label>
                    <select name="akses_internet" class="w-full border border-gray-300 rounded text-sm shadow-sm bg-white">
                        <option value="0" {{ old('akses_internet', $anggotaJemaat->akses_internet) == 0 ? 'selected' : '' }}>TIDAK TERSEDIA</option>
                        <option value="1" {{ old('akses_internet', $anggotaJemaat->akses_internet) == 1 ? 'selected' : '' }}>YA, TERSEDIA</option>
                    </select>
                </div>
            </div>

            {{-- Aset Ekonomi --}}
            <div class="mt-4 p-4 bg-white border border-gray-300 rounded">
                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-3 border-b border-gray-100 pb-2">Potensi Ekonomi Produktif & Aset Alam</label>
                <div class="flex flex-wrap gap-4">
                    @php 
                        $asetArray = $anggotaJemaat->aset_ekonomi ? explode(', ', $anggotaJemaat->aset_ekonomi) : [];
                    @endphp
                    @foreach(['Perkebunan', 'Peternakan', 'Perikanan', 'Kehutanan', 'Usaha Mikro', 'Transportasi'] as $aset)
                    <label class="inline-flex items-center cursor-pointer p-2 bg-gray-50 border border-gray-200 rounded hover:bg-gray-100 transition">
                        <input type="checkbox" name="aset_ekonomi[]" value="{{ $aset }}" class="rounded border-gray-400 text-gray-800 focus:ring-gray-800 h-4 w-4" {{ in_array($aset, $asetArray) ? 'checked' : '' }}>
                        <span class="ml-2 text-xs font-bold text-gray-700 uppercase tracking-widest">{{ $aset }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 pb-10">
            <button type="submit" class="px-8 py-3 bg-gray-800 text-white text-xs font-bold uppercase tracking-widest rounded shadow-sm hover:bg-gray-900 transition flex items-center">
                <i class="fas fa-save mr-2"></i> Perbarui Pangkalan Data
            </button>
        </div>

    </div>
</form>

{{-- SCRIPTS SELECT2 --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        function initSelect2(id, initialData) {
            $(id).select2({
                placeholder: '-- Pencarian Biodata Pangkalan Data --',
                ajax: {
                    url: "{{ route('admin.anggota-jemaat.search') }}",
                    dataType: 'json',
                    delay: 350,
                    data: function (params) { return { q: params.term }; },
                    processResults: function (data) { return { results: data }; },
                    cache: true
                }
            });
            if(initialData) {
                var option = new Option(initialData.text, initialData.id, true, true);
                $(id).append(option).trigger('change');
            }
        }

        @if($anggotaJemaat->ayah_id)
            initSelect2('#select-ayah', { id: '{{ $anggotaJemaat->ayah_id }}', text: '{{ $anggotaJemaat->ayah->nama_lengkap }}' });
        @else
            initSelect2('#select-ayah');
        @endif

        @if($anggotaJemaat->ibu_id)
            initSelect2('#select-ibu', { id: '{{ $anggotaJemaat->ibu_id }}', text: '{{ $anggotaJemaat->ibu->nama_lengkap }}' });
        @else
            initSelect2('#select-ibu');
        @endif
    });
</script>
@endpush
@push('styles')
<style>
    .select2-container .select2-selection--single { height: 42px !important; border-color: #d1d5db !important; border-radius: 0.25rem !important; display: flex; align-items: center; background-color: #f9fafb;}
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px !important; }
</style>
@endpush
@endsection