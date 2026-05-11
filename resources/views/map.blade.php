<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>LekMedan | Executive Emergency System</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>
        body { overflow: hidden; }
        #map { height: 100vh; width: 100vw; z-index: 0; }
        
        .panel-scroll::-webkit-scrollbar { width: 3px; }
        .panel-scroll::-webkit-scrollbar-track { background: transparent; }
        .panel-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .panel-scroll::-webkit-scrollbar-thumb { background: #475569; }

        .ping-marker {
            width: 16px; height: 16px; background-color: #ef4444; border-radius: 50%;
            border: 2px solid white; box-shadow: 0 0 10px rgba(239, 68, 68, 0.6);
            animation: ping-anim 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        @keyframes ping-anim { 75%, 100% { transform: scale(2); opacity: 0; } }

        /* Kustomisasi Box Routing Leaflet agar Elegan & Transparan */
        .leaflet-routing-container {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(12px);
            border-radius: 16px !important;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important;
            border: 1px solid rgba(255,255,255,0.5) !important;
            color: #0f172a !important;
            max-height: 35vh;
            overflow-y: auto;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            margin: 1rem !important;
            padding: 10px !important;
            transition: all 0.3s ease;
        }
        /* Style Dark Mode untuk Box Routing */
        .dark .leaflet-routing-container {
            background: rgba(15, 23, 42, 0.85) !important;
            border-color: rgba(255,255,255,0.1) !important;
            color: #f8fafc !important;
        }
        /* Sembunyikan tulisan default yang jelek */
        .leaflet-routing-alt h2 { display: none !important; }
        .leaflet-routing-alt tr:hover { background-color: rgba(0,0,0,0.05) !important; cursor: pointer; }
        .dark .leaflet-routing-alt tr:hover { background-color: rgba(255,255,255,0.05) !important; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased transition-colors duration-300">

    <div id="map" class="absolute inset-0"></div>

    <div class="pointer-events-none absolute inset-0 z-10 flex flex-col justify-between items-start p-4 md:p-8">
        
        <div class="w-full flex justify-between items-center gap-4">
            <div class="pointer-events-auto flex items-center gap-3 bg-white/90 backdrop-blur-md border border-slate-200 shadow-xl rounded-full px-5 py-3 transition-colors">
                <div class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></div>
                <h1 class="text-sm font-extrabold tracking-widest uppercase text-slate-900">Lek<span class="text-slate-400 font-normal">Medan</span></h1>
            </div>

            <div class="pointer-events-auto flex items-center gap-2">
                <button onclick="toggleTheme()" class="bg-white/90 backdrop-blur-md border border-slate-200 p-3 rounded-full shadow-lg hover:bg-slate-50 transition-colors">
                    <span id="theme-icon" class="text-slate-600">🌙</span>
                </button>
                <button onclick="getUserLocation()" class="bg-slate-900/90 backdrop-blur-md text-white px-5 py-3 rounded-full text-xs font-bold shadow-lg hover:scale-105 transition-transform flex items-center gap-2">
                    📍 <span class="hidden md:inline">Lokasi Saya</span>
                </button>
            </div>
        </div>

        <div class="pointer-events-auto panel-scroll w-full md:w-[400px] max-h-[75vh] overflow-y-auto bg-white/95 backdrop-blur-xl rounded-[24px] shadow-2xl border border-slate-200 flex flex-col transition-colors">
            
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Fokus Penyelamatan</h2>
                <div class="flex bg-slate-100 p-1.5 rounded-xl">
                    <button id="tab-medis" onclick="setMode('rumah_sakit')" class="flex-1 py-2.5 text-xs font-bold rounded-lg bg-blue-600 text-white shadow-sm transition-all">
                        Kesehatan
                    </button>
                    <button id="tab-keamanan" onclick="setMode('keamanan')" class="flex-1 py-2.5 text-xs font-bold rounded-lg text-slate-500 hover:text-slate-700 transition-all">
                        Keamanan
                    </button>
                </div>
            </div>

            <div id="result-card" class="hidden p-6 bg-slate-50/50 border-b border-slate-100 transition-all">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-[0.2em] mb-1">Fasilitas Terdekat</p>
                <h3 id="res-nama" class="text-lg font-extrabold text-slate-900 leading-tight mb-4">Nama Fasilitas</h3>
                
                <div class="flex gap-8 mb-5">
                    <div>
                        <p class="text-[10px] text-slate-400 font-medium mb-1 uppercase tracking-widest">Jarak</p>
                        <p class="text-xl font-bold text-slate-800"><span id="res-km">0</span><span class="text-xs font-normal ml-1">km</span></p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-medium mb-1 uppercase tracking-widest">Waktu</p>
                        <p class="text-xl font-bold text-slate-800"><span id="res-min">0</span><span class="text-xs font-normal ml-1">min</span></p>
                    </div>
                </div>

                <button id="btn-nav" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/30 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
                    Buka di Google Maps
                </button>
            </div>

            <div class="p-6">
                <h2 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Panggilan Darurat Langsung</h2>
                <div class="space-y-3">
                    <a href="tel:113" class="flex justify-between items-center group">
                        <div>
                            <span class="block text-sm font-bold text-slate-800 group-hover:text-red-600 transition-colors">Pemadam Kebakaran</span>
                            <span class="block text-[10px] text-slate-400">Tim Rescue Kota</span>
                        </div>
                        <span class="text-xs font-black text-red-500 bg-red-50 px-3 py-1.5 rounded-md">113</span>
                    </a>
                    <div class="h-px bg-slate-100"></div>
                    <a href="tel:119" class="flex justify-between items-center group">
                        <div>
                            <span class="block text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors">Ambulans Medis</span>
                            <span class="block text-[10px] text-slate-400">Gawat Darurat 24/7</span>
                        </div>
                        <span class="text-xs font-black text-blue-600 bg-blue-50 px-3 py-1.5 rounded-md">119</span>
                    </a>
                    <div class="h-px bg-slate-100"></div>
                    <a href="tel:110" class="flex justify-between items-center group">
                        <div>
                            <span class="block text-sm font-bold text-slate-800 group-hover:text-slate-500 transition-colors">Kepolisian Medan</span>
                            <span class="block text-[10px] text-slate-400">Polrestabes / Polsek</span>
                        </div>
                        <span class="text-xs font-black text-slate-700 bg-slate-100 px-3 py-1.5 rounded-md">110</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    
    <script>
        const map = L.map('map', { zoomControl: false }).setView([3.5952, 98.6722], 13);
        L.control.zoom({ position: 'topright' }).addTo(map);

        const lightTile = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 });
        const darkTile = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 });
        
        let isDarkMode = false;
        lightTile.addTo(map);

        function toggleTheme() {
            isDarkMode = !isDarkMode;
            document.documentElement.classList.toggle('dark', isDarkMode);
            document.getElementById('theme-icon').innerText = isDarkMode ? '☀️' : '🌙';
            
            if (isDarkMode) {
                map.removeLayer(lightTile);
                darkTile.addTo(map);
            } else {
                map.removeLayer(darkTile);
                lightTile.addTo(map);
            }

            // Refresh rute untuk mengupdate warna garis
            if(eventMarker) calculateRoute(eventMarker.getLatLng().lat, eventMarker.getLatLng().lng);
        }

        const facilities = JSON.parse('{!! json_encode($facilities) !!}');
        let routingControl = null;
        let eventMarker = null;
        let currentMode = 'rumah_sakit';

        function getDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        }

        function calculateRoute(destLat, destLng) {
            const filtered = facilities.filter(f => f.kategori === currentMode);
            if (filtered.length === 0) {
                alert("Data fasilitas kosong!");
                if (routingControl) map.removeControl(routingControl);
                return;
            }

            let closest = filtered[0];
            let minDist = getDistance(destLat, destLng, filtered[0].lat, filtered[0].lng);
            
            filtered.forEach(f => {
                const d = getDistance(destLat, destLng, f.lat, f.lng);
                if (d < minDist) { minDist = d; closest = f; }
            });

            if (routingControl) map.removeControl(routingControl);
            
            const routeColor = currentMode === 'rumah_sakit' ? '#2563eb' : (isDarkMode ? '#cbd5e1' : '#0f172a');

            // Set SHOW: TRUE agar instruksi In-App Leaflet muncul kembali
            routingControl = L.Routing.control({
                waypoints: [L.latLng(closest.lat, closest.lng), L.latLng(destLat, destLng)],
                createMarker: () => null,
                lineOptions: { styles: [{color: routeColor, opacity: 0.9, weight: 6}] },
                show: true, // INI YANG MENGEMBALIKAN PANEL NAVIGASI LEAFLET
                routeWhileDragging: false
            }).addTo(map);

            routingControl.on('routesfound', function(e) {
                const summary = e.routes[0].summary;
                
                document.getElementById('res-nama').innerText = closest.nama;
                document.getElementById('res-km').innerText = (summary.totalDistance/1000).toFixed(1);
                document.getElementById('res-min').innerText = Math.ceil(summary.totalTime/60);
                
                // Set tombol GMaps eksternal
                document.getElementById('btn-nav').onclick = () => {
                    window.open(`https://www.google.com/maps/dir/?api=1&origin=${closest.lat},${closest.lng}&destination=${destLat},${destLng}&travelmode=driving`);
                };

                document.getElementById('result-card').classList.remove('hidden');
            });
        }

        function setMode(mode) {
            currentMode = mode;
            const tabMedis = document.getElementById('tab-medis');
            const tabAman = document.getElementById('tab-keamanan');

            tabMedis.className = 'flex-1 py-2.5 text-xs font-bold rounded-lg text-slate-500 hover:text-slate-700 transition-all bg-transparent shadow-none';
            tabAman.className = 'flex-1 py-2.5 text-xs font-bold rounded-lg text-slate-500 hover:text-slate-700 transition-all bg-transparent shadow-none';

            if(mode === 'rumah_sakit') {
                tabMedis.className = 'flex-1 py-2.5 text-xs font-bold rounded-lg bg-blue-600 text-white shadow-sm transition-all';
            } else {
                tabAman.className = 'flex-1 py-2.5 text-xs font-bold rounded-lg bg-slate-800 text-white shadow-sm transition-all';
            }

            if(eventMarker) calculateRoute(eventMarker.getLatLng().lat, eventMarker.getLatLng().lng);
        }

        facilities.forEach(f => {
            const isRS = f.kategori === 'rumah_sakit';
            const color = isRS ? '#2563eb' : '#475569';
            const icon = L.divIcon({
                html: `<div style="background-color: ${color};" class="w-3 h-3 border-2 border-white rounded-full shadow-sm"></div>`,
                className: 'custom-icon', iconSize: [12, 12], iconAnchor: [6, 6]
            });
            L.marker([f.lat, f.lng], { icon: icon }).addTo(map)
                .bindPopup(`<div class="p-1"><b class="text-xs font-bold text-slate-800">${f.nama}</b></div>`, { closeButton: false });
        });

        map.on('click', e => {
            if (eventMarker) map.removeLayer(eventMarker);
            const pulseIcon = L.divIcon({
                html: `<div class="ping-marker"></div>`,
                className: 'custom-icon', iconSize: [16, 16], iconAnchor: [8, 8]
            });
            eventMarker = L.marker(e.latlng, {icon: pulseIcon}).addTo(map);
            calculateRoute(e.latlng.lat, e.latlng.lng);
        });

        function getUserLocation() {
            if(navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    const { latitude, longitude } = pos.coords;
                    map.setView([latitude, longitude], 15);
                    if (eventMarker) map.removeLayer(eventMarker);
                    const pulseIcon = L.divIcon({
                        html: `<div class="ping-marker"></div>`,
                        className: 'custom-icon', iconSize: [16, 16], iconAnchor: [8, 8]
                    });
                    eventMarker = L.marker([latitude, longitude], {icon: pulseIcon}).addTo(map);
                    calculateRoute(latitude, longitude);
                });
            }
        }
    </script>
</body>
</html>