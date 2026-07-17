@extends('layouts.app')

@section('title', 'Pembaruan Data Pengurus Kategorial')

@section('content')
<div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between border-b-2 border-gray-800 pb-4">
    <div>
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Pembaruan Data Pengurus</h2>
        <p class="text-xs text-gray-600 mt-1">Penyuntingan arsip personel untuk jabatan: <span class="font-bold text-gray-900 uppercase">{{ $pengurus->jabatan }}</span></p>
    </div>
    <a href="{{ route('admin.wadah.pengurus.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center mt-3 md:mt-0">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
    </a>
</div>

<form action="{{ route('admin.wadah.pengurus.update', $pengurus->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="space-y-6 max-w-5xl mx-auto">
        
        {{-- PANEL I: KEDUDUKAN STRUKTURAL --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-sitemap mr-2 text-blue-800"></i> I. Kedudukan & Wilayah Kerja</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="jenis_wadah_id" class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Jenis Wadah Kategorial <span class="text-red-600">*</span></label>
                    <select id="jenis_wadah_id" name="jenis_wadah_id" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" required>
                        <option value="">-- Tentukan Wadah Organisasi --</option>
                        @foreach($jenisWadahs as $wadah)
                            <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id', $pengurus->jenis_wadah_id) == $wadah->id ? 'selected' : '' }}>
                                {{ strtoupper($wadah->nama_wadah) }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_wadah_id') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tingkat" class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Hierarki Kepengurusan <span class="text-red-600">*</span></label>
                    <select id="tingkat" name="tingkat" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" required onchange="handleTingkatChange()">
                        <option value="sinode" {{ old('tingkat', $pengurus->tingkat) == 'sinode' ? 'selected' : '' }}>TINGKAT PUSAT SINODE</option>
                        <option value="klasis" {{ old('tingkat', $pengurus->tingkat) == 'klasis' ? 'selected' : '' }}>TINGKAT KLASIS</option>
                        <option value="jemaat" {{ old('tingkat', $pengurus->tingkat) == 'jemaat' ? 'selected' : '' }}>TINGKAT JEMAAT</option>
                    </select>
                    @error('tingkat') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div id="div_klasis" class="{{ $pengurus->tingkat == 'sinode' ? 'hidden' : '' }}">
                    <label for="klasis_id" class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Teritorial Klasis <span class="text-red-600">*</span></label>
                    <select id="klasis_id" name="klasis_id" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" onchange="loadJemaat(this.value)">
                        <option value="">-- Pilihan Klasis --</option>
                        @foreach($klasisList as $klasis)
                            <option value="{{ $klasis->id }}" {{ old('klasis_id', $pengurus->klasis_id) == $klasis->id ? 'selected' : '' }}>
                                {{ strtoupper($klasis->nama_klasis) }}
                            </option>
                        @endforeach
                    </select>
                    @error('klasis_id') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div id="div_jemaat" class="{{ $pengurus->tingkat == 'jemaat' ? '' : 'hidden' }}">
                    <label for="jemaat_id" class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Lokal Jemaat <span class="text-red-600">*</span></label>
                    <select id="jemaat_id" name="jemaat_id" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">-- Menunggu Pilihan Klasis --</option>
                        @foreach($jemaatList as $jemaat)
                            <option value="{{ $jemaat->id }}" {{ old('jemaat_id', $pengurus->jemaat_id) == $jemaat->id ? 'selected' : '' }}>
                                {{ strtoupper($jemaat->nama_jemaat) }}
                            </option>
                        @endforeach
                    </select>
                    @error('jemaat_id') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- PANEL II: PERSONAL & SK --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm border-t-4 border-t-green-700">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-id-badge mr-2 text-green-700"></i> II. Identitas Personal & Legalitas SK</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="jabatan" class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nama Jabatan Organisasi <span class="text-red-600">*</span></label>
                    <input id="jabatan" type="text" name="jabatan" value="{{ old('jabatan', $pengurus->jabatan) }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 uppercase" />
                    @error('jabatan') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="anggota_jemaat_id" class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tautan Pangkalan Data Sensus (Opsional)</label>
                    <input id="anggota_jemaat_id" type="number" name="anggota_jemaat_id" value="{{ old('anggota_jemaat_id', $pengurus->anggota_jemaat_id) }}" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white font-mono text-xs" />
                    @if($pengurus->anggotaJemaat)
                        <p class="text-[9px] font-bold text-blue-800 mt-2 uppercase tracking-widest bg-blue-50 border border-blue-200 p-1 inline-block rounded"><i class="fas fa-check-circle mr-1"></i> Terhubung: {{ $pengurus->anggotaJemaat->nama_lengkap }}</p>
                    @endif
                    @error('anggota_jemaat_id') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="nomor_sk" class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomor Surat Keputusan (SK)</label>
                    <input id="nomor_sk" type="text" name="nomor_sk" value="{{ old('nomor_sk', $pengurus->nomor_sk) }}" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white font-mono text-xs" />
                </div>
                <div>
                    <label for="periode_mulai" class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tanggal Berlaku Jabatan <span class="text-red-600">*</span></label>
                    <input id="periode_mulai" type="date" name="periode_mulai" value="{{ old('periode_mulai', $pengurus->periode_mulai ? $pengurus->periode_mulai->format('Y-m-d') : '') }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50" />
                </div>
                <div>
                    <label for="periode_selesai" class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tanggal Berakhir Jabatan <span class="text-red-600">*</span></label>
                    <input id="periode_selesai" type="date" name="periode_selesai" value="{{ old('periode_selesai', $pengurus->periode_selesai ? $pengurus->periode_selesai->format('Y-m-d') : '') }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50" />
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <label for="is_active" class="inline-flex items-center cursor-pointer">
                    <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', $pengurus->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-blue-800 focus:ring-blue-800 shadow-sm cursor-pointer">
                    <span class="ml-2 text-xs font-bold text-gray-700 uppercase tracking-widest">Status Personil Aktif / Menjabat</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end pt-4 pb-10">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Perbarui Pemegang Jabatan
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    function handleTingkatChange() {
        const tingkat = document.getElementById('tingkat').value;
        const divKlasis = document.getElementById('div_klasis');
        const divJemaat = document.getElementById('div_jemaat');
        const inputKlasis = document.getElementById('klasis_id');
        const inputJemaat = document.getElementById('jemaat_id');

        divKlasis.classList.add('hidden');
        divJemaat.classList.add('hidden');
        inputKlasis.removeAttribute('required');
        inputJemaat.removeAttribute('required');

        if (tingkat === 'klasis') {
            divKlasis.classList.remove('hidden');
            inputKlasis.setAttribute('required', 'required');
        } else if (tingkat === 'jemaat') {
            divKlasis.classList.remove('hidden');
            divJemaat.classList.remove('hidden');
            inputKlasis.setAttribute('required', 'required');
            inputJemaat.setAttribute('required', 'required');
        }
    }

    function loadJemaat(klasisId) {
        const tingkat = document.getElementById('tingkat').value;
        if (tingkat !== 'jemaat') return; 
        
        const jemaatSelect = document.getElementById('jemaat_id');
        const currentJemaatId = "{{ $pengurus->jemaat_id }}"; 

        jemaatSelect.innerHTML = '<option value="">Memuat Pangkalan Data...</option>';

        if (klasisId) {
            fetch(`/api/jemaat-by-klasis/${klasisId}`)
                .then(response => response.json())
                .then(data => {
                    jemaatSelect.innerHTML = '<option value="">-- Pilihan Unit Jemaat --</option>';
                    data.forEach(jemaat => {
                        const selected = jemaat.id == currentJemaatId ? 'selected' : '';
                        jemaatSelect.innerHTML += `<option value="${jemaat.id}" ${selected}>${jemaat.nama_jemaat}</option>`;
                    });
                })
                .catch(error => {
                    jemaatSelect.innerHTML = '<option value="">Gagal merespon jaringan</option>';
                });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        handleTingkatChange();
    });
</script>
@endpush
@endsection