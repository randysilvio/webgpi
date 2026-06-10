@extends('layouts.app')

@section('title', 'Dashboard Kategorial')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & FILTER --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Peta Kekuatan Wadah</h2>
                <p class="text-sm text-slate-500">Analisa demografi dan statistik keanggotaan berdasarkan kategori usia.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.wadah.statistik.print', request()->all()) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-sm transition">
                    <i class="fas fa-print mr-2"></i> Cetak Laporan PDF
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.wadah.statistik.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            {{-- Filter Klasis --}}
            @if(auth()->user()->hasRole(['Super Admin', 'Admin Sinode']))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Klasis</label>
                    <select name="klasis_id" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" onchange="this.form.submit()">
                        <option value="">- Semua Klasis -</option>
                        @foreach($klasisList as $klasis)
                            <option value="{{ $klasis->id }}" {{ request('klasis_id') == $klasis->id ? 'selected' : '' }}>
                                {{ $klasis->nama_klasis }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Filter Jemaat --}}
            @if(auth()->user()->hasRole(['Super Admin', 'Admin Sinode', 'Admin Klasis']))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jemaat</label>
                    <select name="jemaat_id" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500" onchange="this.form.submit()">
                        <option value="">- Semua Jemaat -</option>
                        @foreach($jemaatList as $jemaat)
                            <option value="{{ $jemaat->id }}" {{ request('jemaat_id') == $jemaat->id ? 'selected' : '' }}>
                                {{ $jemaat->nama_jemaat }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Tombol Reset --}}
            <div class="flex items-end">
                <a href="{{ route('admin.wadah.statistik.index') }}" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 px-4 rounded text-sm text-center transition border border-slate-200">
                    <i class="fas fa-undo mr-2"></i> Reset Filter
                </a>
            </div>
        </form>
    </div>

    {{-- 2. DASHBOARD GRAFIK --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Pie Chart --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2">Komposisi Anggota</h3>
            <div class="relative h-64">
                <canvas id="wadahPieChart"></canvas>
            </div>
        </div>

        {{-- Bar Chart --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2">Perbandingan Populasi</h3>
            <div class="relative h-64">
                <canvas id="wadahBarChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 3. KARTU DETAIL (Grid) --}}
    <div>
        <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center">
            <i class="fas fa-table mr-2 text-slate-400"></i> Rincian Data Kategorial
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            @foreach($statistik as $stat)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-slate-200 flex flex-col h-full">
                    {{-- Color Bar --}}
                    <div class="h-1 w-full" style="background-color: {{ $stat['warna'] }};"></div>
                    
                    <div class="p-5 flex flex-col flex-grow">
                        {{-- Header Kartu --}}
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-bold text-lg text-slate-800 leading-tight">{{ $stat['nama'] }}</h4>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide bg-slate-50 px-2 py-0.5 rounded mt-1 inline-block">
                                    {{ $stat['range'] }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="block text-2xl font-black text-slate-800">{{ number_format($stat['total']) }}</span>
                                <span class="text-[9px] text-slate-400 uppercase font-bold">Jiwa</span>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 my-3"></div>

                        {{-- Gender Stats --}}
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-xs text-slate-600">
                                <span class="flex items-center"><i class="fas fa-male w-4 text-center text-blue-500 mr-1"></i> Laki-laki</span>
                                <span class="font-bold">{{ number_format($stat['laki']) }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-slate-600">
                                <span class="flex items-center"><i class="fas fa-female w-4 text-center text-pink-500 mr-1"></i> Perempuan</span>
                                <span class="font-bold">{{ number_format($stat['perempuan']) }}</span>
                            </div>
                            {{-- Mini Bar Gender --}}
                            <div class="w-full bg-slate-100 rounded-full h-1.5 flex overflow-hidden">
                                <div class="bg-blue-500 h-1.5" style="width: {{ $stat['persen_laki'] }}%"></div>
                                <div class="bg-pink-500 h-1.5" style="width: {{ $stat['persen_perempuan'] }}%"></div>
                            </div>
                        </div>

                        {{-- Sidi Stats --}}
                        <div class="grid grid-cols-2 gap-2 mt-auto">
                            <div class="bg-green-50 p-2 rounded border border-green-100 text-center">
                                <span class="block text-xs font-bold text-green-700">{{ number_format($stat['sidi']) }}</span>
                                <span class="block text-[9px] font-bold text-green-600 uppercase">Sidi</span>
                            </div>
                            <div class="bg-slate-50 p-2 rounded border border-slate-100 text-center">
                                <span class="block text-xs font-bold text-slate-600">{{ number_format($stat['belum_sidi']) }}</span>
                                <span class="block text-[9px] font-bold text-slate-400 uppercase">Blm Sidi</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = @json($chartLabels);
        const data = @json($chartData);
        const colors = @json($chartColors);

        // Config Font & Colors
        Chart.defaults.font.family = "'Inter', 'sans-serif'";
        Chart.defaults.color = '#64748b';

        // Pie Chart
        new Chart(document.getElementById('wadahPieChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{ data: data, backgroundColor: colors, borderWidth: 2, borderColor: '#ffffff', hoverOffset: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } } },
                cutout: '70%'
            }
        });

        // Bar Chart
        new Chart(document.getElementById('wadahBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{ 
                    label: 'Populasi', 
                    data: data, 
                    backgroundColor: colors, 
                    borderRadius: 4,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    });
</script>
@endpush