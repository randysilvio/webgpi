@extends('layouts.app')

@section('title', 'Tinjauan Detail Klasis')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white border border-gray-300 p-6 rounded shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">{{ $klasis->nama_klasis }}</h2>
            <p class="text-xs text-gray-500 font-bold mt-1">Kode: {{ $klasis->kode_klasis }} | Pusat: {{ $klasis->pusat_klasis ?? '-' }}</p>
        </div>
        <a href="{{ route('admin.klasis.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white border border-gray-300 p-6 rounded shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm uppercase border-b border-gray-200 pb-2 mb-4">Informasi Administrasi</h4>
            <div class="grid grid-cols-2 gap-4 text-xs font-bold text-gray-600">
                <div>SK Pembentukan: <span class="text-gray-900">{{ $klasis->nomor_sk_pembentukan ?? '-' }}</span></div>
                <div>Tanggal Est: <span class="text-gray-900">{{ $klasis->tanggal_pembentukan ? $klasis->tanggal_pembentukan->format('d/m/Y') : '-' }}</span></div>
                <div>Telepon: <span class="text-gray-900">{{ $klasis->telepon_kantor ?? '-' }}</span></div>
                <div>Email: <span class="text-gray-900">{{ $klasis->email_klasis ?? '-' }}</span></div>
            </div>
        </div>

        <div class="bg-white border border-gray-300 p-6 rounded shadow-sm text-center">
            <h4 class="font-bold text-gray-800 text-sm uppercase mb-4">Peta Wilayah</h4>
            @if($klasis->latitude && $klasis->longitude)
                <div class="h-32 bg-gray-100 rounded flex items-center justify-center border border-gray-200">
                    <span class="text-[10px] font-bold text-gray-500">Koordinat: {{ $klasis->latitude }}, {{ $klasis->longitude }}</span>
                </div>
            @else
                <p class="text-[10px] text-gray-400 italic">Koordinat belum diatur</p>
            @endif
        </div>
    </div>
</div>
@endsection