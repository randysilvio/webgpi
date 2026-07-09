@extends('layouts.app')

@section('title', 'Buku Induk Kepegawaian')

@section('content')
<div class="space-y-6">

    {{-- HEADER & TOOLS --}}
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-gray-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Buku Induk Kepegawaian</h2>
            <p class="text-xs text-gray-600 mt-1">Sistem administrasi pangkalan data Pendeta, Pengajar, dan Staf.</p>
        </div>
        @can('manage pendeta')
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('admin.kepegawaian.pegawai.create') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wide shadow-sm transition flex items-center justify-center">
                <i class="fas fa-user-plus mr-2"></i> Registrasi Baru
            </a>
            <a href="{{ route('admin.kepegawaian.pegawai.export') }}" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wide shadow-sm transition flex items-center justify-center">
                <i class="fas fa-file-excel mr-2"></i> Ekspor Data
            </a>
        </div>
        @endcan
    </div>

    {{-- STATISTIK RINGKAS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Total Personel</p>
            <p class="text-xl font-black text-gray-900 mt-1">{{ number_format($stats->total ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-b-4 border-b-blue-800">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Pendeta Organik</p>
            <p class="text-xl font-black text-blue-900 mt-1">{{ number_format($stats->total_pendeta ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-b-4 border-b-green-700">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Pengajar / Guru</p>
            <p class="text-xl font-black text-green-900 mt-1">{{ number_format($stats->total_pengajar ?? 0) }}</p>
        </div>
        <div class="bg-gray-800 p-4 border border-gray-900 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Pegawai Kantor / Staf</p>
            <p class="text-xl font-black text-white mt-1">{{ number_format($stats->total_pegawai ?? 0) }}</p>
        </div>
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-gray-50 border border-gray-200 rounded p-4 shadow-sm flex flex-col md:flex-row gap-4">
        <form action="{{ route('admin.kepegawaian.pegawai.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4">
            
            <div class="w-full md:w-48 flex-shrink-0">
                <select name="kategori" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                    <option value="">- Semua Kategori -</option>
                    <option value="Pendeta" {{ request('kategori') == 'Pendeta' ? 'selected' : '' }}>PENDETA</option>
                    <option value="Pengajar" {{ request('kategori') == 'Pengajar' ? 'selected' : '' }}>PENGAJAR</option>
                    <option value="Pegawai Kantor" {{ request('kategori') == 'Pegawai Kantor' ? 'selected' : '' }}>STAF KANTOR</option>
                </select>
            </div>

            <div class="w-full md:w-48 flex-shrink-0">
                <select name="status" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                    <option value="">- Semua Status -</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>AKTIF BERDinas</option>
                    <option value="Pensiun" {{ request('status') == 'Pensiun' ? 'selected' : '' }}>PURNA TUGAS</option>
                    <option value="Meninggal" {{ request('status') == 'Meninggal' ? 'selected' : '' }}>MENINGGAL DUNIA</option>
                </select>
            </div>

            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="pl-10 w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" 
                    placeholder="Pencarian Nama Personel atau NIP/NIPG...">
            </div>

            <div class="flex-shrink-0">
                <button type="submit" class="w-full bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-6 py-2.5 rounded text-[10px] font-bold uppercase transition">
                    Saring Data
                </button>
            </div>
        </form>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                        <th class="px-6 py-3 w-64">Identitas & Nomor Induk</th>
                        <th class="px-6 py-3">Kategori Personel</th>
                        <th class="px-6 py-3">Lokasi Penugasan</th>
                        <th class="px-6 py-3 text-center">Status Dinas</th>
                        <th class="px-6 py-3 text-center w-32">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($pegawais as $p)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded border border-gray-300 bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-sm shadow-inner uppercase overflow-hidden shrink-0">
                                        @if($p->foto_diri && \Illuminate\Support\Facades\Storage::disk('public')->exists($p->foto_diri))
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($p->foto_diri) }}" class="h-full w-full object-cover">
                                        @else
                                            {{ substr($p->nama_lengkap, 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.kepegawaian.pegawai.show', $p->id) }}" class="font-bold text-gray-900 uppercase text-xs hover:text-blue-800 transition">{{ $p->nama_lengkap }}</a>
                                        <div class="text-[9px] text-gray-500 font-mono mt-1 font-bold">
                                            @if($p->jenis_pegawai == 'Pendeta')
                                                NIPG: {{ $p->nipg ?? '-' }}
                                            @else
                                                NIP/NIK: {{ $p->nip ?? '-' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="block text-xs font-bold text-gray-800 uppercase">{{ $p->jenis_pegawai }}</span>
                                <span class="text-[9px] text-blue-800 font-bold uppercase tracking-widest bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 inline-block mt-1">
                                    {{ $p->jabatan_terakhir ?? 'Staf' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($p->jemaat)
                                    <span class="block text-xs font-bold text-gray-700 uppercase"><i class="fas fa-church text-gray-400 mr-1 w-3 text-center"></i> {{ $p->jemaat->nama_jemaat }}</span>
                                    <span class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">{{ $p->klasis->nama_klasis ?? '-' }}</span>
                                @elseif($p->klasis)
                                    <span class="block text-xs font-bold text-gray-700 uppercase"><i class="fas fa-map-marker-alt text-gray-400 mr-1 w-3 text-center"></i> {{ $p->klasis->nama_klasis }}</span>
                                    <span class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">Kantor Klasis</span>
                                @else
                                    <span class="block text-xs font-bold text-gray-700 uppercase"><i class="fas fa-building text-gray-400 mr-1 w-3 text-center"></i> Kantor Sinode</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusConfig = match($p->status_aktif) {
                                        'Aktif' => 'bg-green-100 text-green-800 border-green-300',
                                        'Pensiun' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                        'Meninggal' => 'bg-gray-200 text-gray-600 border-gray-400',
                                        default => 'bg-red-100 text-red-800 border-red-300',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border {{ $statusConfig }}">
                                    {{ $p->status_aktif }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.kepegawaian.pegawai.show', $p->id) }}" class="text-gray-400 hover:text-blue-800 transition" title="Buka Arsip"><i class="fas fa-folder-open text-sm"></i></a>
                                    
                                    @can('manage pendeta')
                                    <a href="{{ route('admin.kepegawaian.pegawai.edit', $p->id) }}" class="text-gray-400 hover:text-yellow-600 transition" title="Modifikasi"><i class="fas fa-edit text-sm"></i></a>
                                    
                                    {{-- TOMBOL HAPUS DITAMBAHKAN DI SINI --}}
                                    <form action="{{ route('admin.kepegawaian.pegawai.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Peringatan: Pemusnahan data ini akan menghapus permanen arsip personel beserta akun loginnya (jika ada). Lanjutkan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-700 transition" title="Hapus Arsip Personel">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm">
                                <i class="fas fa-inbox text-3xl mb-3 block text-gray-300"></i>
                                Data kepegawaian tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($pegawais, 'hasPages') && $pegawais->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $pegawais->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection