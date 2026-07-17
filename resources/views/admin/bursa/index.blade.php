@extends('layouts.app')

@section('title', 'Indeks Distribusi Dokumen')

@section('content')
    <x-admin-index 
        title="Manajemen Dokumen Teologi & Liturgi" 
        subtitle="Pangkalan data distribusi materi khotbah dan tata ibadah resmi Sinode GPI Papua."
        create-route="{{ route('admin.bursa.create') }}"
        create-label="Unggah Dokumen Baru"
        :pagination="$materis"
    >
        <x-slot name="tableHead">
            <th class="px-6 py-3 font-bold text-gray-700 text-xs uppercase border-b-2 border-gray-800 bg-gray-100">Uraian Dokumen</th>
            <th class="px-6 py-3 font-bold text-gray-700 text-xs uppercase border-b-2 border-gray-800 bg-gray-100 text-center">Klasifikasi</th>
            <th class="px-6 py-3 font-bold text-gray-700 text-xs uppercase border-b-2 border-gray-800 bg-gray-100 text-right">Biaya / Infaq</th>
            <th class="px-6 py-3 font-bold text-gray-700 text-xs uppercase border-b-2 border-gray-800 bg-gray-100 text-center w-24">Tindakan</th>
        </x-slot>

        @forelse ($materis as $materi)
            <tr class="hover:bg-gray-50 transition border-b border-gray-200">
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-900 text-sm">{{ $materi->judul_dokumen }}</div>
                    <div class="text-[10px] text-gray-500 mt-1 uppercase tracking-wide">Diregistrasi: {{ $materi->created_at->format('d/m/Y') }}</div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase bg-gray-200 text-gray-700 border border-gray-300">{{ $materi->kategori }}</span>
                </td>
                <td class="px-6 py-4 text-right font-mono text-sm font-bold text-gray-800">
                    {{ $materi->harga_dokumen == 0 ? 'GRATIS' : 'Rp ' . number_format($materi->harga_dokumen, 0, ',', '.') }}
                </td>
                <td class="px-6 py-4 text-center">
                    <form action="{{ route('admin.bursa.destroy', $materi) }}" method="POST" onsubmit="return confirm('Musnahkan dokumen ini dari pangkalan data?')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-red-700 transition" title="Musnahkan Rekord"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">Arsip dokumen kosong.</td></tr>
        @endforelse
    </x-admin-index>
@endsection