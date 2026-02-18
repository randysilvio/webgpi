@extends('layouts.app')

@section('title', 'Laporan Inventaris Aset')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    {{-- Panel Kontrol --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 print:hidden flex justify-between items-center">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-widest flex items-center">
            <i class="fas fa-boxes mr-3 text-blue-600"></i> Register Inventaris Aktiva Tetap
        </h2>
        <button onclick="window.print()" class="bg-slate-800 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-slate-900 shadow-lg transition-all flex items-center">
            <i class="fas fa-print mr-2"></i> CETAK DAFTAR ASET
        </button>
    </div>

    {{-- Kertas Laporan (Landscape Mode Preferable for Assets) --}}
    <div class="bg-white p-12 md:p-16 shadow-2xl rounded-sm report-document print:shadow-none print:p-0">
        {{-- KOP SURAT FORMAL --}}
        <div class="flex items-center border-b-4 border-black pb-4 mb-1">
            <div class="flex-shrink-0 mr-8 text-center" style="width: 100px;">
                @if ($setting->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->logo_path))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($setting->logo_path) }}" class="h-24 w-auto object-contain mx-auto">
                @else
                    <div class="text-xs font-bold border border-black p-2">LOGO</div>
                @endif
            </div>
            <div class="flex-grow text-center pr-12">
                <h1 class="text-3xl font-serif font-black uppercase tracking-tighter leading-tight text-black">Gereja Protestan Indonesia Di Papua</h1>
                <h2 class="text-xl font-serif font-bold uppercase mb-1 tracking-widest text-black">
                    @if(Auth::user()->hasRole(['Super Admin', 'Admin Sinode'])) SINODE @else {{ Auth::user()->klasisTugas->nama_klasis ?? (Auth::user()->jemaatTugas->nama_jemaat ?? 'PUSAT') }} @endif
                </h2>
                <div class="text-[11px] leading-tight font-medium text-black">
                    {{ $setting->contact_address ?? 'Alamat Kantor Belum Diatur' }}
                </div>
            </div>
        </div>
        <div class="border-b border-black mb-10"></div>

        {{-- JUDUL --}}
        <div class="text-center mb-12 text-black">
            <h3 class="text-xl font-serif font-bold uppercase underline underline-offset-8 decoration-2">Daftar Inventaris & Aset Tetap</h3>
            <p class="text-md font-serif mt-2 font-medium">Posisi Per Tanggal: {{ now()->isoFormat('D MMMM Y') }}</p>
        </div>

        <table class="w-full text-xs font-serif border-collapse border border-black mb-16 text-black">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-black py-4 px-2 text-center w-10 uppercase tracking-tighter">No</th>
                    <th class="border border-black py-4 px-2 text-left uppercase tracking-tighter">Nama Barang / Uraian Aset</th>
                    <th class="border border-black py-4 px-2 text-left w-36 uppercase tracking-tighter">Kode Inventaris</th>
                    <th class="border border-black py-4 px-2 text-center w-28 uppercase tracking-tighter">Kondisi Fisik</th>
                    <th class="border border-black py-4 px-2 text-right w-44 uppercase tracking-tighter">Nilai Perolehan (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black">
                @forelse($asets as $index => $aset)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="border border-black py-3 px-2 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black py-3 px-2 font-bold uppercase">{{ $aset->nama_aset }}</td>
                    <td class="border border-black py-3 px-2 font-mono uppercase tracking-widest text-[10px]">{{ $aset->kode_aset }}</td>
                    <td class="border border-black py-3 px-2 text-center font-bold">{{ strtoupper($aset->kondisi) }}</td>
                    <td class="border border-black py-3 px-2 text-right font-mono">{{ number_format($aset->nilai_perolehan, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-12 text-center italic border border-black text-gray-500 uppercase tracking-widest">Belum ada aset terdaftar.</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-100 font-black">
                <tr class="border-t-2 border-black">
                    <td colspan="4" class="border border-black py-5 px-4 text-right uppercase tracking-widest text-sm">Total Nilai Kekayaan</td>
                    <td class="border border-black py-5 px-2 text-right text-lg font-mono">Rp {{ number_format($asets->sum('nilai_perolehan'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- TTD --}}
        <div class="mt-24 text-black">
            <div class="grid grid-cols-2 gap-x-32 text-sm font-serif">
                <div class="text-center space-y-28">
                    <div>
                        <p class="mb-1 font-bold">Mengetahui,</p>
                        <p class="font-black uppercase tracking-tight leading-tight">Ketua / Pimpinan</p>
                    </div>
                    <div>
                        <p class="border-t-2 border-black inline-block px-14 pt-1 font-black uppercase tracking-widest">..................................................</p>
                    </div>
                </div>
                <div class="text-center space-y-28">
                    <div>
                        <p class="mb-1">&nbsp;</p>
                        <p class="font-black uppercase tracking-tight leading-tight">Pengurus Barang / Aset</p>
                    </div>
                    <div>
                        <p class="border-t-2 border-black inline-block px-14 pt-1 font-black uppercase tracking-widest">..................................................</p>
                    </div>
                </div>
            </div>
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
        .report-document { box-shadow: none !important; border: none !important; width: 100% !important; }
        table, th, td { border-color: black !important; }
    }
</style>
@endsection