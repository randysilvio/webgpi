@extends('layouts.app')

@section('title', 'Dokumen LRA')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    {{-- Panel Kontrol --}}
    <div class="bg-white p-4 rounded border border-gray-300 shadow-sm print:hidden flex justify-between items-center">
        <form method="GET" class="flex items-center space-x-3">
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Tahun Buku Cetak:</label>
            <select name="tahun" onchange="this.form.submit()" class="border-gray-300 rounded text-xs focus:ring-gray-800 focus:border-gray-800 font-bold text-gray-800 uppercase">
                @for($i = date('Y')-1; $i <= date('Y')+1; $i++)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>T.A. {{ $i }}</option>
                @endfor
            </select>
        </form>
        <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded text-[10px] font-bold uppercase tracking-widest hover:bg-gray-900 transition flex items-center shadow-sm">
            <i class="fas fa-print mr-2"></i> Cetak LRA
        </button>
    </div>

    {{-- Kertas Laporan Cetak (Strict Formatting) --}}
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
                    {{ $setting->contact_address ?? 'Alamat Belum Diatur' }} <br>
                    Telp: {{ $setting->contact_phone ?? '-' }} | Email: {{ $setting->contact_email ?? '-' }}
                </div>
            </div>
        </div>
        <div class="border-b border-black mb-8"></div> 

        {{-- JUDUL LAPORAN --}}
        <div class="text-center mb-10 text-black">
            <h3 class="text-lg font-serif font-black uppercase underline underline-offset-4">Laporan Realisasi Anggaran (LRA)</h3>
            <p class="text-sm font-serif mt-1 font-bold">TAHUN BUKU {{ $tahun }}</p>
        </div>

        @foreach(['Pendapatan', 'Belanja'] as $jenis)
        <div class="mb-10">
            <h4 class="text-[11px] font-black mb-2 uppercase tracking-widest text-black">I. KELOMPOK {{ strtoupper($jenis) }}</h4>
            <table class="w-full text-[11px] font-serif border-collapse border border-black text-black">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-black py-2 px-2 text-center w-20 uppercase">Kode</th>
                        <th class="border border-black py-2 px-2 text-left uppercase">Uraian Mata Anggaran</th>
                        <th class="border border-black py-2 px-2 text-right w-32 uppercase">Anggaran (Rp)</th>
                        <th class="border border-black py-2 px-2 text-right w-32 uppercase">Realisasi (Rp)</th>
                        <th class="border border-black py-2 px-2 text-center w-20 uppercase">Cap. (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumTarget = 0; $sumReal = 0; @endphp
                    @forelse($laporan[$jenis] as $item)
                        @php 
                            $sumTarget += $item->jumlah_target;
                            $sumReal += $item->jumlah_realisasi;
                            $persen = ($item->jumlah_target > 0) ? ($item->jumlah_realisasi / $item->jumlah_target * 100) : 0;
                        @endphp
                        <tr>
                            <td class="border border-black py-1.5 px-2 text-center">{{ $item->mataAnggaran->kode }}</td>
                            <td class="border border-black py-1.5 px-2 font-bold">{{ strtoupper($item->mataAnggaran->nama_mata_anggaran) }}</td>
                            <td class="border border-black py-1.5 px-2 text-right">{{ number_format($item->jumlah_target, 0, ',', '.') }}</td>
                            <td class="border border-black py-1.5 px-2 text-right">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                            <td class="border border-black py-1.5 px-2 text-center font-bold">
                                {{ round($persen, 1) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-4 text-center italic border border-black text-gray-500 text-[10px]">Data anggaran belum disahkan.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-100 font-black">
                    <tr>
                        <td colspan="2" class="border border-black py-2 px-2 text-right uppercase text-[10px] tracking-widest">Total Akumulasi {{ $jenis }}</td>
                        <td class="border border-black py-2 px-2 text-right">Rp {{ number_format($sumTarget, 0, ',', '.') }}</td>
                        <td class="border border-black py-2 px-2 text-right">Rp {{ number_format($sumReal, 0, ',', '.') }}</td>
                        <td class="border border-black py-2 px-2 text-center">
                            {{ $sumTarget > 0 ? round(($sumReal / $sumTarget) * 100, 1) : 0 }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endforeach

        {{-- AREA TANDA TANGAN --}}
        <div class="mt-16 text-black">
            <div class="grid grid-cols-2 gap-x-20 text-xs font-serif">
                <div class="text-center space-y-24">
                    <div>
                        <p class="mb-1 font-bold">Menyetujui/Mengesahkan,</p>
                        <p class="font-black uppercase tracking-tight">
                            @if(Auth::user()->hasRole(['Super Admin', 'Admin Sinode'])) Ketua Sinode GPI Papua @elseif(Auth::user()->hasRole('Admin Klasis')) Ketua Klasis @else Ketua Majelis Jemaat @endif
                        </p>
                    </div>
                    <div>
                        <p class="border-t border-black inline-block px-12 pt-1 font-black uppercase tracking-widest">(.........................................)</p>
                    </div>
                </div>

                <div class="text-center space-y-24">
                    <div>
                        <p class="mb-1 italic">Diterbitkan pada, {{ now()->isoFormat('D MMMM Y') }}</p>
                        <p class="font-black uppercase tracking-tight">Bendahara / Otorisator Keuangan</p>
                    </div>
                    <div>
                        <p class="border-t border-black inline-block px-12 pt-1 font-black uppercase tracking-widest">(.........................................)</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-12 text-[8px] italic text-gray-500 print:block hidden border-t border-gray-300 pt-2 text-right">
            Sistem Informasi Manajemen Terpadu (SIM-GPI) | {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</div>

<style>
    @media print {
        @page { size: A4 portrait; margin: 1.5cm; }
        body { background: white !important; font-family: "Times New Roman", Times, serif !important; color: black !important; }
        aside, header, footer, .print\:hidden { display: none !important; }
        main { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .flex-1 { margin-left: 0 !important; }
        .report-document { box-shadow: none !important; border: none !important; width: 100% !important; padding: 0 !important; }
    }
</style>
@endsection