@props(['action', 'title', 'method' => 'POST', 'backRoute' => null])

<div class="max-w-4xl mx-auto">
    {{-- Header Form --}}
    <div class="flex items-center gap-3 mb-6">
        @if($backRoute)
        <a href="{{ $backRoute }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-slate-300 text-slate-500 hover:bg-slate-50 transition shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        @endif
        <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">{{ $title }}</h2>
    </div>

    {{-- Form Container --}}
    <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
            @method($method)
        @endif

        <div class="bg-white rounded shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 space-y-6">
                {{-- Slot untuk Input Fields --}}
                {{ $slot }}
            </div>
            
            {{-- Footer Actions --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                @if($backRoute)
                <a href="{{ $backRoute }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-xs font-bold uppercase rounded hover:bg-slate-100 transition">
                    Batal
                </a>
                @endif
                <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white text-xs font-bold uppercase tracking-wide rounded hover:bg-slate-900 transition shadow-lg">
                    Simpan Data
                </button>
            </div>
        </div>
    </form>
</div>