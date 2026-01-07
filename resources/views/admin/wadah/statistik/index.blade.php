@extends('admin.layout')

@section('title', 'Dashboard Kategorial')
@section('header-title', 'Statistik & Peta Kekuatan Jemaat')

@section('content')
    {{-- 1. INFO & FILTER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h2 class="text-lg font-black text-gray-800 uppercase tracking-widest">Filter Data</h2>
                <p class="text-xs text-gray-500">Sesuaikan lingkup wilayah untuk melihat peta kekuatan wadah kategorial.</p>
            </div>
            <div class="mt-2 md:mt-0 flex gap-2">
                {{-- TOMBOL CETAK --}}
                <a href="{{ route('admin.wadah.statistik.print', request()->all()) }}" target="_blank" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold uppercase transition shadow flex items-center">
                    <i class="fas fa-print mr-2"></i> Cetak PDF
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.wadah.statistik.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(auth()->user()->hasRole(['Super Admin', 'Admin Sinode']))
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Klasis</label>
                    <select name="klasis_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary" onchange="this.form.submit()">
                        <option value="">-- Seluruh Klasis --</option>
                        @foreach($klasisList as $klasis)
                            <option value="{{ $klasis->id }}" {{ request('klasis_id') == $klasis->id ? 'selected' : '' }}>{{ $klasis->nama_klasis }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if(auth()->user()->hasRole(['Super Admin', 'Admin Sinode', 'Admin Klasis']))
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Jemaat</label>
                    <select name="jemaat_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary" onchange="this.form.submit()">
                        <option value="">-- Seluruh Jemaat --</option>
                        @foreach($jemaatList as $jemaat)
                            <option value="{{ $jemaat->id }}" {{ request('jemaat_id') == $jemaat->id ? 'selected' : '' }}>{{ $jemaat->nama_jemaat }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="flex items-end">
                <a href="{{ route('admin.wadah.statistik.index') }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 px-4 rounded-lg text-xs uppercase tracking-wider text-center transition">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- 2. DASHBOARD GRAFIK --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 border-b pb-2">Komposisi Anggota</h3>
            <div class="relative h-64">
                <canvas id="wadahPieChart"></canvas>
            </div>
        </div>
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 border-b pb-2">Perbandingan Populasi</h3>
            <div class="relative h-64">
                <canvas id="wadahBarChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 3. KARTU DETAIL INFORMATIF --}}
    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 ml-1">Rincian Data Kategorial</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
        @foreach($statistik as $stat)
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden border-t-4" style="border-color: {{ $stat['warna'] }};">
                <div class="p-5">
                    {{-- Header Kartu --}}
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="font-black text-lg text-gray-800 uppercase leading-none">{{ $stat['nama'] }}</h4>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $stat['range'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-2xl font-black text-gray-800 leading-none">{{ number_format($stat['total']) }}</span>
                            <span class="text-[9px] text-gray-500 uppercase">Total Jiwa</span>
                        </div>
                    </div>

                    <hr class="border-gray-100 my-3">

                    {{-- Progress Bar Gender --}}
                    <div class="mb-3">
                        <div class="flex justify-between text-[10px] font-bold text-gray-500 mb-1 uppercase">
                            <span><i class="fas fa-male text-blue-600 mr-1"></i> L: {{ $stat['laki'] }}</span>
                            <span>P: {{ $stat['perempuan'] }} <i class="fas fa-female text-pink-500 ml-1"></i></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden flex">
                            <div class="bg-blue-500 h-2" style="width: {{ $stat['persen_laki'] }}%"></div>
                            <div class="bg-pink-500 h-2" style="width: {{ $stat['persen_perempuan'] }}%"></div>
                        </div>
                    </div>

                    {{-- Info Sidi --}}
                    <div class="grid grid-cols-2 gap-2 text-center mt-4">
                        <div class="bg-green-50 p-2 rounded-lg">
                            <span class="block text-sm font-black text-green-700">{{ $stat['sidi'] }}</span>
                            <span class="block text-[8px] font-bold text-green-600 uppercase">Sudah Sidi</span>
                        </div>
                        <div class="bg-gray-50 p-2 rounded-lg">
                            <span class="block text-sm font-black text-gray-600">{{ $stat['belum_sidi'] }}</span>
                            <span class="block text-[8px] font-bold text-gray-500 uppercase">Belum Sidi</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = @json($chartLabels);
        const data = @json($chartData);
        const colors = @json($chartColors);

        // Pie Chart
        new Chart(document.getElementById('wadahPieChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{ data: data, backgroundColor: colors, borderWidth: 0, hoverOffset: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: { size: 10 } } } }
            }
        });

        // Bar Chart
        new Chart(document.getElementById('wadahBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{ label: 'Populasi', data: data, backgroundColor: colors, borderRadius: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush