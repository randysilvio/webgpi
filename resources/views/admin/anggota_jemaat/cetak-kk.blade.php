<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Keluarga Gereja - {{ $anggota->nama_kepala_keluarga ?? $anggota->nama_lengkap }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 15mm 10mm 15mm; /* Atas Kanan Bawah Kiri - Agak lebar di samping agar rapi */
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
        }
        
        /* HEADER */
        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }
        .header-logo {
            width: 10%;
            text-align: left;
            vertical-align: middle;
        }
        .header-text {
            width: 90%;
            text-align: center;
            vertical-align: middle;
            padding-right: 10%; /* Kompensasi agar teks benar-benar di tengah relatif terhadap halaman */
        }
        .header-text h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .header-text h2 {
            margin: 2px 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-text p {
            margin: 0;
            font-size: 9pt;
            font-style: italic;
        }

        /* JUDUL KK */
        .judul-dokumen {
            text-align: center;
            margin-bottom: 15px;
        }
        .judul-dokumen h2 {
            text-decoration: underline;
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .judul-dokumen span {
            font-size: 10pt;
            font-weight: bold;
        }

        /* INFO DATA KELUARGA (ATAS) */
        .info-kk {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        .info-kk td {
            padding: 1px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 160px;
        }
        .separator {
            width: 10px;
            text-align: center;
        }
        .isi {
            text-transform: uppercase;
            font-weight: bold;
        }

        /* TABEL ANGGOTA KELUARGA */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt; /* Font tabel diperkecil agar muat */
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #e0e0e0;
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 8pt;
        }
        .text-center { text-align: center; }
        
        /* FOOTER TANDA TANGAN */
        .footer {
            margin-top: 25px; /* Jarak dari tabel */
            width: 100%;
            page-break-inside: avoid; /* Mencegah terpotong ke halaman baru */
        }
        .signature-box {
            width: 30%;
            text-align: center;
            float: right;
        }
        .signature-box-left {
            width: 30%;
            text-align: center;
            float: left;
        }
        .signature-space {
            height: 50px; /* Ruang tanda tangan */
        }
        .nama-terang {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    {{-- HEADER / KOP SURAT --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                {{-- Logika Gambar Logo --}}
                @if(isset($setting) && $setting->logo)
                    <img src="{{ public_path('storage/' . $setting->logo) }}" width="70" alt="Logo">
                @else
                    <div style="width: 70px; height: 70px; background: #eee; border:1px dashed #999; text-align:center; line-height:70px; font-size:10px;">LOGO</div>
                @endif
            </td>
            <td class="header-text">
                <h1>GEREJA PROTESTAN INDONESIA DI PAPUA</h1>
                <h1>(GPI PAPUA)</h1>
                <h2>{{ $anggota->jemaat->klasis->nama_klasis ?? 'KLASIS ...' }} - {{ $anggota->jemaat->nama_jemaat ?? 'JEMAAT ...' }}</h2>
                <p>{{ $anggota->jemaat->alamat ?? 'Alamat Jemaat Belum Diisi' }}</p>
            </td>
        </tr>
    </table>

    {{-- JUDUL DOKUMEN --}}
    <div class="judul-dokumen">
        <h2>KARTU KELUARGA JEMAAT</h2>
        <span>NOMOR KK: {{ $anggota->nomor_kk ?? $anggota->kode_keluarga_internal ?? '-' }}</span>
    </div>

    {{-- INFORMASI KEPALA KELUARGA --}}
    <table class="info-kk">
        <tr>
            <td class="label">Nama Kepala Keluarga</td>
            <td class="separator">:</td>
            <td class="isi" width="40%">{{ $anggota->nama_kepala_keluarga ?? $anggota->nama_lengkap }}</td>
            
            <td class="label" style="padding-left: 30px;">Sektor Pelayanan</td>
            <td class="separator">:</td>
            <td class="isi">{{ $anggota->sektor_pelayanan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="separator">:</td>
            <td class="isi">{{ $anggota->alamat_lengkap ?? '-' }}</td>

            <td class="label" style="padding-left: 30px;">Unit Pelayanan</td>
            <td class="separator">:</td>
            <td class="isi">{{ $anggota->unit_pelayanan ?? '-' }}</td>
        </tr>
    </table>

    {{-- TABEL DATA ANGGOTA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="20%">Nama Lengkap</th>
                <th width="10%">NIK</th>
                <th width="3%">L/P</th>
                <th width="15%">Tempat, Tanggal Lahir</th>
                <th width="10%">Hubungan</th>
                <th width="10%">Pendidikan</th>
                <th width="10%">Pekerjaan</th>
                <th width="5%">Baptis</th>
                <th width="5%">Sidi</th>
                <th width="5%">Nikah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($keluarga as $index => $kel)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td style="font-weight: bold; text-transform: uppercase;">{{ $kel->nama_lengkap }}</td>
                <td class="text-center">{{ $kel->nik ?? '-' }}</td>
                <td class="text-center">{{ $kel->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}</td>
                <td>
                    {{ $kel->tempat_lahir ?? '' }}, 
                    {{ $kel->tanggal_lahir ? \Carbon\Carbon::parse($kel->tanggal_lahir)->isoFormat('D MMM Y') : '-' }}
                </td>
                <td class="text-center">{{ $kel->status_dalam_keluarga }}</td>
                <td class="text-center">{{ $kel->pendidikan_terakhir ?? '-' }}</td>
                <td class="text-center">{{ $kel->pekerjaan_utama ?? '-' }}</td>
                
                {{-- Status Sakramen (Centang) --}}
                <td class="text-center" style="font-family: DejaVu Sans, sans-serif;">
                    {{ ($kel->tanggal_baptis || $kel->dataBaptis) ? '✔' : '-' }}
                </td>
                <td class="text-center" style="font-family: DejaVu Sans, sans-serif;">
                    {{ ($kel->tanggal_sidi || $kel->dataSidi) ? '✔' : '-' }}
                </td>
                <td class="text-center" style="font-family: DejaVu Sans, sans-serif;">
                    {{ ($kel->status_pernikahan == 'Kawin') ? '✔' : '-' }}
                </td>
            </tr>
            @endforeach

            {{-- Baris Pelengkap agar tabel tidak terlihat kosong jika anggota sedikit --}}
            @php $sisaBaris = 5 - count($keluarga); @endphp
            @if($sisaBaris > 0)
                @for($i = 0; $i < $sisaBaris; $i++)
                <tr>
                    <td class="text-center" style="color: #eee;">.</td>
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
                @endfor
            @endif
        </tbody>
    </table>

    {{-- FOOTER TANDA TANGAN --}}
    <div class="footer">
        {{-- Tanda Tangan Kiri --}}
        <div class="signature-box-left">
            <p>Mengetahui,<br>Ketua Majelis Jemaat</p>
            <div class="signature-space"></div>
            <p class="nama-terang">( ..................................... )</p>
        </div>

        {{-- Tanda Tangan Kanan --}}
        <div class="signature-box">
            <p>
                {{ $anggota->jemaat->kota ?? 'Papua' }}, {{ date('d F Y') }}<br>
                Kepala Keluarga
            </p>
            <div class="signature-space"></div>
            <p class="nama-terang">{{ $anggota->nama_kepala_keluarga ?? $anggota->nama_lengkap }}</p>
        </div>
    </div>

</body>
</html>