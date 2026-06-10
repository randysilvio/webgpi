<!DOCTYPE html>
<html>
<head>
    <title>Laporan Konsolidasi {{ $tahun }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid black; padding-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background-color: #f2f2f2; text-align: left; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .section-title { background-color: #eee; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN KEUANGAN KONSOLIDASI</div>
        <div>{{ $setting->site_name ?? 'Sinode GPI Papua' }}</div>
        <div>Tahun Anggaran: {{ $tahun }}</div>
    </div>

    {{-- TABEL PENERIMAAN --}}
    <h3>I. PENERIMAAN</h3>
    <table>
        <thead><tr><th>Uraian</th><th class="text-right">Nominal (Rp)</th></tr></thead>
        <tbody>
            <tr class="section-title"><td colspan="2">A. Kas Umum (Induk)</td></tr>
            @foreach($data['induk_masuk'] as $item)
            <tr><td>{{ $item->mataAnggaran->nama_mata_anggaran }}</td><td class="text-right">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td></tr>
            @endforeach
            
            <tr class="section-title"><td colspan="2">B. Kas Wadah Kategorial</td></tr>
            @foreach($data['wadah_masuk'] as $item)
            <tr><td>{{ $item->jenisWadah->nama_wadah }} - {{ $item->nama_pos_anggaran }}</td><td class="text-right">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td></tr>
            @endforeach
            
            <tr class="bold"><td class="text-right">TOTAL PENERIMAAN</td><td class="text-right">{{ number_format($totals['induk_masuk'] + $totals['wadah_masuk'], 0, ',', '.') }}</td></tr>
        </tbody>
    </table>

    {{-- TABEL BELANJA --}}
    <h3>II. BELANJA & PENGELUARAN</h3>
    <table>
        <thead><tr><th>Uraian</th><th class="text-right">Nominal (Rp)</th></tr></thead>
        <tbody>
            <tr class="section-title"><td colspan="2">A. Belanja Rutin (Induk)</td></tr>
            @foreach($data['induk_keluar'] as $item)
            <tr><td>{{ $item->mataAnggaran->nama_mata_anggaran }}</td><td class="text-right">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td></tr>
            @endforeach
            
            <tr class="section-title"><td colspan="2">B. Belanja Program Wadah</td></tr>
            @foreach($data['wadah_keluar'] as $item)
            <tr><td>{{ $item->jenisWadah->nama_wadah }} - {{ $item->nama_pos_anggaran }}</td><td class="text-right">{{ number_format($item->jumlah_realisasi, 0, ',', '.') }}</td></tr>
            @endforeach
            
            <tr class="bold"><td class="text-right">TOTAL BELANJA</td><td class="text-right">{{ number_format($totals['induk_keluar'] + $totals['wadah_keluar'], 0, ',', '.') }}</td></tr>
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-size: 14px; font-weight: bold; border-top: 2px solid black; padding-top: 10px;">
        SALDO KAS BERSIH: Rp {{ number_format($totals['saldo_bersih'], 0, ',', '.') }}
    </div>
</body>
</html>