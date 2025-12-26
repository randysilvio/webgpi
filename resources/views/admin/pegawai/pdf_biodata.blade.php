<!DOCTYPE html>
<html>
<head>
    <title>Biodata Pegawai - {{ $pegawai->nama_lengkap }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 14px; }
        .header p { margin: 0; font-size: 10px; font-style: italic; }
        
        .section-title { background-color: #eee; padding: 5px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; border-bottom: 1px solid #ccc; font-size: 13px; }
        
        .table-data { width: 100%; border-collapse: collapse; }
        .table-data td { padding: 4px; vertical-align: top; }
        .label { width: 180px; font-weight: bold; }
        .colon { width: 10px; text-align: center; }
        
        .table-list { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .table-list th, .table-list td { border: 1px solid #999; padding: 5px; text-align: left; }
        .table-list th { background-color: #f0f0f0; text-align: center; }
        
        .photo-container { position: absolute; top: 110px; right: 0; width: 100px; height: 130px; border: 1px solid #ddd; text-align: center; line-height: 130px; background: #f9f9f9; }
        .photo-img { width: 100%; height: 100%; object-fit: cover; }
        
        .footer { margin-top: 40px; text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Gereja Protestan Indonesia di Papua</h1>
        <h2>(GPI PAPUA)</h2>
        <p>Jl. Jend. Ahmad Yani, Fakfak - Papua Barat</p>
    </div>

    <h2 style="text-align: center; text-decoration: underline; margin-top: 20px;">DAFTAR RIWAYAT HIDUP</h2>
    
    <div class="photo-container">
        @if($pegawai->foto_diri && file_exists(storage_path('app/public/'.$pegawai->foto_diri)))
            <img src="{{ storage_path('app/public/'.$pegawai->foto_diri) }}" class="photo-img">
        @else
            <span style="color:#ccc;">FOTO</span>
        @endif
    </div>

    <div class="section-title">I. DATA PRIBADI</div>
    <table class="table-data">
        <tr><td class="label">Nama Lengkap</td><td class="colon">:</td><td>{{ $pegawai->nama_gelar }}</td></tr>
        <tr><td class="label">NIPG</td><td class="colon">:</td><td>{{ $pegawai->nipg }}</td></tr>
        <tr><td class="label">Jenis Kelamin</td><td class="colon">:</td><td>{{ $pegawai->jenis_kelamin }}</td></tr>
        <tr><td class="label">Tempat, Tanggal Lahir</td><td class="colon">:</td><td>{{ $pegawai->tempat_lahir }}, {{ $pegawai->tanggal_lahir->format('d-m-Y') }}</td></tr>
        <tr><td class="label">Status Pernikahan</td><td class="colon">:</td><td>{{ $pegawai->status_pernikahan }}</td></tr>
        <tr><td class="label">Golongan Darah</td><td class="colon">:</td><td>{{ $pegawai->golongan_darah ?? '-' }}</td></tr>
        <tr><td class="label">Alamat Domisili</td><td class="colon">:</td><td>{{ $pegawai->alamat_domisili }}</td></tr>
        <tr><td class="label">Nomor HP</td><td class="colon">:</td><td>{{ $pegawai->no_hp }}</td></tr>
        <tr><td class="label">Email</td><td class="colon">:</td><td>{{ $pegawai->email }}</td></tr>
    </table>

    <div class="section-title">II. DATA KEPEGAWAIAN</div>
    <table class="table-data">
        <tr><td class="label">Jenis Pegawai</td><td class="colon">:</td><td>{{ $pegawai->jenis_pegawai }}</td></tr>
        <tr><td class="label">Status Kepegawaian</td><td class="colon">:</td><td>{{ $pegawai->status_kepegawaian }}</td></tr>
        <tr><td class="label">Status Aktif</td><td class="colon">:</td><td>{{ $pegawai->status_aktif }}</td></tr>
        <tr><td class="label">Pangkat/Golongan Terakhir</td><td class="colon">:</td><td>{{ $pegawai->golongan_terakhir ?? '-' }}</td></tr>
        <tr><td class="label">Jabatan Terakhir</td><td class="colon">:</td><td>{{ $pegawai->jabatan_terakhir ?? '-' }}</td></tr>
        <tr><td class="label">TMT Pegawai</td><td class="colon">:</td><td>{{ $pegawai->tmt_pegawai ? $pegawai->tmt_pegawai->format('d-m-Y') : '-' }}</td></tr>
        <tr><td class="label">Wilayah Tugas</td><td class="colon">:</td><td>{{ $pegawai->jemaat->nama_jemaat ?? '-' }} ({{ $pegawai->klasis->nama_klasis ?? 'Sinode' }})</td></tr>
        <tr><td class="label">NPWP</td><td class="colon">:</td><td>{{ $pegawai->npwp ?? '-' }}</td></tr>
    </table>

    <div class="section-title">III. DATA KELUARGA</div>
    <table class="table-list">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Nama Lengkap</th>
                <th style="width: 15%;">Hubungan</th>
                <th style="width: 15%;">Tgl Lahir</th>
                <th style="width: 15%;">Pendidikan</th>
                <th style="width: 20%;">Ket. Tunjangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pegawai->keluarga as $index => $k)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $k->nama_lengkap }}</td>
                <td>{{ $k->hubungan }}</td>
                <td style="text-align: center;">{{ $k->tanggal_lahir ? $k->tanggal_lahir->format('d-m-Y') : '-' }}</td>
                <td>{{ $k->pendidikan_terakhir ?? '-' }}</td>
                <td style="text-align: center;">{{ $k->status_tunjangan ? 'Ditanggung' : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align: center;">Tidak ada data keluarga.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">IV. RIWAYAT PENDIDIKAN</div>
    <table class="table-list">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Jenjang</th>
                <th style="width: 40%;">Nama Institusi</th>
                <th style="width: 25%;">Jurusan</th>
                <th style="width: 15%;">Lulus Thn</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pegawai->pendidikan as $index => $edu)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $edu->jenjang }}</td>
                <td>{{ $edu->nama_institusi }}</td>
                <td>{{ $edu->jurusan ?? '-' }}</td>
                <td style="text-align: center;">{{ $edu->tahun_lulus }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align: center;">Belum ada data pendidikan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">V. RIWAYAT KEPANGKATAN / JABATAN</div>
    <table class="table-list">
        <thead>
            <tr>
                <th style="width: 20%;">No. SK</th>
                <th style="width: 15%;">Tgl SK</th>
                <th style="width: 15%;">TMT</th>
                <th style="width: 20%;">Jenis SK</th>
                <th style="width: 30%;">Keterangan (Gol/Jab)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pegawai->riwayatSk as $sk)
            <tr>
                <td>{{ $sk->nomor_sk }}</td>
                <td style="text-align: center;">{{ $sk->tanggal_sk->format('d-m-Y') }}</td>
                <td style="text-align: center;">{{ $sk->tmt_sk->format('d-m-Y') }}</td>
                <td>{{ $sk->jenis_sk }}</td>
                <td>
                    @if($sk->golongan_baru) Gol: {{ $sk->golongan_baru }}<br> @endif
                    @if($sk->jabatan_baru) Jab: {{ $sk->jabatan_baru }} @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align: center;">Belum ada riwayat SK.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d-m-Y H:i') }}</p>
        <br><br><br>
        <p>( __________________________ )</p>
        <p>Admin Sinode GPI Papua</p>
    </div>

</body>
</html>