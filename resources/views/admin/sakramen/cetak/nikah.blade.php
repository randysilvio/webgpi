<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akta Nikah - {{ $data->no_akta_nikah }}</title>
    <style>
        @page { margin: 0; size: A4 portrait; }
        body { font-family: 'Times New Roman', Times, serif; color: #000; margin: 0; padding: 0; line-height: 1.3; }

        /* BINGKAI ORNAMEN */
        .container {
            width: 180mm; height: 267mm; margin: 15mm auto; padding: 10mm;
            border: 5px double #333; position: relative; box-sizing: border-box;
        }

        /* LOGO WATERMARK */
        .watermark {
            position: absolute; top: 55%; left: 50%; transform: translate(-50%, -50%);
            width: 350px; opacity: 0.08; z-index: -1; filter: grayscale(100%);
        }

        /* KOP SURAT */
        .kop-table { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 25px; }
        .kop-logo { width: 80px; height: auto; object-fit: contain; }
        .kop-text { text-align: center; text-transform: uppercase; }
        .kop-header-main { font-size: 14pt; font-weight: bold; letter-spacing: 1px; margin: 0; }
        .kop-header-sub { font-size: 12pt; font-weight: bold; margin: 0; }
        .kop-subheader { font-size: 11pt; font-weight: bold; margin: 3px 0; }
        .kop-address { font-size: 9pt; font-style: italic; text-transform: none; font-weight: normal; }

        /* JUDUL */
        .judul-container { text-align: center; margin-bottom: 25px; }
        .judul-surat { font-size: 22pt; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; border-bottom: 1px solid #000; padding-bottom: 3px; }
        .nomor-surat { font-size: 11pt; font-weight: bold; margin-top: 5px; font-family: Arial, sans-serif; }

        /* KONTEN */
        .content { margin: 0 10px; font-size: 11pt; text-align: justify; }
        .ayat-box { text-align: center; font-style: italic; font-size: 10pt; margin: 0 40px 25px 40px; color: #444; }
        .preambule { text-align: center; margin-bottom: 20px; }

        /* BOX MEMPELAI */
        .mempelai-container { border: 2px solid #333; padding: 20px; margin: 20px 0; }
        .mempelai-block { margin-bottom: 15px; }
        .mempelai-label { font-size: 10pt; font-weight: bold; color: #555; text-transform: uppercase; letter-spacing: 1px; }
        .mempelai-nama { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin: 5px 0 10px 0; color: #000; }
        
        .separator { text-align: center; margin: 15px 0; font-weight: bold; font-style: italic; font-size: 12pt; color: #666; }

        /* TABEL INFO */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .label { width: 120px; color: #333; font-weight: bold; font-size: 10pt; }
        
        /* FOOTER */
        .footer { margin-top: 40px; width: 100%; }
        .ttd-box { float: right; width: 50%; text-align: center; }
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
                    {{ $data->suami->jemaat->klasis->nama_klasis ?? 'Klasis ...' }}<br>
                    {{ $data->suami->jemaat->nama_jemaat ?? 'Jemaat ...' }}
                </div>
                <div class="kop-address">{{ $setting->contact_address ?? 'Alamat Kantor Jemaat' }}</div>
            </td>
        </tr>
    </table>

    <div class="judul-container">
        <span class="judul-surat">Akta Pemberkatan Nikah</span>
        <div class="nomor-surat">Nomor: {{ $data->no_akta_nikah }}</div>
    </div>

    <div class="ayat-box">
        "Demikianlah mereka bukan lagi dua, melainkan satu. Karena itu, apa yang telah dipersatukan Allah, tidak boleh diceraikan manusia." <br><strong>(Matius 19:6)</strong>
    </div>

    <div class="content">
        <p class="preambule">
            Pada hari ini, <strong>{{ \Carbon\Carbon::parse($data->tanggal_nikah)->isoFormat('dddd, D MMMM Y') }}</strong>, 
            bertempat di dalam Persekutuan Ibadah Jemaat <strong>{{ $data->tempat_nikah }}</strong>, 
            telah dilaksanakan Peneguhan dan Pemberkatan Nikah Kudus antara:
        </p>

        <div class="mempelai-container">
            {{-- PRIA --}}
            <div class="mempelai-block">
                <div class="mempelai-label">Mempelai Pria</div>
                <div class="mempelai-nama">{{ $data->suami->nama_lengkap }}</div>
                <table class="info-table">
                    <tr><td class="label">Tempat/Tgl Lahir</td><td>: {{ $data->suami->tempat_lahir }}, {{ $data->suami->tanggal_lahir ? $data->suami->tanggal_lahir->isoFormat('D MMMM Y') : '-' }}</td></tr>
                    <tr><td class="label">Asal Jemaat</td><td>: {{ $data->suami->jemaat->nama_jemaat ?? '-' }}</td></tr>
                </table>
            </div>

            <div class="separator">&mdash; DENGAN &mdash;</div>

            {{-- WANITA --}}
            <div class="mempelai-block">
                <div class="mempelai-label">Mempelai Wanita</div>
                <div class="mempelai-nama">{{ $data->istri->nama_lengkap }}</div>
                <table class="info-table">
                    <tr><td class="label">Tempat/Tgl Lahir</td><td>: {{ $data->istri->tempat_lahir }}, {{ $data->istri->tanggal_lahir ? $data->istri->tanggal_lahir->isoFormat('D MMMM Y') : '-' }}</td></tr>
                    <tr><td class="label">Asal Jemaat</td><td>: {{ $data->istri->jemaat->nama_jemaat ?? '-' }}</td></tr>
                </table>
            </div>
        </div>

        <p class="preambule">
            Pemberkatan dilayani oleh Hamba-Nya: <br>
            <strong style="font-size: 12pt; text-transform: uppercase;">{{ $data->pendeta_pelayan }}</strong>
        </p>
    </div>

    <div class="footer">
        <div class="ttd-box">
            <div class="ttd-date">
                Ditetapkan di: {{ $data->suami->jemaat->kota ?? 'Papua' }}<br>
                Pada Tanggal: {{ \Carbon\Carbon::parse($data->tanggal_nikah)->isoFormat('D MMMM Y') }}
            </div>
            <div class="ttd-role">Pelayan Firman / Pendeta Jemaat,</div>
            <span class="ttd-name">{{ $data->pendeta_pelayan }}</span>
        </div>
    </div>
</div>

</body>
</html>