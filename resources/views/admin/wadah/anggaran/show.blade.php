@extends('layouts.app')

@section('title', 'Detail Anggaran')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    {{-- Header & Navigasi --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.wadah.anggaran.index') }}" class="text-slate-500 hover:text-slate-800 text-xs font-bold uppercase tracking-wide flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
        <h2 class="text-lg font-bold text-slate-800">Detail Pos & Transaksi</h2>
    </div>

    {{-- Kartu Ringkasan --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 relative overflow-hidden">
        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-bl-full -mr-8 -mt-8 z-0"></div>

        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <h1 class="text-2xl font-black text-slate-800 mb-2">{{ $anggaran->nama_pos_anggaran }}</h1>
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-bold uppercase">{{ $anggaran->tahun_anggaran }}</span>
                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-bold uppercase">{{ $anggaran->jenisWadah->nama_wadah }}</span>
                    <span class="px-2 py-1 bg-{{ $anggaran->jenis_anggaran == 'penerimaan' ? 'green' : 'orange' }}-50 text-{{ $anggaran->jenis_anggaran == 'penerimaan' ? 'green' : 'orange' }}-700 rounded text-xs font-bold uppercase border border-{{ $anggaran->jenis_anggaran == 'penerimaan' ? 'green' : 'orange' }}-200">
                        {{ ucfirst($anggaran->jenis_anggaran) }}
                    </span>
                </div>
                @if($anggaran->programKerja)
                    <p class="text-sm text-blue-600"><i class="fas fa-link mr-1"></i> Program: {{ $anggaran->programKerja->nama_program }}</p>
                @endif
            </div>
            
            {{-- Statistik Angka --}}
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100 text-right">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Target Anggaran</p>
                <div class="text-xl font-mono font-bold text-slate-700 mb-3">Rp {{ number_format($anggaran->jumlah_target, 0, ',', '.') }}</div>
                
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Realisasi Saat Ini</p>
                <div class="text-2xl font-mono font-black text-blue-600">Rp {{ number_format($anggaran->jumlah_realisasi, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="mt-6 pt-6 border-t border-slate-100">
            @php
                $persen = $anggaran->jumlah_target > 0 ? ($anggaran->jumlah_realisasi / $anggaran->jumlah_target) * 100 : 0;
                $barColor = $persen >= 100 ? 'bg-green-500' : 'bg-blue-500';
                if($anggaran->jenis_anggaran == 'pengeluaran' && $persen > 100) $barColor = 'bg-red-500';
            @endphp
            <div class="flex justify-between text-xs font-bold text-slate-500 mb-1">
                <span>Capaian Realisasi</span>
                <span>{{ round($persen, 1) }}%</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                <div class="{{ $barColor }} h-3 rounded-full transition-all duration-1000" style="width: {{ min($persen, 100) }}%"></div>
            </div>
            <p class="text-xs text-slate-400 mt-2 text-right">
                {{ $anggaran->selisih >= 0 ? 'Sisa Target: ' : 'Surplus/Over: ' }} 
                <span class="font-mono font-bold text-slate-600">Rp {{ number_format(abs($anggaran->selisih), 0, ',', '.') }}</span>
            </p>
        </div>
    </div>

    {{-- Layout Grid: Form Input & Tabel Riwayat --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Kolom Kiri: Form Input Transaksi --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sticky top-4">
                <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">
                    <i class="fas fa-plus-circle mr-2 text-blue-600"></i> Catat Transaksi Baru
                </h3>

                @if(session('success'))
                    <div class="mb-4 bg-green-50 text-green-700 px-3 py-2 rounded text-xs flex items-center border border-green-200">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.wadah.transaksi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="anggaran_id" value="{{ $anggaran->id }}">

                    <x-form-input type="date" label="Tanggal" name="tanggal_transaksi" value="{{ date('Y-m-d') }}" required />
                    
                    <x-form-input type="number" label="Jumlah (Rp)" name="jumlah" required placeholder="Contoh: 500000" />

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Uraian / Keterangan</label>
                        <textarea name="uraian" rows="3" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 placeholder-slate-400" required placeholder="Contoh: Terima persembahan..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Bukti (Opsional)</label>
                        <input type="file" name="bukti_transaksi" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    </div>

                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 rounded text-sm shadow-md transition">
                        Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>

        {{-- Kolom Kanan: Tabel Riwayat --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest">Riwayat Transaksi</h3>
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead class="bg-white border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Uraian</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase text-right">Jumlah</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($anggaran->transaksi()->latest('tanggal_transaksi')->get() as $t)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-3 text-xs text-slate-600 whitespace-nowrap align-top">
                                    {{ $t->tanggal_transaksi->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-3 text-xs text-slate-700 align-top">
                                    <div class="font-medium">{{ $t->uraian }}</div>
                                    @if($t->bukti_transaksi)
                                        <a href="{{ Storage::url($t->bukti_transaksi) }}" target="_blank" class="text-blue-500 hover:underline mt-1 inline-block text-[10px]">
                                            <i class="fas fa-paperclip mr-1"></i> Lihat Bukti
                                        </a>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-xs font-mono font-bold text-slate-800 text-right align-top">
                                    Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3 text-center align-top">
                                    <form action="{{ route('admin.wadah.transaksi.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini? Saldo akan disesuaikan kembali.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-300 hover:text-red-500 transition" title="Hapus">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic text-sm">Belum ada transaksi yang dicatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection