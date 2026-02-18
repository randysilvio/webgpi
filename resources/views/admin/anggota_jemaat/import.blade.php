@extends('layouts.app')

@section('title', 'Import Anggota Jemaat')

@section('content')
    {{-- 1. Tambahkan CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h2 class="font-bold text-slate-700 uppercase text-xs tracking-wider">Import Anggota (Excel/CSV)</h2>
            <a href="{{ route('admin.anggota-jemaat.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></a>
        </div>

        <div class="p-6">
            @if(session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 text-xs text-red-700">
                    <p class="font-bold">Gagal Import:</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('admin.anggota-jemaat.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-6">
                    {{-- 2. Dropdown Jemaat dengan ID khusus untuk Select2 --}}
                    <div class="bg-blue-50 p-4 rounded border border-blue-100">
                        <label class="block text-xs font-bold uppercase text-blue-800 mb-2">Target Jemaat <span class="text-red-500">*</span></label>
                        
                        <select name="jemaat_id" id="select-jemaat" class="w-full border-slate-300 rounded text-sm" required>
                            <option value="">-- Ketik untuk Mencari Jemaat --</option>
                            @foreach($jemaatOptions as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>

                        <p class="text-[10px] text-blue-600 mt-2 italic">
                            <i class="fas fa-info-circle mr-1"></i> 
                            Data dari file Excel akan otomatis dimasukkan ke Jemaat yang Anda pilih di sini.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Upload File (CSV/Excel)</label>
                        <input type="file" name="import_file" required accept=".xlsx, .xls, .csv" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-300 rounded cursor-pointer">
                        <div class="mt-2 text-[10px] text-slate-400">
                            Pastikan format header CSV sesuai template: 
                            <span class="font-mono bg-slate-100 px-1">nama_lengkap, nik, nomor_kk, ...</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between pt-6 border-t border-slate-100 mt-6">
                    <a href="{{ route('admin.anggota-jemaat.export', ['template' => 'yes']) }}" class="text-slate-500 hover:text-slate-700 text-xs font-bold underline">
                        <i class="fas fa-download mr-1"></i> Download Template
                    </a>

                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 px-6 rounded text-xs uppercase tracking-wide transition shadow-lg">
                        <i class="fas fa-file-import mr-2"></i> Proses Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 3. Script untuk Mengaktifkan Select2 --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#select-jemaat').select2({
            placeholder: "-- Ketik Nama Jemaat --",
            allowClear: true,
            width: '100%' // Penting agar lebar menyesuaikan container
        });
    });
</script>
@endpush

{{-- Styling Tambahan agar Select2 rapi dengan Tailwind --}}
@push('styles')
<style>
    .select2-container .select2-selection--single {
        height: 42px !important;
        border-color: #cbd5e1 !important;
        border-radius: 0.375rem !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
</style>
@endpush

@endsection