@extends('admin.layout')

@section('title', 'Laporan Inventaris Aset')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- Panel Kontrol --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 print:hidden flex justify-between items-center">
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-widest flex items-center">
            <i class="fas fa-boxes mr-3 text-primary"></i> Register Inventaris Aktiva Tetap
        </h2>
        <button onclick="window.print()" class="bg-primary text-white px-8 py-2 rounded-md text-sm font-bold hover:bg-blue-800 shadow-xl transition-all">
            <i class="fas fa-print mr-2"></i> CETAK DAFTAR ASET
        </button>
    </div>

    <div class="bg-white p-12 md:p-16 shadow-2xl rounded-sm report-document print:shadow-none print:p-0">
        {{-- KOP SURAT FORMAL --}}
        <div class="flex items-center border-b-4 border-black pb-4 mb-1">
            <div class="flex-shrink-0 mr-8">
                @if ($setting->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->logo_path))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($setting->logo_path) }}" class="h-28 w-28 object-contain">
                @else
                    <div class="w-24 h-24 bg-gray-100 flex items-center justify-center border-2 border-black rounded-lg">
                         <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L3 7v13h18V7L12 2zm0 2.236L18.99 8H5.01L12 4.236zM5 18V9.618l7 4.118 7-4.118V18H5z"/></svg>
                    </div>
                @endif
            </div>
            <div class="flex-grow text-center pr-12">
                <h1 class="text-3xl font-serif font-black uppercase tracking-tighter leading-tight">Gereja Protestan Indonesia Di Papua</h1>
                <h2 class="text-xl font-serif font-bold uppercase mb-1 tracking-widest">
                    @if(Auth::user()->hasRole(['Super Admin', 'Admin Sinode'])) SINODE @else {{ Auth::user()->klasisTugas->nama_klasis ?? (Auth::user()->jemaatTugas->nama_jemaat ?? 'PUSAT') }} @endif
                </h2>
                <div class="text-[11px] leading-tight font-medium text-gray-800">
                    {{ $setting->contact_address ?? 'Alamat Kantor Belum Diatur Dalam Pengaturan Sistem' }} <br>
                    Telepon: {{ $setting->contact_phone ?? '-' }} | Email: {{ $setting->contact_email ?? '-' }}
                </div>
            </div>
        </div>
        <div class="border-b border-black mb-10"></div>

        {{-- JUDUL --}}
        <div class="text-center mb-12">
            <h3 class="text-xl font-serif font-bold uppercase underline underline-offset-8 decoration-2">Daftar Inventaris & Aset Tetap</h3>
            <p class="text-md font-serif mt-2 font-medium">Laporan Per Tanggal: {{ now()->isoFormat('D MMMM Y') }}</p>
        </div>

        <table class="w-full text-xs font-serif border-collapse border border-black mb-16">
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
                <tr><td colspan="5" class="py-12 text-center italic border border-black text-gray-500 uppercase tracking-widest">Belum ada aset yang terdaftar dalam buku inventaris kekayaan gereja.</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-100 font-black">
                <tr class="border-t-2 border-black">
                    <td colspan="4" class="border border-black py-5 px-4 text-right uppercase tracking-widest text-sm">Total Nilai Kekayaan Inventaris (Aktiva Tetap)</td>
                    <td class="border border-black py-5 px-2 text-right text-lg font-mono">Rp {{ number_format($asets->sum('nilai_perolehan'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- AREA TANDA TANGAN DINAMIS --}}
        <div class="mt-24">
            <div class="grid grid-cols-2 gap-x-32 text-sm font-serif">
                <div class="text-center space-y-28">
                    <div>
                        <p class="mb-1 font-bold">Mengetahui,</p>
                        <p class="font-black uppercase tracking-tight leading-tight">
                            @if(Auth::user()->hasRole(['Super Admin', 'Admin Sinode'])) Ketua Sinode GPI Papua @elseif(Auth::user()->hasRole('Admin Klasis')) Ketua Klasis @else Ketua Majelis Jemaat @endif
                        </p>
                    </div>
                    <div>
                        <p class="border-t-2 border-black inline-block px-14 pt-1 font-black uppercase tracking-widest">..................................................</p>
                        <p class="text-[10px] mt-1 font-medium">Tanda Tangan & Cap Lembaga</p>
                    </div>
                </div>

                <div class="text-center space-y-28">
                    <div>
                        <p class="mb-1">&nbsp;</p>
                        <p class="font-black uppercase tracking-tight leading-tight">Wakil Ketua 2 (Bidang Keuangan)</p>
                    </div>
                    <div>
                        <p class="border-t-2 border-black inline-block px-14 pt-1 font-black uppercase tracking-widest">..................................................</p>
                        <p class="text-[10px] mt-1 font-medium">Tanda Tangan & Cap Lembaga</p>
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
        table { border: 1.5px solid black !important; }
        th, td { border: 1px solid black !important; }
    }
</style>
@endsection