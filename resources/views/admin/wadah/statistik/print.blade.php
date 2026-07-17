<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dokumen Laporan Statistik Kategorial</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #111; line-height: 1.3;}
        
        /* KOP SURAT FORMAL */
        .kop-table { width: 100%; border-bottom: 3px solid #111; margin-bottom: 20px; padding-bottom: 10px; }
        .kop-logo img { height: 60px; width: auto; }
        .kop-text { text-align: center; }
        .kop-title { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; letter-spacing: 1px; }
        .kop-subtitle { font-size: 11pt; font-weight: bold; margin: 2px 0; color: #333; }
        .kop-meta { font-size: 8pt; color: #555; }

        /* JUDUL LAPORAN */
        .report-title { text-align: center; margin-bottom: 25px; }
        .report-title h3 { text-decoration: underline; text-transform: uppercase; margin: 0 0 5px 0; font-size: 12pt; }
        .report-filter { font-size: 9pt; font-weight: bold; text-transform: uppercase; }

        /* TABEL DATA (Kotak Tajam) */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 9pt; border: 1px solid #111;}
        .data-table th, .data-table td { border: 1px solid #555; padding: 6px 8px; text-align: center; }
        .data-table th { background-color: #f3f4f6; text-transform: uppercase; font-weight: bold; font-size: 8pt;}
        .data-table tr:nth-child(even) { background-color: #fafafa; }
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .font-bold { font-weight: bold; }
        .bg-total { background-color: #1f2937; color: #ffffff; }

        /* FOOTER TANDA TANGAN */
        .footer { margin-top: 40px; width: 100%; }
        .signature-box { float: right; width: 40%; text-align: center; font-size: 9pt; }
        .signature-space { height: 60px; }
    </style>
</head>
<body>

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
                    <div style="font-size:9px; border:1px solid #000; padding:10px;">LOGO INSTANSI</div>
                @endif
            </td>
            <td width="85%" class="kop-text">
                <h1 class="kop-title">Gereja Protestan Indonesia di Papua</h1>
                <h2 class="kop-subtitle">(GPI PAPUA)</h2>
                <div class="kop-meta">
                    Dicetak secara elektronis pada: {{ now()->translatedFormat('d F Y, H:i') }} WIT
                </div>
            </td>
        </tr>
    </table>

    <div class="report-title">
        <h3>Laporan Agregat Demografi Wadah Kategorial</h3>
        <div class="report-filter">Cakupan Data: {{ $filterInfo ?? 'Seluruh Wilayah' }}</div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th>Klasifikasi Kategorial</th>
                <th width="15%">Rentang Usia</th>
                <th width="12%">Laki-laki</th>
                <th width="12%">Perempuan</th>
                <th width="12%">Telah Sidi</th>
                <th width="12%">Belum Sidi</th>
                <th width="15%">Total Jiwa</th>
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
                <td class="text-left font-bold">{{ strtoupper($row['nama']) }}</td>
                <td>{{ $row['range'] }} Thn</td>
                <td class="text-right">{{ number_format($row['laki'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row['perempuan'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row['sidi'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row['belum_sidi'], 0, ',', '.') }}</td>
                <td class="text-right font-bold" style="background-color: #f1f5f9;">{{ number_format($row['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right font-bold bg-total uppercase" style="font-size: 8pt;">Akumulasi Keseluruhan</td>
                <td class="text-right font-bold bg-total">{{ number_format($totalL, 0, ',', '.') }}</td>
                <td class="text-right font-bold bg-total">{{ number_format($totalP, 0, ',', '.') }}</td>
                <td class="bg-total text-center" colspan="2">-</td>
                <td class="text-right font-bold bg-total" style="font-size: 11pt;">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Papua, {{ now()->translatedFormat('d F Y') }}<br>Mengesahkan,</p>
            <div class="signature-space"></div>
            <p style="text-decoration: underline; font-weight: bold; text-transform: uppercase;">{{ auth()->user()->name ?? 'Administrator Sistem' }}</p>
            <p style="font-size: 8pt; color: #555;">Sistem Informasi Manajemen Terpadu</p>
        </div>
    </div>

</body>
</html>