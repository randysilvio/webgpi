<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Sidi - {{ $data->anggotaJemaat->nama_lengkap }}</title>
    <style>
        @page { margin: 20px 30px; }
        body { font-family: 'Times New Roman', serif; color: #000; line-height: 1.3; }
        
        .kop-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 5px; margin-bottom: 20px; }
        .kop-logo { width: 80px; height: auto; }
        .kop-header { font-size: 18px; font-weight: bold; text-transform: uppercase; text-align: center; margin: 0; }
        .kop-subheader { font-size: 12px; font-weight: bold; text-align: center; margin: 2px 0; }
        .kop-address { font-size: 10px; font-style: italic; text-align: center; margin-top: 2px; }

        .judul-surat { font-size: 22px; font-weight: bold; text-align: center; text-decoration: underline; margin-bottom: 2px; text-transform: uppercase; }
        .nomor-surat { text-align: center; font-size: 12px; margin-bottom: 30px; font-weight: bold; }

        .content { font-size: 14px; text-align: justify; margin: 0 20px; }
        .ayat-alkitab { font-style: italic; text-align: center; font-size: 12px; margin: 10px 40px 30px 40px; color: #333; }
        
        .info-table { width: 100%; margin-top: 10px; }
        .info-table td { vertical-align: top; padding: 5px 0; }
        .label { width: 160px; font-weight: bold; }
        .titik-dua { width: 10px; }
        .isian { font-weight: bold; text-transform: uppercase; font-size: 15px; }

        .footer-table { width: 100%; margin-top: 60px; page-break-inside: avoid; }
        .ttd-box { text-align: center; width: 40%; float: right; }
        .ttd-kiri { text-align: center; width: 40%; float: left; }
        .ttd-name { font-weight: bold; text-decoration: underline; margin-top: 70px; display: block; }
    </style>
</head>
<body>
    <table class="kop-table">
        <tr>
            <td style="width: 15%; text-align: center;">
                @php
                    $path = null;
                    if($setting && $setting->logo_path) {
                        $publicPath = public_path('storage/' . $setting->logo_path);
                        if(file_exists($publicPath)) { $path = $publicPath; }
                    }
                @endphp
                @if($path) <img src="{{ $path }}" class="kop-logo"> @else <b>LOGO</b> @endif
            </td>
            <td style="width: 85%;">
                <div class="kop-header">GEREJA PROTESTAN INDONESIA DI PAPUA</div>
                <div class="kop-header">(GPI PAPUA)</div>
                <div class="kop-subheader">
                    {{ $data->anggotaJemaat->jemaat->klasis->nama_klasis ?? 'KLASIS ...' }}<br>
                    {{ $data->anggotaJemaat->jemaat->nama_jemaat ?? 'JEMAAT ...' }}
                </div>
                <div class="kop-address">{{ $setting->contact_address ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <div class="judul-surat">SURAT PENEGUHAN SIDI</div>
    <div class="nomor-surat">No. Register: {{ $data->no_akta_sidi }}</div>

    <div class="content">
        <div class="ayat-alkitab">
            "Setiap orang yang mengakui Aku di depan manusia, Aku juga akan mengakuinya di depan Bapa-Ku yang di sorga." <br><strong>(Matius 10:32)</strong>
        </div>

        <p>Telah meneguhkan pengakuan imannya kepada Yesus Kristus sebagai Tuhan dan Juruselamat serta diterima sebagai Anggota Sidi Gereja Protestan Indonesia di Papua:</p>

        <table class="info-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="titik-dua">:</td>
                <td class="isian">{{ $data->anggotaJemaat->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="titik-dua">:</td>
                <td>{{ $data->anggotaJemaat->tempat_lahir }}, {{ $data->anggotaJemaat->tanggal_lahir->isoFormat('D MMMM Y') }}</td>
            </tr>
            <tr>
                <td class="label">Alamat Domisili</td>
                <td class="titik-dua">:</td>
                <td>{{ $data->anggotaJemaat->alamat_lengkap ?? '-' }}</td>
            </tr>
        </table>

        <br>
        <p>Peneguhan Sidi dilaksanakan pada:</p>
        <table class="info-table">
            <tr>
                <td class="label">Hari / Tanggal</td>
                <td class="titik-dua">:</td>
                <td><strong>{{ \Carbon\Carbon::parse($data->tanggal_sidi)->isoFormat('dddd, D MMMM Y') }}</strong></td>
            </tr>
            <tr>
                <td class="label">Tempat / Gereja</td>
                <td class="titik-dua">:</td>
                <td>{{ $data->tempat_sidi }}</td>
            </tr>
            <tr>
                <td class="label">Dilayani Oleh</td>
                <td class="titik-dua">:</td>
                <td>{{ $data->pendeta_pelayan }}</td>
            </tr>
        </table>

        <div class="footer-table">
            <div class="ttd-kiri">
                <br>Yang Mengaku,<br><br><br>
                <span class="ttd-name">{{ $data->anggotaJemaat->nama_lengkap }}</span>
            </div>
            <div class="ttd-box">
                Ditetapkan di: {{ $data->anggotaJemaat->jemaat->nama_jemaat ?? 'Papua' }}<br>
                Pada Tanggal: {{ date('d-m-Y') }}<br>
                <br>
                Pelayan Firman,
                <span class="ttd-name">{{ $data->pendeta_pelayan }}</span>
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
</body>
</html>