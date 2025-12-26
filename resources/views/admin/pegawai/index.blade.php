@extends('admin.layout')

@section('title', 'Data Pegawai')
@section('header-title', 'Manajemen Data Pegawai (HRIS)')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl font-semibold text-gray-800">Direktori Pegawai</h2>
        @can('manage pendeta') {{-- Menggunakan permission yg ada --}}
        <a href="{{ route('admin.kepegawaian.pegawai.create') }}" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out inline-flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Tambah Pegawai
        </a>
        @endcan
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.kepegawaian.pegawai.index') }}" class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Cari Nama / NIPG</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary" placeholder="Nama...">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Jenis Pegawai</label>
                <select name="jenis" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                    <option value="">- Semua -</option>
                    <option value="Pendeta" {{ request('jenis') == 'Pendeta' ? 'selected' : '' }}>Pendeta</option>
                    <option value="Pengajar" {{ request('jenis') == 'Pengajar' ? 'selected' : '' }}>Pengajar</option>
                    <option value="Pegawai Kantor" {{ request('jenis') == 'Pegawai Kantor' ? 'selected' : '' }}>Pegawai Kantor</option>
                    <option value="Koster" {{ request('jenis') == 'Koster' ? 'selected' : '' }}>Koster/Tuagama</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status Aktif</label>
                <select name="status" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                    <option value="">- Semua -</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Pensiun" {{ request('status') == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-800 text-white py-2 px-4 rounded-md hover:bg-gray-700 w-full text-sm font-medium">
                    <i class="fas fa-filter mr-1"></i> Filter Data
                </button>
            </div>
        </div>
    </form>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pegawai</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jenis & Status</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Penempatan</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kontak</th>
                    <th class="px-6 py-3 text-end text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pegawais as $p)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($p->foto_diri)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($p->foto_diri) }}" alt="">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $p->nama_gelar }}</div>
                                    <div class="text-xs text-gray-500">NIPG: {{ $p->nipg }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $p->jenis_pegawai }}
                            </span>
                            <div class="text-xs text-gray-500 mt-1">{{ $p->status_kepegawaian }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="font-medium text-gray-900">{{ $p->jemaat->nama_jemaat ?? '-' }}</div>
                            <div class="text-xs">{{ $p->klasis->nama_klasis ?? 'Sinode' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div><i class="fas fa-phone-alt text-xs mr-1"></i> {{ $p->no_hp ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                            <a href="{{ route('admin.kepegawaian.pegawai.show', $p->id) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="Lihat Profil">
                                <i class="fas fa-id-card"></i>
                            </a>
                            @can('manage pendeta')
                            <a href="{{ route('admin.kepegawaian.pegawai.edit', $p->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data pegawai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $pegawais->links() }}</div>
</div>
@endsection