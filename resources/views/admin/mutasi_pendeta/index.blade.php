@extends('layouts.app')

@section('title', 'Riwayat Mutasi')
@section('header-title', 'Kepegawaian & Mutasi')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">Jurnal Mutasi</h2>
            <p class="text-xs text-slate-500 mt-1">Rekam jejak perpindahan tugas personel gereja.</p>
        </div>
        {{-- Tombol ini biasanya diakses dari Detail Pegawai, tapi jika ingin direct access: --}}
        {{-- 
        <a href="#" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded shadow-sm transition">
            <i class="fas fa-plus mr-2"></i> Catat Mutasi Baru
        </a> 
        --}}
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-white p-4 rounded border border-slate-200 shadow-sm">
        <form action="{{ route('admin.mutasi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="pl-10 w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 placeholder-slate-400" 
                    placeholder="Cari No. SK atau Nama Pendeta...">
            </div>
            <div>
                <select name="jenis_mutasi" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-600">
                    <option value="">-- Jenis Mutasi --</option>
                    <option value="Rutin">Rutin / Periodik</option>
                    <option value="Khusus">Khusus / Pensiun</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full h-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold uppercase text-xs border border-slate-300 rounded transition">
                    Filter Data
                </button>
            </div>
        </form>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-bold text-slate-500 tracking-wider">
                        <th class="px-6 py-4 w-12 text-center">#</th>
                        <th class="px-6 py-4">Dokumen SK</th>
                        <th class="px-6 py-4">Personel</th>
                        <th class="px-6 py-4">Pergerakan (Asal &rarr; Tujuan)</th>
                        <th class="px-6 py-4 text-center">Efektif</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mutasiHistory as $index => $mutasi)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4 text-center text-slate-500 text-sm">
                                {{ $loop->iteration + $mutasiHistory->firstItem() - 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-700">{{ $mutasi->nomor_sk }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">Tgl: {{ \Carbon\Carbon::parse($mutasi->tanggal_sk)->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded bg-slate-100 flex items-center justify-center text-slate-500 text-xs mr-3 font-bold border border-slate-200">
                                        {{ substr($mutasi->pegawai->nama_lengkap ?? 'X', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-700">{{ $mutasi->pegawai->nama_lengkap ?? 'Data Terhapus' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col text-xs">
                                    <span class="text-slate-500 mb-1 flex items-center">
                                        <i class="far fa-circle mr-2 text-slate-300 text-[8px]"></i> 
                                        {{ $mutasi->asal_instansi ?? '-' }}
                                    </span>
                                    <span class="text-slate-800 font-bold flex items-center">
                                        <i class="fas fa-arrow-right mr-2 text-slate-400 text-[10px]"></i> 
                                        {{ $mutasi->tujuan_instansi ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ \Carbon\Carbon::parse($mutasi->tanggal_efektif)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.mutasi.show', $mutasi->id) }}" class="text-slate-400 hover:text-blue-600 transition" title="Lihat Detail">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <i class="fas fa-history text-3xl mb-2 opacity-50"></i><br>
                                Belum ada riwayat mutasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $mutasiHistory->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection