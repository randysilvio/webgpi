@extends('layouts.app')

@section('title', 'Pencatatan Mutasi Personel')
@section('header-title', 'Kepegawaian & Mutasi')

@section('content')
<div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between border-b-2 border-gray-800 pb-4">
    <div>
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Pencatatan Surat Mutasi</h2>
        <p class="text-xs text-gray-600 mt-1">Formulir perpindahan tugas kedinasan atas nama: <strong class="text-gray-900 uppercase">{{ $pegawai->nama_lengkap }}</strong></p>
    </div>
    <a href="{{ route('admin.kepegawaian.pegawai.show', $pegawai->id) }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center mt-3 md:mt-0">
        <i class="fas fa-arrow-left mr-2"></i> Batal & Kembali
    </a>
</div>

<form action="{{ route('admin.mutasi.store', $pegawai->id) }}" method="POST">
    @csrf
    <div class="space-y-6 max-w-5xl mx-auto">

        {{-- PANEL 1: REGISTRASI SURAT KEPUTUSAN --}}
        <div class="bg-white border border-gray-200 p-5 rounded shadow-sm border-t-4 border-t-gray-800">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">I. Legalitas Surat Keputusan (SK)</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomor Registrasi SK <span class="text-red-600">*</span></label>
                    <input type="text" name="nomor_sk" value="{{ old('nomor_sk') }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm font-mono" placeholder="Cth: 001/SK/MPS/2026">
                    @error('nomor_sk') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Kategori Pergerakan Mutasi <span class="text-red-600">*</span></label>
                    <select id="jenis_mutasi" name="jenis_mutasi" onchange="toggleTujuanFields()" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white font-bold text-gray-800">
                        <option value="Rutin">MUTASI RUTIN / PERIODIK</option>
                        <option value="Khusus">MUTASI KHUSUS / PENEMPATAN AWAL</option>
                        <option value="Struktural">MUTASI STRUKTURAL</option>
                        <option value="Emeritus">EMERITUS (PENSIUN)</option>
                        <option value="Keluar">MENGUNDURKAN DIRI / KELUAR</option>
                        <option value="Meninggal">MENINGGAL DUNIA</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tanggal SK Ditetapkan <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal_sk" value="{{ old('tanggal_sk', date('Y-m-d')) }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">TMT (Tanggal Mulai Tugas / Efektif)</label>
                    <input type="date" name="tanggal_efektif" value="{{ old('tanggal_efektif', date('Y-m-d')) }}" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                </div>
            </div>
        </div>

        {{-- PANEL 2: RUTE PERGERAKAN PENUGASAN --}}
        <div class="bg-white border border-gray-200 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">II. Rute Pergerakan Wilayah</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                {{-- Kiri: Asal (Readonly) --}}
                <div class="bg-gray-50 p-4 border border-gray-200 rounded">
                    <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 flex items-center"><i class="far fa-circle mr-2"></i> Titik Asal Keberangkatan</h5>
                    
                    {{-- Menyimpan ID Asal ke database secara diam-diam --}}
                    <input type="hidden" name="asal_klasis_id" value="{{ $asalKlasisId }}">
                    <input type="hidden" name="asal_jemaat_id" value="{{ $asalJemaatId }}">
                    
                    <div class="mb-4">
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Wilayah Klasis Asal</label>
                        <input type="text" readonly value="{{ $pegawai->klasis->nama_klasis ?? 'Kantor Sinode (Pusat)' }}" class="w-full border border-gray-300 rounded text-sm bg-gray-100 text-gray-500 font-bold uppercase cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Lokal Jemaat Asal</label>
                        <input type="text" readonly value="{{ $pegawai->jemaat->nama_jemaat ?? 'Tanpa Jemaat Lokal' }}" class="w-full border border-gray-300 rounded text-sm bg-gray-100 text-gray-500 font-bold uppercase cursor-not-allowed">
                    </div>
                </div>

                {{-- Kanan: Tujuan --}}
                <div id="panel_tujuan" class="bg-blue-50 p-4 border border-blue-200 rounded transition-opacity duration-300">
                    <h5 class="text-[10px] font-black text-blue-800 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-arrow-right mr-2"></i> Titik Tujuan Penempatan Baru</h5>
                    
                    <div class="mb-4">
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Wilayah Klasis Tujuan</label>
                        <select id="tujuan_klasis_id" name="tujuan_klasis_id" onchange="loadJemaat(this.value)" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                            <option value="">- Ditarik Ke Pusat (Sinode) -</option>
                            @foreach($klasisOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('tujuan_klasis_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Lokal Jemaat Tujuan</label>
                        <select id="tujuan_jemaat_id" name="tujuan_jemaat_id" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                            <option value="">- Silakan Pilih Klasis Dahulu -</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- PANEL 3: CATATAN --}}
        <div class="bg-white border border-gray-200 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">III. Catatan Administratif</h4>
            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Keterangan / Alasan Mutasi (Opsional)</label>
                <textarea name="keterangan" rows="3" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50" placeholder="Berikan catatan tambahan jika diperlukan...">{{ old('keterangan') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end pt-2 pb-10">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Eksekusi Mutasi Pegawai
            </button>
        </div>

    </div>
</form>

@push('scripts')
<script>
    function loadJemaat(klasisId) {
        const jemaatSelect = document.getElementById('tujuan_jemaat_id');
        jemaatSelect.innerHTML = '<option value="">Memuat Pangkalan Data...</option>';
        
        if(!klasisId) {
            jemaatSelect.innerHTML = '<option value="">- Silakan Pilih Klasis Dahulu -</option>';
            return;
        }

        fetch(`/api/jemaat-by-klasis/${klasisId}`)
            .then(res => res.json())
            .then(data => {
                jemaatSelect.innerHTML = '<option value="">-- Tentukan Jemaat Tujuan --</option>';
                data.forEach(j => {
                    jemaatSelect.innerHTML += `<option value="${j.id}">${j.nama_jemaat}</option>`;
                });
            })
            .catch(() => {
                jemaatSelect.innerHTML = '<option value="">- Gagal Memuat Data -</option>';
            });
    }

    function toggleTujuanFields() {
        const jenis = document.getElementById('jenis_mutasi').value;
        const panel = document.getElementById('panel_tujuan');
        const selectKlasis = document.getElementById('tujuan_klasis_id');
        const selectJemaat = document.getElementById('tujuan_jemaat_id');

        const jenisNonAktif = ['Emeritus', 'Keluar', 'Meninggal'];

        if (jenisNonAktif.includes(jenis)) {
            panel.classList.add('opacity-50', 'pointer-events-none');
            selectKlasis.value = "";
            selectJemaat.innerHTML = '<option value="">- Otomatis Dinonaktifkan -</option>';
        } else {
            panel.classList.remove('opacity-50', 'pointer-events-none');
            if(selectKlasis.value) {
                loadJemaat(selectKlasis.value);
            } else {
                selectJemaat.innerHTML = '<option value="">- Silakan Pilih Klasis Dahulu -</option>';
            }
        }
    }

    // Trigger saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        toggleTujuanFields();
        const initialKlasis = document.getElementById('tujuan_klasis_id').value;
        if(initialKlasis) {
            loadJemaat(initialKlasis);
        }
    });
</script>
@endpush
@endsection