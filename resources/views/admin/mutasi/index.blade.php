@extends('layouts.app')

@section('title', 'Riwayat Mutasi')
@section('header-title', 'Kepegawaian & Mutasi')

@section('content')
<div class="space-y-6">

    {{-- HEADER & TOOLS --}}
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-gray-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Jurnal Mutasi Personel</h2>
            <p class="text-xs text-gray-600 mt-1">Rekam jejak dan histori perpindahan tugas pelayan gereja.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm transition flex items-center justify-center">
                <i class="fas fa-search mr-2"></i> Pilih Pegawai Untuk Mutasi
            </a>
        </div>
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-gray-50 p-4 rounded border border-gray-200 shadow-sm">
        <form action="{{ route('admin.mutasi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="pl-10 w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" 
                    placeholder="Cari Nomor SK atau Nama Personel...">
            </div>
            
            <div>
                <select name="jenis_mutasi" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white text-gray-700">
                    <option value="">- Kategori Mutasi -</option>
                    @foreach($jenisMutasiOptions ?? [] as $opsi)
                        <option value="{{ $opsi }}" {{ request('jenis_mutasi') == $opsi ? 'selected' : '' }}>{{ strtoupper($opsi) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <button type="submit" class="w-full bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-6 py-2.5 rounded text-[10px] font-bold uppercase transition shadow-sm">
                    Saring Riwayat
                </button>
            </div>
            
        </form>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                        <th class="px-6 py-3 w-12 text-center">#</th>
                        <th class="px-6 py-3">Nomor Surat Keputusan (SK)</th>
                        <th class="px-6 py-3">Identitas Personel</th>
                        <th class="px-6 py-3">Pergerakan Penugasan</th>
                        <th class="px-6 py-3 text-center">TMT / Efektif</th>
                        <th class="px-6 py-3 text-center w-24">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($mutasiHistory as $index => $mutasi)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-center text-gray-500 font-mono text-xs">
                                {{ $loop->iteration + $mutasiHistory->firstItem() - 1 }}
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="text-xs font-bold text-gray-900 uppercase">{{ $mutasi->nomor_sk }}</div>
                                <div class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mt-1">Ditetapkan: {{ \Carbon\Carbon::parse($mutasi->tanggal_sk)->format('d F Y') }}</div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded border border-gray-300 bg-gray-100 flex items-center justify-center text-gray-400 text-xs font-bold uppercase shrink-0">
                                        {{ substr($mutasi->pegawai->nama_lengkap ?? 'X', 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold text-gray-800 uppercase">{{ $mutasi->pegawai->nama_lengkap ?? 'Data Personel Terhapus' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-col text-xs space-y-1.5">
                                    <span class="text-gray-500 font-bold uppercase text-[10px] flex items-center">
                                        <i class="far fa-circle mr-2 text-gray-300"></i> 
                                        Asal: {{ $mutasi->asalJemaat->nama_jemaat ?? $mutasi->asalKlasis->nama_klasis ?? 'Pusat Sinode' }}
                                    </span>
                                    <span class="text-gray-900 font-bold uppercase text-[10px] flex items-center">
                                        <i class="fas fa-arrow-right mr-2 text-blue-800"></i> 
                                        Tujuan: {{ $mutasi->tujuanJemaat->nama_jemaat ?? $mutasi->tujuanKlasis->nama_klasis ?? 'Pusat Sinode / Dinonaktifkan' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center align-top">
                                <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-700 border border-gray-300">
                                    {{ $mutasi->tanggal_efektif ? \Carbon\Carbon::parse($mutasi->tanggal_efektif)->format('d/m/Y') : '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center align-top">
                                <a href="{{ route('admin.mutasi.show', $mutasi->id) }}" class="text-gray-400 hover:text-blue-800 transition" title="Lihat Detail SK">
                                    <i class="fas fa-file-alt text-sm"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">
                                <i class="fas fa-history text-3xl mb-3 block text-gray-300"></i>
                                Belum ada riwayat mutasi yang tercatat dalam sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($mutasiHistory, 'hasPages') && $mutasiHistory->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $mutasiHistory->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection