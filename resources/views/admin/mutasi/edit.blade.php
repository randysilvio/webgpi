@extends('layouts.app')

@section('title', 'Edit Mutasi')
@section('header-title', 'Koreksi Data Mutasi')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Perhatian:</strong> Mengubah data historis mutasi dapat mempengaruhi riwayat jabatan pegawai. Pastikan perubahan sesuai dengan SK fisik.
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.mutasi.update', $mutasi->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-700 uppercase text-xs tracking-wider">Form Koreksi Mutasi</h3>
            </div>
            
            <div class="p-6 space-y-6">
                
                {{-- Data SK --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Nomor SK</label>
                        <input type="text" name="nomor_sk" value="{{ old('nomor_sk', $mutasi->nomor_sk) }}" class="w-full border-slate-300 rounded text-sm font-medium text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Tanggal SK</label>
                        <input type="date" name="tanggal_sk" value="{{ old('tanggal_sk', $mutasi->tanggal_sk) }}" class="w-full border-slate-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Tanggal Efektif</label>
                        <input type="date" name="tanggal_efektif" value="{{ old('tanggal_efektif', $mutasi->tanggal_efektif) }}" class="w-full border-slate-300 rounded text-sm">
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Data Tujuan (Hanya Teks untuk keamanan, jika ingin pindah lokasi buat mutasi baru) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Nama Instansi Asal</label>
                        <input type="text" name="asal_instansi" value="{{ old('asal_instansi', $mutasi->asal_instansi) }}" class="w-full border-slate-300 rounded text-sm bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Nama Instansi Tujuan</label>
                        <input type="text" name="tujuan_instansi" value="{{ old('tujuan_instansi', $mutasi->tujuan_instansi) }}" class="w-full border-slate-300 rounded text-sm bg-slate-50">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="3" class="w-full border-slate-300 rounded text-sm">{{ old('keterangan', $mutasi->keterangan) }}</textarea>
                    </div>
                </div>

            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('admin.mutasi.index') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 rounded text-sm font-medium hover:bg-slate-100">Batal</a>
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded text-sm font-bold uppercase hover:bg-slate-900">Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>
@endsection