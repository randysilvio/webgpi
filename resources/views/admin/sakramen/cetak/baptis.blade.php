<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Baptis - {{ $data->anggotaJemaat->nama_lengkap }}</title>
    <style>
        @page { margin: 20px 30px; }
        body { font-family: 'Times New Roman', serif; color: #000; line-height: 1.3; }
        
        /* KOP SURAT */
        .kop-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 5px; margin-bottom: 20px; }
        .kop-logo { width: 80px; height: auto; }
        .kop-header { font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 0; text-align: center; }
        .kop-subheader { font-size: 12px; font-weight: bold; margin: 2px 0; text-align: center; }
        .kop-address { font-size: 10px; font-style: italic; margin-top: 2px; text-align: center; }

        /* JUDUL */
        .judul-surat { font-size: 22px; font-weight: bold; text-align: center; text-decoration: underline; margin-bottom: 2px; text-transform: uppercase; }
        .nomor-surat { text-align: center; font-size: 12px; margin-bottom: 30px; font-weight: bold; }

        /* ISI */
        .content { font-size: 14px; text-align: justify; margin: 0 20px; }
        .ayat-alkitab { font-style: italic; text-align: center; font-size: 12px; margin: 10px 40px 30px 40px; color: #333; }
        
        .info-table { width: 100%; margin-top: 10px; }
        .info-table td { vertical-align: top; padding: 5px 0; }
        .label { width: 160px; font-weight: bold; }
        .titik-dua { width: 10px; }
        .isian { font-weight: bold; text-transform: uppercase; font-size: 15px; }

        /* FOOTER */
        .footer-table { width: 100%; margin-top: 60px; page-break-inside: avoid; }
        .ttd-box { text-align: center; width: 40%; float: right; }
        .ttd-name { font-weight: bold; text-decoration: underline; margin-top: 70px; display: block; }
        
        .watermark { 
            position: fixed; top: 35%; left: 25%; width: 50%; opacity: 0.05; z-index: -1; transform: rotate(-30deg);
            font-size: 80px; font-weight: bold; text-align: center; border: 5px solid #000; padding: 20px;
        }
    </style>
</head>
<body>
    {{-- WATERMARK --}}
    <div class="watermark">BAPTISAN KUDUS</div>

    {{-- KOP SURAT --}}
    <table class="kop-table">
        <tr>
            <td style="width: 15%; text-align: center; vertical-align: middle;">
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

    <div class="judul-surat">SURAT BAPTISAN KUDUS</div>
    <div class="nomor-surat">No. Akta: {{ $data->no_akta_baptis }}</div>

    <div class="content">
        <div class="ayat-alkitab">
            "Karena itu pergilah, jadikanlah semua bangsa murid-Ku dan baptislah mereka dalam nama Bapa dan Anak dan Roh Kudus." <br><strong>(Matius 28:19)</strong>
        </div>

        <p>Berdasarkan pengakuan iman Gereja dan amanat Tuhan Yesus Kristus, telah dilayani Sakramen Baptisan Kudus kepada:</p>

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
                <td class="label">Jenis Kelamin</td>
                <td class="titik-dua">:</td>
                <td>{{ $data->anggotaJemaat->jenis_kelamin }}</td>
            </tr>
            <tr>
                <td class="label">Nama Ayah</td>
                <td class="titik-dua">:</td>
                <td>{{ $data->anggotaJemaat->ayah->nama_lengkap ?? $data->anggotaJemaat->nama_ayah ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nama Ibu</td>
                <td class="titik-dua">:</td>
                <td>{{ $data->anggotaJemaat->ibu->nama_lengkap ?? $data->anggotaJemaat->nama_ibu ?? '-' }}</td>
            </tr>
        </table>

        <br>
        <table class="info-table">
            <tr>
                <td class="label">Dilaksanakan Pada</td>
                <td class="titik-dua">:</td>
                <td><strong>{{ \Carbon\Carbon::parse($data->tanggal_baptis)->isoFormat('dddd, D MMMM Y') }}</strong></td>
            </tr>
            <tr>
                <td class="label">Bertempat di</td>
                <td class="titik-dua">:</td>
                <td>{{ $data->tempat_baptis }}</td>
            </tr>
            <tr>
                <td class="label">Dilayani Oleh</td>
                <td class="titik-dua">:</td>
                <td>{{ $data->pendeta_pelayan }}</td>
            </tr>
        </table>

        <div class="footer-table">
            <div class="ttd-box">
                Ditetapkan di: {{ $data->anggotaJemaat->jemaat->nama_jemaat ?? 'Papua' }}<br>
                Pada Tanggal: {{ date('d-m-Y') }}<br>
                <br>
                Pelayan Firman / Ketua Majelis,
                <span class="ttd-name">{{ $data->pendeta_pelayan }}</span>
            </div>
        </div>
    </div>
</body>
</html>