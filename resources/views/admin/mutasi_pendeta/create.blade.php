@extends('layouts.app')

@section('title', 'Proses Mutasi')
@section('header-title', 'Proses Mutasi Personel')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-slate-300 text-slate-500 hover:bg-slate-50 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800">Mutasi: {{ $pegawai->nama_lengkap }}</h1>
            <p class="text-xs text-slate-500">Formulir pemindahan tugas dan jabatan.</p>
        </div>
    </div>

    <form action="{{ route('admin.kepegawaian.pegawai.mutasi.store', $pegawai->id) }}" method="POST">
        @csrf

        {{-- SECTION 1: DASAR SURAT KEPUTUSAN --}}
        <div class="bg-white rounded shadow-sm border border-slate-200 mb-6 overflow-hidden">
            <div class="px-6 py-3 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-700 uppercase text-xs tracking-wider">I. Dasar Surat Keputusan (SK)</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Nomor SK <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_sk" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" required placeholder="Contoh: 001/SK/MPS/2026">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Tanggal SK <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_sk" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Tanggal Efektif (TMT) <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_efektif" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" required>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Jenis Mutasi</label>
                    <div class="flex gap-4 mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="jenis_mutasi" value="Rutin" class="form-radio text-slate-600 focus:ring-slate-500" checked>
                            <span class="ml-2 text-sm text-slate-700">Rutin / Periodik</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="jenis_mutasi" value="Struktural" class="form-radio text-slate-600 focus:ring-slate-500">
                            <span class="ml-2 text-sm text-slate-700">Struktural</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="jenis_mutasi" value="Khusus" class="form-radio text-slate-600 focus:ring-slate-500">
                            <span class="ml-2 text-sm text-slate-700">Khusus</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: TUJUAN MUTASI --}}
        <div class="bg-white rounded shadow-sm border border-slate-200 mb-6 overflow-hidden">
            <div class="px-6 py-3 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-700 uppercase text-xs tracking-wider">II. Penempatan Baru</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Lokasi Lama (Read Only) --}}
                <div class="md:col-span-2 bg-slate-50 p-4 rounded border border-slate-100 mb-2 flex justify-between items-center">
                    <div>
                        <span class="text-xs text-slate-400 uppercase font-bold block">Lokasi Lama</span>
                        <span class="text-sm font-semibold text-slate-700">
                            {{ $pegawai->jemaat->nama_jemaat ?? $pegawai->klasis->nama_klasis ?? 'Kantor Sinode' }}
                        </span>
                    </div>
                    <i class="fas fa-arrow-right text-slate-300"></i>
                </div>

                {{-- Lokasi Baru --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Klasis Tujuan</label>
                    <select name="tujuan_klasis_id" id="klasis_id" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" onchange="loadJemaat(this.value)">
                        <option value="">-- Pilih Klasis --</option>
                        @foreach(App\Models\Klasis::all() as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_klasis }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Jemaat Tujuan</label>
                    <select name="tujuan_jemaat_id" id="jemaat_id" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                        <option value="">-- Pilih Jemaat --</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Jabatan Baru</label>
                    <input type="text" name="jabatan_baru" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" placeholder="Misal: Ketua Majelis Jemaat" required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Keterangan Tambahan</label>
                    <textarea name="keterangan" rows="3" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500"></textarea>
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end gap-3">
            <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white text-sm font-bold uppercase tracking-wide rounded hover:bg-slate-900 transition shadow-lg">
                Proses Mutasi
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function loadJemaat(klasisId) {
        const jemaatSelect = document.getElementById('jemaat_id');
        jemaatSelect.innerHTML = '<option value="">Memuat...</option>';
        
        if(!klasisId) {
            jemaatSelect.innerHTML = '<option value="">-- Pilih Klasis Dulu --</option>';
            return;
        }

        fetch(`/api/jemaat-by-klasis/${klasisId}`)
            .then(response => response.json())
            .then(data => {
                jemaatSelect.innerHTML = '<option value="">-- Pilih Jemaat --</option>';
                // Opsi Kantor Klasis
                jemaatSelect.innerHTML += `<option value="kantor_klasis">Kantor Klasis (Non-Jemaat)</option>`;
                
                data.forEach(j => {
                    jemaatSelect.innerHTML += `<option value="${j.id}">${j.nama_jemaat}</option>`;
                });
            });
    }
</script>
@endpush
@endsection