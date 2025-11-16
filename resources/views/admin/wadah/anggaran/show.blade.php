@extends('admin.layout')

@section('title', 'Detail Anggaran')
@section('header-title', 'Detail Pos & Transaksi')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.wadah.anggaran.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Anggaran
        </a>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6 border border-gray-200">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $anggaran->nama_pos_anggaran }}</h3>
                <div class="text-sm text-gray-500 mt-1 flex flex-wrap gap-2">
                    <span class="px-2 py-0.5 bg-gray-100 rounded">{{ ucfirst($anggaran->jenis_anggaran) }}</span>
                    <span class="px-2 py-0.5 bg-gray-100 rounded">{{ $anggaran->tahun_anggaran }}</span>
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded">{{ $anggaran->jenisWadah->nama_wadah }}</span>
                </div>
                @if($anggaran->programKerja)
                    <p class="text-sm text-blue-600 mt-2"><i class="fas fa-link mr-1"></i> Program: {{ $anggaran->programKerja->nama_program }}</p>
                @endif
                @if($anggaran->keterangan)
                    <p class="text-sm text-gray-600 mt-2 italic">"{{ $anggaran->keterangan }}"</p>
                @endif
            </div>
            <div class="text-left md:text-right bg-gray-50 p-4 rounded-lg border border-gray-100 min-w-[200px]">
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Target Anggaran</p>
                <p class="text-2xl font-bold text-gray-800 font-mono">Rp {{ number_format($anggaran->jumlah_target, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="mt-8">
            <div class="flex justify-between text-sm mb-2 font-medium">
                <span class="text-blue-700">Realisasi: Rp {{ number_format($anggaran->jumlah_realisasi, 0, ',', '.') }}</span>
                <span class="text-gray-600">
                    {{ $anggaran->selisih >= 0 ? 'Sisa Target: ' : 'Surplus/Over: ' }} 
                    Rp {{ number_format(abs($anggaran->selisih), 0, ',', '.') }}
                </span>
            </div>
            @php
                $persen = $anggaran->jumlah_target > 0 ? ($anggaran->jumlah_realisasi / $anggaran->jumlah_target) * 100 : 0;
                $color = $persen >= 100 ? 'green' : 'blue';
                if($anggaran->jenis_anggaran == 'pengeluaran' && $persen > 100) $color = 'red';
            @endphp
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div class="bg-{{ $color }}-500 h-4 rounded-full transition-all duration-1000 ease-out" style="width: {{ min($persen, 100) }}%"></div>
            </div>
            <div class="text-right text-xs text-gray-500 mt-1 font-bold">{{ round($persen, 1) }}% Tercapai</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200 sticky top-4">
                <h4 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2">
                    <i class="fas fa-plus-circle mr-2 text-primary"></i> Catat Transaksi
                </h4>
                
                @if(session('success'))
                    <div class="mb-4 bg-green-50 text-green-700 px-3 py-2 rounded border border-green-200 text-sm flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.wadah.transaksi.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="anggaran_id" value="{{ $anggaran->id }}">

                    <div class="mb-4">
                        <label for="tanggal_transaksi" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input id="tanggal_transaksi" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" type="date" name="tanggal_transaksi" value="{{ date('Y-m-d') }}" required />
                    </div>

                    <div class="mb-4">
                        <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
                        <input id="jumlah" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" type="number" name="jumlah" required placeholder="Contoh: 500000" />
                    </div>

                    <div class="mb-4">
                        <label for="uraian" class="block text-sm font-medium text-gray-700 mb-1">Uraian / Keterangan</label>
                        <textarea id="uraian" name="uraian" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required placeholder="Contoh: Terima dari Sektor A"></textarea>
                    </div>

                    <div class="mb-6">
                        <label for="bukti_transaksi" class="block text-sm font-medium text-gray-700 mb-1">Bukti (Foto/PDF)</label>
                        <input type="file" name="bukti_transaksi" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer">
                        <p class="text-xs text-gray-500 mt-1">Opsional. Max 2MB.</p>
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-blue-800 text-white font-bold py-2.5 px-4 rounded shadow-sm transition duration-150">
                        Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h4 class="font-bold text-lg text-gray-800">Riwayat Transaksi</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uraian</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($anggaran->transaksi as $t)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                        {{ $t->tanggal_transaksi->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="font-medium">{{ $t->uraian }}</div>
                                        @if($t->bukti_transaksi)
                                            <a href="{{ Storage::url($t->bukti_transaksi) }}" target="_blank" class="text-blue-600 text-xs inline-flex items-center mt-1 hover:underline">
                                                <i class="fas fa-paperclip mr-1"></i> Lihat Bukti
                                            </a>
                                        @endif
                                        <div class="text-xs text-gray-400 mt-1">Input oleh: {{ $t->user->name ?? 'System' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-right font-mono font-bold">
                                        Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <form action="{{ route('admin.wadah.transaksi.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus transaksi ini? Saldo akan otomatis disesuaikan kembali.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Hapus Transaksi">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">
                                        Belum ada transaksi yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection