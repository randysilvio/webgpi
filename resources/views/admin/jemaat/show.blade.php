@extends('layouts.app')

@section('title', 'Tinjauan Profil Jemaat')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- KOP HEADER --}}
    <div class="bg-white border border-gray-300 rounded shadow-sm p-6 flex flex-col md:flex-row items-center md:items-start gap-6 border-l-8 border-l-blue-800">
        <div class="h-24 w-24 rounded border border-gray-300 flex items-center justify-center bg-gray-100 overflow-hidden shrink-0 shadow-inner">
            @if($jemaat->foto_gereja_path)
                <img src="{{ Storage::url($jemaat->foto_gereja_path) }}" class="h-full w-full object-cover">
            @else
                <i class="fas fa-church text-4xl text-gray-400"></i>
            @endif
        </div>
        <div class="flex-1 text-center md:text-left">
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-widest leading-tight mb-1">{{ $jemaat->nama_jemaat }}</h1>
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 text-xs font-bold text-gray-600 uppercase tracking-widest">
                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded border border-blue-200">#{{ $jemaat->kode_jemaat ?? 'N/A' }}</span>
                <span><i class="fas fa-map-marker-alt text-gray-400 mx-1"></i> {{ $jemaat->klasis->nama_klasis ?? 'Pusat Sinode' }}</span>
            </div>
            
            <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-4">
                <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded text-center min-w-[100px]">
                    <span class="block text-[9px] uppercase text-gray-500 font-bold tracking-widest">Status</span>
                    <span class="text-sm font-black text-gray-800 uppercase">{{ $jemaat->status_jemaat }}</span>
                </div>
                <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded text-center min-w-[100px]">
                    <span class="block text-[9px] uppercase text-gray-500 font-bold tracking-widest">Sensus KK</span>
                    <span class="text-sm font-black text-blue-800">{{ number_format($jemaat->jumlah_kk ?? 0) }}</span>
                </div>
                <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded text-center min-w-[100px]">
                    <span class="block text-[9px] uppercase text-gray-500 font-bold tracking-widest">Total Populasi</span>
                    <span class="text-sm font-black text-green-700">{{ number_format($jemaat->jumlah_total_jiwa ?? 0) }}</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-2 shrink-0 w-full md:w-auto mt-4 md:mt-0">
            <a href="{{ route('admin.jemaat.index') }}" class="text-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-[10px] font-bold uppercase tracking-widest rounded hover:bg-gray-100 transition shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Indeks Wilayah
            </a>
            @can('edit jemaat')
            <a href="{{ route('admin.jemaat.edit', $jemaat->id) }}" class="text-center px-4 py-2 bg-gray-800 text-white text-[10px] font-bold uppercase tracking-widest rounded hover:bg-gray-900 transition shadow-sm">
                <i class="fas fa-edit mr-1"></i> Modifikasi Profil
            </a>
            @endcan
        </div>
    </div>

    {{-- KONTEN DETAIL GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- PANEL KIRI: INFO UMUM & KONTAK --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white border border-gray-300 rounded shadow-sm p-6">
                <h3 class="text-xs font-black text-gray-800 uppercase border-b border-gray-200 pb-2 mb-4"><i class="fas fa-info-circle mr-2 text-blue-800"></i> I. Data Administratif</h3>
                <table class="w-full text-sm">
                    <tr class="border-b border-gray-50">
                        <td class="py-2.5 w-1/3 text-[10px] font-bold text-gray-500 uppercase">Jenis Pelayanan</td>
                        <td class="py-2.5 font-bold text-gray-800">{{ $jemaat->jenis_jemaat }}</td>
                    </tr>
                    <tr class="border-b border-gray-50">
                        <td class="py-2.5 w-1/3 text-[10px] font-bold text-gray-500 uppercase">Tanggal Peresmian</td>
                        <td class="py-2.5 font-bold text-gray-800">{{ $jemaat->tanggal_berdiri ? $jemaat->tanggal_berdiri->isoFormat('D MMMM Y') : 'Belum Terdata' }}</td>
                    </tr>
                    <tr class="border-b border-gray-50">
                        <td class="py-2.5 w-1/3 text-[10px] font-bold text-gray-500 uppercase">Telepon Sekretariat</td>
                        <td class="py-2.5 font-mono text-xs text-gray-700">{{ $jemaat->telepon_kantor ?? '-' }}</td>
                    </tr>
                    <tr class="border-b border-gray-50">
                        <td class="py-2.5 w-1/3 text-[10px] font-bold text-gray-500 uppercase">Surat Elektronik (Email)</td>
                        <td class="py-2.5 text-blue-700">{{ $jemaat->email_jemaat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-2.5 w-1/3 text-[10px] font-bold text-gray-500 uppercase align-top">Alamat Gedung Gereja</td>
                        <td class="py-2.5 text-gray-800 leading-relaxed text-xs">{{ $jemaat->alamat_gereja ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- PANEL KANAN: PENDETA & SDM --}}
        <div class="bg-white border border-gray-300 rounded shadow-sm p-6 flex flex-col h-full">
            <h3 class="text-xs font-black text-gray-800 uppercase border-b border-gray-200 pb-2 mb-4"><i class="fas fa-user-tie mr-2 text-blue-800"></i> II. Pelayan Firman Aktif</h3>
            
            <div class="flex-grow overflow-y-auto max-h-64">
                @if($jemaat->pendetaDitempatkan->count() > 0)
                    <ul class="space-y-3">
                        @foreach($jemaat->pendetaDitempatkan as $p)
                        <li class="bg-gray-50 border border-gray-200 p-3 rounded flex items-center gap-3 hover:bg-gray-100 transition">
                            <div class="w-10 h-10 rounded border border-gray-300 bg-white flex items-center justify-center text-gray-800 text-sm font-black uppercase shadow-sm">
                                {{ substr($p->nama_lengkap, 0, 1) }}
                            </div>
                            <div>
                                <a href="{{ route('admin.kepegawaian.pegawai.show', $p->id) }}" class="text-xs font-bold text-gray-900 hover:text-blue-800 uppercase leading-snug">{{ $p->nama_lengkap }}</a>
                                <p class="text-[10px] font-mono text-gray-500 mt-0.5">NIPG: {{ $p->nipg ?? 'Non-Organik' }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-user-times text-3xl text-gray-300 mb-2 block"></i>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Kekosongan Personel</p>
                        <p class="text-[9px] text-gray-400 mt-1 italic">Belum ada SK penempatan pelayan firman organik di jemaat ini.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection