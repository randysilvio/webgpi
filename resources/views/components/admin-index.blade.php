@props(['title', 'subtitle', 'createRoute' => null, 'createLabel' => 'Tambah Data'])

<div class="space-y-6">
    
    {{-- 1. HEADER HALAMAN --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">{{ $title }}</h2>
            @if($subtitle)
                <p class="text-xs text-slate-500 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        
        {{-- Slot untuk tombol aksi tambahan (misal: Export/Import) --}}
        <div class="flex gap-2">
            @if(isset($actions))
                {{ $actions }}
            @endif

            @if($createRoute)
            <a href="{{ $createRoute }}" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold uppercase tracking-wide rounded shadow-sm transition">
                <i class="fas fa-plus mr-2"></i> {{ $createLabel }}
            </a>
            @endif
        </div>
    </div>

    {{-- 2. SLOT STATISTIK (Opsional) --}}
    @if(isset($stats))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            {{ $stats }}
        </div>
    @endif

    {{-- 3. AREA FILTER & PENCARIAN --}}
    @if(isset($filters))
    <div class="bg-white p-4 rounded border border-slate-200 shadow-sm">
        {{ $filters }}
    </div>
    @endif

    {{-- 4. TABEL DATA --}}
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-bold text-slate-500 tracking-wider">
                        {{ $tableHead }}
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    {{ $slot }}
                </tbody>
            </table>
        </div>
        
        {{-- Pagination (Otomatis cek jika ada) --}}
        @if(isset($pagination) && $pagination->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $pagination->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>