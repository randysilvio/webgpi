@extends('layouts.app')

@section('title', 'Modifikasi Kode Akun')

@section('content')
    <div class="mb-6 flex items-center justify-between border-b-2 border-gray-800 pb-4">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Modifikasi Kode Akun: {{ $mataAnggaran->kode }}</h2>
            <p class="text-xs text-gray-600 mt-1">Sistem Pembaruan Mata Anggaran (COA).</p>
        </div>
        <a href="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
        </a>
    </div>

    <form action="{{ route('admin.perbendaharaan.mata-anggaran.update', $mataAnggaran->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-6 max-w-4xl mx-auto">
            
            {{-- PANEL INFORMASI AKUN --}}
            <div class="bg-white border border-gray-300 p-6 rounded shadow-sm">
                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-list-ol mr-2 text-blue-800"></i> Informasi Klasifikasi Akun</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Kode Akun Registrasi <span class="text-red-600">*</span></label>
                        <input type="text" name="kode" value="{{ old('kode', $mataAnggaran->kode) }}" required 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm font-mono font-bold text-blue-800 bg-blue-50">
                        @error('kode') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nama Mata Anggaran <span class="text-red-600">*</span></label>
                        <input type="text" name="nama_mata_anggaran" value="{{ old('nama_mata_anggaran', $mataAnggaran->nama_mata_anggaran) }}" required 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 uppercase">
                        @error('nama_mata_anggaran') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Jenis Klasifikasi Akun <span class="text-red-600">*</span></label>
                        <select name="jenis" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                            <option value="Pendapatan" {{ old('jenis', $mataAnggaran->jenis) == 'Pendapatan' ? 'selected' : '' }}>Penerimaan (Pendapatan)</option>
                            <option value="Belanja" {{ old('jenis', $mataAnggaran->jenis) == 'Belanja' ? 'selected' : '' }}>Pengeluaran (Belanja)</option>
                        </select>
                        @error('jenis') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Kelompok Akun (Opsional)</label>
                        <input type="text" name="kelompok" value="{{ old('kelompok', $mataAnggaran->kelompok) }}" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mb-4">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Deskripsi & Catatan Penggunaan Tambahan</label>
                    <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">{{ old('deskripsi', $mataAnggaran->deskripsi) }}</textarea>
                </div>

                {{-- Status Aktif --}}
                <div class="flex items-center space-x-3 border-t border-gray-200 pt-4">
                    <input type="checkbox" name="is_active" value="1" id="is_active" {{ $mataAnggaran->is_active ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-blue-800 focus:ring-blue-800 shadow-sm cursor-pointer">
                    <label for="is_active" class="text-xs font-bold text-gray-700 uppercase cursor-pointer select-none">
                        Status Akun Aktif <span class="text-[9px] font-medium text-gray-500 normal-case ml-1 tracking-widest">(Akan muncul pada form pemilihan Buku Kas dan RAPB)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Perbarui Kode Akun
                </button>
            </div>
        </div>
    </form>
@endsection