@extends('layouts.app')

@section('title', 'Registrasi Kode Akun Baru')

@section('content')
    <div class="mb-6 flex items-center justify-between border-b-2 border-gray-800 pb-4">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Buat Kode Akun Baru</h2>
            <p class="text-xs text-gray-600 mt-1">Sistem Pendaftaran Mata Anggaran (COA) untuk klasifikasi transaksi.</p>
        </div>
        <a href="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
        </a>
    </div>

    <form action="{{ route('admin.perbendaharaan.mata-anggaran.store') }}" method="POST">
        @csrf
        <div class="space-y-6 max-w-4xl mx-auto">
            
            {{-- PANEL INFORMASI AKUN --}}
            <div class="bg-white border border-gray-300 p-6 rounded shadow-sm">
                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-list-ol mr-2 text-blue-800"></i> Informasi Klasifikasi Akun</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Kode Akun Registrasi <span class="text-red-600">*</span></label>
                        <input type="text" name="kode" value="{{ old('kode') }}" required placeholder="Contoh: 1.1.01" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm font-mono font-bold text-blue-800 bg-blue-50">
                        @error('kode') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nama Mata Anggaran <span class="text-red-600">*</span></label>
                        <input type="text" name="nama_mata_anggaran" value="{{ old('nama_mata_anggaran') }}" required placeholder="Contoh: Persembahan Syukur" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 uppercase">
                        @error('nama_mata_anggaran') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Jenis Klasifikasi Akun <span class="text-red-600">*</span></label>
                        <select name="jenis" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                            <option value="">-- Tentukan Jenis --</option>
                            <option value="Pendapatan" {{ old('jenis') == 'Pendapatan' ? 'selected' : '' }}>Penerimaan (Pendapatan)</option>
                            <option value="Belanja" {{ old('jenis') == 'Belanja' ? 'selected' : '' }}>Pengeluaran (Belanja)</option>
                        </select>
                        @error('jenis') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Kelompok Akun (Opsional)</label>
                        <input type="text" name="kelompok" value="{{ old('kelompok') }}" placeholder="Cth: Rutin / Pelayanan / Pembangunan" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Deskripsi & Catatan Penggunaan Tambahan</label>
                    <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">{{ old('deskripsi') }}</textarea>
                    <p class="text-[9px] text-gray-500 mt-1 uppercase tracking-widest font-bold">Berikan uraian fungsi dan batasan penggunaan pada pos anggaran ini.</p>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Daftarkan Kode Akun
                </button>
            </div>
        </div>
    </form>
@endsection