@extends('layouts.app')

@section('title', 'Pusat Verifikasi Otorisasi Dokumen')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Pusat Verifikasi Administrasi Dokumen</h2>
        <p class="text-xs text-gray-600 mt-1">Panel otorisasi pengunduhan materi berbayar bagi para Pelayan Firman.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-800 text-xs text-gray-700 uppercase tracking-wider font-bold">
                    <th class="px-6 py-3">No. Register</th>
                    <th class="px-6 py-3">Pemohon (Pendeta)</th>
                    <th class="px-6 py-3">Dokumen Diminta</th>
                    <th class="px-6 py-3 text-center">Bukti Transfer</th>
                    <th class="px-6 py-3 text-center">Tindakan Otorisasi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-200">
                @forelse($transaksis as $trx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-gray-800">{{ $trx->nomor_registrasi }}</td>
                        <td class="px-6 py-4 text-xs font-bold text-gray-900 uppercase">{{ $trx->pendeta->nama_lengkap ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-xs">{{ $trx->materi->judul_dokumen ?? 'Terhapus' }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ Storage::url($trx->bukti_transfer_path) }}" target="_blank" class="text-blue-800 hover:underline text-[10px] font-bold uppercase"><i class="fas fa-external-link-alt mr-1"></i> Periksa Bukti</a>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($trx->status_pembayaran == 'Menunggu Verifikasi')
                                <form action="{{ route('admin.bursa.transaksi.update', $trx) }}" method="POST" class="flex justify-center gap-2">
                                    @csrf @method('PUT')
                                    <button type="submit" name="status_pembayaran" value="Lunas" class="bg-green-700 hover:bg-green-800 text-white px-3 py-1.5 rounded text-[10px] font-bold uppercase">Sah</button>
                                    <button type="submit" name="status_pembayaran" value="Ditolak" class="bg-red-700 hover:bg-red-800 text-white px-3 py-1.5 rounded text-[10px] font-bold uppercase">Tolak</button>
                                </form>
                            @else
                                <span class="text-[10px] font-bold uppercase {{ $trx->status_pembayaran == 'Lunas' ? 'text-green-700' : 'text-red-700' }}">{{ $trx->status_pembayaran }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada antrean verifikasi.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 bg-gray-50">{{ $transaksis->links() }}</div>
    </div>
@endsection