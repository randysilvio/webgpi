@extends('layouts.app')

@section('title', 'Dokumen Register Aset')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    {{-- Panel Kontrol --}}
    <div class="bg-white p-4 rounded border border-gray-300 shadow-sm print:hidden flex justify-between items-center">
        <h2 class="text-[10px] font-black text-gray-800 uppercase tracking-widest flex items-center">
            <i class="fas fa-boxes mr-3 text-gray-500 text-lg"></i> Register Inventaris Aktiva Tetap
        </h2>
        <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded text-[10px] font-bold uppercase tracking-widest hover:bg-gray-900 transition flex items-center shadow-sm">
            <i class="fas fa-print mr-2"></i> Cetak Daftar Aset
        </button>
    </div>

    {{-- Kertas Laporan Cetak (Landscape) --}}
    <div class="bg-white p-12 md:p-16 border border-gray-200 rounded report-document print:shadow-none print:border-none print:p-0">
        
        {{-- KOP SURAT FORMAL --}}
        <div class="flex items-center border-b-[3px] border-black pb-4 mb-1">
            <div class="flex-shrink-0 mr-6 text-center" style="width: 80px;">
                @if ($setting->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->logo_path))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($setting->logo_path) }}" class="h-20 w-auto object-contain mx-auto">
                @else
                    <div class="text-[9px] font-bold border border-black p-2">LOGO</div>
                @endif
            </div>
            <div class="flex-grow text-center pr-12">
                <h1 class="text-2xl font-serif font-black uppercase tracking-tight leading-tight text-black">Gereja Protestan Indonesia Di Papua</h1>
                <h2 class="text-lg font-serif font-bold uppercase mb-1 tracking-widest text-black">
                    @if(Auth::user()->hasRole(['Super Admin', 'Admin Sinode'])) MAJELIS PEKERJA SINODE @else {{ Auth::user()->klasisTugas->nama_klasis ?? (Auth::user()->jemaatTugas->nama_jemaat ?? 'INSTANSI PUSAT') }} @endif
                </h2>
                <div class="text-[10px] leading-tight font-medium text-black uppercase">
                    {{ $setting->contact_address ?? 'Alamat Belum Diatur' }}
                </div>
            </div>
        </div>
        <div class="border-b border-black mb-8"></div>

        {{-- JUDUL LAPORAN --}}
        <div class="text-center mb-10 text-black">
            <h3 class="text-lg font-serif font-black uppercase underline underline-offset-4">Daftar Inventaris & Aset Tetap</h3>
            <p class="text-[11px] font-serif mt-1 font-bold">Posisi Per Tanggal: {{ now()->isoFormat('D MMMM Y') }}</p>
        </div>

        <table class="w-full text-[11px] font-serif border-collapse border border-black mb-12 text-black">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-black py-2 px-2 text-center w-10 uppercase">No.</th>
                    <th class="border border-black py-2 px-2 text-left uppercase">Nama Barang / Uraian Aset</th>
                    <th class="border border-black py-2 px-2 text-left w-32 uppercase">Kode Inventaris</th>
                    <th class="border border-black py-2 px-2 text-center w-24 uppercase">Kondisi Fisik</th>
                    <th class="border border-black py-2 px-2 text-right w-40 uppercase">Nilai Perolehan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asets as $index => $aset)
                <tr>
                    <td class="border border-black py-2 px-2 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black py-2 px-2 font-bold uppercase">{{ $aset->nama_aset }}</td>
                    <td class="border border-black py-2 px-2 font-mono uppercase text-[9px]">{{ $aset->kode_aset }}</td>
                    <td class="border border-black py-2 px-2 text-center font-bold">{{ strtoupper($aset->kondisi) }}</td>
                    <td class="border border-black py-2 px-2 text-right">{{ number_format($aset->nilai_perolehan, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-8 text-center italic border border-black text-gray-500 text-[10px]">Data inventaris kosong.</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-100 font-black">
                <tr>
                    <td colspan="4" class="border border-black py-3 px-4 text-right uppercase text-[10px] tracking-widest">Total Akumulasi Nilai Aset Tetap</td>
                    <td class="border border-black py-3 px-2 text-right text-sm">Rp {{ number_format($asets->sum('nilai_perolehan'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- AREA TANDA TANGAN --}}
        <div class="mt-16 text-black">
            <div class="grid grid-cols-2 gap-x-20 text-xs font-serif">
                <div class="text-center space-y-24">
                    <div>
                        <p class="mb-1 font-bold">Mengetahui,</p>
                        <p class="font-black uppercase tracking-tight">Pimpinan / Ketua Majelis</p>
                    </div>
                    <div>
                        <p class="border-t border-black inline-block px-12 pt-1 font-black uppercase tracking-widest">(.........................................)</p>
                    </div>
                </div>
                <div class="text-center space-y-24">
                    <div>
                        <p class="mb-1 italic">&nbsp;</p>
                        <p class="font-black uppercase tracking-tight">Otorisator Pengurus Barang (Aset)</p>
                    </div>
                    <div>
                        <p class="border-t border-black inline-block px-12 pt-1 font-black uppercase tracking-widest">(.........................................)</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-10 text-[8px] italic text-gray-500 print:block hidden border-t border-gray-300 pt-2 text-right">
            Sistem Informasi Manajemen Terpadu (SIM-GPI) | {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</div>

<style>
    @media print {
        @page { size: A4 landscape; margin: 1.5cm; }
        body { background: white !important; font-family: "Times New Roman", Times, serif !important; color: black !important; }
        aside, header, footer, .print\:hidden { display: none !important; }
        main { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .flex-1 { margin-left: 0 !important; }
        .report-document { box-shadow: none !important; border: none !important; width: 100% !important; padding: 0 !important; }
    }
</style>
@endsection