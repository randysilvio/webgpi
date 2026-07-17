@extends('layouts.app')

@section('title', 'Pangkalan Data Pengguna')

@section('content')
<div class="space-y-6">
    {{-- Header & Pencarian --}}
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-gray-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Otorisasi & Pengguna Sistem</h2>
            <p class="text-xs text-gray-600 mt-1">Sistem kontrol pangkalan data hak akses administrator dan pelayan.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <form action="{{ route('admin.users.index') }}" method="GET" class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Pencarian Nama / Email..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
            </form>
            <a href="{{ route('admin.users.create') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wide shadow-sm transition flex items-center justify-center">
                <i class="fas fa-user-plus mr-2"></i> Registrasi Baru
            </a>
        </div>
    </div>

    {{-- Tabel Arsip --}}
    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                        <th class="px-6 py-3">Identitas Pengguna</th>
                        <th class="px-6 py-3">Hak Otoritas (Role)</th>
                        <th class="px-6 py-3">Wilayah Kerja / Tautan</th>
                        <th class="px-6 py-3 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded bg-gray-200 border border-gray-300 flex items-center justify-center font-bold text-gray-500 uppercase">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                    <div class="text-[10px] text-gray-500 font-medium">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                    <span class="px-2 py-1 bg-gray-800 text-white text-[9px] font-bold uppercase tracking-wider rounded">{{ $role->name }}</span>
                                @empty
                                    <span class="text-[9px] text-gray-400 italic uppercase">Tanpa Otoritas</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-[10px] space-y-1">
                                @if($user->pegawai)
                                    <div class="text-blue-800 font-bold uppercase"><i class="fas fa-id-badge w-4 text-center mr-1"></i> Pegawai: {{ $user->pegawai->nipg }}</div>
                                @endif
                                @if($user->klasis_id)
                                    <div class="text-gray-700"><i class="fas fa-map-marker-alt w-4 text-center mr-1 text-gray-400"></i> Klasis {{ $user->klasisTugas->nama_klasis ?? '-' }}</div>
                                @endif
                                @if($user->jemaat_id)
                                    <div class="text-gray-700"><i class="fas fa-church w-4 text-center mr-1 text-gray-400"></i> Jemaat {{ $user->jemaatTugas->nama_jemaat ?? '-' }}</div>
                                @endif
                                @if(!$user->pegawai && !$user->klasis_id && !$user->jemaat_id)
                                    <span class="text-gray-400 italic">Pusat Sinode / Tidak Spesifik</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-gray-400 hover:text-blue-800 transition" title="Tinjau Detail">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>

                                @if(Auth::user()->hasRole('Super Admin') && $user->id !== Auth::id() && !$user->hasRole('Super Admin'))
                                    <a href="{{ route('admin.users.impersonate', $user->id) }}" class="text-gray-400 hover:text-green-700 transition" title="Login Sebagai Pengguna Ini" onclick="return confirm('Anda akan dialihkan dan bertindak sebagai {{ $user->name }}. Lanjutkan?');">
                                        <i class="fas fa-user-secret text-sm"></i>
                                    </a>
                                @endif

                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-gray-400 hover:text-yellow-600 transition" title="Modifikasi">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                
                                @if($user->id !== 1 && $user->id !== Auth::id())
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Peringatan: Pencabutan akses ini bersifat permanen. Lanjutkan?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-700 transition" title="Cabut Akses">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm">
                            <i class="fas fa-users-slash text-3xl mb-3 block text-gray-300"></i>
                            Pangkalan data pengguna tidak ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection