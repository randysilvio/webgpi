@extends('admin.layout') {{-- Mewarisi layout admin --}}

@section('title', 'Pesan Masuk') {{-- Mengisi judul halaman --}}
@section('header-title', 'Pesan Masuk') {{-- Mengisi judul header --}}

@section('content') {{-- Mulai mengisi konten utama --}}

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flash-message mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
     @if (session('error'))
        <div class="flash-message mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
         <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Pesan Kontak</h3>
         </div>
         <div class="overflow-x-auto">
             <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pengirim</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subjek</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    
                    @forelse ($messages as $message)
                    {{-- Baris akan tebal jika belum dibaca --}}
                    <tr class="{{ !$message->is_read ? 'font-bold' : 'font-normal' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$message->is_read ? 'text-gray-900' : 'text-gray-600' }}">
                            {{ $message->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$message->is_read ? 'text-gray-800' : 'text-gray-500' }}">
                            {{ $message->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$message->is_read ? 'text-gray-800' : 'text-gray-500' }}">
                            {{ Str::limit($message->subject, 30) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$message->is_read ? 'text-gray-800' : 'text-gray-500' }}">
                            {{ $message->created_at->isoFormat('D MMM YYYY, H:mm') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('admin.messages.show', $message) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                            {{-- Form Hapus --}}
                            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada pesan masuk.
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
         </div>
         {{-- Pagination Links --}}
         <div class="px-6 py-4 border-t">
             {{ $messages->links('vendor.pagination.tailwind') }}
         </div>
    </div>

@endsection