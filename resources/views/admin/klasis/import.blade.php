@extends('layouts.app')

@section('title', 'Import Data Klasis')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h2 class="font-bold text-slate-700 uppercase text-xs tracking-wider">Import Klasis (Excel/CSV)</h2>
            <a href="{{ route('admin.klasis.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></a>
        </div>

        <div class="p-6">
            {{-- Alert --}}
            @if(session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 text-xs text-red-700">
                    <p class="font-bold">Gagal Import!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- Instruksi --}}
            <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded text-sm text-blue-800">
                <h4 class="font-bold mb-2">Panduan Singkat:</h4>
                <ol class="list-decimal list-inside space-y-1 text-xs">
                    <li>Unduh template Excel yang disediakan sistem.</li>
                    <li>Isi kolom <strong>Nama Klasis</strong> (Wajib).</li>
                    <li>Kode Klasis harus unik (tidak boleh sama dengan yang sudah ada).</li>
                    <li>Simpan file dan upload pada form di bawah.</li>
                </ol>
                <div class="mt-3">
                    <a href="{{ route('admin.klasis.export', ['template' => 'yes']) }}" class="text-xs font-bold text-white bg-green-600 hover:bg-green-700 px-3 py-1.5 rounded inline-flex items-center transition">
                        <i class="fas fa-download mr-1.5"></i> Download Template .xlsx
                    </a>
                </div>
            </div>

            {{-- Form --}}
            <form action="{{ route('admin.klasis.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-6">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Upload File</label>
                    <input type="file" name="import_file" required accept=".xlsx, .xls, .csv" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-300 rounded cursor-pointer">
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 px-6 rounded text-xs uppercase tracking-wide transition shadow-lg">
                        Mulai Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection