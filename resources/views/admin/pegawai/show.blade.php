@extends('layouts.app')

@section('title', 'Arsip Induk Personel')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ activeTab: 'profil' }">

    {{-- HEADER PROFILE --}}
    <div class="bg-white rounded border border-gray-300 shadow-sm p-6 flex flex-col md:flex-row items-center md:items-start gap-6 mb-6">
        <div class="flex-shrink-0">
            <div class="h-32 w-32 rounded border-4 border-gray-100 overflow-hidden shadow-inner bg-gray-200 flex items-center justify-center">
                @if($pegawai->foto_diri)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($pegawai->foto_diri) }}" class="h-full w-full object-cover">
                @else
                    <i class="fas fa-user text-4xl text-gray-400"></i>
                @endif
            </div>
        </div>
        <div class="flex-grow text-center md:text-left">
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-widest">{{ $pegawai->gelar_depan }} {{ $pegawai->nama_lengkap }} {{ $pegawai->gelar_belakang }}</h1>
            <p class="text-xs font-mono font-bold text-gray-600 mt-1">NIPG: {{ $pegawai->nipg ?? '-' }}</p>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-2 mt-4">
                <span class="px-3 py-1 bg-blue-800 text-white rounded text-[10px] font-bold uppercase tracking-widest">{{ $pegawai->jenis_pegawai }}</span>
                @if($pegawai->status_aktif == 'Aktif')
                    <span class="px-3 py-1 bg-green-100 border border-green-300 text-green-800 rounded text-[10px] font-bold uppercase tracking-widest">Berstatus Aktif</span>
                @else
                    <span class="px-3 py-1 bg-red-100 border border-red-300 text-red-800 rounded text-[10px] font-bold uppercase tracking-widest">{{ $pegawai->status_aktif }}</span>
                @endif
            </div>

            <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-4 text-xs font-bold uppercase text-gray-600">
                <span><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> {{ $pegawai->jemaat->nama_jemaat ?? 'Tanpa Jemaat' }}</span>
                <span><i class="fas fa-map text-gray-400 mr-1"></i> {{ $pegawai->klasis->nama_klasis ?? 'Pusat Sinode' }}</span>
            </div>
        </div>
        <div class="flex-shrink-0 flex gap-2">
            <a href="{{ route('admin.kepegawaian.pegawai.print', $pegawai->id) }}" target="_blank" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase shadow-sm transition">
                <i class="fas fa-print mr-1"></i> Cetak Berkas
            </a>
            <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded text-[10px] font-bold uppercase shadow-sm transition">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    {{-- MENU TAB FORMAL --}}
    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200 bg-gray-50 overflow-x-auto">
            <button @click="activeTab = 'profil'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'profil', 'text-gray-600 hover:text-gray-900': activeTab !== 'profil'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                1. Data Pribadi
            </button>
            <button @click="activeTab = 'sk'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'sk', 'text-gray-600 hover:text-gray-900': activeTab !== 'sk'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                2. Kepangkatan (SK)
            </button>
            <button @click="activeTab = 'pendidikan'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'pendidikan', 'text-gray-600 hover:text-gray-900': activeTab !== 'pendidikan'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                3. Riwayat Studi
            </button>
            <button @click="activeTab = 'keluarga'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'keluarga', 'text-gray-600 hover:text-gray-900': activeTab !== 'keluarga'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                4. Data Keluarga
            </button>
        </div>

        <div class="p-8">
            {{-- TAB 1: PROFIL --}}
            <div x-show="activeTab === 'profil'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Informasi Kelahiran & Kewarganegaraan</h4>
                        <table class="w-full text-sm text-gray-700">
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Tempat, Tanggal Lahir</td><td class="py-2">{{ $pegawai->tempat_lahir ?? '-' }}, {{ $pegawai->tanggal_lahir ? \Carbon\Carbon::parse($pegawai->tanggal_lahir)->format('d M Y') : '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Jenis Kelamin</td><td class="py-2">{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Golongan Darah</td><td class="py-2 font-bold text-red-600">{{ $pegawai->golongan_darah ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Status Sipil</td><td class="py-2">{{ $pegawai->status_pernikahan ?? '-' }}</td></tr>
                            <tr><td class="py-2 font-bold w-1/3 uppercase text-[10px]">NIK KTP Nasional</td><td class="py-2 font-mono text-xs">{{ $pegawai->nik_ktp ?? '-' }}</td></tr>
                        </table>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Informasi Kontak Domisili</h4>
                        <table class="w-full text-sm text-gray-700">
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Alamat Saat Ini</td><td class="py-2">{{ $pegawai->alamat_domisili ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Ponsel / WhatsApp</td><td class="py-2 font-mono text-xs">{{ $pegawai->no_hp ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Surat Elektronik</td><td class="py-2">{{ $pegawai->email ?? '-' }}</td></tr>
                            @if($pegawai->jenis_pegawai == 'Pendeta')
                            <tr><td class="py-2 font-bold w-1/3 uppercase text-[10px] text-blue-800">Tanggal Tahbisan</td><td class="py-2 font-bold">{{ $pegawai->tanggal_tahbisan ? \Carbon\Carbon::parse($pegawai->tanggal_tahbisan)->format('d F Y') : '-' }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAB 2: SK --}}
            <div x-show="activeTab === 'sk'" x-cloak>
                <div class="border-l-2 border-gray-200 pl-6 space-y-6">
                    @forelse($pegawai->riwayatSk ?? [] as $sk)
                    <div class="relative">
                        <div class="absolute -left-[33px] top-1 h-4 w-4 rounded-full bg-blue-800 border-4 border-white"></div>
                        <div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ \Carbon\Carbon::parse($sk->tmt_sk)->format('d M Y') }}</div>
                        <h3 class="text-sm font-black text-gray-800 uppercase">{{ $sk->jenis_sk }}</h3>
                        <p class="text-xs font-mono text-gray-500 mt-1">No Register SK: {{ $sk->nomor_sk }}</p>
                        <div class="mt-3 text-xs bg-gray-50 p-4 rounded border border-gray-200 flex flex-col md:flex-row gap-4">
                            <div><span class="block text-[9px] uppercase font-bold text-gray-400 mb-1">Ditetapkan Sebagai</span> <span class="font-bold text-gray-900">{{ $sk->jabatan_baru ?? '-' }}</span></div>
                            <div><span class="block text-[9px] uppercase font-bold text-gray-400 mb-1">Golongan Ruang</span> <span class="font-bold text-gray-900">{{ $sk->golongan_baru ?? '-' }}</span></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-400 text-xs italic font-bold">Catatan historis kepangkatan belum terekam di Pangkalan Data.</p>
                    @endforelse
                </div>
            </div>

            {{-- TAB 3: PENDIDIKAN --}}
            <div x-show="activeTab === 'pendidikan'" x-cloak>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                                <th class="px-4 py-3">Tingkat Pendidikan</th>
                                <th class="px-4 py-3">Nama Institusi / Universitas</th>
                                <th class="px-4 py-3">Fakultas / Program Studi</th>
                                <th class="px-4 py-3 text-center">Tahun Kelulusan</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-200">
                            @forelse($pegawai->riwayatPendidikan ?? [] as $pend)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-bold text-gray-800">{{ $pend->tingkat }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $pend->nama_institusi }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $pend->jurusan ?? '-' }}</td>
                                <td class="px-4 py-3 text-center font-mono font-bold">{{ $pend->tahun_lulus }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 text-xs italic">Data riwayat pendidikan belum dimasukkan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 4: KELUARGA --}}
            <div x-show="activeTab === 'keluarga'" x-cloak>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                                <th class="px-4 py-3">Nama Anggota Keluarga</th>
                                <th class="px-4 py-3">Hubungan Kekeluargaan</th>
                                <th class="px-4 py-3">Tempat, Tgl Lahir</th>
                                <th class="px-4 py-3">Pekerjaan</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-200">
                            @forelse($pegawai->keluarga ?? [] as $kel)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-bold text-gray-800 uppercase">{{ $kel->nama }}</td>
                                <td class="px-4 py-3">
                                    <span class="bg-blue-50 text-blue-800 border border-blue-200 px-2 py-0.5 rounded text-[10px] font-bold uppercase">{{ $kel->hubungan }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $kel->tempat_lahir }}, {{ \Carbon\Carbon::parse($kel->tanggal_lahir)->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $kel->pekerjaan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 text-xs italic">Data tanggungan keluarga belum dimasukkan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection