@extends('layouts.app')

@section('title', 'Arsip Dokumen Publikasi')

@section('content')
    <x-admin-index 
        title="Buku Induk Publikasi" 
        subtitle="Sistem registrasi, verifikasi, dan manajemen rekam jejak berita resmi."
        create-route="{{ route('admin.posts.create') }}"
        create-label="Registrasi Dokumen Baru"
        :pagination="$posts"
    >
        {{-- SLOT STATS (Dinamic Badge) --}}
        <x-slot name="stats">
            @hasanyrole('Super Admin|Admin Bidang 4')
            <a href="{{ route('admin.posts.index', ['status' => 'pending']) }}" class="bg-white p-5 rounded border {{ request('status') == 'pending' ? 'border-blue-800 ring-1 ring-blue-800' : 'border-gray-200' }} shadow-sm flex items-center justify-between hover:bg-gray-50 transition">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Antrean Verifikasi</p>
                    <p class="text-2xl font-bold text-blue-900 mt-1">{{ $pendingCount ?? 0 }} <span class="text-xs text-gray-400 font-normal">Dokumen</span></p>
                </div>
                <div class="p-3 bg-gray-100 text-gray-600 rounded border border-gray-200"><i class="fas fa-inbox text-lg"></i></div>
            </a>
            @endhasanyrole

            <a href="{{ route('admin.posts.index') }}" class="bg-white p-5 rounded border {{ !request('status') ? 'border-blue-800 ring-1 ring-blue-800' : 'border-gray-200' }} shadow-sm flex items-center justify-between hover:bg-gray-50 transition">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Total Registrasi (Pribadi)</p>
                    <p class="text-2xl font-bold text-blue-900 mt-1">{{ number_format($posts->total()) }} <span class="text-xs text-gray-400 font-normal">Dokumen</span></p>
                </div>
                <div class="p-3 bg-gray-100 text-gray-600 rounded border border-gray-200"><i class="fas fa-archive text-lg"></i></div>
            </a>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-3 font-bold text-gray-700 text-xs uppercase border-b-2 border-gray-800 bg-gray-100">Uraian Dokumen</th>
            <th class="px-6 py-3 font-bold text-gray-700 text-xs uppercase border-b-2 border-gray-800 bg-gray-100 text-center w-40">Status Otorisasi</th>
            <th class="px-6 py-3 font-bold text-gray-700 text-xs uppercase border-b-2 border-gray-800 bg-gray-100 w-48">Jadwal Tayang</th>
            <th class="px-6 py-3 font-bold text-gray-700 text-xs uppercase border-b-2 border-gray-800 bg-gray-100 text-center w-24">Tindakan</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse ($posts as $post)
            <tr class="hover:bg-gray-50 transition border-b border-gray-200 group">
                <td class="px-6 py-4">
                    <div class="flex items-start gap-4">
                        <div class="h-12 w-16 bg-gray-200 border border-gray-300 flex-shrink-0">
                            @if($post->image_path)
                                <img src="{{ Storage::url($post->image_path) }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center text-gray-400"><i class="fas fa-file-image"></i></div>
                            @endif
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 text-sm leading-snug line-clamp-2">{{ $post->title }}</div>
                            <div class="text-[10px] text-gray-500 mt-1 font-medium flex items-center uppercase tracking-wide">
                                <i class="fas fa-user-tie mr-2"></i> Perekam: {{ $post->author->name ?? 'Sistem' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($post->status == 'published')
                        <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase bg-green-100 text-green-800 border border-green-300 w-full text-center">Terpublikasi</span>
                    @elseif($post->status == 'pending')
                        <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase bg-yellow-100 text-yellow-800 border border-yellow-300 w-full text-center">Tinjauan</span>
                    @else
                        <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 w-full text-center">Draf</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="text-xs font-bold text-gray-800">
                        {{ $post->published_at ? $post->published_at->isoFormat('D MMM YYYY') : '-' }}
                    </div>
                    <div class="text-[10px] text-gray-500 mt-0.5">
                        {{ $post->published_at ? $post->published_at->format('H:i') . ' WIT' : 'Tidak Dijadwalkan' }}
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-4">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-gray-400 hover:text-blue-800 transition" title="Verifikasi / Koreksi">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Prosedur ini akan memusnahkan dokumen secara permanen dari pangkalan data. Anda yakin?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-700 transition" title="Musnahkan Rekord">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm"><i class="fas fa-inbox text-2xl mb-3 block text-gray-300"></i>Arsip dokumen publikasi masih kosong.</td></tr>
        @endforelse
    </x-admin-index>
@endsection