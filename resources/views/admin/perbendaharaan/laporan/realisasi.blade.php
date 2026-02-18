@extends('layouts.app')

@section('title', 'Laporan Realisasi Anggaran')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    {{-- Panel Kontrol --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 print:hidden flex justify-between items-center">
        <form method="GET" class="flex items-center space-x-3">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-tight">Tahun Buku:</label>
            <select name="tahun" onchange="this.form.submit()" class="border-slate-300 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500 font-bold text-slate-700">
                @for($i = date('Y')-1; $i <= date('Y')+1; $i++)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </form>
        <button onclick="window.print()" class="bg-slate-800 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-slate-900 transition-all flex items-center shadow-lg">
            <i class="fas fa-print mr-2"></i> CETAK DOKUMEN RESMI
        </button>
    </div>

    {{-- Kertas Laporan --}}
    <div class="bg-white p-12 md:p-16 shadow-2xl rounded-sm print:shadow-none print:p-0 report-document">
        
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
                    {{ $setting->contact_address ?? 'Alamat Kantor Belum Diatur' }} <br>
                    Telepon: {{ $setting->contact_phone ?? '-' }} | Email: {{ $setting->contact_email ?? '-' }}
                </div>
            </div>
        </div>
        <div class="border-b border-black mb-10"></div> 

        {{-- JUDUL LAPORAN --}}
        <div class="text-center mb-12 text-black">
            <h3 class="text-xl font-serif font-bold uppercase underline underline-offset-8 decoration-2">Laporan Realisasi Anggaran (LRA)</h3>
            <p class="text-md font-serif mt-2 font-medium">Tahun Anggaran: {{ $tahun }}</p>
        </div>

        @foreach(['Pendapatan', 'Belanja'] as $jenis)
        <div class="mb-14">
            <h4 class="text-sm font-bold mb-4 uppercase tracking-widest border-b-2 border-black pb-1 text-black">I. KELOMPOK {{ $jenis }}</h4>
            <table class="w-full text-xs font-serif border-collapse border border-black text-black">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-black py-3 px-2 text-center w-20 uppercase tracking-tight">Kode Akun</th>
                        <th class="border border-black py-3 px-2 text-left uppercase tracking-tight">Uraian Mata Anggaran</th>
                        <th class="border border-black py-3 px-2 text-right w-36 uppercase tracking-tight">Anggaran (Rp)</th>
                        <th class="border border-black py-3 px-2 text-right w-36 uppercase tracking-tight">Realisasi (Rp)</th>
                        <th class="border border-black py-3 px-2 text-center w-24 uppercase tracking-tight">Capaian (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black">
                    @php $sumTarget = 0; $sumReal = 0; @endphp
                    @forelse($laporan[$jenis] as $item)
                        @php 
                            $sumTarget += $item->jumlah_target;
                            $sumReal += $item->jumlah_realisasi;
                            $persen = ($item->jumlah_target > 0) ? ($item->jumlah_realisasi / $item->jumlah_target * 100) : 0;
                        @endphp
                        <tr>
                            <td class="border border-black py-2.5 px-2 text-center font-mono text-[10px]">{{ $item->mataAnggaran->kode }}</td>
                            <td class="border border-black py-2.5 px-2 font-bold">{{ strtoupper($item->mataAnggaran->nama_mata_anggaran) }}</td>
                            <td class="border border-black py-2.5 px-2 text-right font-mono">{{ number_format($item->jumlah_target, 0, ',', '.') }}</td>
                            <td class="border border-black py-2.5 px-2 text-right font-mono">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td>
                            <td class="border border-black py-2.5 px-2 text-center font-black">
                                {{ round($persen, 1) }}%
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center italic border border-black text-gray-500">Data anggaran belum tersedia.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-100 font-black">
                    <tr class="border-t-2 border-black">
                        <td colspan="2" class="border border-black py-4 px-4 text-right uppercase tracking-wider">Total Keseluruhan {{ $jenis }}</td>
                        <td class="border border-black py-4 px-2 text-right font-mono">Rp {{ number_format($sumTarget, 0, ',', '.') }}</td>
                        <td class="border border-black py-4 px-2 text-right font-mono">Rp {{ number_format($sumReal, 0, ',', '.') }}</td>
                        <td class="border border-black py-4 px-2 text-center text-lg">
                            {{ $sumTarget > 0 ? round(($sumReal / $sumTarget) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endforeach

        {{-- AREA TANDA TANGAN --}}
        <div class="mt-24 text-black">
            <div class="grid grid-cols-2 gap-x-32 text-sm font-serif">
                <div class="text-center space-y-28">
                    <div>
                        <p class="mb-1 font-bold">Mengetahui/Menyetujui,</p>
                        <p class="font-black uppercase tracking-tight leading-tight">
                            @if(Auth::user()->hasRole(['Super Admin', 'Admin Sinode'])) Ketua Sinode GPI Papua @elseif(Auth::user()->hasRole('Admin Klasis')) Ketua Klasis @else Ketua Majelis Jemaat @endif
                        </p>
                    </div>
                    <div>
                        <p class="border-t-2 border-black inline-block px-14 pt-1 font-black uppercase tracking-widest">..................................................</p>
                        <p class="text-xs mt-1 font-medium italic">Nama Lengkap & Cap</p>
                    </div>
                </div>

                <div class="text-center space-y-28">
                    <div>
                        <p class="mb-1 italic">Ditetapkan pada, {{ now()->isoFormat('D MMMM Y') }}</p>
                        <p class="font-black uppercase tracking-tight leading-tight">Bendahara / Wakil Ketua 2</p>
                    </div>
                    <div>
                        <p class="border-t-2 border-black inline-block px-14 pt-1 font-black uppercase tracking-widest">..................................................</p>
                        <p class="text-xs mt-1 font-medium italic">Nama Lengkap</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-20 text-[9px] italic text-gray-500 print:block hidden border-t border-gray-300 pt-3">
            Dokumen ini dihasilkan melalui Sistem Informasi Manajemen Terpadu (SIM) GPI Papua pada {{ now()->format('d/m/Y H:i') }}.
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
        table, th, td { border-color: black !important; }
    }
</style>
@endsection