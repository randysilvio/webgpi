@extends('layouts.app')

@section('title', 'Laporan Konsolidasi Keuangan')

@section('content')
<div class="space-y-6">
    
    {{-- Header & Filter --}}
    <div class="bg-white p-5 rounded border border-gray-300 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h4 class="text-lg font-black text-gray-900 uppercase tracking-widest">Konsolidasi Keuangan Terpadu</h4>
            <p class="text-xs text-gray-600 mt-1">Gabungan Kas Umum (Induk) & Program Wadah Kategorial.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <form method="GET" class="flex items-center border border-gray-300 rounded shadow-sm bg-gray-50">
                <div class="px-3 text-gray-500 text-xs font-bold uppercase tracking-widest border-r border-gray-300">Tahun Buku</div>
                <select name="tahun" class="w-full border-none py-2 px-4 text-xs font-bold focus:ring-0 bg-transparent text-gray-800" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= date('Y')-3; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
            <a href="{{ route('admin.perbendaharaan.laporan.gabungan', ['tahun' => $tahun, 'export' => 'pdf']) }}" target="_blank" class="bg-gray-800 text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest hover:bg-gray-900 transition shadow-sm flex items-center">
                <i class="fas fa-print mr-2"></i> Ekspor PDF
            </a>
        </div>
    </div>

    {{-- KARTU RINGKASAN --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded border border-gray-300 shadow-sm border-t-4 border-t-green-700">
            <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Total Akumulasi Penerimaan</h4>
            <p class="text-2xl font-mono font-black text-gray-900 mt-2">Rp {{ number_format($totals['induk_masuk'] + $totals['wadah_masuk'], 0, ',', '.') }}</p>
            <div class="mt-4 flex gap-2 text-[9px] font-bold uppercase tracking-widest text-gray-600">
                <span class="bg-gray-100 px-2 py-1 rounded border border-gray-200">Induk: {{ number_format($totals['induk_masuk']) }}</span>
                <span class="bg-gray-100 px-2 py-1 rounded border border-gray-200">Wadah: {{ number_format($totals['wadah_masuk']) }}</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded border border-gray-300 shadow-sm border-t-4 border-t-red-700">
            <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Total Akumulasi Belanja</h4>
            <p class="text-2xl font-mono font-black text-gray-900 mt-2">Rp {{ number_format($totals['induk_keluar'] + $totals['wadah_keluar'], 0, ',', '.') }}</p>
            <div class="mt-4 flex gap-2 text-[9px] font-bold uppercase tracking-widest text-gray-600">
                <span class="bg-gray-100 px-2 py-1 rounded border border-gray-200">Induk: {{ number_format($totals['induk_keluar']) }}</span>
                <span class="bg-gray-100 px-2 py-1 rounded border border-gray-200">Wadah: {{ number_format($totals['wadah_keluar']) }}</span>
            </div>
        </div>

        <div class="bg-gray-800 p-6 rounded shadow-sm border border-gray-900 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Saldo Kas Bersih</h4>
                <p class="text-3xl font-mono font-black mt-2">Rp {{ number_format($totals['saldo_bersih'], 0, ',', '.') }}</p>
                <p class="text-[9px] text-gray-400 mt-4 uppercase tracking-widest font-bold border-t border-gray-700 pt-2">
                    Posisi Akhir Tahun {{ $tahun }}
                </p>
            </div>
            <i class="fas fa-wallet absolute -right-2 -bottom-4 text-7xl text-gray-700 opacity-30"></i>
        </div>
    </div>

    {{-- TABEL RINCIAN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- PENERIMAAN --}}
        <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden">
            <div class="bg-gray-100 px-5 py-3 border-b border-gray-300">
                <h3 class="font-black text-gray-800 text-[11px] uppercase tracking-widest"><i class="fas fa-arrow-down mr-2 text-gray-500"></i> I. Rincian Pendapatan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <tr class="bg-gray-50"><td colspan="2" class="px-5 py-2 font-bold text-[9px] text-gray-500 uppercase tracking-widest border-b border-gray-200">A. Pemasukan Umum (Induk)</td></tr>
                    @foreach($data['induk_masuk'] as $item)
                    <tr class="hover:bg-gray-50 border-b border-gray-100">
                        <td class="px-5 py-3 text-gray-800 font-bold uppercase">{{ $item->mataAnggaran->nama_mata_anggaran }}</td>
                        <td class="px-5 py-3 text-right font-mono font-bold">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    <tr class="bg-gray-50"><td colspan="2" class="px-5 py-2 font-bold text-[9px] text-gray-500 uppercase tracking-widest border-y border-gray-200">B. Pemasukan Wadah Kategorial</td></tr>
                    @foreach($data['wadah_masuk'] as $item)
                    <tr class="hover:bg-gray-50 border-b border-gray-100">
                        <td class="px-5 py-3 text-gray-800">
                            <span class="font-bold text-[8px] bg-gray-200 text-gray-800 px-1.5 py-0.5 rounded mr-1 uppercase">{{ $item->jenisWadah->nama_wadah }}</span> 
                            <span class="font-bold uppercase">{{ $item->nama_pos_anggaran }}</span>
                        </td>
                        <td class="px-5 py-3 text-right font-mono font-bold">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    <tr class="bg-gray-100 border-t-2 border-gray-800">
                        <td class="px-5 py-4 text-right font-black uppercase text-[10px] tracking-widest">Total Pendapatan</td>
                        <td class="px-5 py-4 text-right font-mono font-black text-sm">Rp {{ number_format($totals['induk_masuk'] + $totals['wadah_masuk'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- BELANJA --}}
        <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden">
            <div class="bg-gray-100 px-5 py-3 border-b border-gray-300">
                <h3 class="font-black text-gray-800 text-[11px] uppercase tracking-widest"><i class="fas fa-arrow-up mr-2 text-gray-500"></i> II. Rincian Belanja</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <tr class="bg-gray-50"><td colspan="2" class="px-5 py-2 font-bold text-[9px] text-gray-500 uppercase tracking-widest border-b border-gray-200">A. Belanja Rutin (Induk)</td></tr>
                    @foreach($data['induk_keluar'] as $item)
                    <tr class="hover:bg-gray-50 border-b border-gray-100">
                        <td class="px-5 py-3 text-gray-800 font-bold uppercase">{{ $item->mataAnggaran->nama_mata_anggaran }}</td>
                        <td class="px-5 py-3 text-right font-mono font-bold">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    <tr class="bg-gray-50"><td colspan="2" class="px-5 py-2 font-bold text-[9px] text-gray-500 uppercase tracking-widest border-y border-gray-200">B. Belanja Program Wadah</td></tr>
                    @foreach($data['wadah_keluar'] as $item)
                    <tr class="hover:bg-gray-50 border-b border-gray-100">
                        <td class="px-5 py-3 text-gray-800">
                            <span class="font-bold text-[8px] bg-gray-200 text-gray-800 px-1.5 py-0.5 rounded mr-1 uppercase">{{ $item->jenisWadah->nama_wadah }}</span> 
                            <span class="font-bold uppercase">{{ $item->nama_pos_anggaran }}</span>
                        </td>
                        <td class="px-5 py-3 text-right font-mono font-bold">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    <tr class="bg-gray-100 border-t-2 border-gray-800">
                        <td class="px-5 py-4 text-right font-black uppercase text-[10px] tracking-widest">Total Belanja</td>
                        <td class="px-5 py-4 text-right font-mono font-black text-sm">Rp {{ number_format($totals['induk_keluar'] + $totals['wadah_keluar'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- ANALISA KESEHATAN --}}
    @php
        $totalMasuk = $totals['induk_masuk'] + $totals['wadah_masuk'];
        $totalKeluar = $totals['induk_keluar'] + $totals['wadah_keluar'];
        
        $rasioBelanja = $totalMasuk > 0 ? ($totalKeluar / $totalMasuk) * 100 : 0;
        $kontribusiWadah = $totalMasuk > 0 ? ($totals['wadah_masuk'] / $totalMasuk) * 100 : 0;
        $kontribusiInduk = $totalMasuk > 0 ? ($totals['induk_masuk'] / $totalMasuk) * 100 : 0;
    @endphp

    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden mb-8">
        <div class="bg-gray-100 px-6 py-4 border-b border-gray-300">
            <h3 class="font-black text-gray-800 text-[11px] uppercase tracking-widest"><i class="fas fa-microscope mr-2 text-gray-500"></i> Analisa Kesehatan Rasio Keuangan</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <table class="w-full text-xs">
                    <thead class="text-gray-500 text-[9px] uppercase font-bold tracking-widest border-b border-gray-300">
                        <tr>
                            <th class="py-2 text-left">Indikator</th>
                            <th class="py-2 text-right">Nilai / Persentase</th>
                            <th class="py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-3 font-bold text-gray-800 uppercase">Rasio Belanja vs Pendapatan</td>
                            <td class="py-3 text-right font-mono font-bold">{{ number_format($rasioBelanja, 1) }}%</td>
                            <td class="py-3 text-center">
                                @if($rasioBelanja > 100) <span class="bg-red-100 text-red-800 border border-red-200 text-[9px] font-bold px-2 py-0.5 rounded">DEFISIT</span>
                                @elseif($rasioBelanja > 90) <span class="bg-yellow-100 text-yellow-800 border border-yellow-200 text-[9px] font-bold px-2 py-0.5 rounded">WASPADA</span>
                                @else <span class="bg-green-100 text-green-800 border border-green-200 text-[9px] font-bold px-2 py-0.5 rounded">SEHAT</span> @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="py-3 font-bold text-gray-800 uppercase">Kontribusi Wadah Kategorial</td>
                            <td class="py-3 text-right font-mono font-bold">{{ number_format($kontribusiWadah, 1) }}%</td>
                            <td class="py-3 text-center"><span class="bg-gray-100 text-gray-600 border border-gray-300 text-[9px] font-bold px-2 py-0.5 rounded">INFOMASI</span></td>
                        </tr>
                        <tr>
                            <td class="py-3 font-bold text-gray-800 uppercase">Posisi Neraca Akhir</td>
                            <td class="py-3 text-right font-mono font-black {{ $totals['saldo_bersih'] < 0 ? 'text-red-700' : 'text-gray-900' }}">Rp {{ number_format($totals['saldo_bersih'], 0, ',', '.') }}</td>
                            <td class="py-3 text-center">
                                @if($totals['saldo_bersih'] >= 0) <span class="bg-green-100 text-green-800 border border-green-200 text-[9px] font-bold px-2 py-0.5 rounded">SURPLUS</span>
                                @else <span class="bg-red-100 text-red-800 border border-red-200 text-[9px] font-bold px-2 py-0.5 rounded">DEFISIT</span> @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col justify-center space-y-6">
                <div>
                    <div class="flex justify-between text-[10px] mb-1.5 uppercase font-bold text-gray-500 tracking-widest">
                        <span>Komposisi Pendapatan</span><span>100%</span>
                    </div>
                    <div class="w-full h-4 bg-gray-200 border border-gray-300 overflow-hidden flex">
                        <div class="bg-gray-800 h-full" style="width: {{ $kontribusiInduk }}%"></div>
                        <div class="bg-gray-400 h-full" style="width: {{ $kontribusiWadah }}%"></div>
                    </div>
                    <div class="flex justify-between text-[9px] mt-2 font-bold uppercase text-gray-600">
                        <div><span class="inline-block w-2 h-2 bg-gray-800 mr-1"></span> Induk ({{ number_format($kontribusiInduk,0) }}%)</div>
                        <div><span class="inline-block w-2 h-2 bg-gray-400 mr-1"></span> Wadah ({{ number_format($kontribusiWadah,0) }}%)</div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-[10px] mb-1.5 uppercase font-bold text-gray-500 tracking-widest">
                        <span>Efisiensi Serapan Dana</span><span>Limit: 100%</span>
                    </div>
                    <div class="w-full h-4 bg-gray-200 border border-gray-300 overflow-hidden relative">
                        <div class="{{ $rasioBelanja > 100 ? 'bg-red-700' : 'bg-gray-800' }} h-full transition-all" style="width: {{ min($rasioBelanja, 100) }}%"></div>
                    </div>
                    <div class="text-[9px] mt-2 font-bold uppercase text-gray-600 text-right">
                        {{ number_format($rasioBelanja, 1) }}% Dana Terpakai
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection