<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akta Nikah - {{ $data->no_akta_nikah }}</title>
    <style>
        /* Mengatur Margin Halaman agar Muat 1 Lembar */
        @page { margin: 20px 30px; }
        
        body { 
            font-family: 'Times New Roman', serif; 
            color: #000; 
            line-height: 1.3; /* Jarak baris lebih rapat */
        }
        
        /* KOP SURAT (Lebih Padat) */
        .kop-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 5px; margin-bottom: 15px; }
        .kop-logo { width: 80px; height: auto; } /* Logo sedikit diperkecil */
        .kop-text { text-align: center; }
        .kop-header { font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .kop-subheader { font-size: 12px; font-weight: bold; margin: 2px 0; }
        .kop-address { font-size: 10px; font-style: italic; margin-top: 2px; }

        /* ISI SURAT */
        .judul-surat { font-size: 20px; font-weight: bold; text-align: center; text-decoration: underline; margin-bottom: 2px; text-transform: uppercase; }
        .nomor-surat { text-align: center; font-size: 12px; margin-bottom: 20px; font-weight: bold; }
        
        .content { font-size: 12px; text-align: justify; } /* Font isi 12px agar pas */
        .ayat-alkitab { 
            font-style: italic; 
            text-align: center; 
            font-size: 11px; 
            margin: 10px 30px 20px 30px; 
            color: #333; 
            line-height: 1.4;
        }
        
        /* BOX DATA MEMPELAI (Compact) */
        .mempelai-container { 
            border: 1px solid #000; 
            padding: 15px; 
            margin: 15px 0; 
        }
        .mempelai-block { margin-bottom: 10px; }
        .mempelai-title { font-weight: bold; text-decoration: underline; font-size: 11px; margin-bottom: 3px; }
        .mempelai-name { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; color: #000; }
        
        .info-table { width: 100%; }
        .info-table td { vertical-align: top; padding: 1px 0; } /* Padding baris tabel diperkecil */
        .label { width: 110px; color: #444; font-size: 12px; }
        .separator { text-align: center; margin: 10px 0; font-weight: bold; font-style: italic; font-size: 11px; }

        /* TTD AREA (Posisi di Bawah) */
        .footer-table { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .ttd-box { text-align: center; vertical-align: top; width: 33%; }
        .ttd-name { font-weight: bold; text-decoration: underline; margin-top: 60px; display: block; font-size: 12px; }
        .ttd-role { font-size: 11px; }

        .footer-note { font-size: 9px; margin-top: 20px; text-align: center; border-top: 1px solid #ccc; padding-top: 5px; color: #666; font-style: italic; }
    </style>
</head>
<body>

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
                @if($path)
                    <img src="{{ $path }}" class="kop-logo" alt="Logo">
                @else
                    <div style="width: 70px; height: 70px; border: 1px solid #000; display:flex; align-items:center; justify-content:center; font-size:9px;">NO LOGO</div>
                @endif
            </td>
            <td class="kop-text" style="width: 85%; vertical-align: middle;">
                <h1 class="kop-header">GEREJA PROTESTAN INDONESIA DI PAPUA</h1>
                <h2 class="kop-header">(GPI PAPUA)</h2>
                <div class="kop-subheader">
                    {{ $data->suami->jemaat->klasis->nama_klasis ?? 'KLASIS ...' }}<br>
                    {{ $data->suami->jemaat->nama_jemaat ?? 'JEMAAT ...' }}
                </div>
                <div class="kop-address">
                    {{ $setting->contact_address ?? 'Alamat Jemaat Belum Diatur' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- JUDUL --}}
    <div class="judul-surat">AKTA PEMBERKATAN NIKAH</div>
    <div class="nomor-surat">Nomor: {{ $data->no_akta_nikah }}</div>

    <div class="content">
        {{-- AYAT --}}
        <div class="ayat-alkitab">
            "Demikianlah mereka bukan lagi dua, melainkan satu. Karena itu, apa yang telah dipersatukan Allah, tidak boleh diceraikan manusia." <br><strong>(Matius 19:6)</strong>
        </div>

        <p>
            Pada hari ini, <strong>{{ \Carbon\Carbon::parse($data->tanggal_nikah)->isoFormat('dddd, D MMMM Y') }}</strong>, 
            bertempat di dalam Persekutuan Ibadah Jemaat <strong>{{ $data->tempat_nikah }}</strong>, 
            telah dilaksanakan Peneguhan dan Pemberkatan Nikah Kudus antara:
        </p>

        {{-- BOX MEMPELAI --}}
        <div class="mempelai-container">
            {{-- PRIA --}}
            <div class="mempelai-block">
                <div class="mempelai-title">MEMPELAI PRIA:</div>
                <div class="mempelai-name">{{ $data->suami->nama_lengkap }}</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Tempat/Tgl Lahir</td>
                        <td>: {{ $data->suami->tempat_lahir }}, {{ $data->suami->tanggal_lahir ? \Carbon\Carbon::parse($data->suami->tanggal_lahir)->isoFormat('D MMMM Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Asal Jemaat</td>
                        <td>: {{ $data->suami->jemaat->nama_jemaat ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="separator">— DENGAN —</div>

            {{-- WANITA --}}
            <div class="mempelai-block" style="margin-bottom: 0;">
                <div class="mempelai-title">MEMPELAI WANITA:</div>
                <div class="mempelai-name">{{ $data->istri->nama_lengkap }}</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Tempat/Tgl Lahir</td>
                        <td>: {{ $data->istri->tempat_lahir }}, {{ $data->istri->tanggal_lahir ? \Carbon\Carbon::parse($data->istri->tanggal_lahir)->isoFormat('D MMMM Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Asal Jemaat</td>
                        <td>: {{ $data->istri->jemaat->nama_jemaat ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <p>
            Pemberkatan dilayani oleh Hamba-Nya: <br>
            <strong>{{ $data->pendeta_pelayan }}</strong>
        </p>

        <p>
            Demikian Akta Nikah ini dibuat dan diberikan sebagai bukti sahnya pernikahan gerejawi menurut Tata Gereja GPI Papua.
        </p>

        {{-- TANDA TANGAN --}}
        <table class="footer-table">
            <tr>
                <td class="ttd-box">
                    <br>Mempelai Pria,
                    <span class="ttd-name">{{ $data->suami->nama_lengkap }}</span>
                </td>
                <td class="ttd-box">
                    Ditetapkan di: {{ $data->suami->jemaat->nama_jemaat ?? 'Papua' }}<br>
                    Pada Tanggal: {{ \Carbon\Carbon::parse($data->tanggal_nikah)->isoFormat('D MMMM Y') }}<br>
                    <br>
                    Pelayan Firman,
                    <span class="ttd-name">{{ $data->pendeta_pelayan }}</span>
                    <span class="ttd-role">Pendeta Jemaat</span>
                </td>
                <td class="ttd-box">
                    <br>Mempelai Wanita,
                    <span class="ttd-name">{{ $data->istri->nama_lengkap }}</span>
                </td>
            </tr>
        </table>

        <div class="footer-note">
            Dokumen ini dicetak secara otomatis melalui Sistem Informasi Manajemen Gereja (SIM-G) Sinode GPI Papua.<br>
            ID Arsip: {{ $data->no_akta_nikah }}/{{ date('dmY') }}
        </div>
    </div>
</body>
</html>