@extends('layouts.app')

@section('title', 'Buku Kas Umum')

@section('content')
    <x-admin-index 
        title="Buku Kas Umum (BKU)" 
        subtitle="Pencatatan harian transaksi penerimaan dan pengeluaran kas."
        create-route="{{ route('admin.perbendaharaan.transaksi.create') }}"
        create-label="Catat Kas Baru"
        :pagination="$transaksis"
    >
        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.perbendaharaan.transaksi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                {{-- Filter Bulan --}}
                <div class="relative">
                    <i class="fas fa-calendar absolute left-3 top-2.5 text-slate-400"></i>
                    <select name="bulan" onchange="this.form.submit()" class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-600 font-medium">
                        <option value="">- Semua Bulan -</option>
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Filter Tahun (Optional jika controller mendukung) --}}
                {{-- <x-form-select name="tahun" ... /> --}}

                {{-- Pencarian --}}
                <div class="md:col-span-2 relative">
                    <x-form-input name="search" value="{{ request('search') }}" placeholder="Cari Uraian / No. Bukti..." />
                    <button type="submit" class="absolute right-3 top-2 text-slate-400 hover:text-blue-600">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                {{-- Reset --}}
                <div>
                    <a href="{{ route('admin.perbendaharaan.transaksi.index') }}" class="inline-flex items-center justify-center w-full py-2 border border-slate-300 rounded-lg text-sm font-bold text-slate-600 hover:bg-slate-50 transition">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4 w-32">Tanggal</th>
            <th class="px-6 py-4">Kode & Uraian</th>
            <th class="px-6 py-4 text-right">Penerimaan (Rp)</th>
            <th class="px-6 py-4 text-right">Pengeluaran (Rp)</th>
            <th class="px-6 py-4 text-center">Bukti & Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse($transaksis as $trx)
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <div class="font-bold text-slate-700 text-sm">{{ $trx->tanggal_transaksi->format('d/m/Y') }}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $trx->tanggal_transaksi->format('H:i') }}</div>
                </x-td>
                <x-td>
                    <div class="flex items-start gap-3">
                        <span class="font-mono font-bold text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded border border-slate-200 mt-0.5">
                            {{ $trx->mataAnggaran->kode }}
                        </span>
                        <div>
                            <div class="font-bold text-slate-800 text-sm">{{ $trx->keterangan }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                Akun: {{ $trx->mataAnggaran->nama_mata_anggaran }}
                                @if($trx->nomor_bukti)
                                    <span class="mx-1 text-slate-300">|</span> No. Bukti: <span class="font-mono text-slate-600">{{ $trx->nomor_bukti }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-td>
                <x-td class="text-right">
                    @if($trx->mataAnggaran->jenis == 'Pendapatan')
                        <span class="font-mono font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">
                            {{ number_format($trx->nominal, 0, ',', '.') }}
                        </span>
                    @else
                        <span class="text-slate-300">-</span>
                    @endif
                </x-td>
                <x-td class="text-right">
                    @if($trx->mataAnggaran->jenis == 'Belanja')
                        <span class="font-mono font-bold text-red-600 bg-red-50 px-2 py-1 rounded">
                            {{ number_format($trx->nominal, 0, ',', '.') }}
                        </span>
                    @else
                        <span class="text-slate-300">-</span>
                    @endif
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        @if($trx->file_bukti_path)
                            <a href="{{ Storage::url($trx->file_bukti_path) }}" target="_blank" class="text-blue-500 hover:text-blue-700 p-1" title="Lihat Bukti Lampiran">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                        @endif
                        
                        <form action="{{ route('admin.perbendaharaan.transaksi.destroy', $trx->id) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan transaksi ini? Saldo akan disesuaikan kembali.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 p-1 transition" title="Batalkan Transaksi">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </x-td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                    Belum ada transaksi pada periode ini.
                </td>
            </tr>
        @endforelse

    </x-admin-index>
@endsection