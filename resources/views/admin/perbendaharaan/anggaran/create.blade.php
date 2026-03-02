@extends('layouts.app')

@section('title', 'Susun RAPB')

@section('content')
    <x-admin-form 
        title="Formulir Penyusunan RAPB" 
        action="{{ route('admin.perbendaharaan.anggaran.store') }}" 
        back-route="{{ route('admin.perbendaharaan.anggaran.index') }}"
    >
        <div class="space-y-8">
            
            {{-- HEADER: TAHUN --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100 flex items-center justify-between">
                <div>
                    <label class="block text-xs font-bold text-blue-800 uppercase mb-1">Tahun Anggaran</label>
                    <select name="tahun_anggaran" class="border-blue-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                        <option value="{{ date('Y')+1 }}" selected>{{ date('Y')+1 }}</option>
                    </select>
                </div>
                <div class="text-right max-w-md">
                    <p class="text-xs text-blue-600 italic">
                        <i class="fas fa-info-circle mr-1"></i> 
                        Silakan isi nominal target untuk setiap mata anggaran. Biarkan 0 atau kosong jika tidak dianggarkan.
                    </p>
                </div>
            </div>

            {{-- BAGIAN 1: PENDAPATAN --}}
            <div class="border rounded-xl overflow-hidden">
                <div class="bg-emerald-50 px-4 py-3 border-b border-emerald-100 flex items-center">
                    <i class="fas fa-hand-holding-usd text-emerald-600 mr-2"></i>
                    <h3 class="text-sm font-bold text-emerald-800 uppercase">Kelompok Pendapatan</h3>
                </div>
                <div class="p-4 space-y-3 bg-white">
                    @foreach($mataAnggarans->where('jenis', 'Pendapatan') as $index => $ma)
                        <div class="flex items-center gap-4 p-2 hover:bg-slate-50 rounded transition border border-transparent hover:border-slate-200">
                            <div class="flex-grow">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-mono font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200">{{ $ma->kode }}</span>
                                    <span class="text-sm font-semibold text-slate-700">{{ $ma->nama_mata_anggaran }}</span>
                                </div>
                            </div>
                            <div class="w-48">
                                <input type="hidden" name="anggaran[{{ $index }}][mata_anggaran_id]" value="{{ $ma->id }}">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-bold">Rp</span>
                                    <input type="number" name="anggaran[{{ $index }}][jumlah_target]" placeholder="0" 
                                        class="w-full pl-8 pr-3 py-1.5 border-slate-300 rounded text-sm font-mono text-right focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- BAGIAN 2: BELANJA --}}
            <div class="border rounded-xl overflow-hidden">
                <div class="bg-red-50 px-4 py-3 border-b border-red-100 flex items-center">
                    <i class="fas fa-shopping-bag text-red-600 mr-2"></i>
                    <h3 class="text-sm font-bold text-red-800 uppercase">Kelompok Belanja</h3>
                </div>
                <div class="p-4 space-y-3 bg-white">
                    @foreach($mataAnggarans->where('jenis', 'Belanja') as $index => $ma)
                        <div class="flex items-center gap-4 p-2 hover:bg-slate-50 rounded transition border border-transparent hover:border-slate-200">
                            <div class="flex-grow">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-mono font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200">{{ $ma->kode }}</span>
                                    <span class="text-sm font-semibold text-slate-700">{{ $ma->nama_mata_anggaran }}</span>
                                </div>
                            </div>
                            <div class="w-48">
                                <input type="hidden" name="anggaran[{{ $index + 1000 }}][mata_anggaran_id]" value="{{ $ma->id }}">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-bold">Rp</span>
                                    <input type="number" name="anggaran[{{ $index + 1000 }}][jumlah_target]" placeholder="0" 
                                        class="w-full pl-8 pr-3 py-1.5 border-slate-300 rounded text-sm font-mono text-right focus:ring-red-500 focus:border-red-500">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </x-admin-form>
@endsection