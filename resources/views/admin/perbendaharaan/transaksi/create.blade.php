@extends('layouts.app')

@section('title', 'Catat Kas Baru')

@section('content')
    <x-admin-form 
        title="Formulir Transaksi Kas" 
        action="{{ route('admin.perbendaharaan.transaksi.store') }}" 
        back-route="{{ route('admin.perbendaharaan.transaksi.index') }}"
        has-file="true"
    >
        <div class="space-y-6">
            
            {{-- BAGIAN 1: DETAIL TRANSAKSI --}}
            <div class="bg-slate-50 p-4 rounded border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_transaksi" value="{{ date('Y-m-d') }}" required 
                           class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mata Anggaran (COA) <span class="text-red-500">*</span></label>
                    <select name="mata_anggaran_id" id="mata_anggaran_id" required class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-700">
                        <option value="">-- Pilih Kategori Kas --</option>
                        <optgroup label="PENDAPATAN (PENERIMAAN)">
                            @foreach($mataAnggarans->where('jenis', 'Pendapatan') as $ma)
                                <option value="{{ $ma->id }}">{{ $ma->kode }} - {{ $ma->nama_mata_anggaran }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="BELANJA (PENGELUARAN)">
                            @foreach($mataAnggarans->where('jenis', 'Belanja') as $ma)
                                <option value="{{ $ma->id }}">{{ $ma->kode }} - {{ $ma->nama_mata_anggaran }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>

            {{-- BAGIAN 2: NOMINAL & KETERANGAN --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nominal (Rupiah) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500 font-bold text-sm">Rp</span>
                        </div>
                        <input type="number" name="nominal" required min="1" 
                               class="w-full pl-10 border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 font-mono font-bold text-slate-700" placeholder="0">
                    </div>
                </div>

                <x-form-input label="Nomor Bukti (Opsional)" name="nomor_bukti" placeholder="Cth: KW-2025-001" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Keterangan / Uraian <span class="text-red-500">*</span></label>
                <textarea name="keterangan" rows="3" required 
                          class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 placeholder-slate-400 text-slate-700"
                          placeholder="Jelaskan rincian transaksi secara mendetail..."></textarea>
            </div>

            {{-- FILE UPLOAD --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100">
                <label class="block text-xs font-bold text-blue-700 uppercase mb-1">Upload Bukti Transaksi (Opsional)</label>
                <input type="file" name="file_bukti" accept="image/*,application/pdf"
                       class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-white file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer">
                <p class="mt-2 text-[10px] text-blue-500 italic">Format: JPG, PNG, atau PDF (Maks 2MB).</p>
            </div>

            {{-- NOTIFIKASI REALISASI --}}
            <div id="realization-notice" class="hidden bg-emerald-50 border-l-4 border-emerald-400 p-4 rounded shadow-sm flex items-start gap-3">
                <i class="fas fa-chart-line text-emerald-500 mt-0.5"></i>
                <div class="text-xs text-emerald-800">
                    <strong>Integrasi Otomatis:</strong> Transaksi ini akan langsung tercatat sebagai <em>realisasi</em> pada <strong>Rencana APB</strong> tahun berjalan untuk mata anggaran yang dipilih.
                </div>
            </div>

        </div>
    </x-admin-form>

    @push('scripts')
    <script>
        document.getElementById('mata_anggaran_id').addEventListener('change', function() {
            const notice = document.getElementById('realization-notice');
            if (this.value) {
                notice.classList.remove('hidden');
            } else {
                notice.classList.add('hidden');
            }
        });
    </script>
    @endpush
@endsection