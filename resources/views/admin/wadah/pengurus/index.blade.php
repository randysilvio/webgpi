@extends('layouts.app')

@section('title', 'Daftar Susunan Pengurus Wadah Kategorial')

@section('content')
<div class="space-y-6">

    {{-- HEADER & TOOLS --}}
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-blue-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Buku Personalia Kategorial</h2>
            <p class="text-xs text-gray-600 mt-1">Pangkalan data kepengurusan Wadah Kategorial di tingkat Sinode, Klasis, maupun Jemaat.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.wadah.pengurus.create') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm transition flex items-center justify-center">
                <i class="fas fa-user-plus mr-2"></i> Register Pengurus
            </a>
        </div>
    </div>

    {{-- STATISTIK RINGKAS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Total Personel Wadah</p>
            <p class="text-xl font-black text-gray-900 mt-1">{{ number_format($stats->total ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-b-4 border-b-blue-800">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Kader Aktif</p>
            <p class="text-xl font-black text-blue-900 mt-1">{{ number_format($stats->total_aktif ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-b-4 border-b-gray-400">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Demisioner / Pasif</p>
            <p class="text-xl font-black text-gray-700 mt-1">{{ number_format($stats->total_non_aktif ?? 0) }}</p>
        </div>
        <div class="bg-gray-800 p-4 border border-gray-900 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Tingkat Jemaat</p>
            <p class="text-xl font-black text-white mt-1">{{ number_format($stats->level_jemaat ?? 0) }}</p>
        </div>
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-gray-50 border border-gray-200 rounded p-4 shadow-sm flex flex-col md:flex-row gap-4">
        <form action="{{ route('admin.wadah.pengurus.index') }}" method="GET" class="w-full grid grid-cols-1 md:grid-cols-5 gap-4">
            
            <select name="jenis_wadah_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                <option value="">- Klasifikasi Wadah -</option>
                @foreach($jenisWadahs as $wadah)
                    <option value="{{ $wadah->id }}" {{ request('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>
                        {{ $wadah->nama_wadah }}
                    </option>
                @endforeach
            </select>

            <select name="tingkat" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                <option value="">- Hierarki Tingkatan -</option>
                <option value="sinode" {{ request('tingkat') == 'sinode' ? 'selected' : '' }}>TINGKAT PUSAT SINODE</option>
                <option value="klasis" {{ request('tingkat') == 'klasis' ? 'selected' : '' }}>TINGKAT KLASIS</option>
                <option value="jemaat" {{ request('tingkat') == 'jemaat' ? 'selected' : '' }}>TINGKAT JEMAAT</option>
            </select>

            @if($klasisList->count() > 0)
                <select name="klasis_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                    <option value="">- Filter Regional Klasis -</option>
                    @foreach($klasisList as $klasis)
                        <option value="{{ $klasis->id }}" {{ request('klasis_id') == $klasis->id ? 'selected' : '' }}>
                            {{ $klasis->nama_klasis }}
                        </option>
                    @endforeach
                </select>
            @endif

            <div class="relative {{ $klasisList->count() > 0 ? 'md:col-span-2' : 'md:col-span-3' }}">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="pl-10 w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" 
                    placeholder="Pencarian Nama Pengurus atau Nomor Register SK...">
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
                        <th class="px-5 py-3 w-1/4">Identitas Personel</th>
                        <th class="px-5 py-3">Jabatan & Klasifikasi Wadah</th>
                        <th class="px-5 py-3">Teritorial Penugasan</th>
                        <th class="px-5 py-3 text-center">Durasi Kepengurusan</th>
                        <th class="px-5 py-3 text-center w-24">Status</th>
                        <th class="px-5 py-3 text-center w-24">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($pengurus as $p)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded border border-gray-300 bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-sm shadow-inner uppercase">
                                        {{ substr($p->anggotaJemaat->nama_lengkap ?? 'M', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-gray-900 uppercase">
                                            {{ $p->anggotaJemaat->nama_lengkap ?? 'MANUAL (TanPA REG)' }}
                                        </div>
                                        @if($p->nomor_sk)
                                            <div class="text-[9px] text-gray-500 font-mono mt-1 font-bold">REF SK: {{ $p->nomor_sk }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-xs font-black text-gray-800 uppercase tracking-widest">{{ $p->jabatan }}</div>
                                <span class="inline-block mt-1.5 px-2 py-0.5 rounded text-[9px] font-bold bg-gray-100 border border-gray-200 text-gray-600 uppercase tracking-widest">
                                    {{ $p->jenisWadah->nama_wadah }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-black uppercase text-[10px] text-blue-800 tracking-widest mb-1">{{ $p->tingkat }}</div>
                                @if($p->tingkat == 'klasis')
                                    <div class="text-[10px] text-gray-700 font-bold uppercase">{{ $p->klasis->nama_klasis ?? '-' }}</div>
                                @elseif($p->tingkat == 'jemaat')
                                    <div class="text-[10px] text-gray-700 font-bold uppercase"><i class="fas fa-church text-gray-400 mr-1"></i> {{ $p->jemaat->nama_jemaat ?? '-' }}</div>
                                    <div class="text-[9px] text-gray-400 uppercase tracking-widest">({{ $p->klasis->nama_klasis ?? '-' }})</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="text-[10px] font-mono font-bold text-gray-600 bg-gray-50 px-2 py-1 rounded inline-block border border-gray-200">
                                    {{ $p->periode_mulai->format('Y') }} — {{ $p->periode_selesai->format('Y') }}
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($p->is_active)
                                    <span class="px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest bg-green-100 border border-green-300 text-green-800">Aktif</span>
                                @else
                                    <span class="px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest bg-gray-100 border border-gray-300 text-gray-500">Pasif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.wadah.pengurus.edit', $p->id) }}" class="text-gray-400 hover:text-yellow-600 transition" title="Modifikasi Arsip">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <form action="{{ route('admin.wadah.pengurus.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Peringatan: Pemusnahan data ini akan menghapus jejak struktural pengurus. Lanjutkan?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-700 transition" title="Hapus Data Personel">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500 text-sm">
                                <i class="fas fa-sitemap text-3xl mb-3 block text-gray-300"></i>
                                Buku Personalia tidak menemukan kriteria yang dicari.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pengurus->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">
                {{ $pengurus->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection