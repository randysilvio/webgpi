@extends('layouts.app')

@section('title', 'Tinjauan Detail Pos Anggaran')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    {{-- Header --}}
    <div class="flex items-center justify-between border-b-2 border-gray-800 pb-4 mb-6">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Detail Dokumen Anggaran</h2>
            <p class="text-xs text-gray-600 mt-1">Evaluasi rincian pos dan riwayat arus kas kategorial.</p>
        </div>
        <a href="{{ route('admin.wadah.anggaran.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
        </a>
    </div>

    {{-- KARTU RINGKASAN POS ANGGARAN --}}
    <div class="bg-white rounded border border-gray-300 shadow-sm p-8 relative overflow-hidden border-l-8 border-l-blue-800">
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-widest mb-3 leading-tight">{{ $anggaran->nama_pos_anggaran }}</h1>
                <div class="flex flex-wrap gap-2 mb-5">
                    <span class="bg-gray-100 text-gray-800 text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded border border-gray-300">
                        TAHUN BUKU {{ $anggaran->tahun_anggaran }}
                    </span>
                    <span class="bg-blue-50 text-blue-800 text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded border border-blue-200">
                        {{ $anggaran->jenisWadah->nama_wadah }} ({{ strtoupper($anggaran->tingkat) }})
                    </span>
                    <span class="bg-{{ $anggaran->jenis_anggaran == 'penerimaan' ? 'green' : 'red' }}-50 text-{{ $anggaran->jenis_anggaran == 'penerimaan' ? 'green' : 'red' }}-800 text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded border border-{{ $anggaran->jenis_anggaran == 'penerimaan' ? 'green' : 'red' }}-200">
                        {{ $anggaran->jenis_anggaran == 'penerimaan' ? 'ARUS KAS MASUK' : 'ARUS KAS KELUAR' }}
                    </span>
                </div>
                
                @if($anggaran->program_kerja_id)
                    <div class="bg-gray-50 border border-gray-200 p-4 rounded mt-4">
                        <span class="block text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1 border-b border-gray-200 pb-1">Terkait Agenda Program</span>
                        <a href="{{ route('admin.wadah.program.edit', $anggaran->program_kerja_id) }}" class="text-blue-800 hover:underline font-bold text-xs flex items-center uppercase">
                            <i class="fas fa-link mr-2"></i> {{ $anggaran->programKerja->nama_program }}
                        </a>
                    </div>
                @endif
                
                @if($anggaran->keterangan)
                    <p class="text-xs text-gray-600 mt-4 leading-relaxed italic border-l-2 border-gray-300 pl-3">"{{ $anggaran->keterangan }}"</p>
                @endif
            </div>

            <div class="bg-gray-50 p-6 rounded border border-gray-200 flex flex-col justify-center">
                <div class="mb-4 border-b border-gray-200 pb-3">
                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Target Nominal (RAB)</span>
                    <span class="font-mono font-black text-gray-900 text-xl">Rp {{ number_format($anggaran->jumlah_target, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Akumulasi Realisasi</span>
                    <span class="font-mono font-black text-{{ $anggaran->jenis_anggaran == 'penerimaan' ? 'green-700' : 'red-700' }} text-2xl">Rp {{ number_format($anggaran->jumlah_realisasi, 0, ',', '.') }}</span>
                </div>
                
                @php
                    $persen = $anggaran->jumlah_target > 0 ? ($anggaran->jumlah_realisasi / $anggaran->jumlah_target) * 100 : 0;
                    $barColor = $anggaran->jenis_anggaran == 'penerimaan' ? 'bg-green-600' : 'bg-red-600';
                @endphp
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <div class="flex items-center justify-between text-[10px] font-bold text-gray-600 mb-1.5 uppercase">
                        <span>Serapan Anggaran</span>
                        <span>{{ number_format($persen, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded h-1.5 overflow-hidden border border-gray-300">
                        <div class="{{ $barColor }} h-1.5" style="width: {{ min($persen, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL RIWAYAT TRANSAKSI --}}
    <div class="bg-white border border-gray-300 rounded shadow-sm overflow-hidden mt-8 border-t-4 border-t-gray-800">
        <div class="bg-gray-100 px-6 py-4 flex justify-between items-center border-b border-gray-200">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest"><i class="fas fa-list-ul mr-2 text-gray-500"></i> Riwayat Pencatatan Transaksi</h3>
            
            {{-- TOMBOL PEMICU MODAL --}}
            <button type="button" onclick="toggleModal('modalTransaksi')" class="bg-gray-800 text-white text-[10px] font-bold uppercase tracking-widest px-4 py-2 rounded hover:bg-gray-900 transition shadow-sm">
                <i class="fas fa-plus mr-1"></i> Catat Transaksi
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                        <th class="px-6 py-3 w-32 text-center">Tanggal Nota</th>
                        <th class="px-6 py-3">Uraian Pencatatan / Bukti Dokumen</th>
                        <th class="px-6 py-3 text-right w-40">Nominal Arus Kas</th>
                        <th class="px-6 py-3 text-center w-24">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($anggaran->transaksi as $t)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 text-center align-top">
                                <span class="font-mono text-[11px] font-bold text-gray-700">{{ \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-6 py-3 align-top">
                                <div class="font-bold text-gray-800 text-xs mb-1 uppercase">{{ $t->uraian ?? $t->keterangan }}</div>
                                @if($t->bukti_transaksi)
                                    <a href="{{ Storage::url($t->bukti_transaksi) }}" target="_blank" class="inline-flex items-center text-[9px] font-black text-blue-800 uppercase tracking-widest hover:text-blue-600 transition">
                                        <i class="fas fa-paperclip mr-1 text-gray-400"></i> Lihat Berkas Bukti Transaksi
                                    </a>
                                @else
                                    <span class="text-[9px] font-bold text-gray-400 italic uppercase tracking-widest">Tanpa Berkas Fisik</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right align-top">
                                <span class="font-mono font-black text-gray-900 text-xs">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-3 text-center align-top">
                                <form action="{{ route('admin.wadah.transaksi.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Peringatan: Menghapus catatan transaksi ini akan mengurangi saldo realisasi pos anggaran secara otomatis. Lanjutkan?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-700 transition" title="Batalkan/Hapus Transaksi">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 text-sm">
                                <i class="fas fa-receipt text-3xl mb-3 block text-gray-300"></i>
                                Belum ada transaksi yang dibebankan pada pos anggaran ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL FORM CATAT TRANSAKSI --}}
    <div id="modalTransaksi" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-gray-900/70 backdrop-blur-sm transition-opacity p-4">
        <div class="bg-white rounded shadow-2xl w-full max-w-lg border border-gray-300 overflow-hidden scale-95 transform transition-transform" id="modalContent">
            
            <div class="bg-gray-100 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest"><i class="fas fa-file-invoice-dollar mr-2 text-gray-500"></i> Form Catat Transaksi</h3>
                <button type="button" onclick="toggleModal('modalTransaksi')" class="text-gray-400 hover:text-red-600 transition"><i class="fas fa-times text-lg"></i></button>
            </div>
            
            <form action="{{ route('admin.wadah.transaksi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="anggaran_id" value="{{ $anggaran->id }}">
                
                <div class="p-6 space-y-5">
                    <div class="bg-blue-50 border border-blue-200 p-3 rounded">
                        <p class="text-[10px] font-bold text-blue-800 uppercase tracking-widest">
                            <i class="fas fa-info-circle mr-1"></i> Arus Kas: {{ $anggaran->jenis_anggaran == 'penerimaan' ? 'PEMASUKAN / PENERIMAAN' : 'PENGELUARAN / BELANJA' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tanggal Transaksi <span class="text-red-600">*</span></label>
                        <input type="date" name="tanggal_transaksi" required class="w-full border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Uraian / Keterangan Transaksi <span class="text-red-600">*</span></label>
                        <input type="text" name="uraian" required placeholder="Contoh: Pembelian konsumsi / Penerimaan iuran..." class="w-full border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white uppercase">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nominal Transaksi (Rp) <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold text-[10px]">Rp</span>
                            <input type="number" name="jumlah" required placeholder="0" min="0" class="w-full pl-9 pr-3 py-2 border-gray-300 rounded text-sm font-mono text-right focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Upload Bukti Nota / Dokumen (Opsional)</label>
                        <input type="file" name="bukti_transaksi" accept=".jpg,.jpeg,.png,.pdf" class="w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white p-1">
                        <p class="text-[9px] text-gray-500 mt-1 uppercase tracking-widest font-bold">Maksimal 2MB (Hanya format JPG, PNG, PDF).</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleModal('modalTransaksi')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm hover:bg-gray-100 transition">Batalkan</button>
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm transition flex items-center"><i class="fas fa-save mr-2"></i> Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Script untuk membuka dan menutup Modal Pop-up dengan animasi halus
    function toggleModal(modalID) {
        const modal = document.getElementById(modalID);
        const content = document.getElementById('modalContent');
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        } else {
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }
    }
</script>
@endpush
@endsection