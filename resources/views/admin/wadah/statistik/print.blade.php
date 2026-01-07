<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Statistik Wadah Kategorial</title>
    <style>
        @page { size: A4 portrait; margin: 10mm 15mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; margin: 0; padding: 0; }
        
        .header-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; }
        .header-logo { width: 10%; text-align: center; }
        .header-text { width: 90%; text-align: center; }
        .header-text h1 { margin: 0; font-size: 14pt; text-transform: uppercase; font-weight: bold; }
        .header-text h2 { margin: 2px 0; font-size: 12pt; text-transform: uppercase; }
        .header-text p { margin: 0; font-size: 9pt; font-style: italic; }

        .title { text-align: center; margin-bottom: 20px; }
        .title h3 { text-decoration: underline; text-transform: uppercase; margin: 0; }
        .title span { font-size: 10pt; font-style: italic; }

        table { width: 100%; border-collapse: collapse; font-size: 10pt; margin-bottom: 20px; }
        table th, table td { border: 1px solid #000; padding: 6px; text-align: center; }
        table th { background-color: #f0f0f0; text-transform: uppercase; font-size: 9pt; }
        
        .text-left { text-align: left; }
        .footer { width: 100%; margin-top: 30px; }
        .signature-box { float: right; width: 30%; text-align: center; }
        .signature-space { height: 60px; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if(isset($setting) && $setting->logo)
                    <img src="{{ public_path('storage/' . $setting->logo) }}" width="60" alt="Logo">
                @else
                    <div style="width:60px; height:60px; background:#eee; line-height:60px; border:1px solid #999">Logo</div>
                @endif
            </td>
            <td class="header-text">
                <h1>Gereja Protestan Indonesia di Papua (GPI Papua)</h1>
                <h2>Laporan Data Statistik Kategorial</h2>
                <p>Dicetak pada: {{ now()->translatedFormat('d F Y') }}</p>
            </td>
        </tr>
    </table>

    <div class="title">
        <h3>Rekapitulasi Kekuatan Wadah</h3>
        <span>Filter Data: {{ $filterInfo }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Wadah Kategorial</th>
                <th>Rentang Usia</th>
                <th width="10%">Laki-laki</th>
                <th width="10%">Perempuan</th>
                <th width="10%">Sudah Sidi</th>
                <th width="10%">Belum Sidi</th>
                <th width="10%">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; $totalL = 0; $totalP = 0; @endphp
            @foreach($statistik as $index => $row)
            @php 
                $grandTotal += $row['total'];
                $totalL += $row['laki'];
                $totalP += $row['perempuan'];
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left" style="font-weight: bold;">{{ $row['nama'] }}</td>
                <td>{{ $row['range'] }}</td>
                <td>{{ number_format($row['laki']) }}</td>
                <td>{{ number_format($row['perempuan']) }}</td>
                <td>{{ number_format($row['sidi']) }}</td>
                <td>{{ number_format($row['belum_sidi']) }}</td>
                <td style="font-weight: bold; background-color: #f9f9f9;">{{ number_format($row['total']) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #eee; font-weight: bold;">
                <td colspan="3">GRAND TOTAL</td>
                <td>{{ number_format($totalL) }}</td>
                <td>{{ number_format($totalP) }}</td>
                <td>-</td>
                <td>-</td>
                <td>{{ number_format($grandTotal) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Papua, {{ now()->translatedFormat('d F Y') }}<br>Mengetahui,</p>
            <div class="signature-space"></div>
            <p style="text-decoration: underline; font-weight: bold;">Administrator</p>
        </div>
    </div>

</body>
</html>