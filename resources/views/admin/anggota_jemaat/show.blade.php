@extends('layouts.app')

@section('title', 'Profil Anggota: ' . $anggotaJemaat->nama_lengkap)
@section('header-title', 'Detail Profil Anggota Jemaat')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ activeTab: 'profil' }">

    {{-- 1. HEADER PROFIL (KARTU UTAMA) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row items-center md:items-start gap-6 mb-6">
        
        {{-- Foto Profil --}}
        <div class="flex-shrink-0">
            <div class="h-32 w-32 rounded-full border-4 border-slate-100 overflow-hidden shadow-inner bg-slate-200 flex items-center justify-center">
                @if(isset($anggotaJemaat->foto_profil) && $anggotaJemaat->foto_profil)
                    <img src="{{ Storage::url($anggotaJemaat->foto_profil) }}" class="h-full w-full object-cover">
                @else
                    <span class="text-4xl text-slate-400 font-bold">{{ substr($anggotaJemaat->nama_lengkap, 0, 1) }}</span>
                @endif
            </div>
        </div>

        {{-- Info Utama --}}
        <div class="flex-grow text-center md:text-left">
            <div class="flex flex-col md:flex-row md:items-center gap-2 mb-1">
                <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">{{ $anggotaJemaat->nama_lengkap }}</h1>
                @php
                    $statusClass = match($anggotaJemaat->status_keanggotaan) {
                        'Aktif' => 'bg-green-100 text-green-700 border-green-200',
                        'Meninggal' => 'bg-slate-100 text-slate-600 border-slate-200',
                        'Pindah' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                        default => 'bg-red-100 text-red-700 border-red-200'
                    };
                @endphp
                <span class="inline-block px-3 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $statusClass }}">
                    {{ $anggotaJemaat->status_keanggotaan }}
                </span>
            </div>

            <p class="text-sm font-bold text-blue-600 uppercase tracking-wide mb-3">
                {{ $anggotaJemaat->jemaat->nama_jemaat ?? 'Jemaat Tidak Diketahui' }} 
                <span class="text-slate-300 mx-2">|</span> 
                {{ $anggotaJemaat->sektor_pelayanan ?? 'Sektor -' }}
            </p>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-4 text-xs font-medium text-slate-500">
                <span class="flex items-center"><i class="fas fa-book-open mr-2 text-slate-400"></i> No. Induk: <strong class="ml-1 text-slate-700">{{ $anggotaJemaat->nomor_buku_induk ?? '-' }}</strong></span>
                <span class="flex items-center"><i class="fas fa-id-card mr-2 text-slate-400"></i> NIK: <strong class="ml-1 text-slate-700">{{ $anggotaJemaat->nik ?? '-' }}</strong></span>
                <span class="flex items-center"><i class="fas fa-venus-mars mr-2 text-slate-400"></i> <strong class="ml-1 text-slate-700">{{ $anggotaJemaat->jenis_kelamin }}</strong></span>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex-shrink-0 flex flex-col gap-2">
            <a href="{{ route('admin.anggota-jemaat.edit', $anggotaJemaat->id) }}" class="px-4 py-2 bg-slate-800 text-white text-xs font-bold uppercase rounded hover:bg-slate-900 transition shadow-sm text-center">
                <i class="fas fa-edit mr-1"></i> Edit Data
            </a>
            <a href="{{ route('admin.anggota-jemaat.cetak-kk', $anggotaJemaat->id) }}" target="_blank" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 text-xs font-bold uppercase rounded hover:bg-slate-50 transition shadow-sm text-center">
                <i class="fas fa-print mr-1"></i> Cetak KK
            </a>
        </div>
    </div>

    {{-- 2. NAVIGASI TAB (Simple Underline) --}}
    <div class="border-b border-slate-200 mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button @click="activeTab = 'profil'" 
                :class="activeTab === 'profil' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm uppercase tracking-wide transition">
                Biodata Diri
            </button>
            <button @click="activeTab = 'gerejawi'" 
                :class="activeTab === 'gerejawi' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm uppercase tracking-wide transition">
                Data Gerejawi
            </button>
            <button @click="activeTab = 'renstra'" 
                :class="activeTab === 'renstra' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm uppercase tracking-wide transition">
                Analisis Renstra
            </button>
            <button @click="activeTab = 'keluarga'" 
                :class="activeTab === 'keluarga' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm uppercase tracking-wide transition">
                Keluarga & Silsilah
            </button>
        </nav>
    </div>

    {{-- 3. KONTEN TAB --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 min-h-[400px]">
        
        {{-- TAB 1: BIODATA DIRI --}}
        <div x-show="activeTab === 'profil'" x-transition.opacity>
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Informasi Pribadi & Kontak</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                <dl class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-slate-500">Tempat, Tgl Lahir</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2">
                            {{ $anggotaJemaat->tempat_lahir }}, {{ $anggotaJemaat->tanggal_lahir ? \Carbon\Carbon::parse($anggotaJemaat->tanggal_lahir)->isoFormat('D MMMM Y') : '-' }}
                            <span class="text-slate-400 font-normal ml-1">({{ $anggotaJemaat->tanggal_lahir ? \Carbon\Carbon::parse($anggotaJemaat->tanggal_lahir)->age . ' Tahun' : '' }})</span>
                        </dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-slate-500">Golongan Darah</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2">{{ $anggotaJemaat->golongan_darah ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-slate-500">Disabilitas</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2">{{ $anggotaJemaat->disabilitas ?? 'Tidak Ada' }}</dd>
                    </div>
                </dl>

                <dl class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-slate-500">Nomor HP</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2">{{ $anggotaJemaat->telepon ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-slate-500">Alamat</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2 bg-slate-50 p-2 rounded">
                            {{ $anggotaJemaat->alamat_lengkap ?? '-' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- TAB 2: DATA GEREJAWI --}}
        <div x-show="activeTab === 'gerejawi'" style="display: none;" x-transition.opacity>
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Administrasi & Sakramen</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- BAPTIS --}}
                <div class="bg-blue-50 rounded-lg p-5 border border-blue-100">
                    <h5 class="text-sm font-bold text-blue-900 uppercase mb-2"><i class="fas fa-water mr-2"></i> Baptisan</h5>
                    @if($anggotaJemaat->tanggal_baptis)
                        <p class="text-xs text-blue-800 font-bold">SUDAH DIBAPTIS</p>
                        <p class="text-xs text-blue-600">{{ \Carbon\Carbon::parse($anggotaJemaat->tanggal_baptis)->isoFormat('D MMM Y') }}</p>
                    @else
                        <p class="text-xs text-slate-400 italic">Belum tercatat.</p>
                    @endif
                </div>

                {{-- SIDI --}}
                <div class="bg-purple-50 rounded-lg p-5 border border-purple-100">
                    <h5 class="text-sm font-bold text-purple-900 uppercase mb-2"><i class="fas fa-praying-hands mr-2"></i> Sidi</h5>
                    @if($anggotaJemaat->tanggal_sidi)
                        <p class="text-xs text-purple-800 font-bold">SUDAH SIDI</p>
                        <p class="text-xs text-purple-600">{{ \Carbon\Carbon::parse($anggotaJemaat->tanggal_sidi)->isoFormat('D MMM Y') }}</p>
                    @else
                        <p class="text-xs text-slate-400 italic">Belum sidi.</p>
                    @endif
                </div>

                {{-- NIKAH --}}
                <div class="bg-green-50 rounded-lg p-5 border border-green-100">
                    <h5 class="text-sm font-bold text-green-900 uppercase mb-2"><i class="fas fa-ring mr-2"></i> Nikah</h5>
                    <p class="text-xs text-green-800 font-bold">{{ $anggotaJemaat->status_pernikahan ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- TAB 3: ANALISIS RENSTRA (NEW) --}}
        <div x-show="activeTab === 'renstra'" style="display: none;" x-transition.opacity>
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Indikator Kesejahteraan & Digital</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h5 class="text-sm font-bold text-slate-700 border-b border-slate-100 pb-2 mb-3">Ekonomi & Hunian</h5>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-xs text-slate-500">Pekerjaan Utama</dt>
                            <dd class="text-xs font-bold text-slate-800">{{ $anggotaJemaat->pekerjaan_utama ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs text-slate-500">Status Rumah</dt>
                            <dd class="text-xs font-bold text-slate-800">{{ $anggotaJemaat->status_kepemilikan_rumah ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs text-slate-500">Kondisi Bangunan</dt>
                            <dd class="text-xs font-bold text-slate-800">{{ $anggotaJemaat->kondisi_rumah ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs text-slate-500">Rentang Pengeluaran</dt>
                            <dd class="text-xs font-bold text-slate-800">{{ $anggotaJemaat->rentang_pengeluaran ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h5 class="text-sm font-bold text-slate-700 border-b border-slate-100 pb-2 mb-3">Aset & Digitalisasi</h5>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-xs text-slate-500">Kepemilikan Smartphone</dt>
                            <dd class="text-xs font-bold {{ $anggotaJemaat->punya_smartphone ? 'text-green-600' : 'text-red-500' }}">
                                {{ $anggotaJemaat->punya_smartphone ? 'Ya, Memiliki' : 'Tidak Memiliki' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs text-slate-500">Akses Internet</dt>
                            <dd class="text-xs font-bold {{ $anggotaJemaat->akses_internet ? 'text-green-600' : 'text-red-500' }}">
                                {{ $anggotaJemaat->akses_internet ? 'Tersedia' : 'Tidak Tersedia' }}
                            </dd>
                        </div>
                        <div class="mt-4">
                            <dt class="text-xs text-slate-500 mb-1">Potensi Ekonomi (Aset)</dt>
                            <dd class="text-xs font-bold text-blue-600 bg-blue-50 p-2 rounded">
                                {{ $anggotaJemaat->aset_ekonomi ?? 'Tidak ada data aset' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- TAB 4: KELUARGA & SILSILAH --}}
        <div x-show="activeTab === 'keluarga'" style="display: none;" x-transition.opacity>
            <div class="flex flex-col items-center mb-10">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Orang Tua Biologis</h4>
                <div class="flex gap-4 md:gap-10 w-full max-w-lg justify-center">
                    {{-- Ayah --}}
                    <div class="flex-1 text-center p-3 rounded-lg border {{ $anggotaJemaat->ayah_id ? 'bg-blue-50 border-blue-200' : 'bg-slate-50 border-slate-200' }}">
                        <p class="text-[10px] uppercase text-slate-400 font-bold mb-1">Ayah</p>
                        @if($anggotaJemaat->ayah_id)
                            <a href="{{ route('admin.anggota-jemaat.show', $anggotaJemaat->ayah_id) }}" class="text-sm font-bold text-blue-700 hover:underline">{{ $anggotaJemaat->ayah->nama_lengkap }}</a>
                        @else
                            <span class="text-sm font-bold text-slate-500 italic">{{ $anggotaJemaat->nama_ayah ?: 'Tidak Ada Data' }}</span>
                        @endif
                    </div>
                    {{-- Ibu --}}
                    <div class="flex-1 text-center p-3 rounded-lg border {{ $anggotaJemaat->ibu_id ? 'bg-pink-50 border-pink-200' : 'bg-slate-50 border-slate-200' }}">
                        <p class="text-[10px] uppercase text-slate-400 font-bold mb-1">Ibu</p>
                        @if($anggotaJemaat->ibu_id)
                            <a href="{{ route('admin.anggota-jemaat.show', $anggotaJemaat->ibu_id) }}" class="text-sm font-bold text-pink-700 hover:underline">{{ $anggotaJemaat->ibu->nama_lengkap }}</a>
                        @else
                            <span class="text-sm font-bold text-slate-500 italic">{{ $anggotaJemaat->nama_ibu ?: 'Tidak Ada Data' }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200 pt-8">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Kartu Keluarga (KK)</h4>
                    @if($anggotaJemaat->nomor_kk)
                        <span class="text-xs font-mono bg-slate-100 px-2 py-1 rounded">No. KK: <strong>{{ $anggotaJemaat->nomor_kk }}</strong></span>
                    @endif
                </div>

                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase">Hubungan</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase">Usia</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($anggotaKeluargaLain as $keluarga)
                            <tr class="hover:bg-slate-50 transition {{ $keluarga->id == $anggotaJemaat->id ? 'bg-blue-50/50' : '' }}">
                                <td class="px-4 py-3 text-sm font-medium text-slate-800">
                                    <a href="{{ route('admin.anggota-jemaat.show', $keluarga->id) }}" class="hover:text-blue-600">{{ $keluarga->nama_lengkap }}</a>
                                </td>
                                <td class="px-4 py-3 text-center text-xs text-slate-600">{{ $keluarga->status_dalam_keluarga }}</td>
                                <td class="px-4 py-3 text-center text-xs text-slate-600">{{ $keluarga->tanggal_lahir ? \Carbon\Carbon::parse($keluarga->tanggal_lahir)->age : '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-sm text-slate-400 italic">Tidak ada data keluarga lain.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection