@extends('layouts.app')

@section('title', 'Tinjauan Biodata Anggota')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ activeTab: 'profil' }">

    {{-- KOP HEADER --}}
    <div class="bg-white border border-gray-300 rounded shadow-sm p-6 flex flex-col md:flex-row items-center md:items-start gap-6 border-l-8 border-l-gray-800">
        <div class="h-28 w-28 rounded bg-gray-100 border border-gray-300 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
            @if(isset($anggotaJemaat->foto_profil) && $anggotaJemaat->foto_profil)
                <img src="{{ Storage::url($anggotaJemaat->foto_profil) }}" class="h-full w-full object-cover">
            @else
                <span class="text-4xl text-gray-400 font-black uppercase">{{ substr($anggotaJemaat->nama_lengkap, 0, 1) }}</span>
            @endif
        </div>
        
        <div class="flex-1 text-center md:text-left">
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-widest leading-tight mb-1">{{ $anggotaJemaat->nama_lengkap }}</h1>
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 text-xs font-bold text-gray-600 uppercase tracking-widest mb-3">
                <span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded border border-gray-300 font-mono">No. Induk: {{ $anggotaJemaat->nomor_buku_induk ?? '-' }}</span>
                <span><i class="fas fa-church text-gray-400 mx-1"></i> {{ $anggotaJemaat->jemaat->nama_jemaat ?? 'Tanpa Jemaat' }}</span>
                <span class="text-gray-300">|</span>
                <span>Sektor: {{ $anggotaJemaat->sektor_pelayanan ?? '-' }}</span>
            </div>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-3">
                @php
                    $statusClass = match($anggotaJemaat->status_keanggotaan) {
                        'Aktif' => 'bg-green-100 border border-green-300 text-green-800',
                        'Meninggal' => 'bg-gray-200 border border-gray-400 text-gray-700',
                        'Pindah' => 'bg-yellow-100 border border-yellow-300 text-yellow-800',
                        default => 'bg-red-100 border border-red-300 text-red-800'
                    };
                @endphp
                <span class="px-3 py-1 rounded text-[10px] font-black uppercase tracking-widest {{ $statusClass }}">
                    Status: {{ $anggotaJemaat->status_keanggotaan }}
                </span>
                <span class="px-3 py-1 bg-blue-50 border border-blue-200 text-blue-800 rounded text-[10px] font-black uppercase tracking-widest">
                    NIK: {{ $anggotaJemaat->nik ?? '-' }}
                </span>
                <span class="px-3 py-1 bg-gray-50 border border-gray-200 text-gray-700 rounded text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-venus-mars mr-1 text-gray-400"></i> {{ $anggotaJemaat->jenis_kelamin }}
                </span>
            </div>
        </div>
        
        <div class="flex flex-col gap-2 shrink-0 w-full md:w-auto mt-4 md:mt-0">
            <a href="{{ route('admin.anggota-jemaat.index') }}" class="text-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-[10px] font-bold uppercase tracking-widest rounded hover:bg-gray-100 transition shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Indeks Buku
            </a>
            @can('edit anggota jemaat')
            <a href="{{ route('admin.anggota-jemaat.edit', $anggotaJemaat->id) }}" class="text-center px-4 py-2 bg-gray-800 text-white text-[10px] font-bold uppercase tracking-widest rounded hover:bg-gray-900 transition shadow-sm">
                <i class="fas fa-edit mr-1"></i> Modifikasi
            </a>
            @endcan
            <a href="{{ route('admin.anggota-jemaat.cetak-kk', $anggotaJemaat->id) }}" target="_blank" class="text-center px-4 py-2 bg-white border border-gray-300 text-green-700 text-[10px] font-bold uppercase tracking-widest rounded hover:bg-green-50 transition shadow-sm">
                <i class="fas fa-print mr-1"></i> Cetak KK
            </a>
        </div>
    </div>

    {{-- MENU TAB FORMAL --}}
    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200 bg-gray-50 overflow-x-auto">
            <button @click="activeTab = 'profil'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'profil', 'text-gray-600 hover:text-gray-900': activeTab !== 'profil'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                1. Biodata Sipil
            </button>
            <button @click="activeTab = 'gerejawi'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'gerejawi', 'text-gray-600 hover:text-gray-900': activeTab !== 'gerejawi'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                2. Arsip Sakramen
            </button>
            <button @click="activeTab = 'renstra'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'renstra', 'text-gray-600 hover:text-gray-900': activeTab !== 'renstra'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                3. Analisis Renstra
            </button>
            <button @click="activeTab = 'keluarga'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'keluarga', 'text-gray-600 hover:text-gray-900': activeTab !== 'keluarga'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                4. Pohon Silsilah (KK)
            </button>
        </div>

        <div class="p-8 min-h-[350px]">
            
            {{-- TAB 1: BIODATA SIPIL --}}
            <div x-show="activeTab === 'profil'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Informasi Pribadi</h4>
                        <table class="w-full text-sm text-gray-700">
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Tempat Kelahiran</td><td class="py-2.5 font-bold text-gray-900">{{ $anggotaJemaat->tempat_lahir ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Tanggal Kelahiran</td><td class="py-2.5 font-bold text-gray-900">{{ $anggotaJemaat->tanggal_lahir ? \Carbon\Carbon::parse($anggotaJemaat->tanggal_lahir)->isoFormat('D MMMM Y') : '-' }} <span class="text-[10px] text-gray-400 normal-case">({{ $anggotaJemaat->tanggal_lahir ? \Carbon\Carbon::parse($anggotaJemaat->tanggal_lahir)->age . ' Tahun' : '' }})</span></td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Golongan Darah</td><td class="py-2.5 font-bold text-red-600">{{ $anggotaJemaat->golongan_darah ?? '-' }}</td></tr>
                            <tr><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Status Disabilitas</td><td class="py-2.5 font-bold text-gray-900">{{ $anggotaJemaat->disabilitas ?? 'Tidak Ada' }}</td></tr>
                        </table>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Kontak & Domisili</h4>
                        <table class="w-full text-sm text-gray-700">
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Nomor HP/Telepon</td><td class="py-2.5 font-mono text-xs font-bold text-gray-900">{{ $anggotaJemaat->telepon ?? '-' }}</td></tr>
                            <tr><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase align-top">Alamat Tinggal</td><td class="py-2.5 bg-gray-50 p-3 rounded border border-gray-200 text-xs leading-relaxed">{{ $anggotaJemaat->alamat_lengkap ?? '-' }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAB 2: ARSIP SAKRAMEN --}}
            <div x-show="activeTab === 'gerejawi'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Baptis --}}
                    <div class="bg-white border border-gray-300 rounded p-5 text-center">
                        <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-700 text-xl"><i class="fas fa-water"></i></div>
                        <h5 class="text-[11px] font-black text-gray-800 uppercase tracking-widest mb-1">Sakramen Baptisan</h5>
                        @if($anggotaJemaat->tanggal_baptis)
                            <p class="text-xs font-bold text-blue-800 uppercase bg-blue-50 inline-block px-2 py-1 rounded mt-2">{{ \Carbon\Carbon::parse($anggotaJemaat->tanggal_baptis)->isoFormat('D MMM Y') }}</p>
                            <p class="text-[9px] text-gray-500 mt-2 uppercase">Lokasi: {{ $anggotaJemaat->tempat_baptis ?? '-' }}</p>
                        @else
                            <p class="text-[10px] text-gray-400 italic mt-3 font-bold uppercase tracking-widest">Arsip Kosong</p>
                        @endif
                    </div>
                    {{-- Sidi --}}
                    <div class="bg-white border border-gray-300 rounded p-5 text-center">
                        <div class="w-12 h-12 bg-green-50 border border-green-200 rounded-full flex items-center justify-center mx-auto mb-4 text-green-700 text-xl"><i class="fas fa-praying-hands"></i></div>
                        <h5 class="text-[11px] font-black text-gray-800 uppercase tracking-widest mb-1">Peneguhan Sidi</h5>
                        @if($anggotaJemaat->tanggal_sidi)
                            <p class="text-xs font-bold text-green-800 uppercase bg-green-50 inline-block px-2 py-1 rounded mt-2">{{ \Carbon\Carbon::parse($anggotaJemaat->tanggal_sidi)->isoFormat('D MMM Y') }}</p>
                            <p class="text-[9px] text-gray-500 mt-2 uppercase">Lokasi: {{ $anggotaJemaat->tempat_sidi ?? '-' }}</p>
                        @else
                            <p class="text-[10px] text-gray-400 italic mt-3 font-bold uppercase tracking-widest">Arsip Kosong</p>
                        @endif
                    </div>
                    {{-- Nikah --}}
                    <div class="bg-white border border-gray-300 rounded p-5 text-center">
                        <div class="w-12 h-12 bg-purple-50 border border-purple-200 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-700 text-xl"><i class="fas fa-ring"></i></div>
                        <h5 class="text-[11px] font-black text-gray-800 uppercase tracking-widest mb-1">Status Pernikahan</h5>
                        <p class="text-xs font-bold text-purple-800 uppercase bg-purple-50 inline-block px-2 py-1 rounded mt-2">{{ $anggotaJemaat->status_pernikahan ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- TAB 3: ANALISIS RENSTRA --}}
            <div x-show="activeTab === 'renstra'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Indikator Kesejahteraan Domestik</h4>
                        <table class="w-full text-sm text-gray-700">
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/2 text-[10px] uppercase">Profesi Utama</td><td class="py-2.5 font-bold text-gray-900">{{ $anggotaJemaat->pekerjaan_utama ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/2 text-[10px] uppercase">Legalitas Hak Tinggal</td><td class="py-2.5 font-bold text-gray-900">{{ $anggotaJemaat->status_kepemilikan_rumah ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/2 text-[10px] uppercase">Kondisi Bangunan</td><td class="py-2.5 font-bold text-gray-900">{{ $anggotaJemaat->kondisi_rumah ?? '-' }}</td></tr>
                            <tr><td class="py-2.5 font-bold w-1/2 text-[10px] uppercase">Estimasi Pengeluaran</td><td class="py-2.5 font-bold text-gray-900">{{ $anggotaJemaat->rentang_pengeluaran ?? '-' }}</td></tr>
                        </table>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Indeks Kapabilitas Digital & Ekonomi</h4>
                        <table class="w-full text-sm text-gray-700 mb-6">
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/2 text-[10px] uppercase">Kepemilikan Smartphone</td><td class="py-2.5 font-bold {{ $anggotaJemaat->punya_smartphone ? 'text-green-700' : 'text-red-700' }}">{{ $anggotaJemaat->punya_smartphone ? 'TERSEDIA' : 'TIDAK TERSEDIA' }}</td></tr>
                            <tr><td class="py-2.5 font-bold w-1/2 text-[10px] uppercase">Akses Jaringan Internet</td><td class="py-2.5 font-bold {{ $anggotaJemaat->akses_internet ? 'text-green-700' : 'text-red-700' }}">{{ $anggotaJemaat->akses_internet ? 'MEMADAI' : 'BLANK SPOT' }}</td></tr>
                        </table>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Aset Ekonomi Produktif</span>
                            <div class="bg-gray-50 border border-gray-200 p-3 rounded text-xs font-bold text-gray-800 uppercase tracking-wide leading-relaxed">
                                {{ $anggotaJemaat->aset_ekonomi ?? 'TIDAK ADA DATA POTENSI' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 4: SILSILAH KK --}}
            <div x-show="activeTab === 'keluarga'" x-cloak>
                <div class="flex flex-col items-center mb-10 bg-gray-50 border border-gray-200 rounded p-6">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-300 pb-2 w-full text-center">Tautan Data Biologis Induk</h4>
                    <div class="flex gap-4 md:gap-10 w-full max-w-lg justify-center">
                        {{-- Ayah --}}
                        <div class="flex-1 text-center p-4 rounded bg-white border border-gray-300 shadow-sm">
                            <p class="text-[9px] uppercase text-gray-500 font-bold tracking-widest mb-2"><i class="fas fa-male mr-1"></i> Ayah</p>
                            @if($anggotaJemaat->ayah_id)
                                <a href="{{ route('admin.anggota-jemaat.show', $anggotaJemaat->ayah_id) }}" class="text-xs font-black text-blue-800 uppercase hover:underline">{{ $anggotaJemaat->ayah->nama_lengkap }}</a>
                            @else
                                <span class="text-xs font-bold text-gray-600 uppercase">{{ $anggotaJemaat->nama_ayah ?: 'TIDAK ADA DATA' }}</span>
                            @endif
                        </div>
                        {{-- Ibu --}}
                        <div class="flex-1 text-center p-4 rounded bg-white border border-gray-300 shadow-sm">
                            <p class="text-[9px] uppercase text-gray-500 font-bold tracking-widest mb-2"><i class="fas fa-female mr-1"></i> Ibu</p>
                            @if($anggotaJemaat->ibu_id)
                                <a href="{{ route('admin.anggota-jemaat.show', $anggotaJemaat->ibu_id) }}" class="text-xs font-black text-pink-800 uppercase hover:underline">{{ $anggotaJemaat->ibu->nama_lengkap }}</a>
                            @else
                                <span class="text-xs font-bold text-gray-600 uppercase">{{ $anggotaJemaat->nama_ibu ?: 'TIDAK ADA DATA' }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="border-t-4 border-gray-800 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-[11px] font-black text-gray-800 uppercase tracking-widest"><i class="fas fa-users mr-2 text-gray-400"></i> Register Kartu Keluarga</h4>
                        @if($anggotaJemaat->nomor_kk)
                            <span class="text-[10px] font-mono font-bold bg-gray-100 border border-gray-300 px-3 py-1 rounded shadow-sm">NO. KK: {{ $anggotaJemaat->nomor_kk }}</span>
                        @endif
                    </div>

                    <div class="overflow-x-auto border border-gray-300 rounded shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                                    <th class="px-5 py-3">Nama Anggota Keluarga</th>
                                    <th class="px-5 py-3 text-center">Kedudukan (Status)</th>
                                    <th class="px-5 py-3 text-center">Usia</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-200">
                                @forelse($anggotaKeluargaLain as $keluarga)
                                <tr class="hover:bg-gray-50 transition {{ $keluarga->id == $anggotaJemaat->id ? 'bg-blue-50 border-l-4 border-l-blue-800' : '' }}">
                                    <td class="px-5 py-4 font-bold text-xs uppercase">
                                        <a href="{{ route('admin.anggota-jemaat.show', $keluarga->id) }}" class="hover:text-blue-800 text-gray-900 transition">{{ $keluarga->nama_lengkap }}</a>
                                        @if($keluarga->id == $anggotaJemaat->id) <span class="text-[8px] ml-2 bg-blue-800 text-white px-1.5 py-0.5 rounded uppercase tracking-widest font-medium">Buku Ini</span> @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="text-[9px] font-bold uppercase tracking-widest text-gray-600 bg-gray-100 border border-gray-300 px-2 py-0.5 rounded">{{ $keluarga->status_dalam_keluarga }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-center font-mono font-bold text-gray-700 text-xs">
                                        {{ $keluarga->tanggal_lahir ? \Carbon\Carbon::parse($keluarga->tanggal_lahir)->age . ' THN' : '-' }}
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest italic">Belum ada tautan anggota keluarga lain.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection