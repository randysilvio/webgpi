@extends('admin.layout')

@section('title', 'Direktori Pegawai')
@section('header-title', 'Manajemen Sumber Daya Manusia (HRIS)')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & ACTION --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-black text-gray-800 tracking-tight uppercase">Database Pegawai</h2>
            <p class="text-sm text-gray-500">Kelola data Pendeta, Pegawai Kantor, dan Pengajar.</p>
        </div>
        @can('manage pendeta')
        <a href="{{ route('admin.kepegawaian.pegawai.create') }}" class="bg-primary hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg text-sm font-bold uppercase tracking-wider shadow-lg transition transform hover:-translate-y-0.5 flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Input Pegawai Baru
        </a>
        @endcan
    </div>

    {{-- 2. PANEL ANALISA SEDERHANA --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card Total --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-blue-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pegawai</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-500">
                <i class="fas fa-users text-lg"></i>
            </div>
        </div>

        {{-- Card Pendeta --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-purple-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tenaga Pendeta</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_pendeta ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-purple-50 rounded-full flex items-center justify-center text-purple-500">
                <i class="fas fa-cross text-lg"></i>
            </div>
        </div>

        {{-- Card Non-Pendeta --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-orange-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pegawai Lainnya</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats->total_non_pendeta ?? 0) }}</p>
                <p class="text-[10px] text-gray-400">Tuagama, Admin, Guru, dll</p>
            </div>
            <div class="w-10 h-10 bg-orange-50 rounded-full flex items-center justify-center text-orange-500">
                <i class="fas fa-briefcase text-lg"></i>
            </div>
        </div>

        {{-- Card Aktif vs Pensiun --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Kepegawaian</p>
                <div class="flex gap-3 mt-1">
                    <div>
                        <span class="text-lg font-black text-green-600">{{ number_format($stats->total_aktif ?? 0) }}</span>
                        <span class="text-[10px] text-gray-400 block uppercase">Aktif</span>
                    </div>
                    <div class="border-l border-gray-200 pl-3">
                        <span class="text-lg font-black text-gray-500">{{ number_format($stats->total_pensiun ?? 0) }}</span>
                        <span class="text-[10px] text-gray-400 block uppercase">Pensiun</span>
                    </div>
                </div>
            </div>
            <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center text-green-500">
                <i class="fas fa-user-check text-lg"></i>
            </div>
        </div>
    </div>

    {{-- 3. TABEL DATA & FILTER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- Toolbar Filter --}}
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <form method="GET" action="{{ route('admin.kepegawaian.pegawai.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-4">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Pencarian</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-9 border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary" placeholder="Cari Nama atau NIPG...">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Jenis Pegawai</label>
                        <select name="jenis" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary" onchange="this.form.submit()">
                            <option value="">- Semua Jenis -</option>
                            <option value="Pendeta" {{ request('jenis') == 'Pendeta' ? 'selected' : '' }}>Pendeta</option>
                            <option value="Pengajar" {{ request('jenis') == 'Pengajar' ? 'selected' : '' }}>Pengajar</option>
                            <option value="Pegawai Kantor" {{ request('jenis') == 'Pegawai Kantor' ? 'selected' : '' }}>Pegawai Kantor</option>
                            <option value="Koster" {{ request('jenis') == 'Koster' ? 'selected' : '' }}>Koster/Tuagama</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary" onchange="this.form.submit()">
                            <option value="">- Semua Status -</option>
                            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="Pensiun" {{ request('status') == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex items-end">
                        <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="w-full bg-white border border-gray-300 text-gray-600 py-2 rounded-lg text-sm font-bold hover:bg-gray-100 transition text-center">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Identitas Pegawai</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Jabatan & Status</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Lokasi Tugas</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($pegawais as $p)
                        <tr class="hover:bg-blue-50/40 transition group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-11 w-11">
                                        @if($p->foto_diri && Storage::disk('public')->exists($p->foto_diri))
                                            <img class="h-11 w-11 rounded-full object-cover border-2 border-gray-200 shadow-sm" src="{{ Storage::url($p->foto_diri) }}" alt="">
                                        @else
                                            <div class="h-11 w-11 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-400 border border-gray-200">
                                                <i class="fas fa-user text-lg"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $p->nama_gelar ?? $p->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">
                                            <span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">NIPG: {{ $p->nipg ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $jenisColor = match($p->jenis_pegawai) {
                                        'Pendeta' => 'bg-purple-100 text-purple-700',
                                        'Pengajar' => 'bg-orange-100 text-orange-700',
                                        default => 'bg-blue-100 text-blue-700'
                                    };
                                    $statusColor = match($p->status_kepegawaian) {
                                        'Aktif' => 'text-green-600',
                                        'Pensiun' => 'text-gray-400',
                                        'Cuti' => 'text-yellow-600',
                                        default => 'text-red-600'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 inline-flex text-[10px] leading-tight font-bold uppercase rounded-md {{ $jenisColor }}">
                                    {{ $p->jenis_pegawai }}
                                </span>
                                <div class="text-xs font-bold mt-1.5 flex items-center {{ $statusColor }}">
                                    <i class="fas fa-circle text-[6px] mr-1.5"></i> {{ $p->status_kepegawaian }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $p->jemaat->nama_jemaat ?? 'Kantor Sinode' }}</div>
                                <div class="text-xs text-gray-500 uppercase tracking-wide mt-0.5">
                                    {{ $p->klasis->nama_klasis ?? 'Sinode GPI Papua' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($p->no_hp)
                                    <div class="flex items-center text-xs mb-1">
                                        <i class="fas fa-phone-alt w-4 text-green-500"></i> 
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $p->no_hp)) }}" target="_blank" class="hover:text-green-600 hover:underline">
                                            {{ $p->no_hp }}
                                        </a>
                                    </div>
                                @endif
                                @if($p->email)
                                    <div class="flex items-center text-xs">
                                        <i class="fas fa-envelope w-4 text-blue-400"></i> {{ Str::limit($p->email, 20) }}
                                    </div>
                                @endif
                                @if(!$p->no_hp && !$p->email)
                                    <span class="text-xs text-gray-400 italic">Tidak ada kontak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('admin.kepegawaian.pegawai.show', $p->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm" title="Lihat Profil Lengkap">
                                        <i class="fas fa-id-card-alt"></i>
                                    </a>
                                    @can('manage pendeta')
                                    <a href="{{ route('admin.kepegawaian.pegawai.edit', $p->id) }}" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-500 hover:text-white transition shadow-sm" title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-folder-open text-4xl mb-3 opacity-50"></i>
                                    <span class="text-sm font-medium">Belum ada data pegawai yang sesuai.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($pegawais->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $pegawais->links() }}
            </div>
        @endif
    </div>
</div>
@endsection