<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? 'Laporan Statistik' }}</title>
    <style>
        /* 1. SETUP HALAMAN */
        @page { margin: 1.5cm 2cm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; color: #222; line-height: 1.4; }
        
        /* 2. HELPER CLASSES */
        .page-break { page-break-after: always; }
        .no-break { page-break-inside: avoid; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .mb-10 { margin-bottom: 10px; }
        .mb-20 { margin-bottom: 20px; }
        
        /* 3. HEADER & KOP SURAT */
        .header-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; }
        .logo { width: 70px; height: auto; }
        .kop-org { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; color: #000; }
        .kop-unit { font-size: 16pt; font-weight: 900; text-transform: uppercase; margin: 2px 0; letter-spacing: 1px; }
        .kop-address { font-size: 8pt; font-style: italic; color: #444; }

        /* 4. JUDUL LAPORAN */
        .report-title-box { text-align: center; margin-bottom: 25px; }
        .main-title { font-size: 12pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; }
        .sub-title { font-size: 10pt; font-weight: bold; margin-top: 5px; }

        /* 5. SUMMARY TABLE */
        .summary-table { width: 100%; border: 1px solid #000; background-color: #f8f9fa; margin-bottom: 25px; }
        .summary-table td { padding: 15px; text-align: center; border-right: 1px solid #ccc; width: 33%; }
        .summary-table td:last-child { border-right: none; }
        .summary-label { display: block; font-size: 8pt; text-transform: uppercase; color: #666; font-weight: bold; }
        .summary-value { display: block; font-size: 16pt; font-weight: bold; color: #000; margin-top: 5px; }

        /* 6. LAYOUT & GRID */
        .section-header { 
            background-color: #e9ecef; border-left: 5px solid #2c3e50; 
            padding: 6px 10px; font-weight: bold; font-size: 10pt; 
            text-transform: uppercase; margin: 20px 0 10px 0; 
        }
        .layout-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .layout-table td { vertical-align: top; }
        .col-left { width: 48%; padding-right: 2%; }
        .col-right { width: 48%; padding-left: 2%; }
        
        /* 7. DATA TABLES & CHARTS */
        .data-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        .data-table th { background-color: #2c3e50; color: #fff; padding: 5px; text-align: left; }
        .data-table td { border-bottom: 1px solid #eee; padding: 5px; color: #333; }
        
        .chart-row { margin-bottom: 6px; font-size: 8pt; display: block; width: 100%; }
        .chart-label { display: inline-block; width: 35%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: middle; }
        .chart-bar-area { display: inline-block; width: 50%; background-color: #f1f1f1; height: 10px; border-radius: 2px; vertical-align: middle; }
        .chart-bar-fill { height: 100%; display: block; border-radius: 2px; }
        .chart-value { display: inline-block; width: 12%; text-align: right; font-weight: bold; vertical-align: middle; font-size: 7pt; }

        .bg-blue { background-color: #3498db; }
        .bg-green { background-color: #2ecc71; }
        .bg-yellow { background-color: #f1c40f; }
        .bg-red { background-color: #e74c3c; }
        .bg-grey { background-color: #95a5a6; }

        .footer-table { margin-top: 40px; width: 100%; page-break-inside: avoid; }
    </style>
</head>
<body>

    {{-- LOGIKA BASE64 UNTUK GAMBAR (SOLUSI GAMBAR HILANG) --}}
    @php
        $path = public_path('gpi_logo.png'); // Default Logo
        
        // Cek logo di setting
        if (isset($setting) && $setting->logo_path) {
            // Cek path storage public
            $storagePath = public_path('storage/' . $setting->logo_path);
            if (file_exists($storagePath)) {
                $path = $storagePath;
            }
        }

        // Konversi ke Base64
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataImg = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
    @endphp

    {{-- 1. KOP SURAT --}}
    <table class="header-table">
        <tr>
            <td width="15%" align="center">
                {{-- Gunakan Base64 di src --}}
                <img src="{{ $base64 }}" class="logo">
            </td>
            <td width="85%" align="center">
                <h3 class="kop-org">Gereja Protestan Indonesia di Papua</h3>
                {{-- UPDATE: MAJELIS PEKERJA SINODE --}}
                <h1 class="kop-unit">{{ strtoupper($setting->site_tagline ?? 'MAJELIS PEKERJA SINODE') }}</h1>
                <p class="kop-address">
                    {{ $setting->site_address ?? 'Jln. Jenderal Ahmad Yani, Fakfak, Papua Barat. Kode Pos 98611' }}
                </p>
            </td>
        </tr>
    </table>

    {{-- 2. JUDUL LAPORAN --}}
    <div class="report-title-box">
        <div class="main-title">{{ $title }}</div>
        <div class="sub-title">WILAYAH: {{ $subtitle }}</div>
        <div style="font-size: 8pt; margin-top: 5px; color: #666;">
            Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
        </div>
    </div>

    {{-- 3. EXECUTIVE SUMMARY --}}
    @php
        $totalData = $data->count();
        $totalKK = $data->where('status_dalam_keluarga', 'Kepala Keluarga')->count();
        $totalLaki = $data->where('jenis_kelamin', 'Laki-laki')->count();
        $totalPerempuan = $data->where('jenis_kelamin', 'Perempuan')->count();
    @endphp

    <table class="summary-table no-break">
        <tr>
            <td>
                <span class="summary-label">Total Jiwa</span>
                <span class="summary-value">{{ number_format($totalData) }}</span>
            </td>
            <td>
                <span class="summary-label">Kepala Keluarga</span>
                <span class="summary-value">{{ number_format($totalKK) }}</span>
            </td>
            <td>
                <span class="summary-label">Rasio Gender (L/P)</span>
                <span class="summary-value" style="font-size: 12pt;">
                    {{ number_format($totalLaki) }} / {{ number_format($totalPerempuan) }}
                </span>
            </td>
        </tr>
    </table>

    {{-- 4. BAGIAN I: DEMOGRAFI --}}
    <div class="section-header">I. Analisis Demografi</div>
    
    @php
        $usiaStats = $data->map(function($item) {
            $age = $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->age : 0;
            if ($age <= 12) return 'Anak (0-12)';
            if ($age <= 18) return 'Remaja (13-18)';
            if ($age <= 35) return 'Pemuda (19-35)';
            if ($age <= 60) return 'Dewasa (36-60)';
            return 'Lansia (>60)';
        })->countBy()->sortKeys();
    @endphp

    <table class="layout-table no-break">
        <tr>
            <td class="col-left">
                <div class="mb-10 text-bold" style="font-size: 9pt;">A. Komposisi Kategori Usia</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usiaStats as $cat => $val)
                        <tr>
                            <td>{{ $cat }}</td>
                            <td class="text-center">{{ $val }}</td>
                            <td class="text-center">{{ $totalData > 0 ? round(($val/$totalData)*100, 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td class="col-right">
                <div class="mb-10 text-bold" style="font-size: 9pt;">B. Visualisasi Sebaran</div>
                <div style="border: 1px solid #eee; padding: 10px; border-radius: 4px;">
                    @foreach($usiaStats as $cat => $val)
                        @php $pct = $totalData > 0 ? ($val/$totalData)*100 : 0; @endphp
                        <div class="chart-row">
                            <span class="chart-label">{{ $cat }}</span>
                            <span class="chart-bar-area">
                                <span class="chart-bar-fill bg-blue" style="width: {{ $pct }}%;"></span>
                            </span>
                            <span class="chart-value">{{ round($pct) }}%</span>
                        </div>
                    @endforeach
                </div>
            </td>
        </tr>
    </table>

    {{-- 5. BAGIAN II: INDIKATOR KESEJAHTERAAN --}}
    <div class="section-header">II. Indikator Kesejahteraan & Ekonomi</div>

    @php
        $rumahStats = $data->groupBy('kondisi_rumah')->map->count()->sortDesc();
        $pendidikanStats = $data->groupBy('pendidikan_terakhir')->map->count()->sortDesc()->take(5);
        $pekerjaanStats = $data->groupBy('pekerjaan_utama')->map->count()->sortDesc()->take(5);
        $hp = $data->where('punya_smartphone', 1)->count();
        $net = $data->where('akses_internet', 1)->count();
    @endphp

    <table class="layout-table">
        <tr>
            <td class="col-left">
                <div class="mb-10 text-bold" style="font-size: 9pt;">A. Kondisi Hunian Jemaat</div>
                <div class="mb-20">
                    @foreach($rumahStats as $label => $val)
                        @php 
                            $pct = $totalData > 0 ? ($val/$totalData)*100 : 0;
                            $color = ($label == 'Darurat/Kayu') ? 'bg-red' : (($label == 'Permanen') ? 'bg-green' : 'bg-yellow');
                        @endphp
                        <div class="chart-row">
                            <span class="chart-label">{{ $label ?: 'Tidak Diketahui' }}</span>
                            <span class="chart-bar-area">
                                <span class="chart-bar-fill {{ $color }}" style="width: {{ $pct }}%;"></span>
                            </span>
                            <span class="chart-value">{{ $val }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mb-10 text-bold" style="font-size: 9pt;">B. Pendidikan Terakhir (Top 5)</div>
                <div>
                    @foreach($pendidikanStats as $label => $val)
                        @php $pct = $totalData > 0 ? ($val/$totalData)*100 : 0; @endphp
                        <div class="chart-row">
                            <span class="chart-label">{{ $label ?: 'Tidak Sekolah' }}</span>
                            <span class="chart-bar-area">
                                <span class="chart-bar-fill bg-grey" style="width: {{ $pct }}%;"></span>
                            </span>
                            <span class="chart-value">{{ $val }}</span>
                        </div>
                    @endforeach
                </div>
            </td>

            <td class="col-right">
                <div class="mb-10 text-bold" style="font-size: 9pt;">C. Dominasi Pekerjaan</div>
                <table class="data-table mb-20">
                    @foreach($pekerjaanStats as $label => $val)
                    <tr>
                        <td>{{ $label ?: 'Belum Bekerja' }}</td>
                        <td width="20%" class="text-right">{{ $val }} Org</td>
                    </tr>
                    @endforeach
                </table>

                <div class="mb-10 text-bold" style="font-size: 9pt;">D. Kesiapan Digital</div>
                <div style="background-color: #f8f9fa; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                    <table style="width: 100%;">
                        <tr>
                            <td width="50%" class="text-center" style="border-right: 1px solid #ddd;">
                                <div style="font-size: 8pt; color: #666;">Smartphone</div>
                                <div style="font-size: 14pt; font-weight: bold; color: #2ecc71;">{{ $hp }}</div>
                                <div style="font-size: 7pt;">{{ $totalData>0 ? round(($hp/$totalData)*100) : 0 }}% Penetrasi</div>
                            </td>
                            <td width="50%" class="text-center">
                                <div style="font-size: 8pt; color: #666;">Internet</div>
                                <div style="font-size: 14pt; font-weight: bold; color: #3498db;">{{ $net }}</div>
                                <div style="font-size: 7pt;">{{ $totalData>0 ? round(($net/$totalData)*100) : 0 }}% Akses</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- 6. BAGIAN III: POTENSI ASET --}}
    @php
        $asetAll = [];
        foreach($data as $d) {
            if($d->aset_ekonomi) {
                foreach(explode(',', $d->aset_ekonomi) as $a) {
                    $k = trim($a); if($k) $asetAll[$k] = ($asetAll[$k] ?? 0) + 1;
                }
            }
        }
        arsort($asetAll);
        $asetTop = array_slice($asetAll, 0, 6);
    @endphp

    @if(count($asetTop) > 0)
    <div class="section-header no-break">III. Peta Potensi Ekonomi (Aset Jemaat)</div>
    <table class="layout-table no-break" style="margin-top: 10px;">
        <tr>
            @php $counter = 0; @endphp
            @foreach($asetTop as $label => $val)
                @if($counter > 0 && $counter % 3 == 0) </tr><tr> @endif
                <td width="33%" style="padding: 5px;">
                    <div style="background: #fff; border: 1px solid #ccc; padding: 8px; border-radius: 4px;">
                        <strong style="font-size: 8pt; text-transform: uppercase; color: #555;">{{ $label }}</strong><br>
                        <span style="font-size: 12pt; font-weight: bold; color: #2c3e50;">{{ $val }}</span> 
                        <span style="font-size: 8pt; color: #888;">Unit/KK</span>
                    </div>
                </td>
                @php $counter++; @endphp
            @endforeach
            @while($counter % 3 != 0) <td width="33%"></td> @php $counter++; @endphp @endwhile
        </tr>
    </table>
    @endif

    {{-- 7. FOOTER TTD --}}
    <table class="footer-table">
        <tr>
            <td width="60%">
                <div style="font-size: 8pt; color: #777; font-style: italic;">
                    * Laporan ini digenerate otomatis oleh Sistem Informasi Manajemen Gereja (SIM-G).<br>
                    * Data bersifat dinamis dan dapat berubah sewaktu-waktu sesuai pembaruan operator.
                </div>
            </td>
            <td width="40%" align="center">
                <p style="margin-bottom: 50px; font-size: 9pt;">
                    {{ $setting->site_location ?? 'Fakfak' }}, {{ date('d F Y') }}<br>
                    Mengetahui,<br>
                    <strong>Administrator Sistem</strong>
                </p>
                <p style="text-decoration: underline; font-weight: bold; margin: 0;">
                    {{ Auth::user()->name ?? 'Administrator' }}
                </p>
            </td>
        </tr>
    </table>

</body>
</html>