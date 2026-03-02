@extends('layouts.app')

@section('title', 'Laporan Konsolidasi')

@section('content')
<div class="space-y-6">
    
    {{-- Header & Filter --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <i class="fas fa-chart-pie text-2xl"></i>
            </div>
            <div>
                <h4 class="text-lg font-bold text-slate-800">Konsolidasi Keuangan</h4>
                <p class="text-sm text-slate-500">Gabungan Kas Umum (Induk) & Wadah Kategorial</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <form method="GET" class="flex items-center">
                <div class="relative">
                    <i class="fas fa-calendar-alt absolute left-3 top-2.5 text-slate-400"></i>
                    <select name="tahun" class="pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500 bg-slate-50 font-bold text-slate-700" onchange="this.form.submit()">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </form>
            {{-- Tombol PDF --}}
            <a href="{{ route('admin.perbendaharaan.laporan.gabungan', ['tahun' => $tahun, 'export' => 'pdf']) }}" target="_blank" class="bg-red-600 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-red-700 transition shadow-sm flex items-center">
                <i class="fas fa-file-pdf mr-2"></i> Export PDF
            </a>
        </div>
    </div>

    {{-- Ringkasan Kartu (Saldo) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 relative overflow-hidden group hover:border-green-300 transition">
            <div class="relative z-10">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Penerimaan</h4>
                <p class="text-2xl font-black text-green-600 mt-2">Rp {{ number_format($totals['induk_masuk'] + $totals['wadah_masuk'], 0, ',', '.') }}</p>
                <div class="mt-3 flex gap-2 text-[10px] font-bold uppercase tracking-wide">
                    <span class="bg-green-50 text-green-700 px-2 py-1 rounded border border-green-100">Induk: {{ number_format($totals['induk_masuk']) }}</span>
                    <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded border border-blue-100">Wadah: {{ number_format($totals['wadah_masuk']) }}</span>
                </div>
            </div>
            <i class="fas fa-hand-holding-usd absolute -right-4 -bottom-4 text-8xl text-green-50 opacity-50 group-hover:scale-110 transition duration-500"></i>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 relative overflow-hidden group hover:border-red-300 transition">
            <div class="relative z-10">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Belanja</h4>
                <p class="text-2xl font-black text-red-600 mt-2">Rp {{ number_format($totals['induk_keluar'] + $totals['wadah_keluar'], 0, ',', '.') }}</p>
                <div class="mt-3 flex gap-2 text-[10px] font-bold uppercase tracking-wide">
                    <span class="bg-red-50 text-red-700 px-2 py-1 rounded border border-red-100">Rutin: {{ number_format($totals['induk_keluar']) }}</span>
                    <span class="bg-orange-50 text-orange-700 px-2 py-1 rounded border border-orange-100">Program: {{ number_format($totals['wadah_keluar']) }}</span>
                </div>
            </div>
            <i class="fas fa-shopping-cart absolute -right-4 -bottom-4 text-8xl text-red-50 opacity-50 group-hover:scale-110 transition duration-500"></i>
        </div>

        <div class="bg-slate-800 p-6 rounded-xl shadow-lg text-white relative overflow-hidden group">
            <div class="relative z-10">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Saldo Kas Bersih</h4>
                <p class="text-3xl font-black mt-2 tracking-tight">Rp {{ number_format($totals['saldo_bersih'], 0, ',', '.') }}</p>
                <p class="text-[10px] text-slate-400 mt-3 italic flex items-center">
                    <i class="fas fa-info-circle mr-1"></i> Total aset likuid (Tahun {{ $tahun }})
                </p>
            </div>
            <i class="fas fa-wallet absolute -right-4 -bottom-4 text-8xl text-slate-700 opacity-50 group-hover:text-slate-600 transition duration-500"></i>
        </div>
    </div>

    {{-- Tabel Rincian --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- TABEL PENERIMAAN --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-green-50 px-6 py-4 border-b border-green-100 flex justify-between items-center">
                <h3 class="font-black text-green-800 text-xs uppercase tracking-widest flex items-center">
                    <i class="fas fa-arrow-down mr-2"></i> I. Rincian Penerimaan
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    {{-- Section Induk --}}
                    <tr class="bg-slate-50"><td colspan="2" class="px-6 py-2 font-bold text-[10px] text-slate-500 uppercase tracking-wider">A. Kas Umum (Induk)</td></tr>
                    @foreach($data['induk_masuk'] as $item)
                    <tr class="group hover:bg-green-50/50 transition border-b border-slate-50 last:border-0">
                        <td class="px-6 py-3 text-slate-700">{{ $item->mataAnggaran->nama_mata_anggaran }}</td>
                        <td class="px-6 py-3 text-right font-mono font-medium text-slate-900">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    {{-- Section Wadah --}}
                    <tr class="bg-slate-50"><td colspan="2" class="px-6 py-2 font-bold text-[10px] text-slate-500 uppercase tracking-wider">B. Kas Wadah Kategorial</td></tr>
                    @foreach($data['wadah_masuk'] as $item)
                    <tr class="group hover:bg-green-50/50 transition border-b border-slate-50 last:border-0">
                        <td class="px-6 py-3 text-slate-700">
                            <span class="font-bold text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded mr-1 uppercase">{{ $item->jenisWadah->nama_wadah }}</span> 
                            {{ $item->nama_pos_anggaran }}
                        </td>
                        <td class="px-6 py-3 text-right font-mono font-medium text-slate-900">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    {{-- Total --}}
                    <tr class="bg-green-50 font-bold text-green-900 border-t-2 border-green-100">
                        <td class="px-6 py-4 text-right uppercase text-xs tracking-wider">Total Penerimaan</td>
                        <td class="px-6 py-4 text-right font-mono text-base">Rp {{ number_format($totals['induk_masuk'] + $totals['wadah_masuk'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- TABEL PENGELUARAN --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                <h3 class="font-black text-red-800 text-xs uppercase tracking-widest flex items-center">
                    <i class="fas fa-arrow-up mr-2"></i> II. Rincian Belanja
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    {{-- Section Induk --}}
                    <tr class="bg-slate-50"><td colspan="2" class="px-6 py-2 font-bold text-[10px] text-slate-500 uppercase tracking-wider">A. Belanja Rutin (Induk)</td></tr>
                    @foreach($data['induk_keluar'] as $item)
                    <tr class="group hover:bg-red-50/50 transition border-b border-slate-50 last:border-0">
                        <td class="px-6 py-3 text-slate-700">{{ $item->mataAnggaran->nama_mata_anggaran }}</td>
                        <td class="px-6 py-3 text-right font-mono font-medium text-slate-900">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    {{-- Section Wadah --}}
                    <tr class="bg-slate-50"><td colspan="2" class="px-6 py-2 font-bold text-[10px] text-slate-500 uppercase tracking-wider">B. Belanja Program Wadah</td></tr>
                    @foreach($data['wadah_keluar'] as $item)
                    <tr class="group hover:bg-red-50/50 transition border-b border-slate-50 last:border-0">
                        <td class="px-6 py-3 text-slate-700">
                            <span class="font-bold text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded mr-1 uppercase">{{ $item->jenisWadah->nama_wadah }}</span> 
                            {{ $item->nama_pos_anggaran }}
                        </td>
                        <td class="px-6 py-3 text-right font-mono font-medium text-slate-900">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    {{-- Total --}}
                    <tr class="bg-red-50 font-bold text-red-900 border-t-2 border-red-100">
                        <td class="px-6 py-4 text-right uppercase text-xs tracking-wider">Total Belanja</td>
                        <td class="px-6 py-4 text-right font-mono text-base">Rp {{ number_format($totals['induk_keluar'] + $totals['wadah_keluar'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- ANALISA KESEHATAN KEUANGAN --}}
    @php
        $totalMasuk = $totals['induk_masuk'] + $totals['wadah_masuk'];
        $totalKeluar = $totals['induk_keluar'] + $totals['wadah_keluar'];
        
        $rasioBelanja = $totalMasuk > 0 ? ($totalKeluar / $totalMasuk) * 100 : 0;
        $kontribusiWadah = $totalMasuk > 0 ? ($totals['wadah_masuk'] / $totalMasuk) * 100 : 0;
        $kontribusiInduk = $totalMasuk > 0 ? ($totals['induk_masuk'] / $totalMasuk) * 100 : 0;
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-800 px-6 py-4 border-b border-slate-700">
            <h3 class="font-black text-white text-xs uppercase tracking-widest flex items-center">
                <i class="fas fa-stethoscope mr-2"></i> Analisa Kesehatan Keuangan (Tahun {{ $tahun }})
            </h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {{-- Tabel Indikator --}}
            <div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-2 text-left rounded-l-lg">Indikator</th>
                            <th class="px-4 py-2 text-right">Nilai / Persentase</th>
                            <th class="px-4 py-2 text-center rounded-r-lg">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-700">Rasio Belanja vs Pendapatan</td>
                            <td class="px-4 py-3 text-right font-mono">{{ number_format($rasioBelanja, 1) }}%</td>
                            <td class="px-4 py-3 text-center">
                                @if($rasioBelanja > 100)
                                    <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-1 rounded">DEFISIT</span>
                                @elseif($rasioBelanja > 90)
                                    <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-1 rounded">WASPADA</span>
                                @else
                                    <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded">SEHAT</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-700">Kontribusi Wadah Kategorial</td>
                            <td class="px-4 py-3 text-right font-mono">{{ number_format($kontribusiWadah, 1) }}%</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded">SUPLEMEN</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-700">Posisi Neraca (Surplus/Defisit)</td>
                            <td class="px-4 py-3 text-right font-mono font-bold {{ $totals['saldo_bersih'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                Rp {{ number_format($totals['saldo_bersih'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($totals['saldo_bersih'] >= 0)
                                    <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded">SURPLUS</span>
                                @else
                                    <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-1 rounded">DEFISIT</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Visualisasi Bar --}}
            <div class="flex flex-col justify-center space-y-6">
                <div>
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="font-bold text-slate-600">Komposisi Pendapatan</span>
                        <span class="text-slate-400">Total: 100%</span>
                    </div>
                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden flex">
                        <div class="bg-green-500 h-full" style="width: {{ $kontribusiInduk }}%"></div>
                        <div class="bg-blue-500 h-full" style="width: {{ $kontribusiWadah }}%"></div>
                    </div>
                    <div class="flex justify-between text-[10px] mt-1.5 text-slate-500 font-medium">
                        <div class="flex items-center"><div class="w-2 h-2 bg-green-500 rounded-full mr-1.5"></div> Induk ({{ number_format($kontribusiInduk,0) }}%)</div>
                        <div class="flex items-center"><div class="w-2 h-2 bg-blue-500 rounded-full mr-1.5"></div> Wadah ({{ number_format($kontribusiWadah,0) }}%)</div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="font-bold text-slate-600">Efisiensi Penggunaan Dana</span>
                        <span class="text-slate-400">Limit: 100%</span>
                    </div>
                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden relative">
                        <div class="{{ $rasioBelanja > 100 ? 'bg-red-500' : ($rasioBelanja > 90 ? 'bg-yellow-500' : 'bg-slate-800') }} h-full transition-all" style="width: {{ min($rasioBelanja, 100) }}%"></div>
                    </div>
                    <div class="text-[10px] mt-1.5 text-slate-500 text-right font-mono">
                        {{ number_format($rasioBelanja, 1) }}% Dana Terpakai
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection