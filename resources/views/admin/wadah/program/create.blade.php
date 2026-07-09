@extends('layouts.app')

@section('title', 'Penyusunan Program Kerja')

@section('content')
<div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between border-b-2 border-gray-800 pb-4">
    <div>
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Registrasi Program Baru</h2>
        <p class="text-xs text-gray-600 mt-1">Sistem Perencanaan dan Pembuatan Matriks Program Kategorial.</p>
    </div>
    <a href="{{ route('admin.wadah.program.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center mt-3 md:mt-0">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
    </a>
</div>

<form action="{{ route('admin.wadah.program.store') }}" method="POST">
    @csrf
    <div class="space-y-6 max-w-5xl mx-auto">
        
        {{-- KELOMPOK I: KONTEKS STRUKTURAL --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-sitemap mr-2 text-blue-800"></i> I. Parameter & Konteks Pelaksanaan</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tahun Anggaran Pelaksanaan <span class="text-red-600">*</span></label>
                    <select id="tahun_program" name="tahun_program" onchange="updateParentOptions()" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 font-mono font-bold text-gray-900">
                        @for($i = date('Y') + 1; $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ old('tahun_program', date('Y')) == $i ? 'selected' : '' }}>Tahun {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Wadah Kategorial Penyelenggara <span class="text-red-600">*</span></label>
                    <select id="jenis_wadah_id" name="jenis_wadah_id" required onchange="updateParentOptions()" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">-- Pilihan Wadah Kategorial --</option>
                        @foreach($jenisWadahs as $wadah)
                            <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>{{ strtoupper($wadah->nama_wadah) }}</option>
                        @endforeach
                    </select>
                    @error('jenis_wadah_id') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Hierarki Kewenangan <span class="text-red-600">*</span></label>
                    <select id="tingkat" name="tingkat" required onchange="handleLokasiChange()" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="sinode" {{ old('tingkat') == 'sinode' ? 'selected' : '' }}>Pusat Sinode</option>
                        <option value="klasis" {{ old('tingkat') == 'klasis' ? 'selected' : '' }}>Regional Klasis</option>
                        <option value="jemaat" {{ old('tingkat') == 'jemaat' ? 'selected' : '' }}>Lokal Jemaat</option>
                    </select>
                    @error('tingkat') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div id="div_klasis" class="hidden">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Teritorial Klasis <span class="text-red-600">*</span></label>
                    <select id="klasis_id" name="klasis_id" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" onchange="loadJemaat(this.value); updateParentOptions();">
                        <option value="">-- Lokasi Klasis Terpilih --</option>
                        @foreach($klasisList as $klasis)
                            <option value="{{ $klasis->id }}" {{ old('klasis_id') == $klasis->id ? 'selected' : '' }}>{{ strtoupper($klasis->nama_klasis) }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="div_jemaat" class="hidden">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Teritorial Jemaat <span class="text-red-600">*</span></label>
                    <select id="jemaat_id" name="jemaat_id" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">-- Menunggu Data Klasis --</option>
                        @foreach($jemaatList as $jemaat)
                            <option value="{{ $jemaat->id }}" {{ old('jemaat_id') == $jemaat->id ? 'selected' : '' }}>{{ strtoupper($jemaat->nama_jemaat) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- KELOMPOK II: DESKRIPSI KEGIATAN --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm border-t-4 border-t-green-700">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-file-alt mr-2 text-green-700"></i> II. Rincian & Klasifikasi Program</h4>
            
            <div class="bg-blue-50 border border-blue-200 p-4 rounded mb-6">
                <label class="block text-[10px] font-bold text-blue-900 uppercase mb-1">Tautan Program Induk (Opsional)</label>
                <select id="parent_program_id" name="parent_program_id" class="w-full border border-blue-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 bg-white shadow-sm">
                    <option value="">-- Bukan Program Turunan (Mandiri) --</option>
                </select>
                <p class="text-[9px] text-blue-700 mt-1.5 font-bold uppercase tracking-widest"><i class="fas fa-info-circle mr-1"></i> Jika program ini merupakan derivasi/penjabaran dari program institusi tingkat atas.</p>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomenklatur Program / Kegiatan <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_program" value="{{ old('nama_program') }}" required placeholder="Contoh: Ibadah Padang Gabungan Kaum Bapak" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm uppercase bg-gray-50">
                    @error('nama_program') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Target / Capaian Output (Tujuan)</label>
                    <textarea name="tujuan" rows="2" placeholder="Uraikan luaran spesifik yang diharapkan..." 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">{{ old('tujuan') }}</textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Deskripsi & Metode Pelaksanaan</label>
                    <textarea name="deskripsi" rows="3" placeholder="Rincian teknis kegiatan..." 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Unit Penanggung Jawab (PIC)</label>
                        <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}" placeholder="Cth: Bidang Organisasi / Seksi Ibadah" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Estimasi Kebutuhan Anggaran (RAB)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold text-[10px]">Rp</span>
                            <input type="number" name="target_anggaran" value="{{ old('target_anggaran') }}" placeholder="0" 
                                class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded text-sm font-mono text-right focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 font-bold">
                        </div>
                        <p class="text-[9px] text-gray-500 mt-1 uppercase tracking-widest font-bold">Opsional. Digunakan untuk acuan finansial kegiatan.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 pb-10">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Ajukan Program Kerja
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
        const klasisIdInput = document.getElementById('klasis_id');
        
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

    function updateParentOptions() {
        const tingkat = document.getElementById('tingkat').value;
        const wadahId = document.getElementById('jenis_wadah_id').value;
        const tahun = document.getElementById('tahun_program').value;
        const klasisId = document.getElementById('klasis_id').value;
        const parentSelect = document.getElementById('parent_program_id');

        if ((tingkat !== 'jemaat' && tingkat !== 'klasis') || !wadahId || !tahun) {
            parentSelect.innerHTML = '<option value="">-- Bukan Program Turunan (Mandiri) --</option>';
            return;
        }

        let url = `{{ route('admin.wadah.program.get-parents') }}?tingkat=${tingkat}&wadah_id=${wadahId}&tahun=${tahun}`;
        if (tingkat === 'jemaat') {
            if(!klasisId) return;
            url += `&klasis_id=${klasisId}`;
        }

        parentSelect.innerHTML = '<option value="">Menyelaraskan data server...</option>';

        fetch(url)
            .then(response => response.json())
            .then(data => {
                parentSelect.innerHTML = '<option value="">-- Bukan Program Turunan (Mandiri) --</option>';
                if (data.length > 0) {
                    data.forEach(program => {
                        parentSelect.innerHTML += `<option value="${program.id}">${program.nama_program}</option>`;
                    });
                }
            })
            .catch(err => {
                parentSelect.innerHTML = '<option value="">Koneksi server gagal</option>';
            });
    }

    // Trigger on load
    document.addEventListener('DOMContentLoaded', function() {
        handleLokasiChange();
    });
</script>
@endpush
@endsection