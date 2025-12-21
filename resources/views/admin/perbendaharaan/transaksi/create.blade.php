@extends('admin.layout')

@section('title', 'Catat Kas Baru')
@section('header-title', 'Pencatatan Buku Kas Umum')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6 md:p-8">
    <div class="flex justify-between items-center mb-6 border-b pb-3">
        <h2 class="text-xl font-bold text-gray-800">Formulir Transaksi Kas</h2>
        <a href="{{ route('admin.perbendaharaan.transaksi.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke BKU
        </a>
    </div>

    <form action="{{ route('admin.perbendaharaan.transaksi.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Bagian 1: Waktu & Kategori --}}
            <div class="col-span-2">
                <h3 class="text-sm font-bold text-blue-600 uppercase mb-3 border-b pb-1">Detail Transaksi</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_transaksi" value="{{ date('Y-m-d') }}" required 
                       class="w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                @error('tanggal_transaksi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mata Anggaran (COA) <span class="text-red-500">*</span></label>
                <select name="mata_anggaran_id" id="mata_anggaran_id" required class="w-full border-gray-300 rounded-md focus:ring-primary">
                    <option value="">-- Pilih Kategori Kas --</option>
                    <optgroup label="PENDAPATAN (PENERIMAAN)">
                        @foreach($mataAnggarans->where('jenis', 'Pendapatan') as $ma)
                            <option value="{{ $ma->id }}" data-jenis="Pendapatan">{{ $ma->kode }} - {{ $ma->nama_mata_anggaran }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="BELANJA (PENGELUARAN)">
                        @foreach($mataAnggarans->where('jenis', 'Belanja') as $ma)
                            <option value="{{ $ma->id }}" data-jenis="Belanja">{{ $ma->kode }} - {{ $ma->nama_mata_anggaran }}</option>
                        @endforeach
                    </optgroup>
                </select>
                @error('mata_anggaran_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Bagian 2: Nominal & Bukti --}}
            <div class="col-span-2 mt-4">
                <h3 class="text-sm font-bold text-blue-600 uppercase mb-3 border-b pb-1">Nilai & Lampiran</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rupiah) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 font-bold">Rp</span>
                    </div>
                    <input type="number" name="nominal" required min="1" 
                           class="w-full pl-10 border-gray-300 rounded-md focus:ring-primary focus:border-primary" placeholder="0">
                </div>
                @error('nominal') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Bukti (Kwitansi/Nota)</label>
                <input type="text" name="nomor_bukti" class="w-full border-gray-300 rounded-md" placeholder="Cth: KW-2025-001">
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Uraian <span class="text-red-500">*</span></label>
                <textarea name="keterangan" rows="3" required 
                          class="w-full border-gray-300 rounded-md focus:ring-primary" 
                          placeholder="Jelaskan rincian transaksi secara mendetail..."></textarea>
                @error('keterangan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Transaksi (Opsional)</label>
                <input type="file" name="file_bukti" accept="image/*,application/pdf"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-2 text-xs text-gray-400 italic">Mendukung format JPG, PNG, atau PDF (Maks 2MB).</p>
            </div>
        </div>

        {{-- Panel Info Realisasi --}}
        <div id="realization-notice" class="hidden bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Transaksi ini akan otomatis tercatat sebagai realisasi pada <strong>Rencana APB</strong> tahun berjalan untuk kategori yang dipilih.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t">
            <a href="{{ route('admin.perbendaharaan.transaksi.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-medium hover:bg-gray-300 transition">Batal</a>
            <button type="submit" class="px-8 py-2 bg-primary text-white rounded-md font-bold shadow-lg hover:bg-blue-800 transition">
                <i class="fas fa-save mr-2"></i> Simpan Transaksi
            </button>
        </div>
    </form>
</div>

<script>
    // Logika UI sederhana untuk memberikan feedback kepada user
    document.getElementById('mata_anggaran_id').addEventListener('change', function() {
        const notice = document.getElementById('realization-notice');
        if (this.value) {
            notice.classList.remove('hidden');
        } else {
            notice.classList.add('hidden');
        }
    });
</script>
@endsection