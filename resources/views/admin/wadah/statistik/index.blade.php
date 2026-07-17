@extends('layouts.app')

@section('title', 'Pangkalan Data Kategorial')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & TOOLS --}}
    <div class="bg-white rounded border border-gray-300 p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-gray-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Laporan Agregat Demografi</h2>
            <p class="text-xs text-gray-600 mt-1">Sistem analisa demografi umat berdasarkan struktur kategorial resmi.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.wadah.statistik.print', request()->all()) }}" target="_blank" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2.5 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm transition flex items-center justify-center">
                <i class="fas fa-print mr-2"></i> Cetak Dokumen PDF
            </a>
        </div>
    </div>

    {{-- FILTER SEARCH --}}
    <div class="bg-gray-50 border border-gray-200 rounded p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.wadah.statistik.index') }}" class="w-full flex flex-col md:flex-row gap-4">
            
            {{-- Filter Klasis --}}
            @if(auth()->user()->hasRole(['Super Admin', 'Admin Sinode']))
                <div class="w-full md:w-64 flex-shrink-0">
                    <select name="klasis_id" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" onchange="this.form.submit()">
                        <option value="">- Seluruh Wilayah Sinode -</option>
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
                <div class="w-full md:w-64 flex-shrink-0">
                    <select name="jemaat_id" class="w-full border border-gray-300 rounded text-xs font-bold uppercase focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" onchange="this.form.submit()">
                        <option value="">- Seluruh Jemaat -</option>
                        @foreach($jemaatList as $jemaat)
                            <option value="{{ $jemaat->id }}" {{ request('jemaat_id') == $jemaat->id ? 'selected' : '' }}>
                                {{ $jemaat->nama_jemaat }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="flex-grow"></div>

            {{-- Tombol Reset --}}
            <div class="flex-shrink-0">
                <a href="{{ route('admin.wadah.statistik.index') }}" class="w-full bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-6 py-2.5 rounded text-[10px] font-bold uppercase transition flex items-center justify-center shadow-sm">
                    <i class="fas fa-undo mr-2"></i> Reset Parameter
                </a>
            </div>
            
            <button type="submit" class="hidden">Saring</button>
        </form>
    </div>

    {{-- 2. DASHBOARD GRAFIK --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Pie Chart --}}
        <div class="bg-white p-5 rounded border border-gray-300 shadow-sm border-t-4 border-t-blue-800">
            <h3 class="text-[10px] font-black text-gray-800 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2"><i class="fas fa-chart-pie mr-2 text-gray-500"></i> Komposisi Keanggotaan</h3>
            <div class="relative h-64">
                <canvas id="wadahPieChart"></canvas>
            </div>
        </div>

        {{-- Bar Chart --}}
        <div class="lg:col-span-2 bg-white p-5 rounded border border-gray-300 shadow-sm border-t-4 border-t-gray-800">
            <h3 class="text-[10px] font-black text-gray-800 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2"><i class="fas fa-chart-bar mr-2 text-gray-500"></i> Distribusi Populasi Berdasarkan Usia</h3>
            <div class="relative h-64">
                <canvas id="wadahBarChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 3. KARTU DETAIL DEMOGRAFI KATEGORIAL --}}
    <div class="mt-8">
        <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-4 flex items-center border-b-2 border-gray-800 pb-2">
            <i class="fas fa-table mr-2 text-gray-500"></i> Matriks Rincian Demografi
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            @foreach($statistik as $stat)
                <div class="bg-white rounded border border-gray-300 shadow-sm hover:shadow transition flex flex-col h-full overflow-hidden" style="border-top: 4px solid {{ $stat['warna'] }};">
                    <div class="p-4 flex flex-col flex-grow bg-gray-50/50">
                        {{-- Header Kartu --}}
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-black text-sm text-gray-900 leading-tight uppercase">{{ $stat['nama'] }}</h4>
                                <span class="text-[9px] font-bold text-gray-600 uppercase tracking-widest bg-white px-2 py-0.5 rounded mt-1.5 inline-block border border-gray-300">
                                    USIA: {{ $stat['range'] }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="block text-xl font-mono font-black text-gray-900">{{ number_format($stat['total']) }}</span>
                                <span class="text-[8px] text-gray-500 uppercase font-bold tracking-widest">Total Jiwa</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 mb-3"></div>

                        {{-- Gender Stats --}}
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-[11px] text-gray-800 font-bold uppercase tracking-wider">
                                <span class="flex items-center"><i class="fas fa-male w-4 text-center text-blue-700 mr-1"></i> Laki-laki</span>
                                <span>{{ number_format($stat['laki']) }}</span>
                            </div>
                            <div class="flex justify-between text-[11px] text-gray-800 font-bold uppercase tracking-wider">
                                <span class="flex items-center"><i class="fas fa-female w-4 text-center text-pink-700 mr-1"></i> Perempuan</span>
                                <span>{{ number_format($stat['perempuan']) }}</span>
                            </div>
                            {{-- Mini Bar Gender --}}
                            <div class="w-full bg-gray-300 rounded h-1.5 flex overflow-hidden mt-2 border border-gray-400">
                                <div class="bg-blue-700 h-1.5" style="width: {{ $stat['persen_laki'] }}%"></div>
                                <div class="bg-pink-700 h-1.5" style="width: {{ $stat['persen_perempuan'] }}%"></div>
                            </div>
                        </div>

                        {{-- Sidi Stats --}}
                        <div class="grid grid-cols-2 gap-3 mt-auto pt-3 border-t border-gray-200">
                            <div class="bg-green-50 p-2 border border-green-300 text-center rounded">
                                <span class="block text-xs font-mono font-black text-green-800">{{ number_format($stat['sidi']) }}</span>
                                <span class="block text-[8px] font-bold text-green-700 uppercase tracking-widest mt-0.5">Telah Sidi</span>
                            </div>
                            <div class="bg-white p-2 border border-gray-300 text-center rounded">
                                <span class="block text-xs font-mono font-black text-gray-700">{{ number_format($stat['belum_sidi']) }}</span>
                                <span class="block text-[8px] font-bold text-gray-500 uppercase tracking-widest mt-0.5">Belum Sidi</span>
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

        Chart.defaults.font.family = "'Arial', sans-serif";
        Chart.defaults.color = '#4b5563';

        new Chart(document.getElementById('wadahPieChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{ data: data, backgroundColor: colors, borderWidth: 1, borderColor: '#ffffff', hoverOffset: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, font: { size: 10 } } } },
                cutout: '70%'
            }
        });

        new Chart(document.getElementById('wadahBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{ 
                    label: 'Populasi Umat', 
                    data: data, 
                    backgroundColor: colors, 
                    borderRadius: 2,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 2], color: '#e5e7eb' }, ticks: { font: { size: 10, weight: 'bold' } } },
                    x: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' } } }
                }
            }
        });
    });
</script>
@endpush