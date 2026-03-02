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
        
        /* Marker Pin Style */
        .custom-div-icon i { text-shadow: 1px 1px 2px rgba(0,0,0,0.5); }
        .marker-pin {
            width: 28px; height: 28px; border-radius: 50%; 
            border: 2px solid white; display: flex; 
            align-items: center; justify-content: center; 
            box-shadow: 0 3px 6px rgba(0,0,0,0.4);
            font-size: 12px; color: white; font-weight: bold;
            position: relative;
        }

        /* --- STYLE LABEL MENGAMBANG (DIPERKECIL) --- */
        .custom-floating-label {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 0 !important;
            height: 0 !important;
            overflow: visible;
        }
        .floating-label {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #475569;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            color: #1e293b;
            font-weight: bold;
            font-size: 9px; /* Huruf diperkecil */
            font-family: sans-serif;
            padding: 2px 5px; /* Kotak diperkecil */
            border-radius: 3px;
            white-space: nowrap;
            transform: translate(-50%, -50%); /* Agar tepat berada di tengah ujung garis */
            pointer-events: none; /* Supaya tidak mengganggu klik ke peta/pin */
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
            size: landscape; 
            margin: 10mm;    
        }

        @media print {
            body, html { height: auto; overflow: visible; background: white; }
            
            .action-bar, .leaflet-control-zoom, .leaflet-control-attribution { 
                display: none !important; 
            }

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
            .kop-logo { width: 70px; height: auto; margin-right: 20px; }
            .kop-text h1 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
            .kop-text h2 { margin: 3px 0; font-size: 12pt; font-weight: bold; text-transform: uppercase; }
            .kop-text p { margin: 0; font-size: 9pt; font-style: italic; }

            #map {
                height: 150mm; 
                width: 100%;
                border: 2px solid #333; 
                border-radius: 4px;
            }
            
            .floating-label {
                background: white !important;
                border: 1px solid black !important;
                color: black !important;
                box-shadow: none !important;
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
        var papuaCenter = [-4.2, 137.5]; 
        
        var map = L.map('map', {
            center: papuaCenter, 
            zoom: 6, 
            minZoom: 5,
            zoomControl: {{ $isPrint ? 'false' : 'true' }}, 
            attributionControl: false
        });

        // Basemap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);

        var klasisData = @json($petaKlasis);

        // --- 1. RENDER BATAS WILAYAH (GEOJSON) ---
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
                            // Label kabupaten bawaan sangat diredupkan agar fokus ke label Gereja
                            layer.bindTooltip(feature.properties.NAME_2, {
                                permanent: {{ $isPrint ? 'true' : 'false' }}, 
                                direction: "center", 
                                className: "bg-transparent border-0 shadow-none text-[8px] font-bold text-gray-500 uppercase opacity-30"
                            });
                        }
                    }
                }).addTo(map);
            });

        // --- 2. RENDER PIN GEREJA (TANPA LABEL BAWAAN LEAFLET) ---
        klasisData.forEach(function(klasis) {
            if(klasis.latitude && klasis.longitude) {
                var iconHtml = `<div class='marker-pin' style='background-color:${klasis.warna_peta || '#1e40af'};'><i class='fas fa-church'></i></div>`;
                var icon = L.divIcon({ className: 'custom-div-icon', html: iconHtml, iconSize: [28, 28], iconAnchor: [14, 14] });
                
                L.marker([klasis.latitude, klasis.longitude], {icon: icon, zIndexOffset: 1000})
                 .bindPopup(`<div style="text-align:center;"><b>${klasis.nama_klasis}</b><br><span style="font-size:11px; color:#666;">Pusat: ${klasis.kabupaten_kota}</span></div>`)
                 .addTo(map);
            }
        });

        // --- 3. ALGORITMA LABEL MENGAMBANG & GARIS PENUNJUK PERMANEN ---
        var labelLayers = L.layerGroup().addTo(map); // Simpan label di group khusus

        function drawSmartLabels() {
            labelLayers.clearLayers(); // Bersihkan garis/label lama tiap kali layar digeser/zoom
            
            // Siapkan target awal: Jauhkan dari titik pusat (pin)
            let nodes = klasisData.filter(k => k.latitude && k.longitude).map((k, i) => {
                let pt = map.latLngToLayerPoint([k.latitude, k.longitude]);
                
                // Selang-seling lempar ke kiri atas atau kanan atas agar berjarak jauh
                let dirX = i % 2 === 0 ? 50 : -50; 
                let dirY = -40 - (i % 3 * 15); // Ketinggian berbeda-beda (40, 55, 70)
                
                let targetX = pt.x + dirX;
                let targetY = pt.y + dirY;

                return { 
                    x: targetX, y: targetY, 
                    targetX: targetX, targetY: targetY, 
                    origX: pt.x, origY: pt.y, 
                    klasis: k 
                };
            });

            // Physics Algorithm (Tolak-Menolak antar Label agar tidak tumpang tindih)
            let nodeW = 85; // Area lebar pelindung per teks
            let nodeH = 22; // Area tinggi pelindung per teks
            
            for(let iter = 0; iter < 150; iter++) {
                for(let i = 0; i < nodes.length; i++) {
                    for(let j = 0; j < nodes.length; j++) {
                        if(i === j) continue;
                        let a = nodes[i], b = nodes[j];
                        let dx = a.x - b.x, dy = a.y - b.y;
                        
                        // Jika posisi mereka terlalu dekat
                        if (Math.abs(dx) < nodeW && Math.abs(dy) < nodeH) {
                            let dist = Math.sqrt(dx*dx + dy*dy) || 1;
                            let force = 4; // Kekuatan dorongan menjauh
                            a.x += (dx/dist) * force; a.y += (dy/dist) * force;
                        }
                    }
                    // Tarik kembali ke titik target sasaran secara perlahan
                    nodes[i].x -= (nodes[i].x - nodes[i].targetX) * 0.03;
                    nodes[i].y -= (nodes[i].y - nodes[i].targetY) * 0.03;
                }
            }

            // Mulai Menggambar Garis & Labelnya
            nodes.forEach(node => {
                let latlng = map.layerPointToLatLng([node.x, node.y]);
                let origLatlng = map.layerPointToLatLng([node.origX, node.origY]);
                
                // SELALU gambar garis putus-putus untuk semua titik
                L.polyline([origLatlng, latlng], {
                    color: '#475569', weight: 1.5, dashArray: '4, 4'
                }).addTo(labelLayers);

                // Render Teks Label di ujung garis
                let iconHtml = `<div class='floating-label'>${node.klasis.nama_klasis}</div>`;
                let lblIcon = L.divIcon({ className: 'custom-floating-label', html: iconHtml, iconSize: [0, 0] });
                L.marker(latlng, {icon: lblIcon, zIndexOffset: 2000}).addTo(labelLayers);
            });
        }

        // Panggil fungsi saat render awal dan setiap selesai zoom (agar ukurannya selalu proporsional)
        map.on('zoomend', drawSmartLabels);
        drawSmartLabels();

        // --- 4. AUTO PRINT LOGIC DENGAN RE-CALCULATION ---
        @if($isPrint)
            setTimeout(function() { 
                map.invalidateSize();
                map.setView([-3.8, 138.0], 6); 
                drawSmartLabels(); // Hitung ulang posisi label setelah ukuran kertas disesuaikan
                
                setTimeout(function() { window.print(); }, 800); 
            }, 1000); 
        @endif
    </script>
</body>
</html>