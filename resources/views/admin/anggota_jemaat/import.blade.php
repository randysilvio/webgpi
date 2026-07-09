@extends('layouts.app')

@section('title', 'Migrasi Pangkalan Data Anggota Jemaat')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="max-w-2xl mx-auto mt-8">
    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden">
        
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center border-l-4 border-l-gray-800">
            <div>
                <h2 class="font-black text-gray-900 uppercase text-sm tracking-widest">Import Buku Induk Anggota</h2>
                <p class="text-[10px] font-bold text-gray-500 mt-1">Sistem migrasi massal menggunakan format Excel / CSV.</p>
            </div>
            <a href="{{ route('admin.anggota-jemaat.index') }}" class="text-gray-400 hover:text-red-700 transition" title="Batal & Tutup"><i class="fas fa-times text-lg"></i></a>
        </div>

        <div class="p-6 md:p-8">
            {{-- Alert Kegagalan --}}
            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-700 p-4 text-xs text-red-800 rounded shadow-sm flex items-start">
                    <i class="fas fa-exclamation-triangle mt-0.5 mr-3 text-red-600"></i>
                    <div>
                        <p class="font-black uppercase tracking-wider mb-1">Gagal Memproses Dokumen</p>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.anggota-jemaat.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    
                    {{-- Dropdown Jemaat Select2 --}}
                    <div class="bg-gray-50 p-5 rounded border border-gray-300 shadow-sm">
                        <label class="block text-[10px] font-bold uppercase text-gray-600 mb-2 tracking-widest">Tentukan Target Jemaat Induk <span class="text-red-600">*</span></label>
                        <select name="jemaat_id" id="select-jemaat" class="w-full border-gray-300 rounded text-sm" required>
                            <option value="">-- Ketik untuk Mencari Institusi Jemaat --</option>
                            @foreach($jemaatOptions as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                        <p class="text-[9px] text-gray-500 mt-2 font-bold uppercase tracking-widest">
                            <i class="fas fa-info-circle mr-1 text-blue-800"></i> 
                            Semua baris data dari file Excel akan didaftarkan ke dalam jemaat ini.
                        </p>
                    </div>

                    {{-- Form Upload --}}
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-gray-600 mb-2 tracking-widest">Dokumen Migrasi (.XLSX / .CSV)</label>
                        <div class="p-6 border-2 border-dashed border-gray-300 rounded bg-white text-center hover:bg-gray-50 transition cursor-pointer relative">
                            <i class="fas fa-file-excel text-4xl text-green-700 mb-3 block pointer-events-none"></i>
                            <input type="file" name="import_file" required accept=".xlsx, .xls, .csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="text-[11px] font-black uppercase text-gray-700 tracking-widest pointer-events-none">Pilih Atau Seret Dokumen Kesini</div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center pt-6 border-t border-gray-200 mt-8 gap-4">
                    <a href="{{ route('admin.anggota-jemaat.export', ['template' => 'yes']) }}" class="text-[10px] font-black text-blue-800 hover:text-blue-600 uppercase tracking-widest transition flex items-center bg-blue-50 px-4 py-2 rounded border border-blue-200">
                        <i class="fas fa-download mr-2"></i> Unduh Format Template (Wajib)
                    </a>
                    <button type="submit" class="w-full sm:w-auto bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-8 rounded text-xs uppercase tracking-widest transition shadow-md flex items-center justify-center">
                        <i class="fas fa-cogs mr-2"></i> Eksekusi Migrasi Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script Select2 --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#select-jemaat').select2({
            placeholder: "-- KETIK NAMA JEMAAT --",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush

{{-- Custom CSS untuk menyelaraskan Select2 dengan gaya Tailwind yang tajam --}}
@push('styles')
<style>
    .select2-container .select2-selection--single {
        height: 42px !important;
        border-color: #d1d5db !important;
        border-radius: 0.25rem !important; /* Rounded biasa (bukan full/lg) */
        display: flex !important;
        align-items: center !important;
        background-color: #ffffff;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        font-size: 10px;
        text-transform: uppercase;
        font-weight: bold;
        letter-spacing: 0.05em;
        color: #9ca3af;
    }
</style>
@endpush
@endsection