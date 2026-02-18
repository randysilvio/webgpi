<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Baptis - {{ $data->anggotaJemaat->nama_lengkap }}</title>
    <style>
        /* Mengatur ukuran kertas A4 dan margin 0 agar kita bisa atur manual di body */
        @page { 
            margin: 0; 
            size: A4 portrait;
        }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            color: #000; 
            margin: 0;
            padding: 0;
            line-height: 1.3; /* Line height sedikit dirapatkan */
        }

        /* CONTAINER UTAMA (BINGKAI)
           Menggunakan ukuran absolut (mm) agar presisi di A4 (210x297mm).
           Dikurangi margin sekitar 10-15mm di tiap sisi.
        */
        .container {
            width: 180mm;      /* Lebar A4 (210) - margin kiri kanan */
            height: 267mm;     /* Tinggi A4 (297) - margin atas bawah */
            margin: 15mm auto; /* Posisi tengah */
            padding: 10mm;     /* Jarak konten dari bingkai */
            border: 5px double #333; /* Bingkai Ganda Elegan */
            position: relative;
            box-sizing: border-box; /* Agar padding tidak menambah ukuran total */
        }

        /* WATERMARK LOGO */
        .watermark {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 350px; /* Ukuran watermark disesuaikan */
            opacity: 0.08;
            z-index: -1;
            filter: grayscale(100%);
        }

        /* KOP SURAT */
        .kop-table { 
            width: 100%; 
            border-bottom: 2px solid #000; 
            padding-bottom: 10px; 
            margin-bottom: 20px; /* Jarak ke judul dikurangi */
        }
        .kop-logo { width: 80px; height: auto; object-fit: contain; }
        .kop-text { text-align: center; text-transform: uppercase; }
        .kop-header-main { font-size: 14pt; font-weight: bold; letter-spacing: 1px; margin: 0; }
        .kop-header-sub { font-size: 12pt; font-weight: bold; margin: 0; }
        .kop-subheader { font-size: 11pt; font-weight: bold; margin: 3px 0; }
        .kop-address { font-size: 9pt; font-style: italic; text-transform: none; margin-top: 2px; font-weight: normal; }

        /* JUDUL SURAT */
        .judul-container { text-align: center; margin-bottom: 20px; }
        .judul-surat { 
            font-size: 20pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            border-bottom: 1px solid #000; 
            display: inline-block;
            padding-bottom: 3px;
        }
        .nomor-surat { 
            font-size: 11pt; 
            font-weight: bold; 
            margin-top: 5px; 
            font-family: Arial, sans-serif;
        }

        /* AYAT ALKITAB */
        .ayat-box {
            text-align: center;
            font-style: italic;
            font-size: 10pt;
            margin: 0 40px 25px 40px; /* Margin bawah dikurangi */
            line-height: 1.3;
            color: #444;
        }

        /* KONTEN UTAMA */
        .content { margin: 0 10px; font-size: 11pt; }
        .preambule { text-align: center; margin-bottom: 15px; }

        /* TABEL DATA */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 4px 5px; vertical-align: top; } /* Padding baris dirapatkan */
        .label { width: 32%; font-weight: bold; color: #333; }
        .sep { width: 3%; text-align: center; }
        
        .data { 
            width: 65%; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 12pt; 
            border-bottom: 1px dotted #999; 
        }
        .data-small {
            font-weight: bold; 
            font-size: 11pt;
            border-bottom: 1px dotted #ccc;
        }

        /* FOOTER TTD */
        .footer { margin-top: 40px; width: 100%; } /* Jarak footer ke atas dikurangi */
        .ttd-box { 
            float: right; 
            width: 50%; 
            text-align: center; 
        }
        .ttd-date { font-size: 11pt; margin-bottom: 5px; }
        .ttd-role { font-weight: bold; margin-bottom: 70px; font-size: 11pt; } /* Ruang tanda tangan */
        .ttd-name { 
            font-weight: bold; 
            text-decoration: underline; 
            font-size: 12pt; 
            display: block; 
            text-transform: uppercase;
        }
        .ttd-jabatan { font-size: 10pt; }

    </style>
</head>
<body>

<div class="container">

    {{-- LOGIKA BASE64 UNTUK GAMBAR (Agar Logo Pasti Muncul di PDF) --}}
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

    {{-- WATERMARK --}}
    @if($logoBase64)
        <img src="{{ $logoBase64 }}" class="watermark">
    @endif

    {{-- KOP SURAT --}}
    <table class="kop-table">
        <tr>
            <td width="15%" style="text-align: center;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="kop-logo">
                @else
                    <div style="font-size:9px; border:1px solid #000; padding:5px; width:50px; margin:auto;">LOGO</div>
                @endif
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

    {{-- JUDUL --}}
    <div class="judul-container">
        <span class="judul-surat">Surat Baptisan Kudus</span>
        <div class="nomor-surat">No. Akta: {{ $data->no_akta_baptis }}</div>
    </div>

    {{-- AYAT --}}
    <div class="ayat-box">
        "Karena itu pergilah, jadikanlah semua bangsa murid-Ku dan baptislah mereka dalam nama Bapa dan Anak dan Roh Kudus." <br>
        <strong>(Matius 28:19)</strong>
    </div>

    <div class="content">
        <p class="preambule">
            Berdasarkan Pengakuan Iman Gereja dan Amanat Tuhan Yesus Kristus, <br>
            telah dilayani <strong>Sakramen Baptisan Kudus</strong> kepada:
        </p>

        {{-- TABEL BIODATA --}}
        <table class="info-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="sep">:</td>
                <td class="data" style="font-size: 14pt;">{{ $data->anggotaJemaat->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="sep">:</td>
                <td class="data">{{ $data->anggotaJemaat->tempat_lahir }}, {{ $data->anggotaJemaat->tanggal_lahir ? $data->anggotaJemaat->tanggal_lahir->isoFormat('D MMMM Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="sep">:</td>
                <td class="data">{{ $data->anggotaJemaat->jenis_kelamin }}</td>
            </tr>
            <tr>
                <td class="label">Nama Ayah</td>
                <td class="sep">:</td>
                <td class="data">{{ $data->anggotaJemaat->ayah->nama_lengkap ?? $data->anggotaJemaat->nama_ayah ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nama Ibu</td>
                <td class="sep">:</td>
                <td class="data">{{ $data->anggotaJemaat->ibu->nama_lengkap ?? $data->anggotaJemaat->nama_ibu ?? '-' }}</td>
            </tr>
        </table>

        <br>
        <p class="preambule" style="text-align: left; margin-bottom: 10px;">
            Baptisan Kudus ini dilaksanakan dalam Ibadah Jemaat pada:
        </p>

        {{-- TABEL PELAKSANAAN --}}
        <table class="info-table">
            <tr>
                <td class="label">Hari / Tanggal</td>
                <td class="sep">:</td>
                <td class="data-small"><strong>{{ \Carbon\Carbon::parse($data->tanggal_baptis)->isoFormat('dddd, D MMMM Y') }}</strong></td>
            </tr>
            <tr>
                <td class="label">Bertempat di</td>
                <td class="sep">:</td>
                <td class="data-small">{{ $data->tempat_baptis }}</td>
            </tr>
            <tr>
                <td class="label">Dilayani Oleh</td>
                <td class="sep">:</td>
                <td class="data-small">{{ $data->pendeta_pelayan }}</td>
            </tr>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="ttd-box">
            <div class="ttd-date">
                Ditetapkan di: {{ $data->anggotaJemaat->jemaat->kota ?? 'Papua' }}<br>
                Pada Tanggal: {{ date('d F Y') }}
            </div>
            <div class="ttd-role">Pelayan Firman / Ketua Majelis,</div>
            
            <span class="ttd-name">{{ $data->pendeta_pelayan }}</span>
            <span class="ttd-jabatan">Pendeta Jemaat</span>
        </div>
    </div>

</div>

</body>
</html>