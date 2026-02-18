@extends('layouts.app')

@section('title', 'Berita & Kegiatan')

@section('content')
    <x-admin-index 
        title="Manajemen Konten" 
        subtitle="Kelola artikel berita, pengumuman, dan publikasi kegiatan gereja."
        create-route="{{ route('admin.posts.create') }}"
        create-label="Tambah Berita"
        :pagination="$posts"
    >
        {{-- SLOT STATS --}}
        <x-slot name="stats">
            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Post</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($posts->total()) }}</p>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i class="fas fa-newspaper text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Published</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $posts->where('published_at', '<=', now())->count() }}</p>
                </div>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg"><i class="fas fa-check-double text-lg"></i></div>
            </div>

            <div class="bg-white p-5 rounded border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Terjadwal</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $posts->where('published_at', '>', now())->count() }}</p>
                </div>
                <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg"><i class="fas fa-clock text-lg"></i></div>
            </div>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4">Informasi Berita</th>
            <th class="px-6 py-4 text-center">Status</th>
            <th class="px-6 py-4">Tanggal Publish</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse ($posts as $post)
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <div class="flex items-center gap-4">
                        {{-- Thumbnail --}}
                        <div class="h-12 w-12 rounded-lg bg-slate-100 overflow-hidden border border-slate-200 flex-shrink-0">
                            @if($post->image_path)
                                <img src="{{ Storage::url($post->image_path) }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center text-slate-300">
                                    <i class="fas fa-image text-xl"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 text-sm leading-snug line-clamp-1">{{ $post->title }}</div>
                            <div class="text-[10px] text-slate-400 mt-1 uppercase tracking-tight font-medium">
                                <i class="fas fa-user-edit mr-1"></i> Admin Sinode
                            </div>
                        </div>
                    </div>
                </x-td>
                <x-td class="text-center">
                    @if($post->published_at && $post->published_at <= now())
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-green-100 text-green-700 border border-green-200">Published</span>
                    @elseif($post->published_at && $post->published_at > now())
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-yellow-100 text-yellow-700 border border-yellow-200">Scheduled</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-500 border border-slate-200">Draft</span>
                    @endif
                </x-td>
                <x-td>
                    <div class="text-xs font-medium text-slate-600">
                        {{ $post->published_at ? $post->published_at->isoFormat('D MMM YYYY') : '-' }}
                    </div>
                    <div class="text-[10px] text-slate-400 font-mono">
                        {{ $post->published_at ? $post->published_at->format('H:i') . ' WIT' : '' }}
                    </div>
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-slate-400 hover:text-yellow-600 transition" title="Edit Konten">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Hapus berita ini secara permanen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </x-td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Belum ada konten yang diterbitkan.</td></tr>
        @endforelse
    </x-admin-index>
@endsection