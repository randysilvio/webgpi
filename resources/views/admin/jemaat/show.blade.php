@extends('layouts.app')

@section('title', 'Tinjauan Profil Jemaat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ activeTab: 'profil' }">

    {{-- KOP HEADER --}}
    <div class="bg-white border border-gray-300 rounded shadow-sm p-6 flex flex-col md:flex-row items-center md:items-start gap-6 border-l-8 border-l-blue-800">
        <div class="h-28 w-28 rounded border border-gray-300 flex items-center justify-center bg-gray-100 overflow-hidden shrink-0 shadow-inner">
            @if($jemaat->foto_gereja_path)
                <img src="{{ Storage::url($jemaat->foto_gereja_path) }}" class="h-full w-full object-cover">
            @else
                <i class="fas fa-church text-4xl text-gray-400"></i>
            @endif
        </div>
        <div class="flex-1 text-center md:text-left">
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-widest leading-tight mb-1">{{ $jemaat->nama_jemaat }}</h1>
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 text-xs font-bold text-gray-600 uppercase tracking-widest mb-3">
                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded border border-blue-200 font-mono">KODE: {{ $jemaat->kode_jemaat ?? 'N/A' }}</span>
                <span><i class="fas fa-map-marker-alt text-gray-400 mx-1"></i> {{ $jemaat->klasis->nama_klasis ?? 'Pusat Sinode' }}</span>
            </div>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-3">
                <span class="px-3 py-1 rounded text-[10px] font-black uppercase tracking-widest bg-gray-100 border border-gray-300 text-gray-700">
                    Status: {{ $jemaat->status_jemaat }}
                </span>
                <span class="px-3 py-1 bg-green-50 border border-green-200 text-green-800 rounded text-[10px] font-black uppercase tracking-widest">
                    {{ number_format($jemaat->real_total_kk ?? 0) }} Keluarga
                </span>
                <span class="px-3 py-1 bg-blue-50 border border-blue-200 text-blue-800 rounded text-[10px] font-black uppercase tracking-widest">
                    {{ number_format($jemaat->real_total_jiwa ?? 0) }} Jiwa
                </span>
            </div>
        </div>
        <div class="flex flex-col gap-2 shrink-0 w-full md:w-auto mt-4 md:mt-0">
            <a href="{{ route('admin.jemaat.index') }}" class="text-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-[10px] font-bold uppercase tracking-widest rounded hover:bg-gray-100 transition shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Indeks Wilayah
            </a>
            @can('edit jemaat')
            <a href="{{ route('admin.jemaat.edit', $jemaat->id) }}" class="text-center px-4 py-2 bg-gray-800 text-white text-[10px] font-bold uppercase tracking-widest rounded hover:bg-gray-900 transition shadow-sm">
                <i class="fas fa-edit mr-1"></i> Modifikasi
            </a>
            @endcan
            <a href="{{ route('admin.jemaat.cetak', $jemaat->id) }}" target="_blank" class="text-center px-4 py-2 bg-white border border-gray-300 text-green-700 text-[10px] font-bold uppercase tracking-widest rounded hover:bg-green-50 transition shadow-sm">
                <i class="fas fa-print mr-1"></i> Cetak Profil
            </a>
        </div>
    </div>

    {{-- MENU TAB FORMAL --}}
    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden mb-10">
        <div class="flex border-b border-gray-200 bg-gray-50 overflow-x-auto">
            <button @click="activeTab = 'profil'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'profil', 'text-gray-600 hover:text-gray-900': activeTab !== 'profil'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                1. Data Administratif
            </button>
            <button @click="activeTab = 'demografi'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'demografi', 'text-gray-600 hover:text-gray-900': activeTab !== 'demografi'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                2. Demografi Umat
            </button>
            <button @click="activeTab = 'sdm'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'sdm', 'text-gray-600 hover:text-gray-900': activeTab !== 'sdm'}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                3. Pelayan Firman
            </button>
        </div>

        <div class="p-8 min-h-[350px]">
            
            {{-- TAB 1: PROFIL ADMINISTRATIF --}}
            <div x-show="activeTab === 'profil'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Informasi Umum</h4>
                        <table class="w-full text-sm text-gray-700">
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Jenis Pelayanan</td><td class="py-2.5 font-bold text-gray-900">{{ $jemaat->jenis_jemaat }}</td></tr>
                            <tr><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Tanggal Peresmian</td><td class="py-2.5 font-bold text-gray-900">{{ $jemaat->tanggal_berdiri ? \Carbon\Carbon::parse($jemaat->tanggal_berdiri)->isoFormat('D MMMM Y') : 'Belum Terdata' }}</td></tr>
                        </table>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Kontak & Lokasi</h4>
                        <table class="w-full text-sm text-gray-700">
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Telepon Sekretariat</td><td class="py-2.5 font-mono text-xs font-bold text-gray-900">{{ $jemaat->telepon_kantor ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase">Surat Elektronik</td><td class="py-2.5 text-blue-700 font-bold">{{ $jemaat->email_jemaat ?? '-' }}</td></tr>
                            <tr><td class="py-2.5 font-bold w-1/3 text-[10px] uppercase align-top">Alamat Gedung</td><td class="py-2.5 bg-gray-50 p-3 rounded border border-gray-200 text-xs leading-relaxed">{{ $jemaat->alamat_gereja ?? '-' }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAB 2: DEMOGRAFI & GRAFIK --}}
            <div x-show="activeTab === 'demografi'" x-cloak>
                <div class="flex flex-col md:flex-row items-center justify-center gap-12 bg-gray-50 p-6 rounded border border-gray-200">
                    <div class="w-full md:w-1/3 flex justify-center">
                        <div style="position: relative; height: 220px; width: 220px;">
                            <canvas id="demografiChart"></canvas>
                        </div>
                    </div>
                    <div class="w-full md:w-1/2 space-y-4">
                        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded shadow-sm">
                            <span class="text-xs font-bold text-gray-700 uppercase tracking-widest"><i class="fas fa-male text-blue-600 text-lg mr-3"></i>Laki-Laki</span>
                            <span class="text-lg font-black text-gray-900">{{ number_format($realJiwaLaki ?? 0) }} <span class="text-xs text-gray-400 font-normal">Jiwa</span></span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded shadow-sm">
                            <span class="text-xs font-bold text-gray-700 uppercase tracking-widest"><i class="fas fa-female text-pink-600 text-lg mr-3"></i>Perempuan</span>
                            <span class="text-lg font-black text-gray-900">{{ number_format($realJiwaPerempuan ?? 0) }} <span class="text-xs text-gray-400 font-normal">Jiwa</span></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 3: SDM PELAYAN FIRMAN --}}
            <div x-show="activeTab === 'sdm'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($jemaat->pendetaDitempatkan as $p)
                    <div class="bg-gray-50 border border-gray-200 p-4 rounded flex items-center gap-4 hover:bg-gray-100 transition shadow-sm">
                        <div class="w-12 h-12 rounded border-2 border-white outline outline-1 outline-gray-300 bg-gray-200 flex items-center justify-center text-gray-800 text-lg font-black uppercase shadow-sm">
                            {{ substr($p->nama_lengkap, 0, 1) }}
                        </div>
                        <div>
                            <a href="{{ route('admin.kepegawaian.pegawai.show', $p->id) }}" class="text-sm font-black text-gray-900 hover:text-blue-800 uppercase">{{ $p->nama_lengkap }}</a>
                            <p class="text-[10px] font-mono font-bold text-gray-500 mt-1 uppercase tracking-widest">NIPG: {{ $p->nipg ?? 'Non-Organik' }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 text-center py-12">
                        <i class="fas fa-user-times text-4xl text-gray-300 mb-3 block"></i>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Kekosongan Personel</p>
                        <p class="text-[10px] text-gray-400 mt-1 italic">Belum ada SK penempatan pelayan firman organik di jemaat ini.</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('demografiChart');
        if(ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Laki-Laki', 'Perempuan'],
                    datasets: [{
                        data: [{{ $realJiwaLaki ?? 0 }}, {{ $realJiwaPerempuan ?? 0 }}],
                        backgroundColor: ['#2563eb', '#db2777'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>
@endpush
@endsection