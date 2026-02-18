@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="space-y-6">
    {{-- Header & Pencarian --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Pengguna Sistem</h2>
            <p class="text-sm text-slate-500">Kelola akses dan hak otoritas pengguna.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <form action="{{ route('admin.users.index') }}" method="GET" class="relative w-full md:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / Email..." 
                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500">
                <i class="fas fa-search absolute left-3 top-2.5 text-slate-400"></i>
            </form>
            <a href="{{ route('admin.users.create') }}" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wide shadow-sm transition flex items-center">
                <i class="fas fa-plus mr-2"></i> User Baru
            </a>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Profil Pengguna</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Role / Peran</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Konteks Wilayah</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50 transition duration-150 group">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-9 w-9 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold mr-3 uppercase">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="font-bold text-slate-800 text-sm hover:text-blue-600 transition">
                                    {{ $user->name }}
                                </a>
                                <div class="text-xs text-slate-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @forelse($user->roles as $role)
                                @php
                                    $badgeClass = match($role->name) {
                                        'Super Admin' => 'bg-red-100 text-red-700 border-red-200',
                                        'Admin Klasis' => 'bg-orange-100 text-orange-700 border-orange-200',
                                        'Admin Jemaat' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'Pendeta' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        default => 'bg-slate-100 text-slate-600 border-slate-200'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border {{ $badgeClass }}">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400 italic">User Biasa</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs">
                        @if($user->pendeta)
                            <div class="flex items-center text-purple-700 mb-1"><i class="fas fa-user-tie mr-1.5 w-4"></i> {{ $user->pendeta->nama_lengkap }}</div>
                        @endif
                        @if($user->klasisTugas)
                            <div class="flex items-center text-orange-700 mb-1"><i class="fas fa-map mr-1.5 w-4"></i> {{ $user->klasisTugas->nama_klasis }}</div>
                        @endif
                        @if($user->jemaatTugas)
                            <div class="flex items-center text-blue-700"><i class="fas fa-church mr-1.5 w-4"></i> {{ $user->jemaatTugas->nama_jemaat }}</div>
                        @endif
                        @if(!$user->pendeta && !$user->klasisTugas && !$user->jemaatTugas)
                            <span class="text-slate-400 italic">- Tidak terikat wilayah -</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-slate-400 hover:text-yellow-600" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== 1 && $user->id !== Auth::id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Data user tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($users->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection