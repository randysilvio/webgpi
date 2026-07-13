@extends('layouts.app')

@section('title', 'Import Data Klasis')

@section('content')
<div class="max-w-2xl mx-auto mt-8">
    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden">
        
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center border-l-4 border-l-gray-800">
            <div>
                <h2 class="font-black text-gray-900 uppercase text-sm tracking-widest">Migrasi Direktori Klasis</h2>
                <p class="text-[10px] font-bold text-gray-500 mt-1">Sistem import pangkalan data Klasis dari format Excel / CSV.</p>
            </div>
            <a href="{{ route('admin.klasis.index') }}" class="text-gray-400 hover:text-red-700 transition" title="Batal & Tutup"><i class="fas fa-times text-lg"></i></a>
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

            <form action="{{ route('admin.klasis.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    
                    {{-- Instruksi / Peringatan --}}
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded text-xs text-blue-800">
                        <p class="font-bold uppercase tracking-widest mb-1 text-[10px]"><i class="fas fa-info-circle mr-1"></i> Peraturan Integritas Data</p>
                        <p>Pastikan <strong>Kode Klasis</strong> pada dokumen Excel yang Anda unggah belum pernah terdaftar di dalam sistem untuk menghindari bentrokan (*Duplicate Entry*).</p>
                    </div>

                    {{-- Form Upload --}}
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-gray-600 mb-2 tracking-widest">Dokumen Migrasi (.XLSX / .CSV) <span class="text-red-600">*</span></label>
                        <div class="p-6 border-2 border-dashed border-gray-300 rounded bg-white text-center hover:bg-gray-50 transition cursor-pointer relative">
                            <i class="fas fa-file-excel text-4xl text-green-700 mb-3 block pointer-events-none"></i>
                            <input type="file" name="import_file" required accept=".xlsx, .xls, .csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="text-[11px] font-black uppercase text-gray-700 tracking-widest pointer-events-none">Pilih Atau Seret Dokumen Excel Kesini</div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center pt-6 border-t border-gray-200 mt-8 gap-4">
                    {{-- Opsional: Jika Anda membuat sistem template Klasis, aktifkan rute ini --}}
                    <button type="button" onclick="alert('Templat standar belum diaktifkan untuk modul Klasis.')" class="text-[10px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition flex items-center px-4 py-2 rounded border border-gray-200 bg-gray-50 cursor-not-allowed">
                        <i class="fas fa-download mr-2"></i> Unduh Templat
                    </button>
                    
                    <button type="submit" class="w-full sm:w-auto bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-8 rounded text-xs uppercase tracking-widest transition shadow-md flex items-center justify-center">
                        <i class="fas fa-cogs mr-2"></i> Eksekusi Migrasi Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection