@extends('admin.layout')

@section('title', 'Tambah Pengurus')
@section('header-title', 'Tambah Pengurus Wadah')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg p-6">
        
        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.wadah.pengurus.store') }}">
            @csrf

            <div class="mb-4">
                <label for="jenis_wadah_id" class="block text-sm font-medium text-gray-700 mb-1">Jenis Wadah Kategorial</label>
                <select id="jenis_wadah_id" name="jenis_wadah_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required>
                    <option value="">-- Pilih Wadah --</option>
                    @foreach($jenisWadahs as $wadah)
                        <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>
                            {{ $wadah->nama_wadah }} ({{ $wadah->rentang_usia_min }}-{{ $wadah->rentang_usia_max }} Thn)
                        </option>
                    @endforeach
                </select>
                @error('jenis_wadah_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kepengurusan</label>
                    <select id="tingkat" name="tingkat" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required onchange="handleTingkatChange()">
                        <option value="">-- Pilih Tingkat --</option>
                        <option value="sinode" {{ old('tingkat') == 'sinode' ? 'selected' : '' }}>Sinode</option>
                        <option value="klasis" {{ old('tingkat') == 'klasis' ? 'selected' : '' }}>Klasis</option>
                        <option value="jemaat" {{ old('tingkat') == 'jemaat' ? 'selected' : '' }}>Jemaat</option>
                    </select>
                    @error('tingkat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div id="div_klasis" class="hidden">
                    <label for="klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Klasis</label>
                    <select id="klasis_id" name="klasis_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" onchange="loadJemaat(this.value)">
                        <option value="">-- Pilih Klasis --</option>
                        @foreach($klasisList as $klasis)
                            <option value="{{ $klasis->id }}" {{ old('klasis_id') == $klasis->id ? 'selected' : '' }}>
                                {{ $klasis->nama_klasis }}
                            </option>
                        @endforeach
                    </select>
                    @error('klasis_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div id="div_jemaat" class="mb-4 hidden">
                <label for="jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Jemaat</label>
                <select id="jemaat_id" name="jemaat_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                    <option value="">-- Pilih Klasis Terlebih Dahulu --</option>
                    @foreach($jemaatList as $jemaat)
                        <option value="{{ $jemaat->id }}" {{ old('jemaat_id') == $jemaat->id ? 'selected' : '' }}>
                            {{ $jemaat->nama_jemaat }}
                        </option>
                    @endforeach
                </select>
                @error('jemaat_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <hr class="my-6 border-gray-200">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="anggota_jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">ID Anggota Jemaat (Opsional)</label>
                    <input id="anggota_jemaat_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" type="number" name="anggota_jemaat_id" value="{{ old('anggota_jemaat_id') }}" placeholder="Contoh: 1024" />
                    <p class="text-xs text-gray-500 mt-1">Masukkan ID dari database Anggota Jemaat jika ada.</p>
                    @error('anggota_jemaat_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <input id="jabatan" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" type="text" name="jabatan" value="{{ old('jabatan') }}" required placeholder="Contoh: Ketua, Sekretaris" />
                    @error('jabatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="nomor_sk" class="block text-sm font-medium text-gray-700 mb-1">Nomor SK</label>
                    <input id="nomor_sk" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" type="text" name="nomor_sk" value="{{ old('nomor_sk') }}" />
                </div>
                <div>
                    <label for="periode_mulai" class="block text-sm font-medium text-gray-700 mb-1">Mulai Periode</label>
                    <input id="periode_mulai" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" type="date" name="periode_mulai" value="{{ old('periode_mulai') }}" required />
                </div>
                <div>
                    <label for="periode_selesai" class="block text-sm font-medium text-gray-700 mb-1">Selesai Periode</label>
                    <input id="periode_selesai" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" type="date" name="periode_selesai" value="{{ old('periode_selesai') }}" required />
                </div>
            </div>

            <div class="block mt-4">
                <label for="is_active" class="inline-flex items-center">
                    <input id="is_active" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-600">Status Aktif (Sedang Menjabat)</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-6">
                <a href="{{ route('admin.wadah.pengurus.index') }}" class="text-gray-600 hover:text-gray-900 mr-4 text-sm">Batal</a>
                <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm transition duration-150">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function handleTingkatChange() {
        const tingkat = document.getElementById('tingkat').value;
        const divKlasis = document.getElementById('div_klasis');
        const divJemaat = document.getElementById('div_jemaat');
        const inputKlasis = document.getElementById('klasis_id');
        const inputJemaat = document.getElementById('jemaat_id');

        // Reset visibility
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
        jemaatSelect.innerHTML = '<option value="">Memuat...</option>';

        if (klasisId) {
            fetch(`/api/jemaat-by-klasis/${klasisId}`)
                .then(response => response.json())
                .then(data => {
                    jemaatSelect.innerHTML = '<option value="">-- Pilih Jemaat --</option>';
                    data.forEach(jemaat => {
                        jemaatSelect.innerHTML += `<option value="${jemaat.id}">${jemaat.nama_jemaat}</option>`;
                    });
                })
                .catch(error => {
                    jemaatSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                });
        } else {
            jemaatSelect.innerHTML = '<option value="">-- Pilih Klasis Terlebih Dahulu --</option>';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        handleTingkatChange();
    });
</script>
@endpush
@endsection