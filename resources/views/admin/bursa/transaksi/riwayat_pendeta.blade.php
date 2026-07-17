@extends('layouts.app')

@section('title', 'Riwayat Otorisasi Dokumen')

@section('content')
    <div class="mb-6 flex justify-between items-end border-b-2 border-gray-800 pb-4">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Riwayat Permohonan Dokumen</h2>
            <p class="text-xs text-gray-600 mt-1">Status pengajuan otorisasi pengunduhan dokumen dari Kas Jemaat.</p>
        </div>
        <a href="{{ route('admin.bursa.index') }}" class="bg-blue-800 hover:bg-blue-900 text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wide transition shadow-sm">
            <i class="fas fa-book mr-2"></i> Kembali ke Katalog
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-800 text-xs text-gray-700 uppercase tracking-wider font-bold">
                    <th class="px-6 py-3">No. Register</th>
                    <th class="px-6 py-3">Dokumen</th>
                    <th class="px-6 py-3">Tanggal Pengajuan</th>
                    <th class="px-6 py-3 text-center">Status Berkas</th>
                    <th class="px-6 py-3 text-center">Tindakan</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-200">
                @forelse($transaksis as $trx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-gray-800">{{ $trx->nomor_registrasi }}</td>
                        <td class="px-6 py-4 text-xs font-bold text-gray-900">{{ $trx->materi->judul_dokumen ?? 'Dokumen Ditarik' }}</td>
                        <td class="px-6 py-4 text-xs text-gray-600">{{ $trx->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($trx->status_pembayaran == 'Lunas')
                                <span class="bg-green-100 text-green-800 border border-green-300 px-2 py-1 rounded text-[10px] font-bold uppercase">Otorisasi Disetujui</span>
                            @elseif($trx->status_pembayaran == 'Ditolak')
                                <span class="bg-red-100 text-red-800 border border-red-300 px-2 py-1 rounded text-[10px] font-bold uppercase">Ditolak</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-800 border border-yellow-300 px-2 py-1 rounded text-[10px] font-bold uppercase">Proses Verifikasi</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($trx->status_pembayaran == 'Lunas' && $trx->materi)
                                <a href="{{ route('admin.bursa.download', $trx->materi) }}" class="bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded text-[10px] font-bold uppercase transition"><i class="fas fa-download mr-1"></i> Unduh Berkas</a>
                            @else
                                <span class="text-[10px] text-gray-400 italic">Terkunci</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">Belum ada riwayat permohonan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection