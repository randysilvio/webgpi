@extends('layouts.app')

@section('title', 'Import Anggota Jemaat')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h2 class="font-bold text-slate-700 uppercase text-xs tracking-wider">Import Anggota (Excel)</h2>
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
                    <div class="bg-blue-50 p-4 rounded border border-blue-100">
                        <label class="block text-xs font-bold text-blue-800 uppercase mb-2">Tujuan Import</label>
                        <x-form-select name="jemaat_id" required>
                            <option value="">-- Pilih Jemaat --</option>
                            @foreach($jemaatOptions as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </x-form-select>
                        <p class="text-[10px] text-blue-600 mt-2 italic">Semua data Excel akan dimasukkan ke Jemaat yang dipilih ini.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Upload File</label>
                        <input type="file" name="import_file" required accept=".xlsx, .xls, .csv" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-300 rounded cursor-pointer">
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-slate-100 mt-6">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 px-6 rounded text-xs uppercase tracking-wide transition shadow-lg">
                        Proses Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection