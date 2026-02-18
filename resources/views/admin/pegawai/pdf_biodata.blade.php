<!DOCTYPE html>
<html>
<head>
    <title>Biodata - {{ $pegawai->nama_lengkap }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.4; color: #000; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header h1 { font-size: 16pt; margin: 0; font-weight: bold; text-transform: uppercase; }
        .header h2 { font-size: 12pt; margin: 5px 0; font-weight: normal; }
        
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 20px; font-size: 14pt; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.info td { padding: 4px; vertical-align: top; }
        .label { width: 180px; }
        .sep { width: 10px; text-align: center; }
        
        table.data { border: 1px solid #000; }
        table.data th, table.data td { border: 1px solid #000; padding: 6px; font-size: 11pt; }
        table.data th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .photo { position: absolute; top: 120px; right: 0; width: 113px; height: 151px; border: 1px solid #ccc; object-fit: cover; }
        
        .footer { margin-top: 50px; text-align: right; }
        .ttd { height: 80px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>GEREJA PROTESTAN INDONESIA DI PAPUA</h1>
        <h2>MAJELIS PEKERJA SINODE</h2>
        <small>Jl. Jend. Ahmad Yani No. 12, Fakfak, Papua Barat</small>
    </div>

    <div class="title">BIODATA PEGAWAI</div>

    @if($pegawai->foto_profil)
        @endif

    <table class="info">
        <tr>
            <td class="label">Nama Lengkap</td><td class="sep">:</td>
            <td><b>{{ $pegawai->nama_lengkap }}</b></td>
        </tr>
        <tr>
            <td class="label">NIP / NIPG</td><td class="sep">:</td>
            <td>{{ $pegawai->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tempat, Tgl Lahir</td><td class="sep">:</td>
            <td>{{ $pegawai->tempat_lahir }}, {{ $pegawai->tanggal_lahir ? \Carbon\Carbon::parse($pegawai->tanggal_lahir)->isoFormat('D MMMM Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin</td><td class="sep">:</td>
            <td>{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td class="label">Agama</td><td class="sep">:</td>
            <td>Kristen Protestan</td>
        </tr>
        <tr>
            <td class="label">Status Pegawai</td><td class="sep">:</td>
            <td>{{ $pegawai->jenis_pegawai }} ({{ $pegawai->status_aktif }})</td>
        </tr>
        <tr>
            <td class="label">Jabatan Terakhir</td><td class="sep">:</td>
            <td>{{ $pegawai->jabatan_terakhir }}</td>
        </tr>
        <tr>
            <td class="label">Unit Kerja</td><td class="sep">:</td>
            <td>
                @if($pegawai->jemaat) {{ $pegawai->jemaat->nama_jemaat }}
                @elseif($pegawai->klasis) Klasis {{ $pegawai->klasis->nama_klasis }}
                @else Kantor Sinode @endif
            </td>
        </tr>
    </table>

    <h3>A. Riwayat Kepangkatan & Jabatan</h3>
    <table class="data">
        <thead>
            <tr>
                <th width="15%">TMT</th>
                <th width="20%">No. SK</th>
                <th width="20%">Jenis SK</th>
                <th>Jabatan / Golongan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pegawai->riwayatSk as $sk)
            <tr>
                <td align="center">{{ \Carbon\Carbon::parse($sk->tmt_sk)->format('d-m-Y') }}</td>
                <td>{{ $sk->nomor_sk }}</td>
                <td>{{ $sk->jenis_sk }}</td>
                <td>
                    {{ $sk->jabatan_baru }} 
                    @if($sk->golongan_baru) (Gol. {{ $sk->golongan_baru }}) @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" align="center">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d F Y') }}</p>
        <div class="ttd"></div>
        <p><b>Bagian Kepegawaian</b></p>
    </div>

</body>
</html>