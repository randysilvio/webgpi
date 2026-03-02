<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Sebaran Pelayanan - GPI PAPUA</title>
    
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* --- STYLE UMUM (LAYAR) --- */
        body, html { margin: 0; padding: 0; height: 100%; width: 100%; overflow: hidden; background: #fff; font-family: 'Times New Roman', serif; }
        
        #map { height: 100%; width: 100%; background: #eef2f6; }
        
        /* Marker Style */
        .custom-div-icon i { text-shadow: 1px 1px 2px rgba(0,0,0,0.5); }
        .marker-pin {
            width: 24px; height: 24px; border-radius: 50%; 
            border: 2px solid white; display: flex; 
            align-items: center; justify-content: center; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            font-size: 10px; color: white; font-weight: bold;
        }

        /* Tombol Cetak (Hanya di Layar) */
        .action-bar {
            position: absolute; top: 10px; right: 10px; z-index: 1000;
            display: flex; gap: 5px;
        }
        .btn {
            background: white; border: 1px solid #999; padding: 6px 12px;
            cursor: pointer; border-radius: 4px; font-size: 12px; font-weight: bold; font-family: sans-serif;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2); text-decoration: none; color: #333;
            display: flex; align-items: center; gap: 6px;
        }
        .btn:hover { background: #f0f0f0; }

        /* --- KOP SURAT (Hanya Muncul Saat Print) --- */
        .kop-header { display: none; } 

        /* --- SETTING HALAMAN CETAK (LANDSCAPE) --- */
        @page {
            size: landscape; /* Memaksa printer ke mode Landscape */
            margin: 10mm;    /* Margin kertas */
        }

        @media print {
            body, html { height: auto; overflow: visible; background: white; }
            
            /* Sembunyikan elemen interface */
            .action-bar, .leaflet-control-zoom, .leaflet-control-attribution { 
                display: none !important; 
            }

            /* Tampilkan KOP */
            .kop-header {
                display: flex;
                align-items: center;
                justify-content: center;
                border-bottom: 3px double #000;
                padding-bottom: 10px;
                margin-bottom: 15px;
                width: 100%;
                text-align: center;
            }
            .kop-logo {
                width: 70px; height: auto; margin-right: 20px;
            }
            .kop-text h1 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
            .kop-text h2 { margin: 3px 0; font-size: 12pt; font-weight: bold; text-transform: uppercase; }
            .kop-text p { margin: 0; font-size: 9pt; font-style: italic; }

            /* Atur Ukuran Peta agar pas di Landscape & Tidak Full Satu Halaman */
            #map {
                height: 150mm; /* Tinggi fix sekitar 60-70% halaman A4 Landscape */
                width: 100%;
                border: 2px solid #333; /* Bingkai agar rapi */
                border-radius: 4px;
            }
        }
    </style>
</head>
<body>

    {{-- HEADER KOP SURAT --}}
    <div class="kop-header">
        <img src="{{ asset('img/logo.png') }}" onerror="this.style.display='none'" class="kop-logo" alt="Logo GPI">
        <div class="kop-text">
            <h1>Gereja Protestan Indonesia di Papua</h1>
            <h2>Majelis Pekerja Sinode</h2>
            <p>Jl. Imam Bonjol, Fakfak, Papua Barat. Telepon: 082239473730</p>
            <p>Website: www.gpipapua.org | Email: sinode@gpipapua.org</p>
        </div>
    </div>

    {{-- TOMBOL AKSI --}}
    @if(!$isPrint)
        <div class="action-bar">
            <a href="{{ route('admin.dashboard.peta_widget', ['print' => 1]) }}" target="_blank" class="btn" title="Mode Cetak">
                <i class="fas fa-print"></i> CETAK LANDSCAPE
            </a>
        </div>
    @endif

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // --- 1. KOORDINAT TENGAH (FIXED) ---
        // Kita gunakan koordinat tengah Papua yang optimal untuk Landscape
        var papuaCenter = [-4.2, 137.5]; 
        
        var map = L.map('map', {
            center: papuaCenter, 
            zoom: 6, 
            minZoom: 5,
            zoomControl: {{ $isPrint ? 'false' : 'true' }}, 
            attributionControl: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);

        var klasisData = @json($petaKlasis);

        // --- 2. RENDER WILAYAH (GEOJSON) ---
        fetch("https://raw.githubusercontent.com/ansbmn/indonesia-geojson/master/indonesia-kab.geojson")
            .then(res => res.json())
            .then(data => {
                L.geoJSON(data, {
                    filter: function(feature) {
                        if(!feature.properties) return false;
                        let prov = feature.properties.NAME_1 ? feature.properties.NAME_1.toUpperCase() : '';
                        return prov.includes('PAPUA'); 
                    },
                    style: function(feature) {
                        let geoName = feature.properties.NAME_2 ? feature.properties.NAME_2.toUpperCase() : '';
                        let klasis = klasisData.find(k => {
                            if (!k.kabupaten_kota) return false;
                            let dbName = k.kabupaten_kota.toUpperCase().trim();
                            return geoName.includes(dbName) || dbName.includes(geoName);
                        });
                        
                        return {
                            fillColor: klasis ? klasis.warna_peta : '#94a3b8', 
                            weight: 1, opacity: 1, color: 'white', dashArray: '3', 
                            fillOpacity: klasis ? 0.6 : 0.15 
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties && feature.properties.NAME_2) {
                            layer.bindTooltip(feature.properties.NAME_2, {
                                permanent: {{ $isPrint ? 'true' : 'false' }}, 
                                direction: "center", 
                                className: "bg-transparent border-0 shadow-none text-[8px] font-bold text-gray-700 uppercase"
                            });
                        }
                    }
                }).addTo(map);
            });

        // --- 3. RENDER PIN GEREJA ---
        klasisData.forEach(function(klasis) {
            if(klasis.latitude && klasis.longitude) {
                var iconHtml = `<div class='marker-pin' style='background-color:${klasis.warna_peta};'><i class='fas fa-church'></i></div>`;
                var icon = L.divIcon({ className: 'custom-div-icon', html: iconHtml, iconSize: [24, 24], iconAnchor: [12, 12] });
                L.marker([klasis.latitude, klasis.longitude], {icon: icon}).addTo(map)
                 .bindPopup(`<b>${klasis.nama_klasis}</b><br>${klasis.kabupaten_kota}`);
            }
        });

        // --- 4. AUTO PRINT LOGIC (LANDSCAPE FIX) ---
        @if($isPrint)
            setTimeout(function() { 
                // 1. Beritahu Leaflet ukuran container berubah (karena CSS @media print aktif)
                map.invalidateSize();
                
                // 2. Set View Fokus Landscape
                // Geser sedikit ke latitude -3.5 (lebih ke atas) agar pulau terlihat center 
                // karena ada Kop Surat di atasnya.
                map.setView([-3.8, 138.0], 6); 
                
                // 3. Eksekusi Print
                window.print(); 
            }, 2000); 
        @endif
    </script>
</body>
</html>