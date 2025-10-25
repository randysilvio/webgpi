@extends('admin.layout')

@section('title', 'Manajemen User')
@section('header-title', 'Daftar Pengguna Sistem')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl font-semibold text-gray-800">Data User</h2>
        {{-- Tombol Tambah (Hanya Super Admin) --}}
        {{-- @hasrole('Super Admin') --}}
        <a href="{{ route('admin.users.create') }}" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out whitespace-nowrap inline-flex items-center">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah User Baru
        </a>
        {{-- @endhasrole --}}
    </div>

    {{-- Form Pencarian --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
       <div class="flex items-center">
           <input type="text" name="search" placeholder="Cari Nama/Email User..." value="{{ request('search') }}"
                  class="flex-grow px-4 py-2 border border-gray-300 rounded-l-md focus:ring-primary focus:border-primary text-sm shadow-sm">
           <button type="submit" class="bg-primary text-white px-4 py-2 rounded-r-md hover:bg-blue-700 transition duration-150 ease-in-out shadow-sm">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
           </button>
            @if(request('search'))
           <a href="{{ route('admin.users.index') }}" class="ml-2 text-sm text-gray-600 hover:text-primary underline">Reset</a>
           @endif
       </div>
   </form>

    <div class="overflow-x-auto relative shadow-md sm:rounded-lg border border-gray-200">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama</th>
                    <th scope="col" class="px-6 py-3">Email</th>
                    <th scope="col" class="px-6 py-3">Role</th>
                    <th scope="col" class="px-6 py-3">Terhubung ke</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="text-primary hover:underline" title="Lihat Detail">
                                {{ $user->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                            {{-- Tampilkan roles --}}
                            @if($user->roles->isNotEmpty())
                                @foreach($user->roles as $role)
                                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $role->name == 'Super Admin' ? 'bg-red-100 text-red-800' : 
                                           ($role->name == 'Admin Bidang 3' ? 'bg-purple-100 text-purple-800' : 
                                           ($role->name == 'Admin Klasis' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($role->name == 'Admin Jemaat' ? 'bg-blue-100 text-blue-800' : 
                                           ($role->name == 'Pendeta' ? 'bg-gray-200 text-gray-700' : 'bg-green-100 text-green-800')))) }}">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-gray-400 italic">Tanpa Role</span>
                            @endif
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-xs">
                            {{-- Tampilkan relasi --}}
                            @if($user->pendeta)
                                <span class="font-medium text-gray-700">Pendeta: {{ $user->pendeta->nama_lengkap }}</span>
                            @elseif($user->klasisTugas)
                                <span class="font-medium text-yellow-700">Klasis: {{ $user->klasisTugas->nama_klasis }}</span>
                            @elseif($user->jemaatTugas)
                                 <span class="font-medium text-blue-700">Jemaat: {{ $user->jemaatTugas->nama_jemaat }}</span>
                            @else
                                -
                            @endif
                         </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            {{-- @hasrole('Super Admin') --}}
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 font-medium" title="Edit">Edit</a>
                            
                            {{-- Jangan hapus user ID 1 --}}
                            @if($user->id != 1) 
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium" title="Hapus">Hapus</button>
                                </form>
                            @else
                                <span class="text-gray-400 cursor-not-allowed" title="Super Admin Utama tidak dapat dihapus">Hapus</span>
                            @endif
                            {{-- @endhasrole --}}
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b">
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                            Tidak ada data user yang ditemukan
                            @if(request('search'))
                                untuk pencarian "{{ request('search') }}"
                            @endif
                            .
                            {{-- @hasrole('Super Admin') --}}
                            <a href="{{ route('admin.users.create') }}" class="text-primary hover:underline ml-2">Tambah Baru?</a>
                            {{-- @endhasrole --}}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-6">
        {{ $users->appends(request()->query())->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection