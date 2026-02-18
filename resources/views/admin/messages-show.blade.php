@extends('layouts.app')

@section('title', 'Detail Pesan Masuk')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    {{-- Header Navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.messages') }}" class="text-slate-500 hover:text-slate-800 text-xs font-bold uppercase tracking-wide flex items-center transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Kotak Masuk
        </a>
        
        {{-- Delete Action --}}
        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wide transition shadow-sm flex items-center">
                <i class="fas fa-trash-alt mr-2"></i> Hapus Pesan
            </button>
        </form>
    </div>

    {{-- Message Content Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        
        {{-- Card Header: Subjek & Pengirim --}}
        <div class="bg-slate-50 p-6 border-b border-slate-100">
            <h1 class="text-xl md:text-2xl font-bold text-slate-800 mb-4 leading-tight">{{ $message->subject }}</h1>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 text-sm">
                <div class="flex items-start gap-3">
                    {{-- Avatar Inisial --}}
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg uppercase shadow-sm border border-blue-200">
                        {{ substr($message->name, 0, 1) }}
                    </div>
                    
                    {{-- Detail Pengirim --}}
                    <div>
                        <span class="block font-bold text-slate-800 text-base">{{ $message->name }}</span>
                        <div class="flex flex-wrap gap-x-2 text-slate-500 text-xs">
                            <a href="mailto:{{ $message->email }}" class="hover:text-blue-600 hover:underline flex items-center">
                                <i class="far fa-envelope mr-1"></i> {{ $message->email }}
                            </a>
                            @if($message->phone)
                                <span class="text-slate-300">|</span>
                                <span class="flex items-center">
                                    <i class="fas fa-phone-alt mr-1"></i> {{ $message->phone }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Waktu --}}
                <div class="text-slate-500 md:text-right bg-white px-3 py-1.5 rounded border border-slate-200 shadow-sm">
                    <div class="font-bold text-xs uppercase tracking-wide text-slate-400 mb-0.5">Diterima Pada</div>
                    <div class="font-medium text-slate-700">
                        {{ $message->created_at->isoFormat('dddd, D MMMM YYYY') }}
                        <span class="mx-1 text-slate-300">•</span>
                        {{ $message->created_at->format('H:i') }} WIT
                    </div>
                </div>
            </div>
        </div>

        {{-- Message Body --}}
        <div class="p-8">
            <div class="prose max-w-none text-slate-700 leading-relaxed whitespace-pre-line text-sm md:text-base">
                {{ $message->message }}
            </div>
        </div>

        {{-- Footer / Reply Action --}}
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end">
            <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="bg-slate-800 text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-slate-900 transition shadow-lg flex items-center">
                <i class="fas fa-reply mr-2"></i> Balas Email
            </a>
        </div>
    </div>
</div>
@endsection