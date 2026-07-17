@extends('layouts.app')

@section('title', 'Pusat Analisis Rencana Strategis')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-6">

    {{-- HEADER & PENGATURAN --}}
    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden border-l-4 border-l-gray-800">
        <div class="bg-gray-100 px-6 py-4 flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-200 gap-4">
            <h2 class="text-gray-900 font-black uppercase text-sm tracking-widest">
                <i class="fas fa-filter mr-2 text-gray-500"></i> Pusat Analisis Rencana Strategis (RENSTRA)
            </h2>
            <a href="{{ route('admin.laporan.renstra.print', request()->all()) }}" target="_blank" class="bg-red-700 hover:bg-red-800 text-white text-[10px] font-bold uppercase tracking-widest px-6 py-2.5 rounded transition flex items-center shadow-sm">
                <i class="fas fa-file-pdf mr-2"></i> Ekspor Dokumen Resmi
            </a>
        </div>

        <form method="GET" action="{{ route('admin.laporan.renstra.index') }}" class="p-6 bg-white">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Kelompok 1: Wilayah --}}
                <div class="space-y-3 lg:border-r border-gray-200 lg:pr-6">
                    <h3 class="text-[10px] font-black text-gray-800 uppercase tracking-widest mb-3 border-b border-gray-100 pb-1">Lokasi Pelayanan</h3>
                    <div>
                        <label class="block text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Teritorial Klasis</label>
                        <select name="klasis_id" class="w-full border border-gray-300 rounded text-xs focus:ring-gray-800 focus:border-gray-800 bg-gray-50" onchange="this.form.submit()">
                            <option value="">Semua Teritorial</option>
                            @foreach($filterOptions['klasis'] as $id => $val) 
                                <option value="{{ $id }}" {{ request('klasis_id') == $id ? 'selected' : '' }}>{{ strtoupper($val) }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Unit Jemaat</label>
                        <select name="jemaat_id" class="w-full border border-gray-300 rounded text-xs focus:ring-gray-800 focus:border-gray-800 bg-gray-50" onchange="this.form.submit()">
                            <option value="">Semua Jemaat</option>
                            @foreach($filterOptions['jemaat'] as $id => $val) 
                                <option value="{{ $id }}" {{ request('jemaat_id') == $id ? 'selected' : '' }}>{{ strtoupper($val) }}</option> 
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Kelompok 2: Demografi --}}
                <div class="space-y-3 lg:border-r border-gray-200 lg:pr-6">
                    <h3 class="text-[10px] font-black text-gray-800 uppercase tracking-widest mb-3 border-b border-gray-100 pb-1">Statistik Demografi</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Gender</label>
                            <select name="gender" class="w-full border border-gray-300 rounded text-xs focus:ring-gray-800 focus:border-gray-800 bg-gray-50">
                                <option value="">Semua</option>
                                <option value="Laki-laki" {{ request('gender') == 'Laki-laki' ? 'selected' : '' }}>Pria</option>
                                <option value="Perempuan" {{ request('gender') == 'Perempuan' ? 'selected' : '' }}>Wanita</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Status KK</label>
                            <select name="status_keluarga" class="w-full border border-gray-300 rounded text-xs focus:ring-gray-800 focus:border-gray-800 bg-gray-50">
                                <option value="">Semua</option>
                                <option value="Kepala Keluarga" {{ request('status_keluarga') == 'Kepala Keluarga' ? 'selected' : '' }}>K. Keluarga</option>
                                <option value="Istri" {{ request('status_keluarga') == 'Istri' ? 'selected' : '' }}>Istri</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Usia Minimal</label>
                            <input type="number" name="usia_min" value="{{ request('usia_min') }}" class="w-full border border-gray-300 rounded text-xs focus:ring-gray-800 focus:border-gray-800 bg-gray-50" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Usia Maksimal</label>
                            <input type="number" name="usia_max" value="{{ request('usia_max') }}" class="w-full border border-gray-300 rounded text-xs focus:ring-gray-800 focus:border-gray-800 bg-gray-50" placeholder="100">
                        </div>
                    </div>
                </div>

                {{-- Kelompok 3: Sosial Ekonomi --}}
                <div class="space-y-3 lg:border-r border-gray-200 lg:pr-6">
                    <h3 class="text-[10px] font-black text-gray-800 uppercase tracking-widest mb-3 border-b border-gray-100 pb-1">Kesejahteraan & Profesi</h3>
                    <div>
                        <label class="block text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Profesi Utama</label>
                        <select name="pekerjaan" class="w-full border border-gray-300 rounded text-xs focus:ring-gray-800 focus:border-gray-800 bg-gray-50">
                            <option value="">Semua Kategori Pekerjaan</option>
                            @foreach($filterOptions['pekerjaan'] as $val) 
                                <option value="{{ $val }}" {{ request('pekerjaan') == $val ? 'selected' : '' }}>{{ strtoupper($val) }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Estimasi Pengeluaran / Bulan</label>
                        <select name="penghasilan" class="w-full border border-gray-300 rounded text-xs focus:ring-gray-800 focus:border-gray-800 bg-gray-50">
                            <option value="">Semua Rentang Ekonomi</option>
                            @foreach($filterOptions['penghasilan'] as $val) 
                                <option value="{{ $val }}" {{ request('penghasilan') == $val ? 'selected' : '' }}>{{ strtoupper($val) }}</option> 
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Kelompok 4: Indikator Infrastruktur & Digital --}}
                <div class="space-y-3">
                    <h3 class="text-[10px] font-black text-gray-800 uppercase tracking-widest mb-3 border-b border-gray-100 pb-1">Infrastruktur & Kapabilitas Digital</h3>
                    <div>
                        <label class="block text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Konstruksi Rumah Tinggal</label>
                        <select name="kondisi_rumah" class="w-full border border-gray-300 rounded text-xs focus:ring-gray-800 focus:border-gray-800 bg-gray-50">
                            <option value="">Semua Kondisi Fisik</option>
                            <option value="Permanen" {{ request('kondisi_rumah') == 'Permanen' ? 'selected' : '' }}>Permanen (Layak)</option>
                            <option value="Semi-Permanen" {{ request('kondisi_rumah') == 'Semi-Permanen' ? 'selected' : '' }}>Semi-Permanen</option>
                            <option value="Darurat/Kayu" {{ request('kondisi_rumah') == 'Darurat/Kayu' ? 'selected' : '' }}>Darurat / Kayu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Variabel Khusus</label>
                        <div class="grid grid-cols-2 gap-3">
                            <select name="digital" class="w-full border border-gray-300 rounded text-xs focus:ring-gray-800 focus:border-gray-800 bg-gray-50">
                                <option value="">- Status Digital -</option>
                                <option value="HP" {{ request('digital') == 'HP' ? 'selected' : '' }}>Punya Smartphone</option>
                                <option value="Gaptek" {{ request('digital') == 'Gaptek' ? 'selected' : '' }}>Tanpa Gadget</option>
                            </select>
                            <select name="disabilitas" class="w-full border border-gray-300 rounded text-xs focus:ring-gray-800 focus:border-gray-800 bg-gray-50">
                                <option value="">- Disabilitas -</option>
                                <option value="Ya" {{ request('disabilitas') == 'Ya' ? 'selected' : '' }}>Disabilitas Fisik/Mental</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="mt-8 pt-4 border-t border-gray-200 flex justify-end gap-3">
                @if(count(request()->all()) > 0)
                    <a href="{{ route('admin.laporan.renstra.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-600 text-[10px] font-bold uppercase tracking-widest rounded hover:bg-gray-100 transition shadow-sm">
                        Reset Matriks
                    </a>
                @endif
                <button type="submit" class="px-8 py-2.5 bg-gray-800 text-white text-[10px] font-bold uppercase tracking-widest rounded hover:bg-gray-900 transition shadow-sm flex items-center">
                    <i class="fas fa-check-double mr-2"></i> Kalkulasi Data
                </button>
            </div>
        </form>
    </div>

    {{-- 2. PANEL RINGKASAN METRIK --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm flex items-center justify-between border-t-4 border-t-blue-800">
            <div>
                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Total Sensus Jiwa</p>
                <h3 class="text-3xl font-black text-gray-900 mt-1">{{ number_format($totalJiwa) }}</h3>
            </div>
            <i class="fas fa-users text-3xl text-gray-300"></i>
        </div>
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm flex items-center justify-between border-t-4 border-t-blue-500">
            <div>
                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Total Kepala Keluarga</p>
                <h3 class="text-3xl font-black text-gray-900 mt-1">{{ number_format($totalKK) }}</h3>
            </div>
            <i class="fas fa-house-user text-3xl text-gray-300"></i>
        </div>
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm flex items-center justify-between border-t-4 border-t-green-700">
            <div>
                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Hunian Permanen (Layak)</p>
                <h3 class="text-3xl font-black text-green-800 mt-1">{{ number_format($statsRumah['Permanen'] ?? 0) }}</h3>
            </div>
            <i class="fas fa-check-circle text-3xl text-green-200"></i>
        </div>
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm flex items-center justify-between border-t-4 border-t-red-700">
            <div>
                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Hunian Darurat / Kayu</p>
                <h3 class="text-3xl font-black text-red-800 mt-1">{{ number_format($statsRumah['Darurat/Kayu'] ?? 0) }}</h3>
            </div>
            <i class="fas fa-exclamation-triangle text-3xl text-red-200"></i>
        </div>
    </div>

    {{-- 3. VISUALISASI GRAFIK ANALISIS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Chart 1: Demografi Usia --}}
        <div class="bg-white p-6 rounded border border-gray-300 shadow-sm">
            <h4 class="font-black text-gray-800 text-[10px] uppercase tracking-widest border-b border-gray-200 pb-3 mb-5">Distribusi Kategori Usia</h4>
            <div class="h-64"><canvas id="chartUsia"></canvas></div>
        </div>

        {{-- Chart 2: Pendidikan --}}
        <div class="bg-white p-6 rounded border border-gray-300 shadow-sm">
            <h4 class="font-black text-gray-800 text-[10px] uppercase tracking-widest border-b border-gray-200 pb-3 mb-5">Tingkat Pendidikan Terakhir</h4>
            <div class="h-64"><canvas id="chartPendidikan"></canvas></div>
        </div>

        {{-- Chart 3: Kondisi Rumah (Pie) --}}
        <div class="bg-white p-6 rounded border border-gray-300 shadow-sm">
            <h4 class="font-black text-gray-800 text-[10px] uppercase tracking-widest border-b border-gray-200 pb-3 mb-5">Indikator Kondisi Hunian</h4>
            <div class="h-64 flex justify-center"><canvas id="chartRumah"></canvas></div>
        </div>

        {{-- List: Aset Ekonomi --}}
        <div class="bg-white p-6 rounded border border-gray-300 shadow-sm">
            <h4 class="font-black text-gray-800 text-[10px] uppercase tracking-widest border-b border-gray-200 pb-3 mb-5">Kapital Aset Ekonomi (Sektor Tertinggi)</h4>
            <div class="space-y-5">
                @foreach($statsAset as $aset => $jml)
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1.5 uppercase">
                        <span class="text-gray-700">{{ $aset }}</span>
                        <span class="text-gray-900 font-mono">{{ $jml }} Unit</span>
                    </div>
                    <div class="w-full bg-gray-200 border border-gray-300 rounded h-3 overflow-hidden">
                        <div class="bg-gray-800 h-3" style="width: {{ ($totalKK > 0) ? ($jml/$totalKK)*100 : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
                @if(empty($statsAset)) <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic text-center py-10">Data aset tidak ditemukan pada parameter ini.</p> @endif
            </div>
        </div>
    </div>

</div>

{{-- SCRIPT CHART --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    Chart.defaults.font.family = "Arial, sans-serif";
    Chart.defaults.color = '#4b5563';

    // 1. Chart Usia
    new Chart(document.getElementById('chartUsia'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($statsUsia->keys()) !!},
            datasets: [{
                label: 'Jumlah Penduduk (Jiwa)',
                data: {!! json_encode($statsUsia->values()) !!},
                backgroundColor: '#1e3a8a', // blue-900
                borderRadius: 2
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });

    // 2. Chart Pendidikan
    new Chart(document.getElementById('chartPendidikan'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($statsPendidikan->keys()) !!},
            datasets: [{
                label: 'Jumlah Populasi',
                data: {!! json_encode($statsPendidikan->values()) !!},
                backgroundColor: '#374151', // gray-700
                borderRadius: 2
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } } }
    });

    // 3. Chart Rumah
    new Chart(document.getElementById('chartRumah'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statsRumah->keys()) !!},
            datasets: [{
                data: {!! json_encode($statsRumah->values()) !!},
                backgroundColor: ['#15803d', '#ca8a04', '#b91c1c', '#9ca3af'], // green, yellow, red, gray
                borderWidth: 1,
                borderColor: '#ffffff'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
    });
});
</script>
@endsection