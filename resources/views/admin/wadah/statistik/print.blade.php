<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Statistik Wadah Kategorial</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #333; }
        
        /* KOP SURAT */
        .kop-table { width: 100%; border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        .kop-logo img { height: 60px; width: auto; }
        .kop-text { text-align: center; }
        .kop-title { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .kop-subtitle { font-size: 11pt; font-weight: bold; margin: 2px 0; }
        .kop-meta { font-size: 9pt; font-style: italic; }

        /* JUDUL LAPORAN */
        .report-title { text-align: center; margin-bottom: 25px; }
        .report-title h3 { text-decoration: underline; text-transform: uppercase; margin: 0 0 5px 0; font-size: 12pt; }
        .report-filter { font-size: 9pt; color: #555; font-style: italic; }

        /* TABEL DATA */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 9pt; }
        .data-table th, .data-table td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        .data-table th { background-color: #f1f5f9; text-transform: uppercase; font-weight: bold; color: #1e293b; }
        .data-table tr:nth-child(even) { background-color: #f8fafc; }
        .text-left { text-align: left !important; }
        .font-bold { font-weight: bold; }

        /* FOOTER */
        .footer { margin-top: 40px; width: 100%; }
        .signature-box { float: right; width: 40%; text-align: center; font-size: 10pt; }
        .signature-space { height: 70px; }
    </style>
</head>
<body>

    {{-- LOGIKA BASE64 IMAGE (Agar Logo Muncul di PDF) --}}
    @php
        $logoBase64 = null;
        if(isset($setting) && $setting->logo_path) {
            $path = storage_path('app/public/' . $setting->logo_path);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $dataImg = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
            }
        }
    @endphp

    <table class="kop-table">
        <tr>
            <td width="15%" align="center" class="kop-logo">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @else
                    <div style="font-size:10px; border:1px solid #999; padding:10px;">LOGO</div>
                @endif
            </td>
            <td width="85%" class="kop-text">
                <h1 class="kop-title">Gereja Protestan Indonesia di Papua</h1>
                <h2 class="kop-subtitle">(GPI PAPUA)</h2>
                <div class="kop-meta">
                    Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIT
                </div>
            </td>
        </tr>
    </table>

    <div class="report-title">
        <h3>Laporan Statistik Wadah Kategorial</h3>
        <div class="report-filter">Filter Data: {{ $filterInfo ?? 'Semua Data' }}</div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Wadah Kategorial</th>
                <th>Rentang Usia</th>
                <th width="12%">Laki-laki</th>
                <th width="12%">Perempuan</th>
                <th width="12%">Sudah Sidi</th>
                <th width="12%">Belum Sidi</th>
                <th width="15%">Total</th>
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
                <td class="text-left font-bold">{{ $row['nama'] }}</td>
                <td>{{ $row['range'] }}</td>
                <td>{{ number_format($row['laki']) }}</td>
                <td>{{ number_format($row['perempuan']) }}</td>
                <td>{{ number_format($row['sidi']) }}</td>
                <td>{{ number_format($row['belum_sidi']) }}</td>
                <td class="font-bold" style="background-color: #e2e8f0;">{{ number_format($row['total']) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #cbd5e1; font-weight: bold;">
                <td colspan="3" class="text-left">GRAND TOTAL</td>
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
            <p style="text-decoration: underline; font-weight: bold;">{{ auth()->user()->name ?? 'Administrator' }}</p>
            <p>Admin Sistem</p>
        </div>
    </div>

</body>
</html>