@extends('layouts.app')

@section('title', 'Anggaran & Realisasi Wadah Kategorial')

@section('content')
<div class="space-y-6">

    {{-- HEADER & TOOLS --}}
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-gray-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Anggaran & Keuangan Wadah</h2>
            <p class="text-xs text-gray-600 mt-1">Sistem Perencanaan Anggaran Belanja (RAB) dan pemantauan arus kas kategorial.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.wadah.anggaran.create') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm transition flex items-center justify-center">
                <i class="fas fa-plus-circle mr-2"></i> Buat Pos Anggaran Baru
            </a>
        </div>
    </div>

    {{-- STATISTIK RINGKAS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm flex items-center justify-between border-t-4 border-t-gray-400">
            <div>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Anggaran Dirancang</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($anggarans->total() ?? 0) }} <span class="text-xs text-gray-500 font-bold uppercase">POS</span></p>
            </div>
            <div class="p-2 bg-gray-100 text-gray-400 rounded"><i class="fas fa-file-invoice-dollar text-2xl"></i></div>
        </div>
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm flex items-center justify-between border-t-4 border-t-green-700">
            <div>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Akumulasi Target Pemasukan</p>
                <p class="text-xl font-mono font-black text-green-800 mt-1">Rp {{ number_format($anggarans->where('jenis_anggaran', 'penerimaan')->sum('jumlah_target'), 0, ',', '.') }}</p>
            </div>
            <div class="p-2 bg-green-50 text-green-700 rounded"><i class="fas fa-arrow-down text-2xl"></i></div>
        </div>
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm flex items-center justify-between border-t-4 border-t-red-700">
            <div>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Akumulasi Target Belanja</p>
                <p class="text-xl font-mono font-black text-red-800 mt-1">Rp {{ number_format($anggarans->where('jenis_anggaran', 'pengeluaran')->sum('jumlah_target'), 0, ',', '.') }}</p>
            </div>
            <div class="p-2 bg-red-50 text-red-700 rounded"><i class="fas fa-arrow-up text-2xl"></i></div>
        </div>
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-gray-50 border border-gray-200 rounded p-4 shadow-sm flex flex-col md:flex-row gap-4">
        <form action="{{ route('admin.wadah.anggaran.index') }}" method="GET" class="w-full grid grid-cols-1 md:grid-cols-5 gap-4">
            
            <select name="tahun" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                <option value="">- Tahun Buku -</option>
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endforeach
            </select>

            <select name="wadah" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                <option value="">- Wadah Kategorial -</option>
                @foreach($jenisWadahs as $w)
                    <option value="{{ $w->id }}" {{ request('wadah') == $w->id ? 'selected' : '' }}>
                        {{ strtoupper($w->nama_wadah) }}
                    </option>
                @endforeach
            </select>

            <select name="jenis" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                <option value="">- Arus Kas -</option>
                <option value="penerimaan" {{ request('jenis') == 'penerimaan' ? 'selected' : '' }}>Pemasukan (In)</option>
                <option value="pengeluaran" {{ request('jenis') == 'pengeluaran' ? 'selected' : '' }}>Belanja (Out)</option>
            </select>

            <div class="relative md:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="pl-10 w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" 
                    placeholder="Pencarian Nama Pos Anggaran...">
            </div>

            <button type="submit" class="hidden">Saring</button>
        </form>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                        <th class="px-5 py-3 w-1/3">Pos Anggaran & Tautan Program</th>
                        <th class="px-5 py-3">Organisasi Teritori</th>
                        <th class="px-5 py-3 text-center">Arus Kas</th>
                        <th class="px-5 py-3 text-right">Target & Realisasi (Rp)</th>
                        <th class="px-5 py-3 w-32">Serapan Capaian</th>
                        <th class="px-5 py-3 text-center w-24">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($anggarans as $a)
                        @php
                            $persen = $a->jumlah_target > 0 ? ($a->jumlah_realisasi / $a->jumlah_target) * 100 : 0;
                            // Logika Pewarnaan (Corporate Muted Colors)
                            $barColor = $a->jenis_anggaran == 'penerimaan' ? 'bg-green-700' : 'bg-blue-700';
                            $bgColor = $a->jenis_anggaran == 'penerimaan' ? 'bg-green-50' : 'bg-red-50';
                            $textColor = $a->jenis_anggaran == 'penerimaan' ? 'text-green-800' : 'text-red-800';
                            
                            // Logika Bahaya (Jika Over Budget pada Pengeluaran)
                            if($a->jenis_anggaran == 'pengeluaran' && $persen > 100) {
                                $barColor = 'bg-red-700';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-5 py-4 align-top">
                                <div class="font-bold text-gray-900 uppercase text-xs leading-snug mb-2">{{ $a->nama_pos_anggaran }}</div>
                                @if($a->program_kerja_id)
                                    <div class="text-[9px] text-blue-800 font-bold tracking-widest bg-blue-50 px-2 py-0.5 rounded border border-blue-200 inline-block uppercase">
                                        <i class="fas fa-link mr-1 text-blue-400"></i> PROG: {{ Str::limit($a->programKerja->nama_program, 40) }}
                                    </div>
                                @else
                                    <div class="text-[8px] text-gray-400 font-bold uppercase tracking-widest italic border border-gray-200 px-1.5 py-0.5 rounded inline-block">Kas Rutin Non-Program</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 align-top">
                                <span class="block font-mono font-black text-gray-800 mb-1 text-xs">T.A. {{ $a->tahun_anggaran }}</span>
                                <span class="text-[9px] text-gray-700 font-bold uppercase tracking-widest bg-gray-100 px-1.5 py-0.5 rounded border border-gray-300 inline-block">
                                    {{ $a->jenisWadah->nama_wadah }} ({{ strtoupper($a->tingkat) }})
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center align-top">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border border-{{ $a->jenis_anggaran == 'penerimaan' ? 'green-300' : 'red-300' }} {{ $bgColor }} {{ $textColor }}">
                                    {{ $a->jenis_anggaran == 'penerimaan' ? 'Pemasukan' : 'Belanja' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right align-top">
                                <div class="font-mono text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1">Target: {{ number_format($a->jumlah_target, 0, ',', '.') }}</div>
                                <div class="font-mono font-black {{ $textColor }} text-sm">Real: Rp {{ number_format($a->jumlah_realisasi, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <div class="flex items-center justify-between text-[10px] font-bold text-gray-600 mb-1 uppercase tracking-widest">
                                    <span>Terserap</span>
                                    <span>{{ number_format($persen, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded h-2 overflow-hidden border border-gray-300">
                                    <div class="{{ $barColor }} h-2" style="width: {{ min($persen, 100) }}%"></div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center align-top">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.wadah.anggaran.show', $a->id) }}" class="text-gray-400 hover:text-blue-800 transition" title="Rincian & Transaksi">
                                        <i class="fas fa-list-alt text-sm"></i>
                                    </a>
                                    <a href="{{ route('admin.wadah.anggaran.edit', $a->id) }}" class="text-gray-400 hover:text-yellow-600 transition" title="Modifikasi Dokumen">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <form action="{{ route('admin.wadah.anggaran.destroy', $a->id) }}" method="POST" class="inline" onsubmit="return confirm('Peringatan: Pemusnahan data ini akan menghapus arsip anggaran beserta transaksinya. Lanjutkan?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-700 transition" title="Batalkan/Hapus Program">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500 text-sm">
                                <i class="fas fa-wallet text-3xl mb-3 block text-gray-300"></i>
                                Pangkalan data pos anggaran wadah kategorial belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($anggarans->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">
                {{ $anggarans->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection