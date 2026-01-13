@extends('admin.layout')

@section('title', 'Profil Anggota: ' . $anggotaJemaat->nama_lengkap)
@section('header-title', 'Detail Profil Anggota Jemaat')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- HEADER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 flex items-center px-8">
            <div class="flex items-center space-x-4 text-white/80 text-xs font-black uppercase tracking-widest">
                <i class="fas fa-church text-xl text-yellow-400"></i>
                <span>GPI Papua • {{ $anggotaJemaat->jemaat->klasis->nama_klasis ?? 'Klasis -' }}</span>
            </div>
        </div>
        <div class="px-8 pb-8">
            <div class="relative flex flex-col md:flex-row items-center md:items-end -mt-16 mb-6">
                <div class="h-40 w-40 bg-white p-2 rounded-2xl shadow-xl">
                    <div class="h-full w-full bg-gray-100 rounded-xl flex items-center justify-center text-gray-300">
                        @if($anggotaJemaat->jenis_kelamin == 'Perempuan')
                             <i class="fas fa-female fa-6x text-pink-300"></i>
                        @else
                             <i class="fas fa-user-circle fa-6x"></i>
                        @endif
                    </div>
                </div>
                <div class="mt-4 md:mt-0 md:ml-8 flex-1 text-center md:text-left pb-2">
                    <div class="flex flex-col md:flex-row md:items-center gap-2 mb-1">
                        <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tight">{{ $anggotaJemaat->nama_lengkap }}</h1>
                        <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $anggotaJemaat->status_keanggotaan == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $anggotaJemaat->status_keanggotaan }}
                        </span>
                    </div>
                    <p class="text-sm font-bold text-primary uppercase tracking-widest mb-2">
                        {{ $anggotaJemaat->jemaat->nama_jemaat }} • {{ $anggotaJemaat->sektor_pelayanan ?? 'Sektor -' }} • Unit {{ $anggotaJemaat->unit_pelayanan ?? '-' }}
                    </p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-4 text-[11px] font-bold text-gray-500 uppercase">
                        <span><i class="fas fa-book-open mr-1"></i> No. Induk: {{ $anggotaJemaat->nomor_buku_induk ?? '-' }}</span>
                        <span><i class="fas fa-id-card mr-1"></i> NIK: {{ $anggotaJemaat->nik ?? '-' }}</span>
                    </div>
                </div>
                <div class="mt-6 md:mt-0 flex gap-2">
                    <a href="{{ route('admin.anggota-jemaat.edit', $anggotaJemaat->id) }}" class="bg-primary hover:bg-blue-800 text-white px-6 py-3 rounded-xl text-xs font-black uppercase transition shadow-lg flex items-center">
                        <i class="fas fa-edit mr-2"></i> Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- VISUALISASI POHON KELUARGA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center justify-between mb-10 border-b pb-4">
            <div class="flex items-center">
                <i class="fas fa-sitemap mr-3 text-blue-600 text-xl"></i>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Silsilah & Pohon Keluarga</h3>
            </div>
        </div>

        <div class="flex flex-col items-center">
            {{-- LEVEL 1: ORANG TUA --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 mb-12 relative w-full max-w-2xl">
                {{-- Ayah --}}
                <div class="flex flex-col items-center">
                    <span class="text-[9px] font-black text-blue-400 uppercase mb-2 tracking-widest">Ayah Biologis</span>
                    <div class="p-4 border-2 {{ $anggotaJemaat->ayah_id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50' }} rounded-2xl w-full text-center shadow-sm transition hover:shadow-md">
                        @if($anggotaJemaat->ayah_id)
                            <a href="{{ route('admin.anggota-jemaat.show', $anggotaJemaat->ayah_id) }}" class="text-sm font-black text-blue-800 hover:text-blue-600 uppercase">{{ $anggotaJemaat->ayah->nama_lengkap }}</a>
                        @else
                            <span class="text-sm font-bold text-gray-400 italic uppercase">{{ $anggotaJemaat->nama_ayah ?: 'Data Tidak Tersedia' }}</span>
                        @endif
                    </div>
                </div>
                {{-- Ibu --}}
                <div class="flex flex-col items-center">
                    <span class="text-[9px] font-black text-pink-400 uppercase mb-2 tracking-widest">Ibu Biologis</span>
                    <div class="p-4 border-2 {{ $anggotaJemaat->ibu_id ? 'border-pink-500 bg-pink-50' : 'border-gray-200 bg-gray-50' }} rounded-2xl w-full text-center shadow-sm transition hover:shadow-md">
                        @if($anggotaJemaat->ibu_id)
                            <a href="{{ route('admin.anggota-jemaat.show', $anggotaJemaat->ibu_id) }}" class="text-sm font-black text-pink-800 hover:text-pink-600 uppercase">{{ $anggotaJemaat->ibu->nama_lengkap }}</a>
                        @else
                            <span class="text-sm font-bold text-gray-400 italic uppercase">{{ $anggotaJemaat->nama_ibu ?: 'Data Tidak Tersedia' }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- LEVEL 2: DIRINYA --}}
            <div class="mb-16 relative">
                <div class="p-6 border-4 border-primary bg-primary rounded-3xl w-72 text-center shadow-2xl transform scale-110">
                    <span class="block text-[10px] font-black text-blue-200 uppercase tracking-widest mb-1">Anggota Ini</span>
                    <span class="text-lg font-black text-white uppercase leading-tight">{{ $anggotaJemaat->nama_lengkap }}</span>
                </div>
            </div>

            {{-- LEVEL 3: ANAK-ANAK --}}
            @if($anggotaJemaat->anak->count() > 0)
            <div class="w-full pt-8 border-t-2 border-dashed border-gray-100">
                <h4 class="text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-8">Keturunan / Anak-anak</h4>
                <div class="flex gap-6 flex-wrap justify-center">
                    @foreach($anggotaJemaat->anak as $anak)
                    <div class="flex flex-col items-center group">
                        <div class="p-4 border-2 border-green-500 bg-green-50 rounded-2xl w-44 text-center shadow-sm transition group-hover:-translate-y-1">
                            <a href="{{ route('admin.anggota-jemaat.show', $anak->id) }}" class="text-xs font-black text-green-800 hover:text-green-600 uppercase">{{ $anak->nama_lengkap }}</a>
                            <span class="block text-[8px] font-bold text-green-600 uppercase mt-1 italic">{{ \Carbon\Carbon::parse($anak->tanggal_lahir)->age }} Tahun</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- KOLOM KIRI --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- DATA PRIBADI --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8 border-l-4 border-primary pl-4">Informasi Biodata Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                    <div class="group">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Tempat, Tanggal Lahir</p>
                        <p class="font-bold text-gray-800 uppercase">{{ $anggotaJemaat->tempat_lahir ?? '-' }}, {{ $anggotaJemaat->tanggal_lahir ? $anggotaJemaat->tanggal_lahir->isoFormat('D MMMM Y') : '-' }}</p>
                    </div>
                    <div class="group">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Status Pernikahan</p>
                        <p class="font-bold text-gray-800 uppercase italic">
                            @if($anggotaJemaat->status_pernikahan == 'Kawin')
                                <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i> Kawin</span>
                            @else
                                {{ $anggotaJemaat->status_pernikahan ?? '-' }}
                            @endif
                        </p>
                    </div>
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-2">Alamat Lengkap Domisili</p>
                        <p class="text-sm font-bold text-gray-700 leading-relaxed italic">"{{ $anggotaJemaat->alamat_lengkap ?? 'Alamat belum diinput ke dalam sistem' }}"</p>
                    </div>
                </div>
            </div>

            {{-- ADMINISTRASI GEREJAWI --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8 border-l-4 border-yellow-500 pl-4">Administrasi Gerejawi & Pelayanan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                    <div class="space-y-4">
                        {{-- DATA BAPTIS --}}
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Sakramen Baptis</span>
                            <span class="font-black text-gray-800 uppercase text-right">
                                @if($anggotaJemaat->dataBaptis)
                                    {{ \Carbon\Carbon::parse($anggotaJemaat->dataBaptis->tanggal_baptis)->isoFormat('D MMM Y') }}<br>
                                    <small class="text-blue-600 italic font-medium">Akta: {{ $anggotaJemaat->dataBaptis->no_akta_baptis }}</small>
                                @elseif($anggotaJemaat->tanggal_baptis)
                                    {{ $anggotaJemaat->tanggal_baptis->isoFormat('D MMM Y') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        
                        {{-- DATA SIDI --}}
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Sakramen Sidi</span>
                            <span class="font-black text-gray-800 uppercase text-right">
                                @if($anggotaJemaat->dataSidi)
                                    {{ \Carbon\Carbon::parse($anggotaJemaat->dataSidi->tanggal_sidi)->isoFormat('D MMM Y') }}<br>
                                    <small class="text-blue-600 italic font-medium">Akta: {{ $anggotaJemaat->dataSidi->no_akta_sidi }}</small>
                                @elseif($anggotaJemaat->tanggal_sidi)
                                    {{ $anggotaJemaat->tanggal_sidi->isoFormat('D MMM Y') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        
                        {{-- DATA NIKAH --}}
                        <div class="flex justify-between border-b pb-2 bg-green-50/50 p-2 rounded">
                            <span class="text-green-700 font-bold uppercase text-[10px]">Sakramen Nikah</span>
                            <span class="font-black text-gray-800 uppercase text-right">
                                @php $nikah = $anggotaJemaat->dataPernikahan; @endphp
                                @if($nikah)
                                    {{ \Carbon\Carbon::parse($nikah->tanggal_nikah)->isoFormat('D MMM Y') }}<br>
                                    <small class="text-gray-500 italic font-medium">Akta: {{ $nikah->no_akta_nikah }}</small>
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Wadah Kategorial</span>
                            <span class="font-black text-primary uppercase">{{ $anggotaJemaat->wadah_kategorial ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Masuk Jemaat</span>
                            <span class="font-black text-gray-800 uppercase">{{ $anggotaJemaat->tanggal_masuk_jemaat ? $anggotaJemaat->tanggal_masuk_jemaat->isoFormat('D MMM Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- DATA STATISTIK --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8 border-l-4 border-green-500 pl-4">Statistik Ekonomi & Fasilitas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                    <div class="space-y-4">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Pekerjaan Utama</span>
                            <span class="font-black text-gray-800 uppercase">{{ $anggotaJemaat->pekerjaan_utama ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: KK --}}
        <div class="space-y-6">
            {{-- KARTU KELUARGA --}}
            <div class="bg-gray-800 rounded-2xl shadow-xl p-6 text-white relative overflow-hidden">
                {{-- Hiasan Background --}}
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>

                <div class="flex items-center justify-between mb-6 relative z-10">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Struktur Keluarga (KK)</h3>
                    <div class="flex gap-2">
                        {{-- Tombol Cetak Mini (Icon) --}}
                        <a href="{{ route('admin.anggota-jemaat.cetak-kk', $anggotaJemaat->id) }}" target="_blank" class="text-gray-400 hover:text-yellow-400 transition" title="Cetak Kartu Keluarga">
                            <i class="fas fa-print"></i>
                        </a>
                        <i class="fas fa-users text-gray-600"></i>
                    </div>
                </div>
                
                <div class="mb-6 p-4 bg-white/5 rounded-xl border border-white/10 relative z-10">
                    @if($anggotaJemaat->nomor_kk)
                        <p class="text-[9px] text-gray-500 font-bold uppercase">No. Kartu Keluarga (Sipil)</p>
                        <p class="text-lg font-black tracking-tight text-yellow-400">{{ $anggotaJemaat->nomor_kk }}</p>
                    @elseif($anggotaJemaat->kode_keluarga_internal)
                         <p class="text-[9px] text-green-400/80 font-bold uppercase">KK Gereja (Sementara)</p>
                         <p class="text-sm font-black tracking-tight text-green-400 mt-1">{{ $anggotaJemaat->kode_keluarga_internal }}</p>
                    @else
                        <p class="text-[9px] text-gray-500 font-bold uppercase">No. Kartu Keluarga</p>
                        <p class="text-lg font-black tracking-tight text-gray-600">BELUM ADA KK</p>
                    @endif
                    
                    <p class="text-[9px] text-white/40 font-bold uppercase mt-2 border-t border-white/10 pt-2">
                        Kepala Keluarga: <br>
                        <span class="text-white">{{ $anggotaJemaat->nama_kepala_keluarga ?? '-' }}</span>
                    </p>
                </div>
                
                <div class="space-y-3 mb-6">
                    @forelse($anggotaKeluargaLain as $kel)
                    <div class="flex items-center justify-between p-3 bg-white/5 hover:bg-white/10 rounded-xl transition group">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-gray-700 flex items-center justify-center text-[10px] font-black group-hover:bg-primary transition">
                                {{ substr($kel->nama_lengkap, 0, 1) }}
                            </div>
                            <div>
                                <a href="{{ route('admin.anggota-jemaat.show', $kel->id) }}" class="block text-[11px] font-black hover:text-blue-400 transition leading-tight uppercase">{{ $kel->nama_lengkap }}</a>
                                <span class="text-[8px] font-bold text-gray-500 uppercase tracking-tighter">{{ $kel->status_dalam_keluarga }}</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] text-gray-700 group-hover:text-white transition"></i>
                    </div>
                    @empty
                    <div class="text-center py-4 opacity-50">
                        <i class="fas fa-user-friends fa-2x mb-2"></i>
                        <p class="text-[10px] italic">Satu orang dalam KK.</p>
                    </div>
                    @endforelse
                </div>

                {{-- TOMBOL CETAK BESAR --}}
                <a href="{{ route('admin.anggota-jemaat.cetak-kk', $anggotaJemaat->id) }}" target="_blank" class="flex items-center justify-center w-full py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 text-gray-900 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg transform transition hover:-translate-y-1">
                    <i class="fas fa-print mr-2"></i> Cetak Dokumen KK
                </a>
            </div>
        </div>
    </div>
</div>
@endsection