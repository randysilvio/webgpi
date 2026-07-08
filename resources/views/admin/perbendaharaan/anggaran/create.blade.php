@extends('layouts.app')

@section('title', 'Penyusunan RAPB')

@section('content')
<div class="mb-6 flex items-center justify-between border-b-2 border-gray-800 pb-4">
    <div>
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Formulir Penyusunan RAPB</h2>
        <p class="text-xs text-gray-600 mt-1">Rencana Anggaran Pendapatan dan Belanja Jemaat / Sinode.</p>
    </div>
    <a href="{{ route('admin.perbendaharaan.anggaran.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
    </a>
</div>

<form action="{{ route('admin.perbendaharaan.anggaran.store') }}" method="POST">
    @csrf
    <div class="space-y-6 max-w-5xl mx-auto">
        
        {{-- HEADER: TAHUN ANGGARAN --}}
        <div class="bg-gray-50 border border-gray-300 p-5 rounded shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="w-full md:w-1/3">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Tahun Anggaran Berjalan</label>
                <select name="tahun_anggaran" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 bg-white font-bold">
                    <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                    <option value="{{ date('Y')+1 }}" selected>{{ date('Y')+1 }}</option>
                </select>
            </div>
            <div class="w-full md:w-2/3 bg-blue-50 border border-blue-200 p-3 rounded">
                <p class="text-[10px] text-blue-800 font-bold uppercase tracking-widest mb-1"><i class="fas fa-info-circle mr-1"></i> Petunjuk Pengisian</p>
                <p class="text-[10px] text-blue-700 leading-relaxed">Silakan tetapkan nominal target untuk setiap pos mata anggaran yang tersedia di bawah ini. Biarkan kosong atau isi dengan "0" apabila pos tersebut tidak dianggarkan pada tahun yang dipilih.</p>
            </div>
        </div>

        {{-- BAGIAN 1: PENDAPATAN --}}
        <div class="bg-white border border-gray-300 rounded shadow-sm overflow-hidden">
            <div class="bg-gray-100 px-5 py-3 border-b border-gray-300 flex items-center">
                <i class="fas fa-hand-holding-usd text-gray-700 mr-3 text-lg"></i>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">I. Kelompok Pendapatan (Pemasukan)</h3>
            </div>
            <div class="p-5 space-y-3">
                @foreach($mataAnggarans->where('jenis', 'Pendapatan') as $index => $ma)
                    <div class="flex flex-col md:flex-row items-center gap-4 p-3 bg-gray-50 border border-gray-200 rounded hover:bg-gray-100 transition">
                        <div class="flex-grow w-full md:w-auto">
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-mono font-bold bg-white border border-gray-300 text-gray-600 px-2 py-1 rounded">{{ $ma->kode }}</span>
                                <span class="text-xs font-bold text-gray-800 uppercase">{{ $ma->nama_mata_anggaran }}</span>
                            </div>
                        </div>
                        <div class="w-full md:w-64 flex-shrink-0">
                            <input type="hidden" name="anggaran[{{ $index }}][mata_anggaran_id]" value="{{ $ma->id }}">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-[10px] font-bold">Rp</span>
                                <input type="number" name="anggaran[{{ $index }}][jumlah_target]" placeholder="0" 
                                    class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded text-sm font-mono font-bold text-right focus:ring-blue-800 focus:border-blue-800">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- BAGIAN 2: BELANJA --}}
        <div class="bg-white border border-gray-300 rounded shadow-sm overflow-hidden">
            <div class="bg-gray-100 px-5 py-3 border-b border-gray-300 flex items-center">
                <i class="fas fa-shopping-cart text-gray-700 mr-3 text-lg"></i>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">II. Kelompok Belanja (Pengeluaran)</h3>
            </div>
            <div class="p-5 space-y-3">
                @foreach($mataAnggarans->where('jenis', 'Belanja') as $index => $ma)
                    <div class="flex flex-col md:flex-row items-center gap-4 p-3 bg-gray-50 border border-gray-200 rounded hover:bg-gray-100 transition">
                        <div class="flex-grow w-full md:w-auto">
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-mono font-bold bg-white border border-gray-300 text-gray-600 px-2 py-1 rounded">{{ $ma->kode }}</span>
                                <span class="text-xs font-bold text-gray-800 uppercase">{{ $ma->nama_mata_anggaran }}</span>
                            </div>
                        </div>
                        <div class="w-full md:w-64 flex-shrink-0">
                            <input type="hidden" name="anggaran[{{ $index + 1000 }}][mata_anggaran_id]" value="{{ $ma->id }}">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-[10px] font-bold">Rp</span>
                                <input type="number" name="anggaran[{{ $index + 1000 }}][jumlah_target]" placeholder="0" 
                                    class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded text-sm font-mono font-bold text-right focus:ring-blue-800 focus:border-blue-800">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end pt-4 pb-10">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Tetapkan RAPB
            </button>
        </div>

    </div>
</form>
@endsection