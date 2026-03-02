@extends('layouts.app')

@section('title', 'Buat Program Kerja')

@section('content')
    <x-admin-form 
        title="Buat Program Kerja Baru" 
        action="{{ route('admin.wadah.program.store') }}" 
        back-route="{{ route('admin.wadah.program.index') }}"
    >
        <div class="space-y-6">
            
            {{-- KONTEKS PROGRAM --}}
            <div class="bg-slate-50 p-4 rounded border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-select label="Tahun Program" name="tahun_program" id="tahun_program" onchange="updateParentOptions()">
                    @for($i = date('Y') + 1; $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ old('tahun_program', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </x-form-select>

                <x-form-select label="Wadah Kategorial" name="jenis_wadah_id" id="jenis_wadah_id" required onchange="updateParentOptions()">
                    <option value="">-- Pilih Wadah --</option>
                    @foreach($jenisWadahs as $wadah)
                        <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>{{ $wadah->nama_wadah }}</option>
                    @endforeach
                </x-form-select>

                <x-form-select label="Tingkat Struktur" name="tingkat" id="tingkat" required onchange="handleLokasiChange()">
                    <option value="sinode">Sinode</option>
                    <option value="klasis">Klasis</option>
                    <option value="jemaat">Jemaat</option>
                </x-form-select>
            </div>

            {{-- LOKASI DINAMIS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div id="div_klasis" class="hidden">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Klasis</label>
                    <select id="klasis_id" name="klasis_id" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" onchange="loadJemaat(this.value); updateParentOptions();">
                        <option value="">-- Pilih Klasis --</option>
                        @foreach($klasisList as $klasis)
                            <option value="{{ $klasis->id }}">{{ $klasis->nama_klasis }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="div_jemaat" class="hidden">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jemaat</label>
                    <select id="jemaat_id" name="jemaat_id" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                        <option value="">-- Pilih Jemaat --</option>
                        @foreach($jemaatList as $jemaat)
                            <option value="{{ $jemaat->id }}">{{ $jemaat->nama_jemaat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- PARENT PROGRAM --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100">
                <label class="block text-xs font-bold text-blue-700 uppercase mb-1">Program Induk (Opsional)</label>
                <p class="text-[10px] text-blue-600 mb-2">Program ini mengacu pada program tingkat atas (misal: Program Jemaat mengacu ke Program Klasis).</p>
                <select id="parent_program_id" name="parent_program_id" class="w-full border-blue-300 rounded text-sm focus:ring-blue-500 bg-white">
                    <option value="">-- Tidak Ada / Program Mandiri --</option>
                </select>
            </div>

            <div class="border-t border-slate-100 my-4"></div>

            {{-- DETAIL PROGRAM --}}
            <x-form-input label="Nama Program" name="nama_program" required placeholder="Contoh: Ibadah Padang Gabungan" />

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tujuan / Output</label>
                <textarea name="tujuan" rows="2" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">{{ old('tujuan') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Deskripsi Kegiatan</label>
                <textarea name="deskripsi" rows="3" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Penanggung Jawab (Seksi/Bidang)" name="penanggung_jawab" />
                <x-form-input type="number" label="Target Anggaran (Rp)" name="target_anggaran" placeholder="0" />
            </div>

        </div>
    </x-admin-form>

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

            if ((tingkat !== 'jemaat' && tingkat !== 'klasis') || !wadahId || !tahun) {
                parentSelect.innerHTML = '<option value="">-- Tidak Ada / Program Mandiri --</option>';
                return;
            }

            let url = `{{ route('admin.wadah.program.get-parents') }}?tingkat=${tingkat}&wadah_id=${wadahId}&tahun=${tahun}`;
            if (tingkat === 'jemaat') {
                if(!klasisId) return;
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