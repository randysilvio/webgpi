@extends('layouts.app')

@section('title', 'Migrasi Pangkalan Data Jemaat')

@section('content')
<div class="max-w-2xl mx-auto mt-8">
    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden">
        
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center border-l-4 border-l-green-700">
            <div>
                <h2 class="font-black text-gray-900 uppercase text-sm tracking-widest">Import Basis Data Jemaat</h2>
                <p class="text-[10px] font-bold text-gray-500 mt-1">Sistem migrasi massal menggunakan format Excel / CSV.</p>
            </div>
            <a href="{{ route('admin.jemaat.index') }}" class="text-gray-400 hover:text-red-700 transition" title="Batal & Tutup"><i class="fas fa-times text-lg"></i></a>
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

            {{-- Instruksi Operasional --}}
            <div class="mb-8 p-5 bg-blue-50 border border-blue-200 rounded shadow-sm text-blue-900">
                <h4 class="font-black mb-3 text-[10px] uppercase tracking-widest border-b border-blue-200 pb-2"><i class="fas fa-info-circle mr-2"></i> Petunjuk Standardisasi Data:</h4>
                <ol class="list-decimal list-inside space-y-2 text-xs font-medium">
                    <li>Unduh <strong>Template Excel Rasmi</strong> dari tombol di bawah.</li>
                    <li>Jangan mengubah format baris pertama (Header Kolom).</li>
                    <li>Kolom <strong class="text-red-700">Nama Jemaat</strong> dan <strong class="text-red-700">ID Klasis</strong> bersifat mandat/wajib diisi.</li>
                    <li>Pastikan <strong>Kode Jemaat</strong> unik (tidak menduplikasi data yang telah ada di server).</li>
                </ol>
                <div class="mt-4 pt-4 border-t border-blue-200 flex justify-end">
                    <a href="{{ route('admin.jemaat.export', ['template' => 'yes']) }}" class="text-[10px] font-black text-green-800 bg-green-100 border border-green-300 hover:bg-green-200 px-4 py-2 rounded uppercase tracking-widest transition flex items-center shadow-sm">
                        <i class="fas fa-download mr-2"></i> Unduh File Template (.xlsx)
                    </a>
                </div>
            </div>

            {{-- Form Upload Eksekusi --}}
            <form action="{{ route('admin.jemaat.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-8 p-6 border-2 border-dashed border-gray-300 rounded bg-gray-50 text-center hover:bg-gray-100 transition">
                    <i class="fas fa-file-excel text-4xl text-green-600 mb-3 block"></i>
                    <label class="block text-[11px] font-black uppercase text-gray-700 mb-3 tracking-widest cursor-pointer">Pilih Atau Seret Dokumen Excel Ke Sini</label>
                    <input type="file" name="import_file" required accept=".xlsx, .xls, .csv" 
                        class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-gray-800 file:text-white hover:file:bg-gray-900 cursor-pointer mx-auto max-w-sm">
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-8 rounded text-xs uppercase tracking-widest transition shadow-md flex items-center">
                        <i class="fas fa-cogs mr-2"></i> Eksekusi Migrasi Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection