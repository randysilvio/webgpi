@extends('layouts.app')

@section('title', 'Pusat Analisis Renstra')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-8">

    {{-- 1. SECTION FILTER SUPER LENGKAP --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
            <h2 class="text-white font-bold uppercase text-sm tracking-widest">
                <i class="fas fa-filter mr-2"></i> Filter Data Komprehensif
            </h2>
            <a href="{{ route('admin.laporan.renstra.print', request()->all()) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded transition flex items-center shadow-lg">
                <i class="fas fa-file-pdf mr-2"></i> Ekspor Laporan PDF
            </a>
        </div>

        <form method="GET" action="{{ route('admin.laporan.renstra.index') }}" class="p-6 bg-slate-50">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                
                {{-- Kelompok 1: Wilayah --}}
                <div class="space-y-3 border-b lg:border-b-0 lg:border-r border-slate-200 pb-4 lg:pb-0 lg:pr-4">
                    <h3 class="text-xs font-black text-blue-600 uppercase mb-2">Wilayah Pelayanan</h3>
                    <div>
                        <label class="label-filter">Klasis</label>
                        <select name="klasis_id" class="input-filter" onchange="this.form.submit()">
                            <option value="">Semua Klasis</option>
                            @foreach($filterOptions['klasis'] as $id => $val) 
                                <option value="{{ $id }}" {{ request('klasis_id') == $id ? 'selected' : '' }}>{{ $val }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label-filter">Jemaat</label>
                        <select name="jemaat_id" class="input-filter" onchange="this.form.submit()">
                            <option value="">Semua Jemaat</option>
                            @foreach($filterOptions['jemaat'] as $id => $val) 
                                <option value="{{ $id }}" {{ request('jemaat_id') == $id ? 'selected' : '' }}>{{ $val }}</option> 
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Kelompok 2: Demografi --}}
                <div class="space-y-3 border-b lg:border-b-0 lg:border-r border-slate-200 pb-4 lg:pb-0 lg:pr-4">
                    <h3 class="text-xs font-black text-purple-600 uppercase mb-2">Demografi</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="label-filter">Gender</label>
                            <select name="gender" class="input-filter">
                                <option value="">Semua</option>
                                <option value="Laki-laki" {{ request('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ request('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="label-filter">Status KK</label>
                            <select name="status_keluarga" class="input-filter">
                                <option value="">Semua</option>
                                <option value="Kepala Keluarga" {{ request('status_keluarga') == 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                                <option value="Istri" {{ request('status_keluarga') == 'Istri' ? 'selected' : '' }}>Istri</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="label-filter">Usia Min</label>
                            <input type="number" name="usia_min" value="{{ request('usia_min') }}" class="input-filter" placeholder="0">
                        </div>
                        <div>
                            <label class="label-filter">Usia Max</label>
                            <input type="number" name="usia_max" value="{{ request('usia_max') }}" class="input-filter" placeholder="100">
                        </div>
                    </div>
                </div>

                {{-- Kelompok 3: Sosial Ekonomi --}}
                <div class="space-y-3 border-b lg:border-b-0 lg:border-r border-slate-200 pb-4 lg:pb-0 lg:pr-4">
                    <h3 class="text-xs font-black text-green-600 uppercase mb-2">Sosial Ekonomi</h3>
                    <div>
                        <label class="label-filter">Pekerjaan</label>
                        <select name="pekerjaan" class="input-filter">
                            <option value="">Semua Pekerjaan</option>
                            @foreach($filterOptions['pekerjaan'] as $val) 
                                <option value="{{ $val }}" {{ request('pekerjaan') == $val ? 'selected' : '' }}>{{ $val }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label-filter">Ekonomi (Pengeluaran)</label>
                        <select name="penghasilan" class="input-filter">
                            <option value="">Semua Rentang</option>
                            @foreach($filterOptions['penghasilan'] as $val) 
                                <option value="{{ $val }}" {{ request('penghasilan') == $val ? 'selected' : '' }}>{{ $val }}</option> 
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Kelompok 4: Renstra Khusus --}}
                <div class="space-y-3">
                    <h3 class="text-xs font-black text-orange-600 uppercase mb-2">Indikator Renstra</h3>
                    <div>
                        <label class="label-filter">Kondisi Rumah</label>
                        <select name="kondisi_rumah" class="input-filter">
                            <option value="">Semua Kondisi</option>
                            <option value="Permanen" {{ request('kondisi_rumah') == 'Permanen' ? 'selected' : '' }}>Permanen</option>
                            <option value="Semi-Permanen" {{ request('kondisi_rumah') == 'Semi-Permanen' ? 'selected' : '' }}>Semi-Permanen</option>
                            <option value="Darurat/Kayu" {{ request('kondisi_rumah') == 'Darurat/Kayu' ? 'selected' : '' }}>Darurat / Kayu</option>
                        </select>
                    </div>
                    <div>
                        <label class="label-filter">Aset & Digital</label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="digital" class="input-filter">
                                <option value="">- Digital -</option>
                                <option value="HP" {{ request('digital') == 'HP' ? 'selected' : '' }}>Punya HP</option>
                                <option value="Gaptek" {{ request('digital') == 'Gaptek' ? 'selected' : '' }}>Non-Digital</option>
                            </select>
                            <select name="disabilitas" class="input-filter">
                                <option value="">- Fisik -</option>
                                <option value="Ya" {{ request('disabilitas') == 'Ya' ? 'selected' : '' }}>Disabilitas</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="mt-6 pt-4 border-t border-slate-200 flex justify-end gap-3">
                @if(count(request()->all()) > 0)
                    <a href="{{ route('admin.laporan.renstra.index') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-xs font-bold uppercase rounded hover:bg-slate-50 transition">
                        Reset Filter
                    </a>
                @endif
                <button type="submit" class="px-6 py-2 bg-slate-800 text-white text-xs font-bold uppercase rounded hover:bg-slate-900 transition shadow-md">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- 2. DASHBOARD METRICS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Data Terfilter</p>
                <h3 class="text-3xl font-black text-slate-800">{{ number_format($totalJiwa) }}</h3>
                <p class="text-xs text-slate-500">Jiwa</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-600">
                <i class="fas fa-users text-xl"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kepala Keluarga</p>
                <h3 class="text-3xl font-black text-slate-800">{{ number_format($totalKK) }}</h3>
                <p class="text-xs text-slate-500">Keluarga</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                <i class="fas fa-house-user text-xl"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Rumah Layak</p>
                <h3 class="text-3xl font-black text-green-600">{{ number_format($statsRumah['Permanen'] ?? 0) }}</h3>
                <p class="text-xs text-slate-500">Unit Permanen</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Rumah Darurat</p>
                <h3 class="text-3xl font-black text-red-600">{{ number_format($statsRumah['Darurat/Kayu'] ?? 0) }}</h3>
                <p class="text-xs text-slate-500">Perlu Bantuan</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
        </div>
    </div>

    {{-- 3. GRAPHIC ANALYSIS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Chart 1: Demografi Usia --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h4 class="font-bold text-slate-700 text-xs uppercase border-b pb-4 mb-4">Sebaran Kategori Usia</h4>
            <div class="h-64"><canvas id="chartUsia"></canvas></div>
        </div>

        {{-- Chart 2: Pendidikan --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h4 class="font-bold text-slate-700 text-xs uppercase border-b pb-4 mb-4">Tingkat Pendidikan Terakhir</h4>
            <div class="h-64"><canvas id="chartPendidikan"></canvas></div>
        </div>

        {{-- Chart 3: Kondisi Rumah (Pie) --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h4 class="font-bold text-slate-700 text-xs uppercase border-b pb-4 mb-4">Persentase Kondisi Hunian</h4>
            <div class="h-64 flex justify-center"><canvas id="chartRumah"></canvas></div>
        </div>

        {{-- List: Aset Ekonomi --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h4 class="font-bold text-slate-700 text-xs uppercase border-b pb-4 mb-4">Potensi Aset Ekonomi (Top 5)</h4>
            <div class="space-y-4">
                @foreach($statsAset as $aset => $jml)
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-slate-600">{{ $aset }}</span>
                        <span class="text-slate-800">{{ $jml }} Unit</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ ($totalKK > 0) ? ($jml/$totalKK)*100 : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
                @if(empty($statsAset)) <p class="text-xs text-slate-400 italic text-center py-10">Data aset tidak ditemukan.</p> @endif
            </div>
        </div>
    </div>

</div>

{{-- STYLING TAMBAHAN --}}
@push('styles')
<style>
    .label-filter { display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 4px; }
    .input-filter { width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; padding: 6px 10px; color: #334155; }
    .input-filter:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1); }
</style>
@endpush

{{-- SCRIPT CHART --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    // 1. Chart Usia
    new Chart(document.getElementById('chartUsia'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($statsUsia->keys()) !!},
            datasets: [{
                label: 'Jumlah Jiwa',
                data: {!! json_encode($statsUsia->values()) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 4
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
                label: 'Jumlah',
                data: {!! json_encode($statsPendidikan->values()) !!},
                backgroundColor: '#8b5cf6',
                borderRadius: 4
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
                backgroundColor: ['#22c55e', '#f59e0b', '#ef4444', '#cbd5e1'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
    });
});
</script>
@endsection