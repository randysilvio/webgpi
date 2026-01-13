@extends('admin.layout')

@section('title', 'Buat Program Kerja')
@section('header-title', 'Buat Program Kerja Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg p-6">
        
        <form method="POST" action="{{ route('admin.wadah.program.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="tahun_program" class="block text-sm font-medium text-gray-700 mb-1">Tahun Program</label>
                    <select id="tahun_program" name="tahun_program" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" onchange="updateParentOptions()">
                        @for($i = date('Y') + 1; $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ old('tahun_program', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="jenis_wadah_id" class="block text-sm font-medium text-gray-700 mb-1">Wadah Kategorial</label>
                    <select id="jenis_wadah_id" name="jenis_wadah_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required onchange="updateParentOptions()">
                        <option value="">-- Pilih Wadah --</option>
                        @foreach($jenisWadahs as $wadah)
                            <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>{{ $wadah->nama_wadah }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-1">Tingkat</label>
                    <select id="tingkat" name="tingkat" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required onchange="handleLokasiChange()">
                        <option value="">-- Pilih --</option>
                        <option value="sinode">Sinode</option>
                        <option value="klasis">Klasis</option>
                        <option value="jemaat">Jemaat</option>
                    </select>
                </div>
                <div id="div_klasis" class="hidden">
                    <label for="klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Klasis</label>
                    <select id="klasis_id" name="klasis_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" onchange="loadJemaat(this.value); updateParentOptions();">
                        <option value="">-- Pilih Klasis --</option>
                        @foreach($klasisList as $klasis)
                            <option value="{{ $klasis->id }}">{{ $klasis->nama_klasis }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="div_jemaat" class="hidden">
                    <label for="jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Jemaat</label>
                    <select id="jemaat_id" name="jemaat_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                        <option value="">-- Pilih Jemaat --</option>
                        @foreach($jemaatList as $jemaat)
                            <option value="{{ $jemaat->id }}">{{ $jemaat->nama_jemaat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-100" id="div_parent">
                <label for="parent_program_id" class="block text-sm font-medium text-blue-800 mb-1">Program Induk (Opsional)</label>
                <p class="text-xs text-blue-600 mb-2">Program ini mengacu pada program di tingkat atasnya (misal: Program Jemaat mengacu ke Program Klasis).</p>
                <select id="parent_program_id" name="parent_program_id" class="block w-full border-blue-300 rounded-md shadow-sm bg-white sm:text-sm">
                    <option value="">-- Tidak Ada / Program Mandiri --</option>
                </select>
            </div>

            <hr class="my-6 border-gray-200">

            <div class="mb-4">
                <label for="nama_program" class="block text-sm font-medium text-gray-700 mb-1">Nama Program</label>
                <input id="nama_program" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" type="text" name="nama_program" value="{{ old('nama_program') }}" required placeholder="Contoh: Ibadah Padang Gabungan" />
            </div>

            <div class="mb-4">
                <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan / Output</label>
                <textarea id="tujuan" name="tujuan" rows="2" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm">{{ old('tujuan') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kegiatan</label>
                <textarea id="deskripsi" name="deskripsi" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="penanggung_jawab" class="block text-sm font-medium text-gray-700 mb-1">Penanggung Jawab (Seksi/Bidang)</label>
                    <input id="penanggung_jawab" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}" />
                </div>
                <div>
                    <label for="target_anggaran" class="block text-sm font-medium text-gray-700 mb-1">Target Anggaran (Rp)</label>
                    <input id="target_anggaran" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" type="number" name="target_anggaran" value="{{ old('target_anggaran') }}" placeholder="0" />
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <a href="{{ route('admin.wadah.program.index') }}" class="text-gray-600 hover:text-gray-900 mr-4 text-sm">Batal</a>
                <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm transition duration-150">
                    Simpan Program
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function handleLokasiChange() {
        const tingkat = document.getElementById('tingkat').value;
        const divKlasis = document.getElementById('div_klasis');
        const divJemaat = document.getElementById('div_jemaat');
        
        divKlasis.classList.add('hidden');
        divJemaat.classList.add('hidden');

        if (tingkat === 'klasis') {
            divKlasis.classList.remove('hidden');
        } else if (tingkat === 'jemaat') {
            divKlasis.classList.remove('hidden');
            divJemaat.classList.remove('hidden');
        }
        updateParentOptions();
    }

    function loadJemaat(klasisId) {
        const jemaatSelect = document.getElementById('jemaat_id');
        jemaatSelect.innerHTML = '<option value="">Memuat...</option>';
        if(!klasisId) return;

        fetch(`/api/jemaat-by-klasis/${klasisId}`)
            .then(response => response.json())
            .then(data => {
                jemaatSelect.innerHTML = '<option value="">-- Pilih Jemaat --</option>';
                data.forEach(jemaat => {
                    jemaatSelect.innerHTML += `<option value="${jemaat.id}">${jemaat.nama_jemaat}</option>`;
                });
            });
    }

    function updateParentOptions() {
        const tingkat = document.getElementById('tingkat').value;
        const wadahId = document.getElementById('jenis_wadah_id').value;
        const tahun = document.getElementById('tahun_program').value;
        const klasisId = document.getElementById('klasis_id').value;
        const parentSelect = document.getElementById('parent_program_id');

        // Hanya cari parent jika tingkat adalah Jemaat atau Klasis
        if ((tingkat !== 'jemaat' && tingkat !== 'klasis') || !wadahId || !tahun) {
            parentSelect.innerHTML = '<option value="">-- Tidak Ada / Program Mandiri --</option>';
            return;
        }

        // AJAX Call
        let url = `{{ route('admin.wadah.program.get-parents') }}?tingkat=${tingkat}&wadah_id=${wadahId}&tahun=${tahun}`;
        if (tingkat === 'jemaat') {
            if(!klasisId) return; // Butuh klasis ID untuk cari program klasis
            url += `&klasis_id=${klasisId}`;
        }

        parentSelect.innerHTML = '<option value="">Mencari program induk...</option>';

        fetch(url)
            .then(response => response.json())
            .then(data => {
                parentSelect.innerHTML = '<option value="">-- Tidak Ada / Program Mandiri --</option>';
                if (data.length > 0) {
                    data.forEach(program => {
                        parentSelect.innerHTML += `<option value="${program.id}">${program.nama_program}</option>`;
                    });
                }
            })
            .catch(err => {
                parentSelect.innerHTML = '<option value="">Gagal memuat data</option>';
            });
    }
</script>
@endpush
@endsection