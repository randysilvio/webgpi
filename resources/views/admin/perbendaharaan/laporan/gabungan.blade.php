@extends('admin.layout')

@section('title', 'Laporan Konsolidasi')
@section('header-title', 'Laporan Keuangan Terpadu')

@section('content')
<div class="space-y-6">
    
    {{-- Header & Filter --}}
    <div class="bg-white p-4 rounded-xl shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-2">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                <i class="fas fa-chart-pie text-xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800">Konsolidasi Keuangan</h4>
                <p class="text-xs text-gray-500">Menggabungkan Kas Umum & Kas Wadah Kategorial</p>
            </div>
        </div>
        
        <div class="flex gap-3">
            <form method="GET" class="flex items-center">
                <label class="text-xs font-bold text-gray-500 mr-2 uppercase">Tahun:</label>
                <select name="tahun" class="border-gray-300 rounded-lg text-sm focus:ring-indigo-500" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
            {{-- Tombol PDF --}}
            <a href="{{ route('admin.perbendaharaan.laporan.gabungan', ['tahun' => $tahun, 'export' => 'pdf']) }}" target="_blank" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-700 transition flex items-center">
                <i class="fas fa-file-pdf mr-2"></i> Export PDF
            </a>
        </div>
    </div>

    {{-- Ringkasan Kartu (Saldo) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500 relative overflow-hidden">
            <div class="relative z-10">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Penerimaan</h4>
                <p class="text-2xl font-black text-green-700 mt-2">Rp {{ number_format($totals['induk_masuk'] + $totals['wadah_masuk'], 0, ',', '.') }}</p>
                <div class="mt-2 text-xs font-medium text-gray-400">
                    <span class="text-green-600 bg-green-50 px-1 rounded">Induk: {{ number_format($totals['induk_masuk']) }}</span> + 
                    <span class="text-blue-600 bg-blue-50 px-1 rounded">Wadah: {{ number_format($totals['wadah_masuk']) }}</span>
                </div>
            </div>
            <i class="fas fa-hand-holding-usd absolute -right-4 -bottom-4 text-8xl text-green-50 opacity-50"></i>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500 relative overflow-hidden">
            <div class="relative z-10">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Belanja</h4>
                <p class="text-2xl font-black text-red-700 mt-2">Rp {{ number_format($totals['induk_keluar'] + $totals['wadah_keluar'], 0, ',', '.') }}</p>
                <div class="mt-2 text-xs font-medium text-gray-400">
                    <span class="text-red-600 bg-red-50 px-1 rounded">Rutin: {{ number_format($totals['induk_keluar']) }}</span> + 
                    <span class="text-orange-600 bg-orange-50 px-1 rounded">Program: {{ number_format($totals['wadah_keluar']) }}</span>
                </div>
            </div>
            <i class="fas fa-shopping-cart absolute -right-4 -bottom-4 text-8xl text-red-50 opacity-50"></i>
        </div>

        <div class="bg-indigo-600 p-6 rounded-xl shadow-lg text-white relative overflow-hidden">
            <div class="relative z-10">
                <h4 class="text-xs font-bold text-indigo-200 uppercase tracking-wider">Saldo Kas Bersih</h4>
                <p class="text-3xl font-black mt-2 tracking-tight">Rp {{ number_format($totals['saldo_bersih'], 0, ',', '.') }}</p>
                <p class="text-[10px] text-indigo-300 mt-2 italic">*Total aset likuid saat ini (Tahun {{ $tahun }})</p>
            </div>
            <i class="fas fa-wallet absolute -right-4 -bottom-4 text-8xl text-indigo-500 opacity-50"></i>
        </div>
    </div>

    {{-- Tabel Rincian --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- TABEL PENERIMAAN --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-green-50 px-6 py-3 border-b border-green-100 flex justify-between items-center">
                <h3 class="font-black text-green-800 text-xs uppercase tracking-widest">I. Rincian Penerimaan</h3>
                <i class="fas fa-arrow-down text-green-300"></i>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    {{-- Section Induk --}}
                    <tr class="bg-gray-50"><td colspan="2" class="px-4 py-2 font-bold text-xs text-gray-500 uppercase tracking-wider pl-6">A. Kas Umum (Induk)</td></tr>
                    @foreach($data['induk_masuk'] as $item)
                    <tr class="group hover:bg-green-50/30 transition">
                        <td class="px-6 py-2 border-b border-gray-100 text-gray-700">{{ $item->mataAnggaran->nama_mata_anggaran }}</td>
                        <td class="px-6 py-2 border-b border-gray-100 text-right font-mono text-gray-900">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    {{-- Section Wadah --}}
                    <tr class="bg-gray-50"><td colspan="2" class="px-4 py-2 font-bold text-xs text-gray-500 uppercase tracking-wider pl-6">B. Kas Wadah Kategorial</td></tr>
                    @foreach($data['wadah_masuk'] as $item)
                    <tr class="group hover:bg-green-50/30 transition">
                        <td class="px-6 py-2 border-b border-gray-100 text-gray-700">
                            <span class="font-bold text-xs text-blue-600 mr-1">[{{ $item->jenisWadah->nama_wadah }}]</span> 
                            {{ $item->nama_pos_anggaran }}
                        </td>
                        <td class="px-6 py-2 border-b border-gray-100 text-right font-mono text-gray-900">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    {{-- Total --}}
                    <tr class="bg-green-100 font-bold text-green-900">
                        <td class="px-6 py-3 text-right uppercase text-xs">Total Penerimaan</td>
                        <td class="px-6 py-3 text-right font-mono">Rp {{ number_format($totals['induk_masuk'] + $totals['wadah_masuk'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- TABEL PENGELUARAN --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-red-50 px-6 py-3 border-b border-red-100 flex justify-between items-center">
                <h3 class="font-black text-red-800 text-xs uppercase tracking-widest">II. Rincian Belanja</h3>
                <i class="fas fa-arrow-up text-red-300"></i>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    {{-- Section Induk --}}
                    <tr class="bg-gray-50"><td colspan="2" class="px-4 py-2 font-bold text-xs text-gray-500 uppercase tracking-wider pl-6">A. Belanja Rutin (Induk)</td></tr>
                    @foreach($data['induk_keluar'] as $item)
                    <tr class="group hover:bg-red-50/30 transition">
                        <td class="px-6 py-2 border-b border-gray-100 text-gray-700">{{ $item->mataAnggaran->nama_mata_anggaran }}</td>
                        <td class="px-6 py-2 border-b border-gray-100 text-right font-mono text-gray-900">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    {{-- Section Wadah --}}
                    <tr class="bg-gray-50"><td colspan="2" class="px-4 py-2 font-bold text-xs text-gray-500 uppercase tracking-wider pl-6">B. Belanja Program Wadah</td></tr>
                    @foreach($data['wadah_keluar'] as $item)
                    <tr class="group hover:bg-red-50/30 transition">
                        <td class="px-6 py-2 border-b border-gray-100 text-gray-700">
                            <span class="font-bold text-xs text-blue-600 mr-1">[{{ $item->jenisWadah->nama_wadah }}]</span> 
                            {{ $item->nama_pos_anggaran }}
                        </td>
                        <td class="px-6 py-2 border-b border-gray-100 text-right font-mono text-gray-900">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    {{-- Total --}}
                    <tr class="bg-red-100 font-bold text-red-900">
                        <td class="px-6 py-3 text-right uppercase text-xs">Total Belanja</td>
                        <td class="px-6 py-3 text-right font-mono">Rp {{ number_format($totals['induk_keluar'] + $totals['wadah_keluar'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- BARU: TABEL ANALISA SEDERHANA --}}
    @php
        $totalMasuk = $totals['induk_masuk'] + $totals['wadah_masuk'];
        $totalKeluar = $totals['induk_keluar'] + $totals['wadah_keluar'];
        
        // Menghindari Division by Zero
        $rasioBelanja = $totalMasuk > 0 ? ($totalKeluar / $totalMasuk) * 100 : 0;
        $kontribusiWadah = $totalMasuk > 0 ? ($totals['wadah_masuk'] / $totalMasuk) * 100 : 0;
        $kontribusiInduk = $totalMasuk > 0 ? ($totals['induk_masuk'] / $totalMasuk) * 100 : 0;
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-6">
        <div class="bg-gray-800 px-6 py-3 border-b border-gray-700 flex justify-between items-center">
            <h3 class="font-black text-white text-xs uppercase tracking-widest"><i class="fas fa-chart-line mr-2"></i> Analisa Kesehatan Keuangan (Tahun {{ $tahun }})</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {{-- 1. Tabel Indikator --}}
            <div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold">
                        <tr>
                            <th class="px-4 py-2 text-left">Indikator</th>
                            <th class="px-4 py-2 text-right">Nilai / Persentase</th>
                            <th class="px-4 py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="px-4 py-3 font-medium">Rasio Belanja vs Pendapatan</td>
                            <td class="px-4 py-3 text-right font-mono">{{ number_format($rasioBelanja, 1) }}%</td>
                            <td class="px-4 py-3 text-center">
                                @if($rasioBelanja > 100)
                                    <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-1 rounded">DEFISIT</span>
                                @elseif($rasioBelanja > 90)
                                    <span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-1 rounded">WASPADA</span>
                                @else
                                    <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-1 rounded">SEHAT</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Kontribusi Wadah Kategorial</td>
                            <td class="px-4 py-3 text-right font-mono">{{ number_format($kontribusiWadah, 1) }}%</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-1 rounded">SUPLEMEN</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Posisi Neraca (Surplus/Defisit)</td>
                            <td class="px-4 py-3 text-right font-mono font-bold {{ $totals['saldo_bersih'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                Rp {{ number_format($totals['saldo_bersih'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($totals['saldo_bersih'] >= 0)
                                    <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-1 rounded">SURPLUS</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-1 rounded">DEFISIT</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 2. Visualisasi Bar Sederhana --}}
            <div class="flex flex-col justify-center space-y-6">
                {{-- Bar Pendapatan --}}
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-bold text-gray-600">Komposisi Pendapatan</span>
                        <span class="text-gray-400">Total: 100%</span>
                    </div>
                    <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden flex">
                        <div class="bg-green-500 h-full" style="width: {{ $kontribusiInduk }}%" title="Induk: {{ number_format($kontribusiInduk,1) }}%"></div>
                        <div class="bg-blue-500 h-full" style="width: {{ $kontribusiWadah }}%" title="Wadah: {{ number_format($kontribusiWadah,1) }}%"></div>
                    </div>
                    <div class="flex justify-between text-[10px] mt-1 text-gray-500">
                        <div class="flex items-center"><div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div> Induk ({{ number_format($kontribusiInduk,0) }}%)</div>
                        <div class="flex items-center"><div class="w-2 h-2 bg-blue-500 rounded-full mr-1"></div> Wadah ({{ number_format($kontribusiWadah,0) }}%)</div>
                    </div>
                </div>

                {{-- Bar Efisiensi --}}
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-bold text-gray-600">Efisiensi Penggunaan Dana</span>
                        <span class="text-gray-400">Limit: 100%</span>
                    </div>
                    <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden relative">
                        <div class="{{ $rasioBelanja > 100 ? 'bg-red-500' : ($rasioBelanja > 90 ? 'bg-yellow-500' : 'bg-indigo-500') }} h-full transition-all" style="width: {{ min($rasioBelanja, 100) }}%"></div>
                    </div>
                    <div class="text-[10px] mt-1 text-gray-500 text-right">
                        {{ number_format($rasioBelanja, 1) }}% Dana Terpakai
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection