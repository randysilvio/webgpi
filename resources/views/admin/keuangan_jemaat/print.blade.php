<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Kas Jemaat - {{ $jemaatAktif->nama_jemaat }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: A4 landscape; margin: 1cm; }
            body { font-family: "Times New Roman", Times, serif !important; color: black !important; background: white !important; }
            .print-btn { display: none !important; }
        }
        body { font-family: "Times New Roman", Times, serif; color: black; background: #f3f4f6; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid black; padding: 6px 8px; font-size: 11px; }
        th { font-weight: bold; text-align: center; background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .uppercase { text-transform: uppercase; }
        .font-bold { font-weight: bold; }
        .font-black { font-weight: 900; }
    </style>
</head>
<body class="p-8 max-w-7xl mx-auto bg-white min-h-screen shadow-xl my-8 print:my-0 print:shadow-none">

    <button onclick="window.print()" class="print-btn fixed bottom-8 right-8 bg-blue-800 hover:bg-blue-900 text-white px-6 py-3 rounded-full shadow-2xl font-bold uppercase tracking-widest text-xs z-50 transition">
        <i class="fas fa-print mr-2"></i> Cetak Dokumen
    </button>

    {{-- KOP SURAT FORMAL --}}
    <div class="flex items-center border-b-[3px] border-black pb-4 mb-8">
        <div class="flex-shrink-0 mr-6 text-center" style="width: 80px;">
            {{-- FIX: Menggunakan isset() agar tidak error jika variabel belum ada --}}
            @if (isset($setting) && $setting->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->logo_path))
                <img src="{{ \Illuminate\Support\Facades\Storage::url($setting->logo_path) }}" class="h-20 w-auto object-contain mx-auto">
            @else
                <div class="text-[9px] font-bold border border-black p-2">LOGO</div>
            @endif
        </div>
        <div class="flex-grow text-center pr-12">
            <h1 class="text-2xl font-black uppercase tracking-tight leading-tight">Gereja Protestan Indonesia Di Papua</h1>
            <h2 class="text-lg font-bold uppercase mb-1 tracking-widest">
                KLASIS {{ $jemaatAktif->klasis->nama_klasis ?? 'TIDAK DIKETAHUI' }}<br>
                JEMAAT {{ $jemaatAktif->nama_jemaat }}
            </h2>
            <div class="text-[10px] leading-tight font-medium uppercase">
                {{ $jemaatAktif->alamat_gereja ?? 'Alamat Belum Diatur' }}
            </div>
        </div>
    </div>

    {{-- JUDUL LAPORAN --}}
    <div class="text-center mb-6">
        <h3 class="text-lg font-black uppercase underline underline-offset-4 tracking-widest">Laporan Buku Kas Umum</h3>
        <p class="text-sm font-bold uppercase mt-1">Kategori Kas: {{ $kategori }}</p>
        <p class="text-xs font-bold uppercase mt-1">Bulan: {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No. Urut</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 33%;">Uraian Transaksi</th>
                <th style="width: 10%;">Kode Ayat</th>
                <th style="width: 13%;">Terima (Rp)</th>
                <th style="width: 13%;">Keluar (Rp)</th>
                <th style="width: 14%;">Saldo (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="6" class="text-right font-bold uppercase text-[10px] tracking-widest">Saldo Pindahan Per Akhir {{ DateTime::createFromFormat('!m', ($bulan == 1 ? 12 : $bulan - 1))->format('F') }} {{ $bulan == 1 ? $tahun - 1 : $tahun }}</td>
                <td class="text-right font-black">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
            </tr>

            @forelse($transaksiBulanIni as $index => $trx)
            <tr>
                <td class="text-center">{{ $index + 1 }}.</td>
                <td class="text-center">{{ $trx->tanggal->format('d/m/Y') }}</td>
                <td class="uppercase">{{ $trx->uraian }}</td>
                <td class="text-center">{{ $trx->kode_ayat ?? '-' }}</td>
                <td class="text-right">{{ $trx->jenis_transaksi == 'Pemasukan' ? number_format($trx->nominal, 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $trx->jenis_transaksi == 'Pengeluaran' ? number_format($trx->nominal, 0, ',', '.') : '-' }}</td>
                <td class="text-right font-bold">{{ number_format($trx->saldo_berjalan, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center italic py-4 text-gray-500">Nihil / Tidak ada transaksi tercatat pada bulan ini.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            @php
                $totalMasuk = $transaksiBulanIni->where('jenis_transaksi', 'Pemasukan')->sum('nominal');
                $totalKeluar = $transaksiBulanIni->where('jenis_transaksi', 'Pengeluaran')->sum('nominal');
            @endphp
            <tr>
                <td colspan="4" class="text-right font-black uppercase text-[10px] tracking-widest">Jumlah Mutasi Bulan Ini</td>
                <td class="text-right font-black">{{ number_format($totalMasuk, 0, ',', '.') }}</td>
                <td class="text-right font-black">{{ number_format($totalKeluar, 0, ',', '.') }}</td>
                <td class="bg-gray-100"></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right font-black uppercase tracking-widest border-t-2 border-black">Posisi Saldo Kas Akhir Per {{ date('t', strtotime("$tahun-$bulan-01")) }} {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}</td>
                <td class="text-right font-black border-t-2 border-black">{{ number_format($saldoAkhir, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- AREA TANDA TANGAN --}}
    <div class="mt-16 text-black break-inside-avoid">
        <div class="grid grid-cols-2 gap-x-20 text-xs font-serif">
            <div class="text-center space-y-24">
                <div>
                    <p class="mb-1 font-bold">Mengetahui,</p>
                    <p class="font-black uppercase tracking-tight">Ketua Majelis Jemaat</p>
                </div>
                <div>
                    <p class="border-t border-black inline-block px-12 pt-1 font-black uppercase tracking-widest">(.........................................)</p>
                </div>
            </div>
            <div class="text-center space-y-24">
                <div>
                    <p class="mb-1 italic">&nbsp;</p>
                    <p class="font-black uppercase tracking-tight">Bendahara Jemaat / Pemegang Kas</p>
                </div>
                <div>
                    <p class="border-t border-black inline-block px-12 pt-1 font-black uppercase tracking-widest">(.........................................)</p>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>