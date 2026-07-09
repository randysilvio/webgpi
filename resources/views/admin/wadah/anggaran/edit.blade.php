@extends('layouts.app')

@section('title', 'Pembaruan Pos Anggaran')

@section('content')
<div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between border-b-2 border-gray-800 pb-4">
    <div>
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Penyuntingan Pos Anggaran</h2>
        <p class="text-xs text-gray-600 mt-1">Modifikasi arsip rancangan anggaran keuangan wadah.</p>
    </div>
    <a href="{{ route('admin.wadah.anggaran.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center mt-3 md:mt-0">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
    </a>
</div>

<form action="{{ route('admin.wadah.anggaran.update', $anggaran->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="space-y-6 max-w-5xl mx-auto">

        {{-- INFO READONLY (STRUKTURAL TETAP) --}}
        <div class="bg-gray-100 border border-gray-300 p-5 rounded shadow-sm border-l-4 border-l-gray-800 flex items-start gap-4">
            <i class="fas fa-lock text-gray-500 text-2xl mt-1"></i>
            <div>
                <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-1">Parameter Kedudukan (Bersifat Tetap)</h4>
                <p class="text-[10px] text-gray-600 leading-relaxed font-bold uppercase">
                    Tahun Anggaran: <span class="text-blue-800">{{ $anggaran->tahun_anggaran }}</span> <br>
                    Klasifikasi Wadah: <span class="text-blue-800">{{ $anggaran->jenisWadah->nama_wadah }}</span> <br>
                    Hierarki Pelaksanaan: <span class="text-gray-900">{{ strtoupper($anggaran->tingkat) }}</span>
                    @if($anggaran->klasis) <span class="mx-1">|</span> Wilayah: <span class="text-gray-900">{{ strtoupper($anggaran->klasis->nama_klasis) }}</span> @endif
                </p>
            </div>
        </div>

        {{-- STATUS & RINCIAN --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm border-t-4 border-t-green-700">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-file-invoice-dollar mr-2 text-green-700"></i> I. Modifikasi Rincian & Target Anggaran</h4>

            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomenklatur Pos Anggaran <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_pos_anggaran" value="{{ old('nama_pos_anggaran', $anggaran->nama_pos_anggaran) }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm uppercase bg-gray-50 font-bold">
                    @error('nama_pos_anggaran') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Klasifikasi Arus Kas <span class="text-red-600">*</span></label>
                        <select name="jenis_anggaran" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                            <option value="penerimaan" {{ old('jenis_anggaran', $anggaran->jenis_anggaran) == 'penerimaan' ? 'selected' : '' }}>Penerimaan (Pemasukan)</option>
                            <option value="pengeluaran" {{ old('jenis_anggaran', $anggaran->jenis_anggaran) == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran (Belanja)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Target Nominal Anggaran (Rp) <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold text-[10px]">Rp</span>
                            <input type="number" name="jumlah_target" value="{{ old('jumlah_target', (int)$anggaran->jumlah_target) }}" required 
                                class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded text-sm font-mono text-right focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 font-bold">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Deskripsi & Catatan Penggunaan Tambahan</label>
                    <textarea name="keterangan" rows="3" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">{{ old('keterangan', $anggaran->keterangan) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 pb-10">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Perbarui Pos Anggaran
            </button>
        </div>

    </div>
</form>
@endsection