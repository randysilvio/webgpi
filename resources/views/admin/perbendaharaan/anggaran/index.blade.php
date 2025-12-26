@extends('admin.layout')

@section('title', 'Rencana Anggaran (RAPB)')
@section('header-title', 'Penyusunan Anggaran Induk')

@section('content')
<div class="space-y-6">
    {{-- Ringkasan Anggaran --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
            <p class="text-xs font-bold text-gray-400 uppercase">Target Pendapatan</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500">
            <p class="text-xs font-bold text-gray-400 uppercase">Rencana Belanja</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
            <p class="text-xs font-bold text-gray-400 uppercase">Estimasi Surplus/Defisit</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">Rp {{ number_format($totalPendapatan - $totalBelanja, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <form action="{{ route('admin.perbendaharaan.anggaran.index') }}" method="GET" class="flex items-center space-x-2">
                <select name="tahun" onchange="this.form.submit()" class="border-gray-300 rounded-md text-sm">
                    @for($i = date('Y'); $i <= date('Y')+2; $i++)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>Tahun {{ $i }}</option>
                    @endfor
                </select>
            </form>
            <a href="{{ route('admin.perbendaharaan.anggaran.create') }}" class="bg-primary text-white px-4 py-2 rounded-md text-sm font-bold shadow hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i> Susun RAPB
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata Anggaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Target (Rp)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($anggarans as $ang)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $ang->mataAnggaran->kode }} - {{ $ang->mataAnggaran->nama_mata_anggaran }}</td>
                        <td class="px-6 py-4 text-sm {{ $ang->mataAnggaran->jenis == 'Pendapatan' ? 'text-green-600' : 'text-red-600' }}">{{ $ang->mataAnggaran->jenis }}</td>
                        <td class="px-6 py-4 text-right text-sm font-bold">{{ number_format($ang->jumlah_target, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ $ang->status_anggaran }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">Belum ada anggaran yang disusun untuk tahun ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection