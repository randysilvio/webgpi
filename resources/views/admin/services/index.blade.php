@extends('admin.layout')

@section('title', 'Manajemen Layanan')
@section('header-title', 'Manajemen Layanan Holistik')

@section('content')

     {{-- Flash Messages --}}
     @if (session('success')) <div class="flash-message mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert"><p>{{ session('success') }}</p></div> @endif
     @if (session('error')) <div class="flash-message mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert"><p>{{ session('error') }}</p></div> @endif

    {{-- Tombol Tambah --}}
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.services.create') }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow hover:shadow-md">
            <svg class="w-4 h-4 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Layanan
        </a>
    </div>

     {{-- Tabel Daftar Layanan --}}
     <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
         <div class="overflow-x-auto">
             <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Layanan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tema Warna</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ikon</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($services as $service)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $service->order }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $service->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm capitalize">
                            {{-- Tampilkan warna tema --}}
                            <span class_="{{ 'text-' . $service->color_theme . '-700 bg-' . $service->color_theme . '-100' }} px-2 py-0.5 rounded text-xs font-medium">{{ $service->color_theme }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ $service->icon ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('admin.services.edit', $service) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus layanan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data layanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
         </div>
         <div class="px-6 py-4 border-t">{{ $services->links('vendor.pagination.tailwind') }}</div>
    </div>
@endsection