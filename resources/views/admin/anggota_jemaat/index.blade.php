@extends('admin.layout')

@section('title', 'Manajemen Anggota Jemaat')
@section('header-title', 'Daftar Anggota Jemaat')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & ACTION --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-black text-gray-800 tracking-tight uppercase">Direktori Anggota</h2>
            <p class="text-sm text-gray-500">Database Umat GPI Papua.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('import anggota jemaat')
            <a href="{{ route('admin.anggota-jemaat.import-form') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm text-xs uppercase tracking-wider flex items-center">
                <i class="fas fa-file-excel mr-2"></i> Import
            </a>
            @endcan
            @can('export anggota jemaat')
            <a href="{{ route('admin.anggota-jemaat.export', request()->query()) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm text-xs uppercase tracking-wider flex items-center">
                <i class="fas fa-download mr-2"></i> Export
            </a>
            @endcan
            @can('create anggota jemaat')
            <a href="{{ route('admin.anggota-jemaat.create') }}" class="bg-primary hover:bg-blue-800 text-white font-bold py-2 px-4 rounded-lg shadow-sm text-xs uppercase tracking-wider flex items-center">
                <i class="fas fa-user-plus mr-2"></i> Tambah Anggota
            </a>
            @endcan
        </div>
    </div>

    {{-- 2. PANEL ANALISA SEDERHANA --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card Total --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-primary flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Anggota</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-primary">
                <i class="fas fa-users text-lg"></i>
            </div>
        </div>

        {{-- Card Laki-laki --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-cyan-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Laki-laki</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_laki ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-cyan-50 rounded-full flex items-center justify-center text-cyan-500">
                <i class="fas fa-male text-lg"></i>
            </div>
        </div>

        {{-- Card Perempuan --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-pink-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Perempuan</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_perempuan ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-pink-50 rounded-full flex items-center justify-center text-pink-500">
                <i class="fas fa-female text-lg"></i>
            </div>
        </div>

        {{-- Card Kepala Keluarga --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-purple-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kepala Keluarga</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_kk ?? 0) }}</p>
                <p class="text-[10px] text-gray-400">Aktif</p>
            </div>
            <div class="w-10 h-10 bg-purple-50 rounded-full flex items-center justify-center text-purple-500">
                <i class="fas fa-house-user text-lg"></i>
            </div>
        </div>
    </div>

    {{-- 3. FILTER & TABEL DATA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- Toolbar Filter --}}
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <form method="GET" action="{{ route('admin.anggota-jemaat.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Filter Klasis --}}
                    @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']) && isset($klasisFilterOptions) && $klasisFilterOptions->count() > 0)
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Klasis</label>
                            <select name="klasis_id" id="klasis_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary" onchange="this.form.submit()">
                                <option value="">- Semua Klasis -</option>
                                @foreach($klasisFilterOptions as $id => $nama)
                                    <option value="{{ $id }}" {{ request('klasis_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Filter Jemaat --}}
                    @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3', 'Admin Klasis']) && isset($jemaatFilterOptions) && $jemaatFilterOptions->count() > 0)
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Jemaat</label>
                            <select name="jemaat_id" id="jemaat_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary" onchange="this.form.submit()">
                                <option value="">- Semua Jemaat -</option>
                                @foreach($jemaatFilterOptions as $id => $nama)
                                    <option value="{{ $id }}" {{ request('jemaat_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Filter Status --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Status Keanggotaan</label>
                        <select name="status_keanggotaan" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary" onchange="this.form.submit()">
                            <option value="">- Semua Status -</option>
                            <option value="Aktif" {{ request('status_keanggotaan') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Pindah" {{ request('status_keanggotaan') == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                            <option value="Meninggal" {{ request('status_keanggotaan') == 'Meninggal' ? 'selected' : '' }}>Meninggal</option>
                            <option value="Tidak Aktif" {{ request('status_keanggotaan') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="md:col-span-{{ Auth::user()->hasRole('Admin Jemaat') ? '3' : '1' }}">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Pencarian</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-9 border-gray-300 rounded-lg text-sm focus:ring-primary" placeholder="Cari Nama, NIK, No Induk...">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-gray-500 font-bold border-b">
                    <tr>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Identitas Anggota</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">No. Induk / NIK</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Jemaat</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Usia & Gender</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider">Status</th>
                        <th class="px-6 py-4 uppercase text-[10px] tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($anggotaJemaatData as $anggota)
                        <tr class="hover:bg-blue-50/40 transition group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center font-bold text-xs mr-3 border border-gray-200">
                                        {{ substr($anggota->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">
                                            <a href="{{ route('admin.anggota-jemaat.show', $anggota->id) }}" class="hover:text-primary hover:underline">
                                                {{ $anggota->nama_lengkap }}
                                            </a>
                                        </div>
                                        <div class="text-[10px] text-gray-500 mt-0.5">{{ $anggota->status_dalam_keluarga ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-600">
                                <div>{{ $anggota->nomor_buku_induk ?? '-' }}</div>
                                <div class="text-[10px] text-gray-400">{{ $anggota->nik }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 font-medium text-xs">{{ $anggota->jemaat->nama_jemaat ?? '-' }}</div>
                                @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Bidang 3']))
                                    <div class="text-[10px] text-gray-500">{{ $anggota->jemaat->klasis->nama_klasis ?? '-' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-700">
                                    {{ $anggota->tanggal_lahir ? \Carbon\Carbon::parse($anggota->tanggal_lahir)->age . ' Thn' : '-' }}
                                </div>
                                <div class="text-[10px] {{ $anggota->jenis_kelamin == 'Laki-laki' ? 'text-blue-500' : 'text-pink-500' }}">
                                    {{ $anggota->jenis_kelamin == 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($anggota->status_keanggotaan) {
                                        'Aktif' => 'bg-green-100 text-green-800',
                                        'Pindah' => 'bg-yellow-100 text-yellow-800',
                                        'Meninggal' => 'bg-gray-200 text-gray-600',
                                        'Tidak Aktif' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $statusColor }}">
                                    {{ $anggota->status_keanggotaan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    @can('edit anggota jemaat')
                                    <a href="{{ route('admin.anggota-jemaat.edit', $anggota->id) }}" class="p-2 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-500 hover:text-white transition shadow-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete anggota jemaat')
                                    <form action="{{ route('admin.anggota-jemaat.destroy', $anggota->id) }}" method="POST" onsubmit="return confirm('Hapus data anggota ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-50 text-red-600 rounded hover:bg-red-500 hover:text-white transition shadow-sm" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-users-slash text-4xl mb-3 opacity-20"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider">Data tidak ditemukan</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($anggotaJemaatData->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $anggotaJemaatData->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const klasisSelect = document.getElementById('klasis_id');
        const jemaatSelect = document.getElementById('jemaat_id');
        
        // Auto-submit saat Klasis berubah (agar Jemaat terfilter di backend)
        if (klasisSelect) {
            klasisSelect.addEventListener('change', function() {
                // Reset jemaat selection
                if(jemaatSelect) jemaatSelect.value = '';
                this.form.submit();
            });
        }
    });
</script>
@endpush