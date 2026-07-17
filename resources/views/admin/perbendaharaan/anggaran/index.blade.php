@extends('layouts.app')

@section('title', 'Rencana Anggaran (RAPB)')

@section('content')
<div class="space-y-6">

    {{-- HEADER & TOOLS --}}
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-gray-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Anggaran Induk (RAPB)</h2>
            <p class="text-xs text-gray-600 mt-1">Penyusunan dan pemantauan Rencana Anggaran Pendapatan dan Belanja.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.perbendaharaan.anggaran.create') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wide shadow-sm transition flex items-center justify-center">
                <i class="fas fa-plus-circle mr-2"></i> Susun RAPB Baru
            </a>
        </div>
    </div>

    {{-- STATISTIK & FILTER --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm flex items-center justify-between border-l-4 border-green-600">
            <div>
                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Estimasi Pendapatan</p>
                <p class="text-lg font-mono font-black text-gray-900 mt-1">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-arrow-down text-2xl text-green-200"></i>
        </div>
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm flex items-center justify-between border-l-4 border-red-600">
            <div>
                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Rencana Belanja</p>
                <p class="text-lg font-mono font-black text-gray-900 mt-1">Rp {{ number_format($totalBelanja ?? 0, 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-arrow-up text-2xl text-red-200"></i>
        </div>
        <div class="bg-gray-800 p-5 rounded border border-gray-900 shadow-sm flex items-center justify-between text-white relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Proyeksi Saldo Bersih</p>
                <p class="text-lg font-mono font-black text-white mt-1">Rp {{ number_format(($totalPendapatan ?? 0) - ($totalBelanja ?? 0), 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-chart-line text-4xl text-gray-700 absolute -right-2 -bottom-2 opacity-50"></i>
        </div>
        
        <div class="bg-gray-50 p-4 rounded border border-gray-300 shadow-sm flex items-center">
            <form action="{{ route('admin.perbendaharaan.anggaran.index') }}" method="GET" class="w-full">
                <label class="block text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-2">Pilih Tahun Buku</label>
                <select name="tahun" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold focus:ring-blue-800 focus:border-blue-800 bg-white">
                    @for($i = date('Y')-1; $i <= date('Y')+2; $i++)
                        <option value="{{ $i }}" {{ (isset($tahun) && $tahun == $i) ? 'selected' : '' }}>T.A. {{ $i }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                        <th class="px-6 py-3">Uraian Mata Anggaran</th>
                        <th class="px-6 py-3 text-center">Klasifikasi</th>
                        <th class="px-6 py-3 text-right">Target RAPB (Rp)</th>
                        <th class="px-6 py-3 text-center">Validasi Status</th>
                        <th class="px-6 py-3 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($anggarans as $ang)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 uppercase text-xs">{{ $ang->mataAnggaran->nama_mata_anggaran ?? 'Mata Anggaran Dihapus' }}</div>
                                @if($ang->mataAnggaran)
                                <div class="text-[9px] text-gray-500 font-mono font-bold bg-gray-100 border border-gray-200 px-2 py-0.5 rounded inline-block mt-1">
                                    KODE: {{ $ang->mataAnggaran->kode }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($ang->mataAnggaran)
                                <span class="px-2 py-1 rounded text-[9px] font-bold uppercase {{ $ang->mataAnggaran->jenis == 'Pendapatan' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800' }}">
                                    {{ $ang->mataAnggaran->jenis }}
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-black text-gray-800 text-sm">
                                {{ number_format($ang->jumlah_target, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded text-[9px] font-bold uppercase bg-gray-100 text-gray-600 border border-gray-300">
                                    {{ $ang->status_anggaran ?? 'Draf' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="#" class="text-gray-400 hover:text-blue-800 transition" title="Modifikasi Item">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm">
                                <i class="fas fa-folder-open text-3xl mb-3 block text-gray-300"></i>
                                Belum ada penyusunan anggaran untuk Tahun Buku {{ $tahun ?? date('Y') }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- SAFEGUARD: Hanya tampilkan paginasi JIKA object mendukung metode 'hasPages' --}}
        @if(method_exists($anggarans, 'hasPages') && $anggarans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $anggarans->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection