@extends('layouts.app')

@section('title', 'Tinjauan Detail Klasis')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ activeTab: 'profil' }">

    {{-- KOP HEADER --}}
    <div class="bg-white border border-gray-300 rounded shadow-sm p-6 flex flex-col md:flex-row items-center md:items-start gap-6 border-l-8 border-l-gray-800">
        <div class="h-28 w-28 rounded bg-gray-100 border border-gray-300 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
            @if($klasis->foto_kantor_path)
                <img src="{{ Storage::url($klasis->foto_kantor_path) }}" class="h-full w-full object-cover">
            @else
                <i class="fas fa-map-marked-alt text-4xl text-gray-400"></i>
            @endif
        </div>
        
        <div class="flex-1 text-center md:text-left">
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-widest leading-tight mb-1">{{ $klasis->nama_klasis }}</h1>
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 text-xs font-bold text-gray-600 uppercase tracking-widest mb-3">
                <span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded border border-gray-300 font-mono">KODE: {{ $klasis->kode_klasis }}</span>
                <span><i class="fas fa-map-marker-alt text-gray-400 mx-1"></i> Pusat: {{ $klasis->pusat_klasis ?? '-' }}</span>
            </div>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-3">
                <span class="px-3 py-1 bg-blue-50 border border-blue-200 text-blue-800 rounded text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-church mr-1"></i> {{ $klasis->jemaat->count() }} Jemaat
                </span>
                <span class="px-3 py-1 bg-green-50 border border-green-200 text-green-700 rounded text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-user-tie mr-1"></i> Ketua MPK: {{ $klasis->ketuaMp->nama_lengkap ?? 'Belum Ditunjuk' }}
                </span>
            </div>
        </div>
        
        <div class="flex flex-col gap-2 shrink-0 w-full md:w-auto mt-4 md:mt-0">
            <a href="{{ route('admin.klasis.index') }}" class="text-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-[10px] font-bold uppercase tracking-widest rounded hover:bg-gray-100 transition shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Indeks Wilayah
            </a>
            @hasanyrole('Super Admin|Admin Bidang 3')
            <a href="{{ route('admin.klasis.edit', $klasis->id) }}" class="text-center px-4 py-2 bg-gray-800 text-white text-[10px] font-bold uppercase tracking-widest rounded hover:bg-gray-900 transition shadow-sm">
                <i class="fas fa-edit mr-1"></i> Modifikasi
            </a>
            @endhasanyrole
        </div>
    </div>

    {{-- MENU TAB FORMAL --}}
    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden mb-10">
        <div class="flex border-b border-gray-200 bg-gray-50 overflow-x-auto">
            <button @click="activeTab = 'profil'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'profil', 'text-gray-600 hover:text-gray-900': activeTab !== 'profil'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                1. Administrasi & Kontak
            </button>
            <button @click="activeTab = 'jemaat'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'jemaat', 'text-gray-600 hover:text-gray-900': activeTab !== 'jemaat'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                2. Daftar Jemaat ({{ $klasis->jemaat->count() }})
            </button>
        </div>

        <div class="p-8 min-h-[350px]">
            
            {{-- TAB 1: BIODATA SIPIL --}}
            <div x-show="activeTab === 'profil'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Legalitas & Sejarah</h4>
                        <table class="w-full text-sm text-gray-700">
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">SK Pembentukan</td><td class="py-2.5 font-bold text-gray-900">{{ $klasis->nomor_sk_pembentukan ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Tgl Berdiri</td><td class="py-2.5 font-bold text-gray-900">{{ $klasis->tanggal_pembentukan ? $klasis->tanggal_pembentukan->format('d M Y') : '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Klasis Induk</td><td class="py-2.5 font-bold text-gray-900">{{ $klasis->klasis_induk ?? '-' }}</td></tr>
                            <tr><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase align-top">Sejarah Singkat</td><td class="py-2.5 text-xs text-gray-600 italic leading-relaxed">{{ $klasis->sejarah_singkat ?? 'Belum ada catatan sejarah.' }}</td></tr>
                        </table>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Kontak & Lokasi Geografis</h4>
                        <table class="w-full text-sm text-gray-700 mb-4">
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Telepon Kantor</td><td class="py-2.5 font-mono text-xs font-bold text-gray-900">{{ $klasis->telepon_kantor ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Surel (Email)</td><td class="py-2.5 text-xs font-bold text-gray-900">{{ $klasis->email_klasis ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase align-top">Alamat Kantor</td><td class="py-2.5 bg-gray-50 p-3 rounded border border-gray-200 text-xs leading-relaxed">{{ $klasis->alamat_kantor ?? '-' }}</td></tr>
                        </table>
                        
                        {{-- Placeholder Peta Geografis (Jika API tersedia kelak) --}}
                        <div class="h-32 bg-gray-50 rounded border border-gray-200 flex flex-col items-center justify-center relative overflow-hidden">
                            @if($klasis->latitude && $klasis->longitude)
                                <div class="absolute inset-0 bg-blue-50 opacity-50" style="background-color: {{ $klasis->warna_peta ?? '#eff6ff' }};"></div>
                                <i class="fas fa-map-marker-alt text-2xl mb-2 z-10" style="color: {{ $klasis->warna_peta ?? '#1e40af' }};"></i>
                                <span class="text-[10px] font-bold text-gray-600 z-10 uppercase tracking-widest">Titik Koordinat Geografis</span>
                                <span class="text-[9px] font-mono text-gray-500 z-10 mt-1">{{ $klasis->latitude }}, {{ $klasis->longitude }}</span>
                            @else
                                <i class="fas fa-map text-3xl text-gray-300 mb-2"></i>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Koordinat Peta Belum Dikalibrasi</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: DAFTAR JEMAAT --}}
            <div x-show="activeTab === 'jemaat'" x-cloak>
                <div class="overflow-x-auto border border-gray-300 rounded shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                                <th class="px-5 py-3 text-center w-12">#</th>
                                <th class="px-5 py-3">Nama Jemaat / Organisasi</th>
                                <th class="px-5 py-3 text-center">Status</th>
                                <th class="px-5 py-3">Alamat / Lokasi</th>
                                <th class="px-5 py-3 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-200">
                            @forelse($klasis->jemaat as $jemaat)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-4 text-center font-mono text-xs text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-5 py-4 font-bold text-xs uppercase text-gray-900">
                                    {{ $jemaat->nama_jemaat }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @php
                                        $statusClass = $jemaat->status_jemaat == 'Mandiri' 
                                            ? 'bg-green-100 text-green-800 border-green-300' 
                                            : 'bg-yellow-100 text-yellow-800 border-yellow-300';
                                    @endphp
                                    <span class="text-[9px] font-bold uppercase tracking-widest {{ $statusClass }} border px-2 py-0.5 rounded">{{ $jemaat->status_jemaat ?? '-' }}</span>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-600">
                                    {{ Str::limit($jemaat->alamat ?? '-', 40) }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('admin.jemaat.show', $jemaat->id) }}" class="text-gray-400 hover:text-blue-800 transition" title="Lihat Direktori Jemaat">
                                        <i class="fas fa-folder-open text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest italic">Belum ada jemaat yang bernaung di wilayah ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection