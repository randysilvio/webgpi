@extends('admin.layout')

@section('title', 'Tambah Mutasi Pendeta: ' . $pendeta->nama_lengkap)
@section('header-title', 'Tambah Riwayat Mutasi untuk Pdt. ' . $pendeta->nama_lengkap)

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8 max-w-3xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir Mutasi Pendeta</h2>

    @if (session('error'))
        <div class="flash-message mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif
    {{-- Tampilkan error validasi Laravel --}}
     @if ($errors->any())
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm">
            <p class="font-bold">Terjadi Kesalahan:</p>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('admin.pendeta.mutasi.store', $pendeta->id) }}" method="POST">
        @csrf
        <div class="space-y-6">

            {{-- Informasi Dasar Mutasi --}}
            <section class="border rounded-lg p-6">
                 <h3 class="text-lg font-semibold text-gray-700 mb-4">1. Informasi SK Mutasi</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label for="tanggal_sk" class="block text-sm font-medium text-gray-700 mb-1">Tanggal SK <span class="text-red-600">*</span></label>
                        <input type="date" id="tanggal_sk" name="tanggal_sk" value="{{ old('tanggal_sk') }}" required
                               class="input-field @error('tanggal_sk') input-error @enderror">
                        @error('tanggal_sk') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="nomor_sk" class="block text-sm font-medium text-gray-700 mb-1">Nomor SK <span class="text-red-600">*</span></label>
                        <input type="text" id="nomor_sk" name="nomor_sk" value="{{ old('nomor_sk') }}" required
                               class="input-field @error('nomor_sk') input-error @enderror" placeholder="Contoh: SK/MPS/01/X/2025">
                        @error('nomor_sk') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="jenis_mutasi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Mutasi <span class="text-red-600">*</span></label>
                        <select id="jenis_mutasi" name="jenis_mutasi" required
                                class="input-field @error('jenis_mutasi') input-error @enderror">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Penempatan Awal" {{ old('jenis_mutasi') == 'Penempatan Awal' ? 'selected' : '' }}>Penempatan Awal</option>
                            <option value="Pindah Tugas" {{ old('jenis_mutasi') == 'Pindah Tugas' ? 'selected' : '' }}>Pindah Tugas</option>
                            <option value="Emeritus" {{ old('jenis_mutasi') == 'Emeritus' ? 'selected' : '' }}>Emeritus</option>
                            <option value="Keluar" {{ old('jenis_mutasi') == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                            <option value="Meninggal" {{ old('jenis_mutasi') == 'Meninggal' ? 'selected' : '' }}>Meninggal</option>
                            <option value="Lainnya" {{ old('jenis_mutasi') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('jenis_mutasi') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="tanggal_efektif" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Efektif Mutasi</label>
                        <input type="date" id="tanggal_efektif" name="tanggal_efektif" value="{{ old('tanggal_efektif') }}"
                               class="input-field @error('tanggal_efektif') input-error @enderror">
                        @error('tanggal_efektif') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                 </div>
            </section>

            {{-- Detail Penempatan --}}
             <section class="border rounded-lg p-6">
                 <h3 class="text-lg font-semibold text-gray-700 mb-4">2. Detail Penempatan</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label for="asal_klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Asal Klasis (Sebelumnya)</label>
                        <select id="asal_klasis_id" name="asal_klasis_id"
                                class="input-field @error('asal_klasis_id') input-error @enderror">
                            <option value="">-- Pilih Klasis Asal --</option>
                            @foreach ($klasisOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('asal_klasis_id', $asalKlasisId) == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                        @error('asal_klasis_id') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="asal_jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Asal Jemaat (Sebelumnya)</label>
                        <select id="asal_jemaat_id" name="asal_jemaat_id"
                                class="input-field @error('asal_jemaat_id') input-error @enderror">
                            <option value="">-- Pilih Jemaat Asal --</option>
                             {{-- Opsi Jemaat Asal di-load dinamis --}}
                             @if($asalKlasisId) {{-- Jika ada data awal, load opsi jemaatnya --}}
                                @php
                                    $jemaatAsalOptions = \App\Models\Jemaat::where('klasis_id', $asalKlasisId)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
                                @endphp
                                @foreach($jemaatAsalOptions as $id => $nama)
                                     <option value="{{ $id }}" {{ old('asal_jemaat_id', $asalJemaatId) == $id ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                             @endif
                        </select>
                        @error('asal_jemaat_id') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="tujuan_klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Klasis (Baru)</label>
                        <select id="tujuan_klasis_id" name="tujuan_klasis_id"
                                class="input-field @error('tujuan_klasis_id') input-error @enderror">
                            <option value="">-- Pilih Klasis Tujuan --</option>
                            @foreach ($klasisOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('tujuan_klasis_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                         <p class="mt-1 text-xs text-gray-500 italic">Kosongkan jika Emeritus/Keluar/Meninggal.</p>
                        @error('tujuan_klasis_id') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="tujuan_jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Jemaat (Baru)</label>
                        <select id="tujuan_jemaat_id" name="tujuan_jemaat_id"
                                class="input-field @error('tujuan_jemaat_id') input-error @enderror">
                            <option value="">-- Pilih Jemaat Tujuan --</option>
                            {{-- Opsi Jemaat Tujuan di-load dinamis --}}
                             @if(old('tujuan_klasis_id')) {{-- Jika ada old input klasis tujuan, load jemaatnya --}}
                                @php
                                    $jemaatTujuanOptions = \App\Models\Jemaat::where('klasis_id', old('tujuan_klasis_id'))->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
                                @endphp
                                @foreach($jemaatTujuanOptions as $id => $nama)
                                     <option value="{{ $id }}" {{ old('tujuan_jemaat_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                             @endif
                        </select>
                         <p class="mt-1 text-xs text-gray-500 italic">Pilih Klasis Tujuan terlebih dahulu.</p>
                        @error('tujuan_jemaat_id') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                     <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                                  class="input-field @error('keterangan') input-error @enderror">{{ old('keterangan') }}</textarea>
                        @error('keterangan') <p class="error-message">{{ $message }}</p> @enderror
                    </div>
                 </div>
            </section>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            <a href="{{ route('admin.pendeta.show', $pendeta->id) }}" class="btn-secondary"> Batal </a>
            <button type="submit" class="btn-primary"> Simpan Data Mutasi </button>
        </div>
    </form>
</div>

{{-- Style umum (bisa dipindah ke layout) --}}
@push('styles')
<style>
    .input-field { display: block; width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #D1D5DB; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
    .input-field:focus { outline: none; border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); }
    .input-error { border-color: #EF4444 !important; }
    .input-error:focus { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important; }
    .error-message { margin-top: 0.25rem; font-size: 0.75rem; color: #DC2626; }
    .btn-primary { background-color: #3B82F6; color: white; font-weight: 600; padding: 0.5rem 1.5rem; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: background-color 150ms ease-in-out; }
    .btn-primary:hover { background-color: #2563EB; }
    .btn-secondary { background-color: #E5E7EB; color: #1F2937; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: background-color 150ms ease-in-out; }
    .btn-secondary:hover { background-color: #D1D5DB; }
    .flash-message { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const klasisAsalSelect = document.getElementById('asal_klasis_id');
    const jemaatAsalSelect = document.getElementById('asal_jemaat_id');
    const klasisTujuanSelect = document.getElementById('tujuan_klasis_id');
    const jemaatTujuanSelect = document.getElementById('tujuan_jemaat_id');
    // Ganti route name jika Anda menamakannya berbeda
    const apiUrl = "{{ route('api.jemaat.by.klasis', ['klasisId' => ':klasisId']) }}";

    function populateJemaatOptions(klasisSelect, jemaatSelect, selectedJemaatId = null) {
        const selectedKlasisId = klasisSelect.value;
        const currentSelectedJemaat = jemaatSelect.value; // Simpan pilihan saat ini (dari old())

        jemaatSelect.innerHTML = '<option value="">-- Memuat Jemaat... --</option>'; // Loading state
        jemaatSelect.disabled = true;

        if (!selectedKlasisId) {
            jemaatSelect.innerHTML = '<option value="">-- Pilih Klasis Dahulu --</option>';
            jemaatSelect.disabled = false;
            return;
        }

        const fetchUrl = apiUrl.replace(':klasisId', selectedKlasisId);

        fetch(fetchUrl)
            .then(response => {
                if (!response.ok) { throw new Error('Network response was not ok'); }
                return response.json();
            })
            .then(data => {
                jemaatSelect.innerHTML = '<option value="">-- Pilih Jemaat --</option>'; // Reset
                if(data && data.length > 0) {
                    data.forEach(jemaat => {
                        const option = document.createElement('option');
                        option.value = jemaat.id;
                        option.textContent = jemaat.nama_jemaat;
                        if (selectedJemaatId && jemaat.id == selectedJemaatId) {
                            option.selected = true;
                        } else if (!selectedJemaatId && currentSelectedJemaat && jemaat.id == currentSelectedJemaat) {
                            option.selected = true;
                        }
                        jemaatSelect.appendChild(option);
                    });
                } else {
                     jemaatSelect.innerHTML = '<option value="" disabled>-- Tidak ada jemaat --</option>';
                }
                jemaatSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching jemaat:', error);
                jemaatSelect.innerHTML = '<option value="" disabled>-- Gagal memuat --</option>';
                jemaatSelect.disabled = false;
            });
    }

    if (klasisAsalSelect) {
        klasisAsalSelect.addEventListener('change', () => populateJemaatOptions(klasisAsalSelect, jemaatAsalSelect));
        const initialAsalJemaatId = '{{ old('asal_jemaat_id', $asalJemaatId ?? '') }}'; // Gunakan $asalJemaatId dari controller
        if (klasisAsalSelect.value) {
            populateJemaatOptions(klasisAsalSelect, jemaatAsalSelect, initialAsalJemaatId);
        } else {
             jemaatAsalSelect.innerHTML = '<option value="">-- Pilih Klasis Dahulu --</option>';
        }
    }

    if (klasisTujuanSelect) {
        klasisTujuanSelect.addEventListener('change', () => populateJemaatOptions(klasisTujuanSelect, jemaatTujuanSelect));
        const initialTujuanJemaatId = '{{ old('tujuan_jemaat_id') }}';
         if (klasisTujuanSelect.value) {
            populateJemaatOptions(klasisTujuanSelect, jemaatTujuanSelect, initialTujuanJemaatId);
        } else {
             jemaatTujuanSelect.innerHTML = '<option value="">-- Pilih Klasis Dahulu --</option>';
        }
    }
});
</script>
@endpush

@endsection