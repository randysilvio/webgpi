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
                <i class="fas fa-file-excel mr-2"></i> Ekspor Excel
            </a>
        </div>
        @endcan
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-gray-50 border border-gray-200 rounded p-4 shadow-sm">
        <form action="{{ route('admin.kepegawaian.pegawai.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <div class="relative md:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="pl-10 w-full border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm" 
                    placeholder="Cari Nama Pegawai atau NIP/NIPG...">
            </div>

            <div>
                <select name="jenis" class="w-full border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                    <option value="">-- Klasifikasi Personel --</option>
                    <option value="Pendeta" {{ request('jenis') == 'Pendeta' ? 'selected' : '' }}>Pelayan Firman (Pendeta)</option>
                    <option value="Pengajar" {{ request('jenis') == 'Pengajar' ? 'selected' : '' }}>Pengajar Agama</option>
                    <option value="Pegawai Kantor" {{ request('jenis') == 'Pegawai Kantor' ? 'selected' : '' }}>Pegawai Kantor Sinode</option>
                </select>
            </div>

            <div>
                <select name="status" class="w-full border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                    <option value="">-- Status Kedinasan --</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif Menjabat</option>
                    <option value="Emeritus" {{ request('status') == 'Emeritus' ? 'selected' : '' }}>Emeritus (Pensiun)</option>
                    <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti Diluar Tanggungan</option>
                </select>
            </div>

            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-6 py-2 rounded text-[10px] font-bold uppercase transition">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- TABEL INDUK --}}
    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                        <th class="px-6 py-3">Identitas Pegawai</th>
                        <th class="px-6 py-3">NIP / NIPG</th>
                        <th class="px-6 py-3">Klasifikasi</th>
                        <th class="px-6 py-3">Status Dinas</th>
                        <th class="px-6 py-3">Lokasi Penugasan</th>
                        <th class="px-6 py-3 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($pegawais as $p)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded border border-gray-300 shadow-sm flex items-center justify-center font-bold text-gray-400 overflow-hidden bg-gray-100">
                                        @if($p->foto_diri)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($p->foto_diri) }}" class="h-full w-full object-cover">
                                        @else
                                            <i class="fas fa-user"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 leading-snug">
                                            {{ $p->gelar_depan }} {{ $p->nama_lengkap }} {{ $p->gelar_belakang }}
                                        </div>
                                        <div class="text-[10px] text-gray-500 font-medium">{{ $p->email ?? 'Tanpa Email' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-gray-800 text-xs">{{ $p->nipg }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase bg-blue-50 text-blue-800 border border-blue-200 rounded">
                                    {{ $p->jenis_pegawai }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($p->status_aktif == 'Aktif')
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase bg-green-100 text-green-800 border border-green-300 rounded">Aktif</span>
                                @elseif($p->status_aktif == 'Emeritus')
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase bg-yellow-100 text-yellow-800 border border-yellow-300 rounded">Emeritus</span>
                                @else
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase bg-gray-100 text-gray-600 border border-gray-300 rounded">{{ $p->status_aktif }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-[10px] space-y-1 font-medium text-gray-700">
                                    @if($p->klasis) <div><i class="fas fa-map-marker-alt text-gray-400 w-3 text-center mr-1"></i> {{ $p->klasis->nama_klasis }}</div> @endif
                                    @if($p->jemaat) <div><i class="fas fa-church text-gray-400 w-3 text-center mr-1"></i> {{ $p->jemaat->nama_jemaat }}</div> @endif
                                    @if(!$p->klasis && !$p->jemaat) <div class="italic text-gray-400">Pusat Sinode</div> @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.kepegawaian.pegawai.show', $p->id) }}" class="text-gray-400 hover:text-blue-800 transition" title="Buka Arsip"><i class="fas fa-folder-open text-sm"></i></a>
                                    @can('manage pendeta')
                                    <a href="{{ route('admin.kepegawaian.pegawai.edit', $p->id) }}" class="text-gray-400 hover:text-yellow-600 transition" title="Modifikasi"><i class="fas fa-edit text-sm"></i></a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">
                                <i class="fas fa-inbox text-3xl mb-3 block text-gray-300"></i>
                                Data kepegawaian tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pegawais->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $pegawais->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection