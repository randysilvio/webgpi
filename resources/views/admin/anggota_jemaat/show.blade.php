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
                {{-- Logika Foto (Jika ada fitur upload foto) --}}
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
                {{-- Badge Status --}}
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
                        <dt class="text-sm font-medium text-slate-500">Tempat Lahir</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2">{{ $anggotaJemaat->tempat_lahir ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-slate-500">Tanggal Lahir</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2">
                            {{ $anggotaJemaat->tanggal_lahir ? \Carbon\Carbon::parse($anggotaJemaat->tanggal_lahir)->isoFormat('D MMMM Y') : '-' }}
                            <span class="text-slate-400 font-normal ml-1">({{ $anggotaJemaat->tanggal_lahir ? \Carbon\Carbon::parse($anggotaJemaat->tanggal_lahir)->age . ' Tahun' : '' }})</span>
                        </dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-slate-500">Golongan Darah</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2">{{ $anggotaJemaat->golongan_darah ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-slate-500">Status Nikah</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2">{{ $anggotaJemaat->status_pernikahan ?? '-' }}</dd>
                    </div>
                </dl>

                <dl class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-slate-500">Nomor HP</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2">{{ $anggotaJemaat->telepon ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-slate-500">Email</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2">{{ $anggotaJemaat->email ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-slate-500">Pekerjaan</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2">{{ $anggotaJemaat->pekerjaan_utama ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-slate-500">Alamat Domisili</dt>
                        <dd class="text-sm font-bold text-slate-800 col-span-2 leading-relaxed bg-slate-50 p-3 rounded border border-slate-100">
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
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center mr-3"><i class="fas fa-water"></i></div>
                        <h5 class="text-sm font-bold text-blue-900 uppercase">Baptisan Kudus</h5>
                    </div>
                    @if($anggotaJemaat->tanggal_baptis || $anggotaJemaat->dataBaptis)
                        <p class="text-xs text-blue-800 font-bold">SUDAH DIBAPTIS</p>
                        <p class="text-xs text-blue-600 mt-1">Tanggal: {{ $anggotaJemaat->tanggal_baptis ? \Carbon\Carbon::parse($anggotaJemaat->tanggal_baptis)->isoFormat('D MMM Y') : '-' }}</p>
                        <p class="text-xs text-blue-600">Tempat: {{ $anggotaJemaat->tempat_baptis ?? '-' }}</p>
                    @else
                        <p class="text-xs text-slate-400 italic">Belum tercatat dibaptis.</p>
                    @endif
                </div>

                {{-- SIDI --}}
                <div class="bg-purple-50 rounded-lg p-5 border border-purple-100">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 rounded-full bg-purple-200 text-purple-700 flex items-center justify-center mr-3"><i class="fas fa-praying-hands"></i></div>
                        <h5 class="text-sm font-bold text-purple-900 uppercase">Sidi Gereja</h5>
                    </div>
                    @if($anggotaJemaat->tanggal_sidi || $anggotaJemaat->dataSidi)
                        <p class="text-xs text-purple-800 font-bold">SUDAH SIDI</p>
                        <p class="text-xs text-purple-600 mt-1">Tanggal: {{ $anggotaJemaat->tanggal_sidi ? \Carbon\Carbon::parse($anggotaJemaat->tanggal_sidi)->isoFormat('D MMM Y') : '-' }}</p>
                        <p class="text-xs text-purple-600">Tempat: {{ $anggotaJemaat->tempat_sidi ?? '-' }}</p>
                    @else
                        <p class="text-xs text-slate-400 italic">Belum sidi / Masih anggota baptis.</p>
                    @endif
                </div>

                {{-- NIKAH --}}
                <div class="bg-green-50 rounded-lg p-5 border border-green-100">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 rounded-full bg-green-200 text-green-700 flex items-center justify-center mr-3"><i class="fas fa-ring"></i></div>
                        <h5 class="text-sm font-bold text-green-900 uppercase">Pernikahan</h5>
                    </div>
                    @if($anggotaJemaat->status_pernikahan == 'Kawin')
                        <p class="text-xs text-green-800 font-bold">SUDAH MENIKAH</p>
                        <p class="text-xs text-green-600 mt-1">Status: {{ $anggotaJemaat->status_pernikahan }}</p>
                        @if($anggotaJemaat->dataPernikahan)
                            <p class="text-xs text-green-600">Akta: {{ $anggotaJemaat->dataPernikahan->no_akta_nikah }}</p>
                        @endif
                    @else
                        <p class="text-xs text-slate-400 italic">{{ $anggotaJemaat->status_pernikahan ?? 'Belum Menikah' }}</p>
                    @endif
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">Unit Pelayanan</span>
                        <span class="font-bold text-slate-700">{{ $anggotaJemaat->unit_pelayanan ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">Asal Gereja (Mutasi Masuk)</span>
                        <span class="font-bold text-slate-700">{{ $anggotaJemaat->asal_gereja_sebelumnya ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: KELUARGA & SILSILAH --}}
        <div x-show="activeTab === 'keluarga'" style="display: none;" x-transition.opacity>
            
            {{-- BAGIAN ORANG TUA (POHON KELUARGA) --}}
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
                {{-- Garis Konektor --}}
                <div class="h-6 w-px bg-slate-300 my-2"></div>
                <div class="px-6 py-2 bg-slate-800 text-white text-xs font-bold rounded-full uppercase tracking-wider shadow-md">
                    {{ $anggotaJemaat->nama_lengkap }}
                </div>
            </div>

            {{-- BAGIAN KARTU KELUARGA --}}
            <div class="border-t border-slate-200 pt-8">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Anggota dalam satu Kartu Keluarga (KK)</h4>
                    @if($anggotaJemaat->nomor_kk)
                        <span class="text-xs font-mono bg-slate-100 px-2 py-1 rounded">No. KK: <strong>{{ $anggotaJemaat->nomor_kk }}</strong></span>
                    @endif
                </div>

                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nama Anggota</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase">Hubungan</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase">L/P</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase">Usia</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($anggotaKeluargaLain as $keluarga)
                            <tr class="hover:bg-slate-50 transition {{ $keluarga->id == $anggotaJemaat->id ? 'bg-blue-50/50' : '' }}">
                                <td class="px-4 py-3 text-sm font-medium text-slate-800">
                                    <a href="{{ route('admin.anggota-jemaat.show', $keluarga->id) }}" class="hover:text-blue-600">
                                        {{ $keluarga->nama_lengkap }}
                                    </a>
                                    @if($keluarga->id == $anggotaJemaat->id) <span class="text-[10px] text-blue-500 ml-2">(Ini)</span> @endif
                                </td>
                                <td class="px-4 py-3 text-center text-xs text-slate-600">{{ $keluarga->status_dalam_keluarga }}</td>
                                <td class="px-4 py-3 text-center text-xs text-slate-600">{{ $keluarga->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}</td>
                                <td class="px-4 py-3 text-center text-xs text-slate-600">{{ $keluarga->tanggal_lahir ? \Carbon\Carbon::parse($keluarga->tanggal_lahir)->age : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-400 italic">
                                    Tidak ada data keluarga lain yang terhubung dengan Nomor KK ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection