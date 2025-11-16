<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pos Anggaran & Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $anggaran->nama_pos_anggaran }}</h3>
                        <p class="text-sm text-gray-500">{{ ucfirst($anggaran->jenis_anggaran) }} | {{ $anggaran->tahun_anggaran }} | {{ $anggaran->jenisWadah->nama_wadah }}</p>
                        @if($anggaran->programKerja)
                            <p class="text-sm text-blue-600 mt-1">Program: {{ $anggaran->programKerja->nama_program }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Target Anggaran</p>
                        <p class="text-xl font-bold">Rp {{ number_format($anggaran->jumlah_target, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex justify-between text-sm mb-1">
                        <span>Realisasi: <strong>Rp {{ number_format($anggaran->jumlah_realisasi, 0, ',', '.') }}</strong></span>
                        <span>{{ $anggaran->selisih >= 0 ? 'Sisa: ' : 'Over: ' }} Rp {{ number_format(abs($anggaran->selisih), 0, ',', '.') }}</span>
                    </div>
                    @php
                        $persen = $anggaran->jumlah_target > 0 ? ($anggaran->jumlah_realisasi / $anggaran->jumlah_target) * 100 : 0;
                        $color = $persen >= 100 ? 'green' : 'blue';
                        if($anggaran->jenis_anggaran == 'pengeluaran' && $persen > 100) $color = 'red';
                    @endphp
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-{{ $color }}-600 h-4 rounded-full transition-all duration-500" style="width: {{ min($persen, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h4 class="font-bold text-lg mb-4">Catat Transaksi Baru</h4>
                        
                        @if(session('success'))
                            <div class="mb-4 bg-green-100 text-green-700 px-3 py-2 rounded text-sm">{{ session('success') }}</div>
                        @endif

                        <form method="POST" action="{{ route('admin.wadah.transaksi.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="anggaran_id" value="{{ $anggaran->id }}">

                            <div class="mb-3">
                                <x-input-label for="tanggal_transaksi" :value="__('Tanggal')" />
                                <x-text-input id="tanggal_transaksi" class="block mt-1 w-full" type="date" name="tanggal_transaksi" :value="date('Y-m-d')" required />
                            </div>

                            <div class="mb-3">
                                <x-input-label for="jumlah" :value="__('Jumlah (Rp)')" />
                                <x-text-input id="jumlah" class="block mt-1 w-full" type="number" name="jumlah" required />
                            </div>

                            <div class="mb-3">
                                <x-input-label for="uraian" :value="__('Uraian / Keterangan')" />
                                <textarea id="uraian" name="uraian" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: Terima dari sektor A"></textarea>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="bukti_transaksi" :value="__('Bukti (Foto/PDF) - Opsional')" />
                                <input type="file" name="bukti_transaksi" class="block mt-1 w-full text-sm border border-gray-300 rounded cursor-pointer bg-gray-50">
                            </div>

                            <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded shadow-sm">
                                Simpan Transaksi
                            </button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h4 class="font-bold text-lg mb-4">Riwayat Transaksi</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Uraian</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($anggaran->transaksi as $t)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                                {{ $t->tanggal_transaksi->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                {{ $t->uraian }}
                                                @if($t->bukti_transaksi)
                                                    <a href="{{ Storage::url($t->bukti_transaksi) }}" target="_blank" class="text-blue-600 text-xs ml-1 hover:underline">[Lihat Bukti]</a>
                                                @endif
                                                <div class="text-xs text-gray-400 mt-0.5">Oleh: {{ $t->user->name ?? 'System' }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 text-right font-mono">
                                                Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-center">
                                                <form action="{{ route('admin.wadah.transaksi.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini? Saldo akan disesuaikan kembali.');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada transaksi.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>