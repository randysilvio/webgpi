@extends('layouts.app')

@section('title', 'Buat Pos Anggaran')

@section('content')
    <x-admin-form 
        title="Buat Pos Anggaran Baru" 
        action="{{ route('admin.wadah.anggaran.store') }}" 
        back-route="{{ route('admin.wadah.anggaran.index') }}"
    >
        <div class="space-y-6">
            
            {{-- KONTEKS ANGGARAN --}}
            <div class="bg-slate-50 p-4 rounded border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tahun Anggaran</label>
                    <select id="tahun_anggaran" name="tahun_anggaran" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" onchange="loadPrograms()">
                        @for($i = date('Y') + 1; $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ old('tahun_anggaran', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Wadah Kategorial</label>
                    <select id="jenis_wadah_id" name="jenis_wadah_id" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" required onchange="loadPrograms()">
                        <option value="">-- Pilih Wadah --</option>
                        @foreach($jenisWadahs as $wadah)
                            <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>{{ $wadah->nama_wadah }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tingkat Struktur</label>
                    <select id="tingkat" name="tingkat" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" required onchange="handleLokasi()">
                        <option value="sinode">Sinode</option>
                        <option value="klasis">Klasis</option>
                        <option value="jemaat">Jemaat</option>
                    </select>
                </div>
            </div>

            {{-- LOKASI DINAMIS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div id="div_klasis" class="hidden">
                    <label for="klasis_id" class="block text-xs font-bold text-slate-500 uppercase mb-1">Pilih Klasis</label>
                    <select id="klasis_id" name="klasis_id" class="w-full border-slate-300 rounded text-sm" onchange="loadJemaat(this.value); loadPrograms();">
                        <option value="">-- Pilih Klasis --</option>
                        @foreach($klasisList as $klasis)
                            <option value="{{ $klasis->id }}">{{ $klasis->nama_klasis }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="div_jemaat" class="hidden">
                    <label for="jemaat_id" class="block text-xs font-bold text-slate-500 uppercase mb-1">Pilih Jemaat</label>
                    <select id="jemaat_id" name="jemaat_id" class="w-full border-slate-300 rounded text-sm" onchange="loadPrograms()">
                        <option value="">-- Pilih Jemaat --</option>
                        @foreach($jemaatList as $jemaat)
                            <option value="{{ $jemaat->id }}">{{ $jemaat->nama_jemaat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="border-t border-slate-100 my-4"></div>

            {{-- DETAIL POS --}}
            <x-form-input label="Nama Pos Anggaran" name="nama_pos_anggaran" required placeholder="Contoh: Iuran Anggota, Biaya Rapat, Belanja Modal" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jenis Anggaran</label>
                    <select name="jenis_anggaran" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" required>
                        <option value="penerimaan">Penerimaan (Uang Masuk)</option>
                        <option value="pengeluaran">Pengeluaran (Belanja)</option>
                    </select>
                </div>
                <x-form-input type="number" label="Target Jumlah (Rp)" name="jumlah_target" required placeholder="0" />
            </div>

            {{-- RELASI PROGRAM --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tautkan ke Program Kerja (Opsional)</label>
                <select id="program_kerja_id" name="program_kerja_id" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                    <option value="">-- Tidak Terkait Program / Rutin --</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-1 italic">Daftar program akan muncul otomatis berdasarkan Tahun, Wadah, dan Tingkat yang dipilih.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="2" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">{{ old('keterangan') }}</textarea>
            </div>

        </div>
    </x-admin-form>

    @push('scripts')
    <script>
        function handleLokasi() {
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