@extends('layouts.app')
@section('title', 'Buku Kas Jemaat')
@section('content')
<div class="space-y-6">

    {{-- KONTROL & FILTER --}}
    <div class="bg-white p-5 rounded border border-gray-300 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4 border-l-4 border-l-green-700">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest">Buku Kas Jemaat</h2>
            <p class="text-xs text-gray-600 mt-1">Catatan Transaksi Keuangan Harian</p>
        </div>
        
        <form method="GET" class="flex gap-2 w-full md:w-auto">
            @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin Sinode', 'Admin Klasis']))
            <select name="jemaat_id" class="border-gray-300 rounded text-xs font-bold shadow-sm" onchange="this.form.submit()">
                <option value="">- Pilih Jemaat -</option>
                @foreach($jemaatList as $j) <option value="{{ $j->id }}" {{ $jemaatId == $j->id ? 'selected' : '' }}>{{ $j->nama_jemaat }}</option> @endforeach
            </select>
            @endif
            <select name="bulan" class="border-gray-300 rounded text-xs font-bold shadow-sm" onchange="this.form.submit()">
                @foreach(range(1, 12) as $m) <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == $m ? 'selected' : '' }}>Bulan {{ $m }}</option> @endforeach
            </select>
            <input type="number" name="tahun" value="{{ $tahun }}" class="w-20 border-gray-300 rounded text-xs font-bold shadow-sm" onchange="this.form.submit()">
            
            {{-- TOMBOL CETAK LANGSUNG DI INDEX --}}
            <button type="submit" name="print" value="1" formtarget="_blank" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded text-[10px] font-bold uppercase transition flex items-center shadow-sm" {{ !$jemaatId ? 'disabled' : '' }}>
                <i class="fas fa-print mr-2"></i> Cetak Laporan
            </button>
        </form>
    </div>

    {{-- FORM TAMBAH TRANSAKSI (Hanya Untuk Jemaat) --}}
    @if(Auth::user()->hasRole('Admin Jemaat'))
    <form action="{{ route('admin.keuangan-jemaat.store') }}" method="POST" class="bg-gray-50 p-4 rounded border border-gray-200 shadow-sm flex flex-wrap gap-3 items-end">
        @csrf
        <div class="flex-grow"><label class="text-[9px] font-bold uppercase text-gray-600 block mb-1">Tanggal</label><input type="date" name="tanggal" required class="w-full text-xs rounded border-gray-300"></div>
        <div class="flex-grow"><label class="text-[9px] font-bold uppercase text-gray-600 block mb-1">Uraian / Majelis</label><input type="text" name="uraian" required class="w-full text-xs rounded border-gray-300" placeholder="Keterangan..."></div>
        <div class="w-24"><label class="text-[9px] font-bold uppercase text-gray-600 block mb-1">Kode Ayat</label><input type="text" name="kode_ayat" class="w-full text-xs rounded border-gray-300"></div>
        <div class="w-32"><label class="text-[9px] font-bold uppercase text-gray-600 block mb-1">Jenis</label><select name="jenis_transaksi" class="w-full text-xs rounded border-gray-300"><option value="Pemasukan">Terima (+)</option><option value="Pengeluaran">Keluar (-)</option></select></div>
        <div class="flex-grow"><label class="text-[9px] font-bold uppercase text-gray-600 block mb-1">Nominal (Rp)</label><input type="number" name="nominal" required class="w-full text-xs rounded border-gray-300"></div>
        <input type="hidden" name="kategori_kas" value="{{ $kategori }}">
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-[10px] font-bold uppercase hover:bg-gray-900"><i class="fas fa-save mr-1"></i> Simpan</button>
    </form>
    @endif

    {{-- TABEL BUKU KAS (Sesuai Format Excel) --}}
    <div class="bg-white border border-gray-300 rounded shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-800 uppercase tracking-widest text-[9px] text-gray-600">
                    <th class="px-4 py-3 border-r border-gray-200 w-10 text-center">No</th>
                    <th class="px-4 py-3 border-r border-gray-200 w-24">Tanggal</th>
                    <th class="px-4 py-3 border-r border-gray-200">Uraian</th>
                    <th class="px-4 py-3 border-r border-gray-200 w-20 text-center">Kode Ayat</th>
                    <th class="px-4 py-3 border-r border-gray-200 w-32 text-right">Terima (Rp)</th>
                    <th class="px-4 py-3 border-r border-gray-200 w-32 text-right">Keluar (Rp)</th>
                    <th class="px-4 py-3 w-32 text-right text-gray-900">Saldo (Rp)</th>
                    @if(Auth::user()->hasRole('Admin Jemaat')) <th class="px-2 py-3 text-center">Aksi</th> @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 font-mono">
                {{-- Baris Saldo Awal --}}
                <tr class="bg-blue-50/50">
                    <td colspan="6" class="px-4 py-3 font-bold text-right text-blue-900 uppercase text-[10px]">Saldo Bulan Sebelumnya</td>
                    <td class="px-4 py-3 font-black text-right text-blue-900">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
                    @if(Auth::user()->hasRole('Admin Jemaat')) <td></td> @endif
                </tr>

                {{-- Loop Transaksi --}}
                @foreach($transaksiBulanIni as $index => $trx)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-center border-r border-gray-200 text-[10px] text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 border-r border-gray-200">{{ $trx->tanggal->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 border-r border-gray-200 font-sans text-sm">{{ $trx->uraian }}</td>
                    <td class="px-4 py-3 border-r border-gray-200 text-center text-gray-500">{{ $trx->kode_ayat ?? '-' }}</td>
                    <td class="px-4 py-3 border-r border-gray-200 text-right text-green-700">{{ $trx->jenis_transaksi == 'Pemasukan' ? number_format($trx->nominal, 0, ',', '.') : '-' }}</td>
                    <td class="px-4 py-3 border-r border-gray-200 text-right text-red-600">{{ $trx->jenis_transaksi == 'Pengeluaran' ? number_format($trx->nominal, 0, ',', '.') : '-' }}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900">{{ number_format($trx->saldo_berjalan, 0, ',', '.') }}</td>
                    
                    @if(Auth::user()->hasRole('Admin Jemaat'))
                    <td class="px-2 py-3 text-center">
                        <form action="{{ route('admin.keuangan-jemaat.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                            @csrf @method('DELETE') <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash text-[10px]"></i></button>
                        </form>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection