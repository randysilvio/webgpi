<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Sidi - {{ $data->anggotaJemaat->nama_lengkap }}</title>
    <style>
        @page { margin: 0; size: A4 portrait; }
        body { font-family: 'Times New Roman', Times, serif; color: #000; margin: 0; padding: 0; line-height: 1.3; }

        .container {
            width: 180mm; height: 267mm; margin: 15mm auto; padding: 10mm;
            border: 5px double #333; position: relative; box-sizing: border-box;
        }

        .watermark {
            position: absolute; top: 55%; left: 50%; transform: translate(-50%, -50%);
            width: 350px; opacity: 0.08; z-index: -1; filter: grayscale(100%);
        }

        .kop-table { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 25px; }
        .kop-logo { width: 80px; height: auto; object-fit: contain; }
        .kop-text { text-align: center; text-transform: uppercase; }
        .kop-header-main { font-size: 14pt; font-weight: bold; letter-spacing: 1px; margin: 0; }
        .kop-header-sub { font-size: 12pt; font-weight: bold; margin: 0; }
        .kop-subheader { font-size: 11pt; font-weight: bold; margin: 3px 0; }
        .kop-address { font-size: 9pt; font-style: italic; text-transform: none; font-weight: normal; }

        .judul-container { text-align: center; margin-bottom: 25px; }
        .judul-surat { font-size: 22pt; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; border-bottom: 1px solid #000; padding-bottom: 3px; }
        .nomor-surat { font-size: 11pt; font-weight: bold; margin-top: 5px; font-family: Arial, sans-serif; }

        .content { margin: 0 10px; font-size: 11pt; text-align: justify; }
        .ayat-box { text-align: center; font-style: italic; font-size: 10pt; margin: 0 40px 25px 40px; color: #444; }
        .preambule { text-align: center; margin-bottom: 20px; }

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 4px 5px; vertical-align: top; }
        .label { width: 32%; font-weight: bold; color: #333; }
        .sep { width: 3%; text-align: center; }
        .data { width: 65%; font-weight: bold; text-transform: uppercase; font-size: 13pt; border-bottom: 1px dotted #999; }
        .data-small { font-weight: bold; font-size: 11pt; border-bottom: 1px dotted #ccc; }

        .footer { margin-top: 40px; width: 100%; }
        .ttd-box { float: right; width: 50%; text-align: center; }
        .ttd-kiri { float: left; width: 40%; text-align: center; margin-top: 20px; }
        .ttd-date { font-size: 11pt; margin-bottom: 5px; }
        .ttd-role { font-weight: bold; margin-bottom: 70px; font-size: 11pt; }
        .ttd-name { font-weight: bold; text-decoration: underline; font-size: 12pt; display: block; text-transform: uppercase; }
    </style>
</head>
<body>

<div class="container">
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

    @if($logoBase64) <img src="{{ $logoBase64 }}" class="watermark"> @endif

    <table class="kop-table">
        <tr>
            <td width="15%" style="text-align: center;">
                @if($logoBase64) <img src="{{ $logoBase64 }}" class="kop-logo"> @else NO LOGO @endif
            </td>
            <td width="85%" class="kop-text">
                <div class="kop-header-main">{{ $setting->site_name ?? 'GEREJA PROTESTAN INDONESIA DI PAPUA' }}</div>
                <div class="kop-header-sub">(GPI PAPUA)</div>
                <div class="kop-subheader">
                    {{ $data->anggotaJemaat->jemaat->klasis->nama_klasis ?? 'Klasis ...' }}<br>
                    {{ $data->anggotaJemaat->jemaat->nama_jemaat ?? 'Jemaat ...' }}
                </div>
                <div class="kop-address">{{ $setting->contact_address ?? 'Alamat Kantor Jemaat' }}</div>
            </td>
        </tr>
    </table>

    <div class="judul-container">
        <span class="judul-surat">Surat Peneguhan Sidi</span>
        <div class="nomor-surat">No. Register: {{ $data->no_akta_sidi }}</div>
    </div>

    <div class="ayat-box">
        "Setiap orang yang mengakui Aku di depan manusia, Aku juga akan mengakuinya di depan Bapa-Ku yang di sorga." <br><strong>(Matius 10:32)</strong>
    </div>

    <div class="content">
        <p class="preambule">
            Telah meneguhkan pengakuan imannya kepada Yesus Kristus sebagai Tuhan dan Juruselamat serta diterima sebagai <strong>Anggota Sidi</strong>:
        </p>

        <table class="info-table">
            <tr><td class="label">Nama Lengkap</td><td class="sep">:</td><td class="data" style="font-size: 15pt;">{{ $data->anggotaJemaat->nama_lengkap }}</td></tr>
            <tr><td class="label">Tempat, Tanggal Lahir</td><td class="sep">:</td><td class="data">{{ $data->anggotaJemaat->tempat_lahir }}, {{ $data->anggotaJemaat->tanggal_lahir ? $data->anggotaJemaat->tanggal_lahir->isoFormat('D MMMM Y') : '-' }}</td></tr>
            <tr><td class="label">Alamat Domisili</td><td class="sep">:</td><td class="data">{{ $data->anggotaJemaat->alamat_lengkap ?? '-' }}</td></tr>
        </table>

        <br>
        <p class="preambule" style="text-align: left; margin-bottom: 10px;">
            Peneguhan Sidi dilaksanakan dalam Ibadah Jemaat pada:
        </p>

        <table class="info-table">
            <tr><td class="label">Hari, Tanggal</td><td class="sep">:</td><td class="data-small"><strong>{{ \Carbon\Carbon::parse($data->tanggal_sidi)->isoFormat('dddd, D MMMM Y') }}</strong></td></tr>
            <tr><td class="label">Bertempat di</td><td class="sep">:</td><td class="data-small">{{ $data->tempat_sidi }}</td></tr>
            <tr><td class="label">Dilayani Oleh</td><td class="sep">:</td><td class="data-small">{{ $data->pendeta_pelayan }}</td></tr>
        </table>
    </div>

    <div class="footer">
        <div class="ttd-kiri">
            <br>Yang Mengaku,<br><br><br><br>
            <span class="ttd-name">{{ $data->anggotaJemaat->nama_lengkap }}</span>
        </div>
        <div class="ttd-box">
            <div class="ttd-date">
                Ditetapkan di: {{ $data->anggotaJemaat->jemaat->kota ?? 'Papua' }}<br>
                Pada Tanggal: {{ date('d F Y') }}
            </div>
            <div class="ttd-role">Pelayan Firman / Ketua Majelis,</div>
            <span class="ttd-name">{{ $data->pendeta_pelayan }}</span>
            <span class="ttd-jabatan">Pendeta Jemaat</span>
        </div>
        <div style="clear:both"></div>
    </div>
</div>

</body>
</html>