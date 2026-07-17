@extends('layouts.app')

@section('title', 'Registrasi Inventaris Aset')

@section('content')
<div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between border-b-2 border-gray-800 pb-4">
    <div>
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Buku Register Aset</h2>
        <p class="text-xs text-gray-600 mt-1">Sistem pencatatan barang inventaris dan aset tetap organisasi.</p>
    </div>
    <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center mt-3 md:mt-0">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
    </a>
</div>

<form action="{{ route('admin.perbendaharaan.aset.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="space-y-6 max-w-5xl mx-auto">
        
        {{-- KELOMPOK 1: INFORMASI IDENTITAS ASET --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-cube mr-2 text-blue-800"></i> I. Identitas & Klasifikasi Barang</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nama / Uraian Aset <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_aset" value="{{ old('nama_aset') }}" required 
                        placeholder="Contoh: Kendaraan Dinas Roda 4" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 uppercase">
                    @error('nama_aset') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomor Registrasi / Kode Aset</label>
                    <input type="text" name="kode_aset" value="{{ old('kode_aset') }}" 
                        placeholder="Biarkan kosong untuk penomoran otomatis sistem" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white font-mono">
                    @error('kode_aset') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Kelompok Kategori <span class="text-red-600">*</span></label>
                    <select name="kategori" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">-- Pilih Kelompok Barang --</option>
                        @foreach(['Tanah', 'Gedung', 'Kendaraan', 'Peralatan Elektronik', 'Meubelair', 'Alat Musik', 'Lainnya'] as $cat)
                            <option value="{{ $cat }}" {{ old('kategori') == $cat ? 'selected' : '' }}>{{ strtoupper($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Kondisi Fisik Saat Ini <span class="text-red-600">*</span></label>
                    <select name="kondisi" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">-- Kondisi Hasil Inventarisasi --</option>
                        <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>Kondisi Baik</option>
                        <option value="Rusak Ringan" {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="Rusak Berat" {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat (Afkir)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- KELOMPOK 2: NILAI & LEGALITAS --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm border-t-4 border-t-green-700">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-file-contract mr-2 text-green-700"></i> II. Dokumen Legalitas & Nilai Kapital</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tanggal Akuisisi / Pembelian</label>
                    <input type="date" name="tanggal_perolehan" value="{{ old('tanggal_perolehan') }}" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nilai Perolehan Historis (IDR)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold text-[10px]">Rp</span>
                        <input type="number" name="nilai_perolehan" value="{{ old('nilai_perolehan') }}" placeholder="0" 
                            class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded text-sm font-mono text-right focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">No. Bukti / Sertifikat / BPKB</label>
                    <input type="text" name="nomor_dokumen" value="{{ old('nomor_dokumen') }}" placeholder="Nomor BAST atau Akta..." 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white font-mono text-xs">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Status Hak Milik</label>
                    <select name="status_kepemilikan" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="Milik Sendiri" {{ old('status_kepemilikan') == 'Milik Sendiri' ? 'selected' : '' }}>Aset Hak Milik</option>
                        <option value="Sewa" {{ old('status_kepemilikan') == 'Sewa' ? 'selected' : '' }}>Sewa / Kontrak</option>
                        <option value="Pinjam Pakai" {{ old('status_kepemilikan') == 'Pinjam Pakai' ? 'selected' : '' }}>Pinjam Pakai (Titipan)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- KELOMPOK 3: FISIK & LAMPIRAN --}}
        <div class="bg-gray-100 border border-gray-300 p-5 rounded shadow-sm border-l-4 border-l-blue-800">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-300 pb-2 mb-4"><i class="fas fa-map-marker-alt mr-2 text-gray-500"></i> III. Penempatan & Bukti Fisik</h4>
            
            <div class="mb-6">
                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Lokasi Keberadaan Barang</label>
                <input type="text" name="lokasi_fisik" value="{{ old('lokasi_fisik') }}" placeholder="Contoh: Gedung A, Ruang Administrasi" 
                    class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-4 border border-gray-200 rounded">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-2 border-b border-gray-100 pb-1">Salinan Dokumen Kepemilikan (PDF)</label>
                    <input type="file" name="file_dokumen" accept=".pdf" 
                        class="w-full text-[10px] text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-gray-800 file:text-white hover:file:bg-gray-900 cursor-pointer">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-2 border-b border-gray-100 pb-1">Dokumentasi Foto Fisik Barang</label>
                    <input type="file" name="foto_aset" accept="image/*" onchange="previewImage(event, 'foto-preview')" 
                        class="w-full text-[10px] text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-gray-800 file:text-white hover:file:bg-gray-900 cursor-pointer">
                    <div class="mt-3">
                        <img id="foto-preview" src="#" alt="Preview" class="h-24 hidden rounded border border-gray-300 shadow-sm object-cover bg-gray-50 p-1">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 pb-10">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Daftarkan Inventaris
            </button>
        </div>

    </div>
</form>

@push('scripts')
<script>
    function previewImage(event, id) {
        const input = event.target;
        const preview = document.getElementById(id);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection