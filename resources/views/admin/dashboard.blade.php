@extends('admin.layout')

@section('title', 'Dashboard')
@section('header-title', 'Dashboard Utama')

@section('content')
    {{-- Header Sambutan --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h2>
            <p class="text-gray-600 mt-1">
                Anda login sebagai:
                @forelse (Auth::user()->getRoleNames() as $role)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                        {{ $role }}
                    </span>
                @empty
                    <span class="text-gray-500 italic">Guest</span>
                @endforelse
            </p>
            @if (Auth::user()->klasisTugas)
                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-map-marker-alt mr-1"></i> {{ Auth::user()->klasisTugas->nama_klasis }}</p>
            @endif
            @if (Auth::user()->jemaatTugas)
                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-church mr-1"></i> {{ Auth::user()->jemaatTugas->nama_jemaat }}</p>
            @endif
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <span class="text-sm text-gray-500 bg-white px-3 py-1 rounded shadow-sm border border-gray-200">
                <i class="far fa-calendar-alt mr-1"></i> {{ now()->isoFormat('dddd, D MMMM Y') }}
            </span>
        </div>
    </div>

    {{-- GRID STATISTIK UTAMA --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 flex items-center justify-between transition hover:shadow-md">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Anggota</p>
                <p class="text-2xl font-extrabold text-gray-900 mt-1">
                    {{ number_format($stats['anggota'] ?? 0) }}
                </p>
                <p class="text-xs text-blue-600 mt-1 font-medium">Jiwa</p>
            </div>
            <div class="p-3 rounded-full bg-blue-50 text-blue-600">
                <i class="fas fa-users fa-2x"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 flex items-center justify-between transition hover:shadow-md">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Jemaat</p>
                <p class="text-2xl font-extrabold text-gray-900 mt-1">
                    {{ number_format($stats['jemaat'] ?? 0) }}
                </p>
                <p class="text-xs text-green-600 mt-1 font-medium">Gereja</p>
            </div>
            <div class="p-3 rounded-full bg-green-50 text-green-600">
                <i class="fas fa-church fa-2x"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500 flex items-center justify-between transition hover:shadow-md">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pegawai</p>
                <p class="text-2xl font-extrabold text-gray-900 mt-1">
                    {{ number_format($stats['pendeta'] ?? 0) }}
                </p>
                <p class="text-xs text-orange-600 mt-1 font-medium">Aktif Melayani</p>
            </div>
            <div class="p-3 rounded-full bg-orange-50 text-orange-600">
                <i class="fas fa-user-tie fa-2x"></i>
            </div>
        </div>

        @if(auth()->user()->hasRole(['Super Admin', 'Admin Sinode']))
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500 flex items-center justify-between transition hover:shadow-md">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Klasis</p>
                <p class="text-2xl font-extrabold text-gray-900 mt-1">
                    {{ number_format($stats['klasis'] ?? 0) }}
                </p>
                <p class="text-xs text-purple-600 mt-1 font-medium">Wilayah Pelayanan</p>
            </div>
            <div class="p-3 rounded-full bg-purple-50 text-purple-600">
                <i class="fas fa-map-marked-alt fa-2x"></i>
            </div>
        </div>
        @else
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-gray-500 flex items-center justify-between transition hover:shadow-md">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Wilayah Tugas</p>
                <p class="text-lg font-bold text-gray-900 mt-1 truncate w-32" title="{{ Auth::user()->klasisTugas->nama_klasis ?? (Auth::user()->jemaatTugas->nama_jemaat ?? 'Sinode') }}">
                    {{ Auth::user()->klasisTugas->nama_klasis ?? (Auth::user()->jemaatTugas->nama_jemaat ?? 'Sinode') }}
                </p>
                <p class="text-xs text-gray-500 mt-1 font-medium">Status Aktif</p>
            </div>
            <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                <i class="fas fa-id-badge fa-2x"></i>
            </div>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        {{-- KOLOM KIRI: KEUANGAN --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-wallet text-gray-400 mr-2"></i> Keuangan Wadah ({{ date('Y') }})
                    </h3>
                    <a href="{{ route('admin.wadah.anggaran.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Detail &rarr;</a>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 rounded-lg p-4 text-center border border-blue-100">
                        <p class="text-xs text-blue-500 font-bold uppercase mb-1">Target Anggaran</p>
                        <p class="text-xl font-extrabold text-blue-700">Rp {{ number_format($stats['keuangan_target'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center border border-green-100">
                        <p class="text-xs text-green-500 font-bold uppercase mb-1">Realisasi (Masuk)</p>
                        <p class="text-xl font-extrabold text-green-700">Rp {{ number_format($stats['keuangan_realisasi'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 text-center border border-purple-100 flex flex-col justify-center">
                        <p class="text-xs text-purple-500 font-bold uppercase mb-1">Capaian</p>
                        @php
                            $persen = ($stats['keuangan_target'] > 0) ? ($stats['keuangan_realisasi'] / $stats['keuangan_target'] * 100) : 0;
                        @endphp
                        <div class="flex items-center justify-center">
                            <span class="text-2xl font-extrabold text-purple-700 mr-2">{{ round($persen, 1) }}%</span>
                            @if($persen >= 100) <i class="fas fa-check-circle text-green-500"></i> @endif
                        </div>
                        <div class="w-full bg-purple-200 rounded-full h-1.5 mt-2">
                            <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ min($persen, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: WIDGETS --}}
        <div class="space-y-6">
            
            {{-- WIDGET: PERINGATAN PENSIUN (Hanya muncul jika ada data) --}}
            @if(isset($pensiunAkanDatang) && $pensiunAkanDatang->isNotEmpty())
            <div class="bg-red-50 rounded-xl shadow-sm p-5 border border-red-100">
                <h3 class="text-md font-bold text-red-800 mb-3 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Persiapan Pensiun (1 Tahun)
                </h3>
                <ul class="space-y-3">
                    @foreach($pensiunAkanDatang as $p)
                        <li class="flex justify-between items-start bg-white p-2 rounded border border-red-100 shadow-sm">
                            <div>
                                <a href="{{ route('admin.kepegawaian.pegawai.show', $p->id) }}" class="text-sm font-bold text-gray-800 hover:text-blue-600 block">
                                    {{ $p->nama_lengkap }}
                                </a>
                                <span class="text-xs text-gray-500">{{ $p->jenis_pegawai }} - {{ $p->klasis->nama_klasis ?? 'Sinode' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-red-600 block">
                                    {{ \Carbon\Carbon::parse($p->tanggal_pensiun)->diffForHumans() }}
                                </span>
                                <span class="text-[10px] text-gray-400">Tgl: {{ \Carbon\Carbon::parse($p->tanggal_pensiun)->format('d/m/Y') }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-3 text-center border-t border-red-100 pt-2">
                    <a href="{{ route('admin.kepegawaian.pegawai.index', ['status' => 'Aktif']) }}" class="text-xs text-red-700 hover:text-red-900 font-medium">Lihat Semua Pegawai &rarr;</a>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i> Aksi Cepat
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <a href="{{ route('admin.anggota-jemaat.create') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-blue-300 transition group">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-4 group-hover:bg-blue-600 group-hover:text-white transition">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Tambah Anggota</p>
                            <p class="text-xs text-gray-500">Input data jemaat baru</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.wadah.anggaran.index') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition group">
                        <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-4 group-hover:bg-green-600 group-hover:text-white transition">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Catat Transaksi</p>
                            <p class="text-xs text-gray-500">Keuangan Wadah</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.wadah.program.create') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-purple-300 transition group">
                        <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-4 group-hover:bg-purple-600 group-hover:text-white transition">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Buat Program Kerja</p>
                            <p class="text-xs text-gray-500">Rencanakan kegiatan baru</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- INFO SISTEM --}}
    <div class="mt-8 bg-blue-900 rounded-xl shadow-lg p-6 text-white flex flex-col md:flex-row items-center justify-between">
        <div class="mb-4 md:mb-0">
            <h4 class="text-lg font-bold"><i class="fas fa-info-circle mr-2"></i> Sistem Informasi Manajemen GPI Papua</h4>
            <p class="text-blue-200 text-sm mt-1">Versi 1.3.0 | Modul Kepegawaian (HRIS) & Wadah Kategorial Aktif</p>
        </div>
        <div class="flex space-x-4">
            <a href="https://gpipapua.org" target="_blank" class="px-4 py-2 bg-blue-800 hover:bg-blue-700 rounded-lg text-sm font-medium transition">
                Kunjungi Website
            </a>
            <a href="#" class="px-4 py-2 bg-white text-blue-900 hover:bg-gray-100 rounded-lg text-sm font-medium transition">
                Bantuan Teknis
            </a>
        </div>
    </div>

@endsection