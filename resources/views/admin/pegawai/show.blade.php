@extends('layouts.app')

@section('title', 'Detail Pegawai')
@section('header-title', 'Profil Personel')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ activeTab: 'profil' }">

    {{-- HEADER PROFILE --}}
    <div class="bg-white rounded shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row items-center md:items-start gap-6 mb-6">
        <div class="flex-shrink-0">
            <div class="h-32 w-32 rounded-full border-4 border-slate-100 overflow-hidden shadow-inner bg-slate-200 flex items-center justify-center">
                @if($pegawai->foto_profil)
                    <img src="{{ asset('storage/'.$pegawai->foto_profil) }}" class="h-full w-full object-cover">
                @else
                    <i class="fas fa-user text-4xl text-slate-400"></i>
                @endif
            </div>
        </div>
        <div class="flex-grow text-center md:text-left">
            <h1 class="text-2xl font-bold text-slate-800">{{ $pegawai->nama_lengkap }}</h1>
            <p class="text-slate-500 font-medium">{{ $pegawai->nip ?? 'NIP Belum Diisi' }}</p>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-2 mt-3">
                <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded text-xs font-bold uppercase border border-slate-200">
                    {{ $pegawai->jenis_pegawai }}
                </span>
                @if($pegawai->status_aktif == 'Aktif')
                    <span class="px-3 py-1 bg-green-50 text-green-700 rounded text-xs font-bold uppercase border border-green-100">Aktif</span>
                @else
                    <span class="px-3 py-1 bg-red-50 text-red-700 rounded text-xs font-bold uppercase border border-red-100">{{ $pegawai->status_aktif }}</span>
                @endif
            </div>

            <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-4 text-sm text-slate-600">
                <span><i class="fas fa-map-marker-alt mr-1 text-slate-400"></i> {{ $pegawai->jemaat->nama_jemaat ?? $pegawai->klasis->nama_klasis ?? 'Kantor Sinode' }}</span>
                <span><i class="fas fa-briefcase mr-1 text-slate-400"></i> {{ $pegawai->jabatan_terakhir ?? 'Jabatan -' }}</span>
            </div>
        </div>
        <div class="flex-shrink-0">
            <a href="{{ route('admin.kepegawaian.pegawai.print', $pegawai->id) }}" target="_blank" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded text-sm font-medium hover:bg-slate-50 transition shadow-sm">
                <i class="fas fa-print mr-2"></i> Cetak Biodata
            </a>
        </div>
    </div>

    {{-- TABS NAVIGATION --}}
    <div class="border-b border-slate-200 mb-6">
        <nav class="flex space-x-8">
            <button @click="activeTab = 'profil'" 
                :class="activeTab === 'profil' ? 'border-slate-800 text-slate-800' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap pb-4 px-1 border-b-2 font-bold text-sm transition">
                Data Diri
            </button>
            <button @click="activeTab = 'sk'" 
                :class="activeTab === 'sk' ? 'border-slate-800 text-slate-800' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap pb-4 px-1 border-b-2 font-bold text-sm transition">
                Riwayat SK & Jabatan
            </button>
            <button @click="activeTab = 'pendidikan'" 
                :class="activeTab === 'pendidikan' ? 'border-slate-800 text-slate-800' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap pb-4 px-1 border-b-2 font-bold text-sm transition">
                Pendidikan
            </button>
            <button @click="activeTab = 'keluarga'" 
                :class="activeTab === 'keluarga' ? 'border-slate-800 text-slate-800' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap pb-4 px-1 border-b-2 font-bold text-sm transition">
                Keluarga
            </button>
        </nav>
    </div>

    {{-- CONTENT TABS --}}
    <div class="bg-white rounded shadow-sm border border-slate-200 p-6 min-h-[300px]">
        
        {{-- 1. TAB PROFIL --}}
        <div x-show="activeTab === 'profil'" class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
            <div>
                <h4 class="text-xs font-bold uppercase text-slate-400 mb-3 tracking-wider border-b border-slate-100 pb-1">Info Pribadi</h4>
                <dl class="space-y-3 text-sm">
                    <div class="grid grid-cols-3">
                        <dt class="font-medium text-slate-600">NIK</dt>
                        <dd class="col-span-2 text-slate-800">{{ $pegawai->nik ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3">
                        <dt class="font-medium text-slate-600">Tempat Lahir</dt>
                        <dd class="col-span-2 text-slate-800">{{ $pegawai->tempat_lahir ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3">
                        <dt class="font-medium text-slate-600">Tanggal Lahir</dt>
                        <dd class="col-span-2 text-slate-800">{{ $pegawai->tanggal_lahir ? \Carbon\Carbon::parse($pegawai->tanggal_lahir)->format('d F Y') : '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3">
                        <dt class="font-medium text-slate-600">Gender</dt>
                        <dd class="col-span-2 text-slate-800">{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                    </div>
                </dl>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase text-slate-400 mb-3 tracking-wider border-b border-slate-100 pb-1">Kontak & Domisili</h4>
                <dl class="space-y-3 text-sm">
                    <div class="grid grid-cols-3">
                        <dt class="font-medium text-slate-600">Alamat</dt>
                        <dd class="col-span-2 text-slate-800">{{ $pegawai->alamat ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3">
                        <dt class="font-medium text-slate-600">No. HP</dt>
                        <dd class="col-span-2 text-slate-800">{{ $pegawai->no_hp ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3">
                        <dt class="font-medium text-slate-600">Email</dt>
                        <dd class="col-span-2 text-slate-800">{{ $pegawai->email ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- 2. TAB SK (Riwayat Jabatan) --}}
        <div x-show="activeTab === 'sk'" style="display: none;">
            {{-- Tombol Tambah --}}
            <div class="flex justify-end mb-4">
                <button onclick="document.getElementById('modal-sk').classList.remove('hidden')" class="text-xs font-bold text-slate-600 hover:text-slate-900 uppercase">
                    + Tambah Riwayat SK
                </button>
            </div>

            {{-- Timeline SK --}}
            <div class="border-l-2 border-slate-200 ml-3 space-y-8">
                @forelse($pegawai->riwayatSk as $sk)
                <div class="relative pl-8">
                    <div class="absolute -left-[9px] top-0 h-4 w-4 rounded-full bg-slate-300 border-2 border-white"></div>
                    <div class="mb-1 text-xs font-bold uppercase text-slate-500">{{ \Carbon\Carbon::parse($sk->tmt_sk)->format('d M Y') }}</div>
                    <h3 class="text-lg font-bold text-slate-800">{{ $sk->jenis_sk }}</h3>
                    <p class="text-sm text-slate-600">No SK: {{ $sk->nomor_sk }}</p>
                    <div class="mt-2 text-sm bg-slate-50 p-3 rounded border border-slate-100">
                        <span class="block"><strong>Jabatan:</strong> {{ $sk->jabatan_baru ?? '-' }}</span>
                        <span class="block"><strong>Golongan:</strong> {{ $sk->golongan_baru ?? '-' }}</span>
                    </div>
                </div>
                @empty
                <p class="text-slate-400 italic text-sm pl-8">Belum ada data riwayat SK.</p>
                @endforelse
            </div>
        </div>

        {{-- 3. TAB PENDIDIKAN --}}
        <div x-show="activeTab === 'pendidikan'" style="display: none;">
            {{-- Isi sama dengan logika sebelumnya, sesuaikan style --}}
            <p class="text-slate-400 italic text-sm">Daftar riwayat pendidikan...</p>
        </div>

        {{-- 4. TAB KELUARGA --}}
        <div x-show="activeTab === 'keluarga'" style="display: none;">
            {{-- Isi sama dengan logika sebelumnya, sesuaikan style --}}
            <p class="text-slate-400 italic text-sm">Data suami/istri dan anak...</p>
        </div>

    </div>
</div>
@endsection