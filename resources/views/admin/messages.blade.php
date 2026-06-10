@extends('layouts.app')

@section('title', 'Pesan Masuk')

@section('content')
<div class="space-y-6">
    
    {{-- Header & Search --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Kotak Masuk</h2>
            <p class="text-sm text-slate-500">Pesan dari formulir kontak website publik.</p>
        </div>
        <div class="w-full md:w-auto">
            {{-- Search Form (Jika menggunakan livewire atau controller search biasa) --}}
            <form method="GET" class="relative">
                <i class="fas fa-search absolute left-3 top-3 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Pengirim / Subjek..." 
                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500 w-full md:w-64">
            </form>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session('success'))
        <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg border border-green-200 flex items-center text-sm shadow-sm">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Tabel Pesan --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Pengirim</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Subjek Pesan</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Waktu</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($messages as $message)
                <tr class="hover:bg-slate-50 transition duration-150 group {{ !$message->is_read ? 'bg-blue-50/30' : '' }}">
                    <td class="px-6 py-4 align-top">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-xs font-bold mr-3 uppercase">
                                {{ substr($message->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm {{ !$message->is_read ? 'font-bold text-slate-800' : 'font-medium text-slate-700' }}">
                                    {{ $message->name }}
                                </div>
                                <div class="text-xs text-slate-500">{{ $message->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-top">
                        <span class="block text-sm {{ !$message->is_read ? 'font-bold text-slate-800' : 'text-slate-600' }}">
                            {{ Str::limit($message->subject, 40) }}
                        </span>
                        <span class="text-xs text-slate-400 mt-1 block">
                            {{ Str::limit($message->message, 60) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 align-top whitespace-nowrap">
                        <div class="text-xs text-slate-500 font-medium">
                            {{ $message->created_at->diffForHumans() }}
                        </div>
                        <div class="text-[10px] text-slate-400">
                            {{ $message->created_at->format('d/m/Y H:i') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 align-top text-center">
                        <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.messages.show', $message) }}" class="text-slate-400 hover:text-blue-600 transition" title="Baca Pesan">
                                <i class="fas fa-envelope-open-text"></i>
                            </a>
                            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pesan ini permanen?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">
                        <div class="flex flex-col items-center">
                            <i class="far fa-envelope-open fa-3x mb-3 text-slate-300"></i>
                            <span>Tidak ada pesan masuk saat ini.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        {{-- Pagination --}}
        @if($messages->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection