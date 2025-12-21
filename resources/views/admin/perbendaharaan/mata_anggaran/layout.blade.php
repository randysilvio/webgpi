<div class="pt-4 mt-4 border-t border-gray-700">
    <span class="px-4 text-xs font-semibold uppercase text-gray-500">Perbendaharaan & Aset</span>
    
    {{-- Menu Inventaris Aset (Fase 7.1) --}}
    <a href="{{ route('admin.perbendaharaan.aset.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors @if(Request::routeIs('admin.perbendaharaan.aset.*')) active @endif">
        <i class="fas fa-boxes w-5 h-5 mr-3 fa-fw"></i>
        <span>Inventaris Aset</span>
    </a>

    {{-- Menu Mata Anggaran / COA (Fase 7.2) --}}
    @hasanyrole('Super Admin|Admin Sinode')
    <a href="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors @if(Request::routeIs('admin.perbendaharaan.mata-anggaran.*')) active @endif">
        <i class="fas fa-list-ol w-5 h-5 mr-3 fa-fw"></i>
        <span>Mata Anggaran</span>
    </a>
    @endhasanyrole

    {{-- Menu Rencana APB (Future) --}}
    <a href="#" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors opacity-50 cursor-not-allowed">
        <i class="fas fa-file-invoice w-5 h-5 mr-3 fa-fw"></i>
        <span>Rencana APB</span>
    </a>
</div>