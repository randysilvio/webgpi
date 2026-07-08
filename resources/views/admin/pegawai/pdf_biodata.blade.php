<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Biodata Personel - {{ $pegawai->nama_lengkap }}</title>
    <style>
        @page { size: A4 portrait; margin: 20mm 15mm; }
        body { font-family: 'Arial', sans-serif; font-size: 10pt; color: #111; line-height: 1.4; }
        
        /* KOP SURAT FORMAL */
        .kop-table { width: 100%; border-bottom: 3px solid #111; margin-bottom: 15px; padding-bottom: 10px; }
        .kop-logo img { height: 60px; width: auto; }
        .kop-text { text-align: center; }
        .kop-title { font-size: 13pt; font-weight: bold; text-transform: uppercase; margin: 0; letter-spacing: 1px; }
        .kop-subtitle { font-size: 11pt; font-weight: bold; margin: 2px 0; color: #333; }
        .kop-address { font-size: 8pt; color: #555; }

        /* JUDUL DOKUMEN */
        .doc-title { text-align: center; margin-bottom: 25px; }
        .doc-title h3 { text-decoration: underline; text-transform: uppercase; margin: 0; font-size: 12pt; }
        .doc-title p { font-size: 9pt; margin-top: 5px; }

        /* TABEL IDENTITAS */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 4px; vertical-align: top; font-size: 10pt; }
        .label { width: 150px; font-weight: bold; }
        .sep { width: 15px; text-align: center; font-weight: bold; }
        
        /* FOTO PROFIL */
        .photo-box { position: absolute; top: 120px; right: 10px; width: 3cm; height: 4cm; border: 1px solid #111; padding: 2px; }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }
        .no-photo { width: 100%; height: 100%; background: #f3f4f6; text-align: center; line-height: 4cm; font-size: 8pt; color: #888; }

        /* SUB HEADERS */
        h4 { font-size: 11pt; border-bottom: 1px solid #333; padding-bottom: 3px; margin-bottom: 10px; margin-top: 20px; text-transform: uppercase; }

        /* DATA TABEL RIWAYAT */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #111; padding: 5px; font-size: 9pt; }
        .data-table th { background-color: #e5e7eb; text-align: center; font-weight: bold; text-transform: uppercase; }
        
        /* FOOTER */
        .footer { margin-top: 50px; width: 100%; }
        .signature-box { float: right; width: 40%; text-align: center; font-size: 9pt; }
        .signature-space { height: 60px; }
    </style>
</head>
<body>

    @php
        $logoBase64 = null;
        $setting = \App\Models\Setting::first();
        if($setting && $setting->logo_path) {
            $path = storage_path('app/public/' . $setting->logo_path);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $dataImg = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
            }
        }

        $fotoBase64 = null;
        if($pegawai->foto_diri) {
            $fPath = storage_path('app/public/' . $pegawai->foto_diri);
            if (file_exists($fPath)) {
                $fType = pathinfo($fPath, PATHINFO_EXTENSION);
                $fDataImg = file_get_contents($fPath);
                $fotoBase64 = 'data:image/' . $fType . ';base64,' . base64_encode($fDataImg);
            }
        }
    @endphp

    <table class="kop-table">
        <tr>
            <td width="15%" align="center" class="kop-logo">
                @if($logoBase64) <img src="{{ $logoBase64 }}"> @endif
            </td>
            <td width="85%" class="kop-text">
                <h1 class="kop-title">Gereja Protestan Indonesia di Papua</h1>
                <h2 class="kop-subtitle">MAJELIS PEKERJA SINODE</h2>
                <div class="kop-address">{{ $setting->address ?? 'Fakfak, Papua Barat' }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-title">
        <h3>Kutipan Buku Induk Kepegawaian</h3>
        <p>No. Registrasi: #P-{{ str_pad($pegawai->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="photo-box">
        @if($fotoBase64)
            <img src="{{ $fotoBase64 }}">
        @else
            <div class="no-photo">FOTO 3x4</div>
        @endif
    </div>

    <h4>A. Keterangan Identitas Diri</h4>
    <table class="info-table">
        <tr><td class="label">Nama Lengkap & Gelar</td><td class="sep">:</td><td>{{ $pegawai->gelar_depan }} {{ $pegawai->nama_lengkap }} {{ $pegawai->gelar_belakang }}</td></tr>
        <tr><td class="label">Nomor Induk (NIPG)</td><td class="sep">:</td><td><strong>{{ $pegawai->nipg ?? '-' }}</strong></td></tr>
        <tr><td class="label">Tempat, Tgl Lahir</td><td class="sep">:</td><td>{{ $pegawai->tempat_lahir ?? '-' }}, {{ $pegawai->tanggal_lahir ? \Carbon\Carbon::parse($pegawai->tanggal_lahir)->format('d F Y') : '-' }}</td></tr>
        <tr><td class="label">Jenis Kelamin</td><td class="sep">:</td><td>{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-Laki' : 'Perempuan' }}</td></tr>
        <tr><td class="label">Status Perkawinan</td><td class="sep">:</td><td>{{ $pegawai->status_pernikahan ?? '-' }}</td></tr>
        <tr><td class="label">Alamat Domisili</td><td class="sep">:</td><td>{{ $pegawai->alamat_domisili ?? '-' }}</td></tr>
    </table>

    <h4>B. Kedudukan & Penugasan</h4>
    <table class="info-table">
        <tr><td class="label">Klasifikasi Personel</td><td class="sep">:</td><td>{{ strtoupper($pegawai->jenis_pegawai) }} ({{ $pegawai->status_kepegawaian }})</td></tr>
        <tr><td class="label">Status Aktif</td><td class="sep">:</td><td>{{ strtoupper($pegawai->status_aktif) }}</td></tr>
        <tr><td class="label">Lokasi Penugasan</td><td class="sep">:</td>
            <td>
                @if($pegawai->jemaat) Jemaat {{ $pegawai->jemaat->nama_jemaat }}
                @elseif($pegawai->klasis) Klasis {{ $pegawai->klasis->nama_klasis }}
                @else Instansi Pusat Sinode @endif
            </td>
        </tr>
    </table>

    <h4>C. Riwayat Kepangkatan (SK)</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th width="15%">Tanggal TMT</th>
                <th width="25%">Nomor Surat Keputusan</th>
                <th width="25%">Klasifikasi SK</th>
                <th width="35%">Jabatan & Golongan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pegawai->riwayatSk ?? [] as $sk)
            <tr>
                <td align="center">{{ \Carbon\Carbon::parse($sk->tmt_sk)->format('d-m-Y') }}</td>
                <td>{{ $sk->nomor_sk }}</td>
                <td>{{ $sk->jenis_sk }}</td>
                <td>{{ $sk->jabatan_baru ?? '-' }} {{ $sk->golongan_baru ? '(Gol. '.$sk->golongan_baru.')' : '' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" align="center" style="font-style: italic; color: #555;">Riwayat belum tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Papua, {{ now()->translatedFormat('d F Y') }}<br>Disahkan Oleh,</p>
            <div class="signature-space"></div>
            <p style="text-decoration: underline; font-weight: bold;">Sistem Manajemen Birokrasi</p>
            <p style="font-size: 8pt; margin-top: -10px;">(Tercetak Secara Elektronik)</p>
        </div>
    </div>

</body>
</html>