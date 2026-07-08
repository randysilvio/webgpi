@extends('layouts.app')

@section('title', 'Arsip Jurnal Pelayanan')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 uppercase tracking-wide">Pangkalan Data Pastoral</h2>
        <p class="text-sm text-gray-600 mt-1">Sistem dokumentasi rekam jejak, analisis konteks, dan resolusi pelayanan Jemaat.</p>
    </div>

    {{-- KOTAK INFORMASI STRUKTURAL --}}
    @if(auth()->user()->hasRole('Pendeta'))
        <div class="bg-blue-50 border border-blue-200 p-4 rounded mb-6 flex items-center justify-between">
            <div>
                <h4 class="font-bold text-blue-900 text-sm uppercase tracking-wide"><i class="fas fa-church mr-2"></i> Lokasi Tugas Saat Ini: {{ auth()->user()->pegawai->jemaat->nama_jemaat ?? 'Belum Ditugaskan' }}</h4>
                <p class="text-xs text-blue-700 mt-1">Seluruh jurnal yang Anda tulis akan menjadi milik arsip jemaat ini. Anda juga dapat membaca riwayat peninggalan pendeta sebelumnya di jemaat ini.</p>
            </div>
            <a href="{{ route('admin.jurnal.create') }}" class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-2.5 rounded text-xs font-bold uppercase tracking-wide shadow-sm transition whitespace-nowrap">
                <i class="fas fa-pen-nib mr-2"></i> Tulis Jurnal Baru
            </a>
        </div>
    @endif

    {{-- TABEL ARSIP FORMAL --}}
    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 text-xs uppercase tracking-wider"><i class="fas fa-archive mr-2 text-gray-400"></i> Indeks Dokumen Jurnal</h3>
            
            <form method="GET" action="{{ route('admin.jurnal.index') }}" class="flex gap-2">
                <select name="kategori" class="border-gray-300 rounded text-xs text-gray-700 focus:ring-blue-800 focus:border-blue-800" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori Pelayanan --</option>
                    <option value="Diakonia & Sosial" {{ request('kategori') == 'Diakonia & Sosial' ? 'selected' : '' }}>Diakonia & Sosial</option>
                    <option value="Resolusi Konflik" {{ request('kategori') == 'Resolusi Konflik' ? 'selected' : '' }}>Resolusi Konflik</option>
                    <option value="Pembangunan Fisik" {{ request('kategori') == 'Pembangunan Fisik' ? 'selected' : '' }}>Pembangunan Fisik</option>
                    <option value="Pembinaan Teologi" {{ request('kategori') == 'Pembinaan Teologi' ? 'selected' : '' }}>Pembinaan Teologi</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-800 text-xs text-gray-700 uppercase tracking-wider font-bold">
                        <th class="px-6 py-3">Tgl Kegiatan</th>
                        <th class="px-6 py-3">Lokasi Jemaat & Penulis</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3 w-1/3">Konteks & Analisis Singkat</th>
                        <th class="px-6 py-3 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($jurnals as $jurnal)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-800 whitespace-nowrap">
                                {{ $jurnal->tanggal_kegiatan->isoFormat('D MMM YYYY') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 text-xs uppercase">{{ $jurnal->jemaat->nama_jemaat }}</div>
                                <div class="text-[10px] text-gray-500 mt-1 uppercase tracking-wide"><i class="fas fa-user-tie mr-1"></i> {{ $jurnal->pendeta->nama_lengkap ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase bg-gray-200 text-gray-700 border border-gray-300">{{ $jurnal->kategori }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600 leading-relaxed line-clamp-2">
                                {{ Str::limit($jurnal->konteks_situasi, 80) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.jurnal.show', $jurnal->id) }}" class="inline-block bg-white border border-gray-300 text-gray-600 hover:text-blue-800 hover:bg-gray-100 px-3 py-1.5 rounded text-[10px] font-bold uppercase transition shadow-sm">
                                    <i class="fas fa-folder-open mr-1"></i> Buka Berkas
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm">
                                <i class="fas fa-box-open text-3xl mb-3 block text-gray-300"></i>
                                Rekam jejak pastoral belum tersedia untuk indeks ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $jurnals->links() }}
        </div>
    </div>
@endsection