@extends('layouts.app')

@section('title', 'Direktori Pegawai')
@section('header-title', 'HRIS & Personalia')

@section('content')
<div class="space-y-6">

    {{-- HEADER & TOOLS --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">Database Personel</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola data Pendeta, Pegawai Kantor, dan Pengajar.</p>
        </div>
        @can('manage pendeta')
        <a href="{{ route('admin.kepegawaian.pegawai.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded shadow-sm transition">
            <i class="fas fa-plus mr-2"></i> Registrasi Baru
        </a>
        @endcan
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-white p-4 rounded border border-slate-200 shadow-sm">
        <form action="{{ route('admin.kepegawaian.pegawai.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            
            {{-- Search Bar --}}
            <div class="md:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="pl-10 w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 placeholder-slate-400" 
                    placeholder="Cari Nama, NIP, atau Jabatan...">
            </div>

            {{-- Filter Jenis --}}
            <div class="md:col-span-3">
                <select name="jenis" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-600">
                    <option value="">-- Semua Jenis --</option>
                    <option value="Pendeta" {{ request('jenis') == 'Pendeta' ? 'selected' : '' }}>Pendeta</option>
                    <option value="Pengajar" {{ request('jenis') == 'Pengajar' ? 'selected' : '' }}>Pengajar</option>
                    <option value="Pegawai Kantor" {{ request('jenis') == 'Pegawai Kantor' ? 'selected' : '' }}>Pegawai Kantor</option>
                </select>
            </div>

            {{-- Filter Status --}}
            <div class="md:col-span-3">
                <select name="status" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-600">
                    <option value="">-- Semua Status --</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Pensiun" {{ request('status') == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                </select>
            </div>

            {{-- Tombol Submit --}}
            <div class="md:col-span-1">
                <button type="submit" class="w-full h-full bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-300 rounded text-sm transition" title="Terapkan Filter">
                    <i class="fas fa-filter"></i>
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
                        <th class="px-6 py-4">Profil</th>
                        <th class="px-6 py-4">Jabatan / Gol</th>
                        <th class="px-6 py-4">Unit Kerja</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pegawais as $index => $p)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4 text-center text-slate-500 text-sm">
                                {{ $pegawais->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 flex-shrink-0 bg-slate-200 rounded-full flex items-center justify-center overflow-hidden border border-slate-300">
                                        @if($p->foto_profil)
                                            <img src="{{ asset('storage/'.$p->foto_profil) }}" class="h-full w-full object-cover">
                                        @else
                                            <i class="fas fa-user text-slate-400 text-xs"></i>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-bold text-slate-800">{{ $p->nama_lengkap }}</div>
                                        <div class="text-xs text-slate-500">{{ $p->nip ?? 'NIP: -' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-700 font-medium">{{ $p->jabatan_terakhir ?? '-' }}</div>
                                <div class="text-xs text-slate-500">Gol: {{ $p->golongan_terakhir ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                @if($p->jemaat) 
                                    {{ $p->jemaat->nama_jemaat }} 
                                @elseif($p->klasis)
                                    Klasis {{ $p->klasis->nama_klasis }}
                                @else 
                                    Sinode / Umum 
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($p->status_aktif == 'Aktif')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-green-50 text-green-700 border border-green-100">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">{{ $p->status_aktif }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.kepegawaian.pegawai.show', $p->id) }}" class="text-slate-400 hover:text-blue-600 transition"><i class="fas fa-eye"></i></a>
                                    @can('manage pendeta')
                                    <a href="{{ route('admin.kepegawaian.pegawai.edit', $p->id) }}" class="text-slate-400 hover:text-yellow-600 transition"><i class="fas fa-pencil-alt"></i></a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i><br>
                                Data tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pegawais->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $pegawais->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection