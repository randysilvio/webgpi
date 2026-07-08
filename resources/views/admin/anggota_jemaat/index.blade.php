@extends('layouts.app')

@section('title', 'Buku Induk Anggota Jemaat')

@section('content')
<div class="space-y-6">

    {{-- HEADER & TOOLS --}}
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-gray-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Buku Induk Umat Jemaat</h2>
            <p class="text-xs text-gray-600 mt-1">Pangkalan data registrasi seluruh anggota jemaat (Sidi, Baptis, dan Anak).</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            @can('import anggota jemaat')
            <a href="{{ route('admin.anggota-jemaat.import-form') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded text-[10px] font-bold uppercase hover:bg-gray-100 transition flex items-center">
                <i class="fas fa-file-excel mr-2 text-green-700"></i> Import Data
            </a>
            @endcan
            @can('export anggota jemaat')
            <a href="{{ route('admin.anggota-jemaat.export', request()->query()) }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded text-[10px] font-bold uppercase hover:bg-gray-100 transition flex items-center">
                <i class="fas fa-download mr-2 text-blue-800"></i> Export Data
            </a>
            @endcan
            <a href="{{ route('admin.anggota-jemaat.create') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase shadow-sm transition flex items-center">
                <i class="fas fa-user-plus mr-2"></i> Registrasi Umat Baru
            </a>
        </div>
    </div>

    {{-- STATISTIK RINGKAS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Total Populasi Umat</p>
            <p class="text-xl font-black text-gray-900 mt-1">{{ number_format($stats->total ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-b-4 border-b-blue-800">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Laki-Laki</p>
            <p class="text-xl font-black text-blue-900 mt-1">{{ number_format($stats->total_laki ?? 0) }}</p>
        </div>
        <div class="bg-white p-4 border border-gray-300 shadow-sm text-center border-b-4 border-b-pink-700">
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Perempuan</p>
            <p class="text-xl font-black text-pink-900 mt-1">{{ number_format($stats->total_perempuan ?? 0) }}</p>
        </div>
        <div class="bg-gray-800 p-4 border border-gray-900 shadow-sm text-center">
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Total Kepala Keluarga</p>
            <p class="text-xl font-black text-white mt-1">{{ number_format($stats->total_kk ?? 0) }}</p>
        </div>
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-gray-50 border border-gray-200 rounded p-4 shadow-sm">
        <form action="{{ route('admin.anggota-jemaat.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            
            @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3', 'Admin Klasis']))
                <select name="klasis_id" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" onchange="this.form.submit()">
                    <option value="">-- Semua Klasis --</option>
                    @foreach($klasisFilterOptions ?? [] as $id => $nama)
                        <option value="{{ $id }}" {{ request('klasis_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
                
                <select name="jemaat_id" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" onchange="this.form.submit()">
                    <option value="">-- Semua Jemaat --</option>
                    @foreach($jemaatFilterOptions ?? [] as $id => $nama)
                        <option value="{{ $id }}" {{ request('jemaat_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            @endif

            <select name="status_keanggotaan" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" onchange="this.form.submit()">
                <option value="">-- Semua Status --</option>
                <option value="Aktif" {{ request('status_keanggotaan') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Pindah" {{ request('status_keanggotaan') == 'Pindah' ? 'selected' : '' }}>Pindah Wilayah</option>
                <option value="Meninggal" {{ request('status_keanggotaan') == 'Meninggal' ? 'selected' : '' }}>Meninggal Dunia</option>
                <option value="Tidak Aktif" {{ request('status_keanggotaan') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>

            <div class="relative md:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="pl-10 w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" 
                    placeholder="Pencarian Nama Anggota atau NIK/Register...">
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
                        <th class="px-5 py-3 w-40 text-center">No. Register / NIK</th>
                        <th class="px-5 py-3">Identitas Umat</th>
                        <th class="px-5 py-3">Organisasi Jemaat</th>
                        <th class="px-5 py-3 text-center">Usia & Kelamin</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center w-24">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse ($anggotaJemaatData as $anggota)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-5 py-4 text-center">
                                <div class="font-mono text-[11px] font-black text-gray-900">{{ $anggota->nomor_buku_induk ?? 'TIDAK TERDAFTAR' }}</div>
                                <div class="text-[9px] text-gray-500 font-mono mt-1">NIK: {{ $anggota->nik ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-900 uppercase text-xs">
                                    <a href="{{ route('admin.anggota-jemaat.show', $anggota->id) }}" class="hover:text-blue-800 transition">{{ $anggota->nama_lengkap }}</a>
                                </div>
                                <div class="text-[9px] text-gray-500 font-bold uppercase mt-1 tracking-widest bg-gray-100 border border-gray-200 px-1.5 py-0.5 rounded inline-block">
                                    {{ $anggota->status_dalam_keluarga ?? 'TIDAK DIKETAHUI' }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-xs font-bold text-gray-700 uppercase"><i class="fas fa-church text-gray-400 mr-1"></i> {{ $anggota->jemaat->nama_jemaat ?? '-' }}</span>
                                @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']))
                                    <div class="text-[10px] text-gray-500 mt-1 uppercase"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> {{ $anggota->jemaat->klasis->nama_klasis ?? '-' }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="text-xs font-black text-gray-800">{{ $anggota->tanggal_lahir ? \Carbon\Carbon::parse($anggota->tanggal_lahir)->age . ' THN' : '-' }}</div>
                                <div class="text-[9px] font-bold uppercase tracking-widest mt-1 {{ $anggota->jenis_kelamin == 'Laki-laki' ? 'text-blue-700' : 'text-pink-700' }}">
                                    {{ $anggota->jenis_kelamin }}
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @php
                                    $statusColor = match($anggota->status_keanggotaan) {
                                        'Aktif' => 'bg-green-100 text-green-800 border-green-300',
                                        'Pindah' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                        'Meninggal' => 'bg-gray-200 text-gray-600 border-gray-400',
                                        default => 'bg-red-100 text-red-800 border-red-300'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-widest rounded border {{ $statusColor }}">
                                    {{ $anggota->status_keanggotaan }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.anggota-jemaat.show', $anggota->id) }}" class="text-gray-400 hover:text-blue-800 transition" title="Buka Detail">
                                        <i class="fas fa-folder-open text-xs"></i>
                                    </a>
                                    @can('edit anggota jemaat')
                                    <a href="{{ route('admin.anggota-jemaat.edit', $anggota->id) }}" class="text-gray-400 hover:text-yellow-600 transition" title="Modifikasi Data">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    @endcan
                                    @can('delete anggota jemaat')
                                    <form action="{{ route('admin.anggota-jemaat.destroy', $anggota->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Memusnahkan Data Anggota Jemaat?\n\nSemua riwayat akan hilang. Lanjutkan?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-700 transition" title="Musnahkan Arsip">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500 text-xs italic">
                                <i class="fas fa-users-slash text-3xl mb-3 block text-gray-300"></i>
                                Pangkalan Data Umat masih kosong atau tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($anggotaJemaatData->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">
                {{ $anggotaJemaatData->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection