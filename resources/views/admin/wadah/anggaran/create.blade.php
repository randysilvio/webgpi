@extends('admin.layout')

@section('title', 'Buat Pos Anggaran')
@section('header-title', 'Buat Pos Anggaran Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <form method="POST" action="{{ route('admin.wadah.anggaran.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="tahun_anggaran" class="block text-sm font-medium text-gray-700 mb-1">Tahun Anggaran</label>
                    <select id="tahun_anggaran" name="tahun_anggaran" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" onchange="loadPrograms()">
                        @for($i = date('Y') + 1; $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ old('tahun_anggaran', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="jenis_wadah_id" class="block text-sm font-medium text-gray-700 mb-1">Wadah</label>
                    <select id="jenis_wadah_id" name="jenis_wadah_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required onchange="loadPrograms()">
                        <option value="">-- Pilih --</option>
                        @foreach($jenisWadahs as $wadah)
                            <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>{{ $wadah->nama_wadah }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-1">Tingkat</label>
                    <select id="tingkat" name="tingkat" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required onchange="handleLokasi()">
                        <option value="sinode">Sinode</option>
                        <option value="klasis">Klasis</option>
                        <option value="jemaat">Jemaat</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div id="div_klasis" class="hidden">
                    <label for="klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Klasis</label>
                    <select id="klasis_id" name="klasis_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" onchange="loadJemaat(this.value); loadPrograms();">
                        <option value="">-- Pilih Klasis --</option>
                        @foreach($klasisList as $klasis)
                            <option value="{{ $klasis->id }}">{{ $klasis->nama_klasis }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="div_jemaat" class="hidden">
                    <label for="jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Jemaat</label>
                    <select id="jemaat_id" name="jemaat_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" onchange="loadPrograms()">
                        <option value="">-- Pilih Jemaat --</option>
                        @foreach($jemaatList as $jemaat)
                            <option value="{{ $jemaat->id }}">{{ $jemaat->nama_jemaat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-6 border-gray-200">

            <div class="mb-4">
                <label for="nama_pos_anggaran" class="block text-sm font-medium text-gray-700 mb-1">Nama Pos Anggaran</label>
                <input id="nama_pos_anggaran" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm font-bold" type="text" name="nama_pos_anggaran" value="{{ old('nama_pos_anggaran') }}" required placeholder="Contoh: Iuran Anggota, Biaya Rapat, dll" />
                @error('nama_pos_anggaran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="jenis_anggaran" class="block text-sm font-medium text-gray-700 mb-1">Jenis Anggaran</label>
                    <select id="jenis_anggaran" name="jenis_anggaran" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required>
                        <option value="penerimaan">Penerimaan (Uang Masuk)</option>
                        <option value="pengeluaran">Pengeluaran (Belanja)</option>
                    </select>
                </div>
                <div>
                    <label for="jumlah_target" class="block text-sm font-medium text-gray-700 mb-1">Target Jumlah (Rp)</label>
                    <input id="jumlah_target" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" type="number" name="jumlah_target" value="{{ old('jumlah_target') }}" required placeholder="0" />
                </div>
            </div>

            <div class="mb-4">
                <label for="program_kerja_id" class="block text-sm font-medium text-gray-700 mb-1">Tautkan ke Program Kerja (Opsional)</label>
                <select id="program_kerja_id" name="program_kerja_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                    <option value="">-- Tidak Terkait Program / Rutin --</option>
                    </select>
                <p class="text-xs text-gray-500 mt-1">Pilih program jika anggaran ini spesifik untuk program tertentu.</p>
            </div>

            <div class="mb-4">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="2" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex items-center justify-end mt-6">
                <a href="{{ route('admin.wadah.anggaran.index') }}" class="text-gray-600 hover:text-gray-900 mr-4 text-sm">Batal</a>
                <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm transition duration-150">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function handleLokasi() {
        const tingkat = document.getElementById('tingkat').value;
        const divKlasis = document.getElementById('div_klasis');
        const divJemaat = document.getElementById('div_jemaat');
        divKlasis.classList.add('hidden'); divJemaat.classList.add('hidden');

        if (tingkat === 'klasis') { divKlasis.classList.remove('hidden'); }
        else if (tingkat === 'jemaat') { divKlasis.classList.remove('hidden'); divJemaat.classList.remove('hidden'); }
        loadPrograms();
    }

    function loadJemaat(klasisId) {
        const jemaatSelect = document.getElementById('jemaat_id');
        jemaatSelect.innerHTML = '<option value="">Memuat...</option>';
        if(!klasisId) return;
        fetch(`/api/jemaat-by-klasis/${klasisId}`).then(r => r.json()).then(d => {
            jemaatSelect.innerHTML = '<option value="">-- Pilih Jemaat --</option>';
            d.forEach(j => jemaatSelect.innerHTML += `<option value="${j.id}">${j.nama_jemaat}</option>`);
        });
    }

    function loadPrograms() {
        const tahun = document.getElementById('tahun_anggaran').value;
        const wadah = document.getElementById('jenis_wadah_id').value;
        const tingkat = document.getElementById('tingkat').value;
        const klasis = document.getElementById('klasis_id').value;
        const jemaat = document.getElementById('jemaat_id').value;
        const progSelect = document.getElementById('program_kerja_id');

        if(!tahun || !wadah || !tingkat) return;

        let url = `{{ route('admin.wadah.anggaran.get-programs') }}?tahun=${tahun}&wadah_id=${wadah}&tingkat=${tingkat}`;
        if(tingkat === 'klasis') url += `&klasis_id=${klasis}`;
        if(tingkat === 'jemaat') url += `&jemaat_id=${jemaat}`;

        progSelect.innerHTML = '<option>Memuat...</option>';
        fetch(url).then(r => r.json()).then(d => {
            progSelect.innerHTML = '<option value="">-- Tidak Terkait Program / Rutin --</option>';
            d.forEach(p => progSelect.innerHTML += `<option value="${p.id}">${p.nama_program}</option>`);
        });
    }
</script>
@endpush
@endsection