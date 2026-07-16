<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Jemaat - {{ $jemaat->nama_jemaat }}</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #000; }
        .header-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 15px; padding-bottom: 5px; }
        .header-logo { width: 15%; text-align: left; vertical-align: middle; }
        .header-text { width: 85%; text-align: center; vertical-align: middle; padding-right: 15%; }
        .header-text h1 { margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .header-text h2 { margin: 2px 0; font-size: 11pt; font-weight: bold; text-transform: uppercase; }
        .title { text-align: center; margin: 20px 0; text-transform: uppercase; text-decoration: underline; font-size: 12pt; font-weight: bold; }
        .section-title { font-weight: bold; background: #e0e0e0; padding: 5px; margin-top: 20px; font-size: 10pt; text-transform: uppercase; border: 1px solid #000; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        table.data th { background: #f5f5f5; font-weight: bold; }
        .grid-stats { width: 100%; border-collapse: collapse; margin-top: 10px; text-align: center; }
        .grid-stats td { border: 1px solid #000; padding: 10px; width: 25%; }
        .stats-num { font-size: 14pt; font-weight: bold; margin-top: 5px; }
    </style>
</head>
<body>

    @php
        $logoBase64 = null;
        if(isset($setting) && $setting->logo_path) {
            $path = storage_path('app/public/' . $setting->logo_path);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path));
            }
        }
    @endphp

    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" width="60" alt="Logo">
                @else
                    <div style="width: 60px; height: 60px; background: #eee; border:1px dashed #999; text-align:center; line-height:60px; font-size:9px;">LOGO</div>
                @endif
            </td>
            <td class="header-text">
                <h1>GEREJA PROTESTAN INDONESIA DI PAPUA</h1>
                <h1>(GPI PAPUA)</h1>
                <h2>{{ $jemaat->klasis->nama_klasis ?? 'KLASIS ...' }}</h2>
            </td>
        </tr>
    </table>

    <div class="title">Buku Registrasi Profil Jemaat</div>

    <div class="section-title">A. Identitas & Administrasi Jemaat</div>
    <table class="data">
        <tr><td width="30%">Nama Jemaat</td><td style="font-weight: bold; text-transform: uppercase;">{{ $jemaat->nama_jemaat }}</td></tr>
        <tr><td>Kode Jemaat</td><td style="font-family: monospace;">{{ $jemaat->kode_jemaat ?? '-' }}</td></tr>
        <tr><td>Klasifikasi Pelayanan</td><td>{{ $jemaat->jenis_jemaat }}</td></tr>
        <tr><td>Tanggal Peresmian</td><td>{{ $jemaat->tanggal_berdiri ? \Carbon\Carbon::parse($jemaat->tanggal_berdiri)->isoFormat('D MMMM Y') : 'Belum Terdata' }}</td></tr>
        <tr><td>Alamat Gedung Gereja</td><td>{{ $jemaat->alamat_gereja ?? '-' }}</td></tr>
        <tr><td>Telepon / Email</td><td>{{ $jemaat->telepon_kantor ?? '-' }} / {{ $jemaat->email_jemaat ?? '-' }}</td></tr>
    </table>

    <div class="section-title">B. Data Demografi & Populasi</div>
    <table class="grid-stats">
        <tr>
            <td>Status Jemaat<div class="stats-num">{{ strtoupper($jemaat->status_jemaat) }}</div></td>
            <td>Sensus Keluarga<div class="stats-num">{{ number_format($jemaat->real_total_kk ?? 0) }} KK</div></td>
            <td>Laki-Laki<div class="stats-num">{{ number_format($realJiwaLaki ?? 0) }} Jiwa</div></td>
            <td>Perempuan<div class="stats-num">{{ number_format($realJiwaPerempuan ?? 0) }} Jiwa</div></td>
        </tr>
    </table>
    <div style="text-align: right; font-weight: bold; margin-top: 5px; font-size: 11pt;">
        Total Keseluruhan Populasi: {{ number_format($jemaat->real_total_jiwa ?? 0) }} Jiwa
    </div>

    <div class="section-title">C. Personalia Pelayan Firman</div>
    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="65%">Nama Lengkap Pendeta</th>
                <th width="30%">NIPG</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jemaat->pendetaDitempatkan as $index => $p)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-transform: uppercase; font-weight: bold;">{{ $p->nama_lengkap }}</td>
                <td style="font-family: monospace; text-align: center;">{{ $p->nipg ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align: center; font-style: italic;">Belum ada penempatan Pendeta/Pelayan Firman Organik.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 40px; text-align: right; width: 100%;">
        <p>Dicetak dari Pangkalan Data GPI Papua<br>Pada tanggal: {{ date('d F Y') }}</p>
    </div>

</body>
</html>