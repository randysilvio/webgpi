@extends('admin.layout')

@section('title', 'Susun RAPB')
@section('header-title', 'Formulir Penyusunan Rencana Anggaran')

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('admin.perbendaharaan.anggaran.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center mb-6 space-x-4 border-b pb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tahun Anggaran</label>
                    <select name="tahun_anggaran" class="border-gray-300 rounded-md">
                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                        <option value="{{ date('Y')+1 }}">{{ date('Y')+1 }}</option>
                    </select>
                </div>
                <div class="flex-grow text-right">
                    <p class="text-xs text-gray-500">Pastikan nominal yang diinput adalah total rencana dalam 1 tahun.</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- Bagian Pendapatan --}}
                <div>
                    <h3 class="text-lg font-bold text-green-700 mb-4 flex items-center"><i class="fas fa-hand-holding-usd mr-2"></i> Kelompok Pendapatan</h3>
                    <div class="space-y-4">
                        @foreach($mataAnggarans->where('jenis', 'Pendapatan') as $index => $ma)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex-grow">
                                <span class="text-xs font-mono text-gray-400">{{ $ma->kode }}</span>
                                <p class="text-sm font-semibold text-gray-800">{{ $ma->nama_mata_anggaran }}</p>
                            </div>
                            <div class="w-1/3">
                                <input type="hidden" name="anggaran[{{ $index }}][mata_anggaran_id]" value="{{ $ma->id }}">
                                <input type="number" name="anggaran[{{ $index }}][jumlah_target]" placeholder="Rp 0" class="w-full border-gray-300 rounded-md text-right text-sm font-bold focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Bagian Belanja --}}
                <div>
                    <h3 class="text-lg font-bold text-red-700 mb-4 flex items-center"><i class="fas fa-shopping-cart mr-2"></i> Kelompok Belanja</h3>
                    <div class="space-y-4">
                        @foreach($mataAnggarans->where('jenis', 'Belanja') as $index => $ma)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex-grow">
                                <span class="text-xs font-mono text-gray-400">{{ $ma->kode }}</span>
                                <p class="text-sm font-semibold text-gray-800">{{ $ma->nama_mata_anggaran }}</p>
                            </div>
                            <div class="w-1/3">
                                <input type="hidden" name="anggaran[{{ $index + 100 }}][mata_anggaran_id]" value="{{ $ma->id }}">
                                <input type="number" name="anggaran[{{ $index + 100 }}][jumlah_target]" placeholder="Rp 0" class="w-full border-gray-300 rounded-md text-right text-sm font-bold focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t pt-6">
                <a href="{{ route('admin.perbendaharaan.anggaran.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-medium">Batal</a>
                <button type="submit" class="px-8 py-2 bg-primary text-white rounded-md font-bold shadow-lg hover:bg-blue-800 transition">Simpan Rencana Anggaran</button>
            </div>
        </div>
    </form>
</div>
@endsection