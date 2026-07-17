@extends('layouts.app')

@section('title', 'Registrasi Pos Anggaran Baru')

@section('content')
<div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between border-b-2 border-gray-800 pb-4">
    <div>
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Registrasi Pos Anggaran</h2>
        <p class="text-xs text-gray-600 mt-1">Sistem Perencanaan dan Pembuatan Matriks Anggaran Kategorial.</p>
    </div>
    <a href="{{ route('admin.wadah.anggaran.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center mt-3 md:mt-0">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
    </a>
</div>

<form action="{{ route('admin.wadah.anggaran.store') }}" method="POST">
    @csrf
    <div class="space-y-6 max-w-5xl mx-auto">
        
        {{-- KELOMPOK I: KONTEKS STRUKTURAL --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-sitemap mr-2 text-blue-800"></i> I. Parameter & Konteks Pelaksanaan</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tahun Anggaran Berjalan <span class="text-red-600">*</span></label>
                    <select id="tahun_anggaran" name="tahun_anggaran" onchange="loadPrograms()" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 font-mono font-bold text-gray-900">
                        @for($i = date('Y') + 1; $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ old('tahun_anggaran', date('Y')) == $i ? 'selected' : '' }}>Tahun {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Wadah Kategorial Penyelenggara <span class="text-red-600">*</span></label>
                    <select id="jenis_wadah_id" name="jenis_wadah_id" required onchange="loadPrograms()" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">-- Pilihan Wadah Kategorial --</option>
                        @foreach($jenisWadahs as $wadah)
                            <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>{{ strtoupper($wadah->nama_wadah) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Hierarki Kewenangan <span class="text-red-600">*</span></label>
                    <select id="tingkat" name="tingkat" required onchange="handleLokasiChange()" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="sinode" {{ old('tingkat') == 'sinode' ? 'selected' : '' }}>Pusat Sinode</option>
                        <option value="klasis" {{ old('tingkat') == 'klasis' ? 'selected' : '' }}>Regional Klasis</option>
                        <option value="jemaat" {{ old('tingkat') == 'jemaat' ? 'selected' : '' }}>Lokal Jemaat</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div id="div_klasis" class="hidden">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Teritorial Klasis <span class="text-red-600">*</span></label>
                    <select id="klasis_id" name="klasis_id" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" onchange="loadJemaat(this.value); loadPrograms();">
                        <option value="">-- Lokasi Klasis Terpilih --</option>
                        @foreach($klasisList as $klasis)
                            <option value="{{ $klasis->id }}" {{ old('klasis_id') == $klasis->id ? 'selected' : '' }}>{{ strtoupper($klasis->nama_klasis) }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="div_jemaat" class="hidden">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Teritorial Jemaat <span class="text-red-600">*</span></label>
                    <select id="jemaat_id" name="jemaat_id" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" onchange="loadPrograms();">
                        <option value="">-- Menunggu Data Klasis --</option>
                        @foreach($jemaatList as $jemaat)
                            <option value="{{ $jemaat->id }}" {{ old('jemaat_id') == $jemaat->id ? 'selected' : '' }}>{{ strtoupper($jemaat->nama_jemaat) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- KELOMPOK II: DESKRIPSI KEUANGAN --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm border-t-4 border-t-green-700">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-file-invoice-dollar mr-2 text-green-700"></i> II. Rincian & Target Anggaran</h4>
            
            <div class="bg-blue-50 border border-blue-200 p-4 rounded mb-6">
                <label class="block text-[10px] font-bold text-blue-900 uppercase mb-1">Tautan Program Kerja (Opsional)</label>
                <select id="program_kerja_id" name="program_kerja_id" class="w-full border border-blue-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 bg-white shadow-sm font-bold text-gray-800">
                    <option value="">-- Bukan Anggaran Program (Anggaran Rutin) --</option>
                </select>
                <p class="text-[9px] text-blue-700 mt-1.5 font-bold uppercase tracking-widest"><i class="fas fa-info-circle mr-1"></i> Tautkan jika pos anggaran ini dirancang untuk mendanai program spesifik.</p>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomenklatur Pos Anggaran <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_pos_anggaran" value="{{ old('nama_pos_anggaran') }}" required placeholder="Contoh: Belanja Konsumsi Ibadah Padang" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm uppercase bg-gray-50">
                    @error('nama_pos_anggaran') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Klasifikasi Arus Kas <span class="text-red-600">*</span></label>
                        <select name="jenis_anggaran" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                            <option value="penerimaan" {{ old('jenis_anggaran') == 'penerimaan' ? 'selected' : '' }}>Penerimaan (Pemasukan)</option>
                            <option value="pengeluaran" {{ old('jenis_anggaran') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran (Belanja)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Target Nominal Anggaran (Rp) <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold text-[10px]">Rp</span>
                            <input type="number" name="jumlah_target" value="{{ old('jumlah_target') }}" required placeholder="0" 
                                class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded text-sm font-mono text-right focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 font-bold">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Deskripsi & Catatan Penggunaan Tambahan</label>
                    <textarea name="keterangan" rows="3" placeholder="Rincian penjelasan pos anggaran..." 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 pb-10">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Daftarkan Pos Anggaran
            </button>
        </div>

    </div>
</form>

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
        loadPrograms();
    }

    function loadJemaat(klasisId) {
        const jemaatSelect = document.getElementById('jemaat_id');
        jemaatSelect.innerHTML = '<option value="">Memuat Pangkalan Data...</option>';
        if(!klasisId) {
            jemaatSelect.innerHTML = '<option value="">-- Menunggu Data Klasis --</option>';
            return;
        }

        fetch(`/api/jemaat-by-klasis/${klasisId}`)
            .then(response => response.json())
            .then(data => {
                jemaatSelect.innerHTML = '<option value="">-- Tentukan Lokal Jemaat --</option>';
                data.forEach(jemaat => {
                    jemaatSelect.innerHTML += `<option value="${jemaat.id}">${jemaat.nama_jemaat}</option>`;
                });
            });
    }

    function loadPrograms() {
        const tahun = document.getElementById('tahun_anggaran').value;
        const wadah = document.getElementById('jenis_wadah_id').value;
        const tingkat = document.getElementById('tingkat').value;
        const klasis = document.getElementById('klasis_id').value;
        const jemaat = document.getElementById('jemaat_id').value;
        const progSelect = document.getElementById('program_kerja_id');

        if(!tahun || !wadah || !tingkat) {
            progSelect.innerHTML = '<option value="">-- Bukan Anggaran Program (Anggaran Rutin) --</option>';
            return;
        }

        let url = `{{ route('admin.wadah.anggaran.get-programs') }}?tahun=${tahun}&wadah_id=${wadah}&tingkat=${tingkat}`;
        if(tingkat === 'klasis') url += `&klasis_id=${klasis}`;
        if(tingkat === 'jemaat') url += `&jemaat_id=${jemaat}`;

        progSelect.innerHTML = '<option value="">Menyelaraskan data server...</option>';

        fetch(url)
            .then(response => response.json())
            .then(data => {
                progSelect.innerHTML = '<option value="">-- Bukan Anggaran Program (Anggaran Rutin) --</option>';
                if (data.length > 0) {
                    data.forEach(program => {
                        progSelect.innerHTML += `<option value="${program.id}">${program.nama_program}</option>`;
                    });
                }
            })
            .catch(err => {
                progSelect.innerHTML = '<option value="">Koneksi server gagal</option>';
            });
    }

    // Trigger on load
    document.addEventListener('DOMContentLoaded', function() {
        handleLokasiChange();
    });
</script>
@endpush
@endsection