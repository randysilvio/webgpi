@extends('admin.layout')

@section('title', 'Lihat Pesan Masuk')
@section('header-title', 'Lihat Pesan Masuk')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.messages') }}" class="inline-flex items-center text-sm font-medium text-primary hover:text-blue-700">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Pesan
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
        {{-- Header Pesan --}}
        <div class="border-b pb-4 mb-4">
            <h3 class="text-xl font-semibold text-gray-900">{{ $message->subject }}</h3>
            <div class="mt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-700">
                    <span class="font-medium">Dari:</span> {{ $message->name }} ({{ $message->email }})
                </div>
                <div class="text-sm text-gray-500 mt-1 sm:mt-0">
                    <span class="font-medium">Telepon:</span> {{ $message->phone ?? '-' }}
                </div>
                <div class="text-sm text-gray-500 mt-1 sm:mt-0">
                    {{ $message->created_at->isoFormat('dddd, D MMMM YYYY - H:mm') }} WIT
                </div>
            </div>
        </div>

        {{-- Isi Pesan --}}
        <div class="prose max-w-none text-gray-800 leading-relaxed whitespace-pre-line">
            {{ $message->message }}
        </div>

         {{-- Aksi Hapus --}}
         <div class="mt-6 pt-6 border-t flex justify-end">
            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus Pesan
                </button>
            </form>
         </div>

    </div>
@endsection