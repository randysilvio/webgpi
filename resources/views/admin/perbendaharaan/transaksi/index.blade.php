@extends('admin.layout')

@section('title', 'Buku Kas Umum')
@section('header-title', 'Buku Kas Umum (BKU)')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-xl font-semibold text-gray-800">Catatan Transaksi Keuangan</h2>
        <a href="{{ route('admin.perbendaharaan.transaksi.create') }}" class="bg-primary hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 inline-flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Catat Kas Baru
        </a>
    </div>

    {{-- Filter Sederhana --}}
    <form method="GET" action="{{ route('admin.perbendaharaan.transaksi.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg border">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Ket / No Bukti..." class="border-gray-300 rounded-md text-sm">
        <select name="bulan" class="border-gray-300 rounded-md text-sm">
            <option value="">Semua Bulan</option>
            @for($i=1; $i<=12; $i++)
                <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
            @endfor
        </select>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-900 text-sm font-bold">Filter</button>
        <a href="{{ route('admin.perbendaharaan.transaksi.index') }}" class="text-center py-2 text-sm text-gray-500 hover:underline">Reset</a>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kode / Mata Anggaran</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Keterangan</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Penerimaan (Rp)</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Pengeluaran (Rp)</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($transaksis as $trx)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm whitespace-nowrap">{{ $trx->tanggal_transaksi->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="font-mono font-bold text-blue-700">{{ $trx->mataAnggaran->kode }}</span><br>
                        <span class="text-xs text-gray-500">{{ $trx->mataAnggaran->nama_mata_anggaran }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="font-medium text-gray-900">{{ $trx->keterangan }}</div>
                        <div class="text-xs text-gray-400">Bukti: {{ $trx->nomor_bukti ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-green-600">
                        {{ $trx->mataAnggaran->jenis == 'Pendapatan' ? number_format($trx->nominal, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-red-600">
                        {{ $trx->mataAnggaran->jenis == 'Belanja' ? number_format($trx->nominal, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center space-x-2">
                            @if($trx->file_bukti_path)
                                <a href="{{ Storage::url($trx->file_bukti_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900"><i class="fas fa-file-invoice"></i></a>
                            @endif
                            <form action="{{ route('admin.perbendaharaan.transaksi.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Batalkan transaksi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">Belum ada transaksi bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $transaksis->links() }}</div>
</div>
@endsection