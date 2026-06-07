<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="LekMedan - Sistem Informasi Geografis Layanan Darurat Kota Medan. Laporkan insiden, temukan rute tercepat ke rumah sakit &amp; pos keamanan secara real-time.">
    <title>LEK MEDAN • Emergency Response</title>
    
    <!-- Leaflet + Routing -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    
    <!-- Tailwind via CDN for standalone map experience -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                        'display': ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Space+Grotesk:wght@500;600&amp;display=swap');
        
        :root {
            --red-urgent: #DC2626;
            --navy: #1E3A8A;
        }
        
        body { 
            font-family: 'Inter', system_ui, sans-serif; 
            overflow: hidden;
            touch-action: manipulation;
        }
        
        .font-display { font-family: 'Space Grotesk', 'Inter', sans-serif; }
        
        #map { 
            height: 100vh; 
            width: 100vw; 
            z-index: 0; 
            background: #0F172A; 
        }
        
        .leaflet-popup-content-wrapper {
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            border: 1px solid #e2e8f0;
        }
        
        .emergency-shadow {
            box-shadow: 0 10px 15px -3px rgb(220 38 38 / 0.15), 0 4px 6px -4px rgb(220 38 38 / 0.15);
        }
        
        .panel {
            backdrop-filter: blur(20px);
            background: rgba(255,255,255,0.96);
        }
        
        .dark .panel {
            background: rgba(15,23,42,0.96);
        }
        
        .section-header {
            font-size: 10px;
            letter-spacing: 1.5px;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .facility-row {
            transition: all 0.1s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .facility-row:hover {
            background-color: #f8fafc;
            transform: translateX(2px);
        }
        
        .dark .facility-row:hover {
            background-color: #1e2937;
        }
        
        .map-control-btn {
            transition: transform 0.1s ease, box-shadow 0.1s ease;
        }
        
        .map-control-btn:active {
            transform: scale(0.95);
        }
        
        .incident-type-btn {
            transition: all 0.1s ease;
        }
        
        .incident-type-btn.active {
            border-color: #DC2626;
            background-color: #fef2f2;
            color: #991B1B;
        }
        
        .lek-logo {
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .lek-logo:hover {
            transform: scale(1.02);
        }
        
        .status-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        
        .route-info {
            animation: slideUp 0.3s ease forwards;
        }
        
        .leaflet-routing-container {
            background: white !important;
            border-radius: 20px !important;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
            border: 1px solid #e2e8f0 !important;
            max-height: 280px;
            overflow-y: auto;
        }
        
        .dark .leaflet-routing-container {
            background: #0F172A !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        
        .leaflet-routing-alt {
            padding: 8px 12px !important;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-200">

    <!-- TOP COMMAND BAR -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-slate-900/95 backdrop-blur-xl border-b border-slate-800">
        <div class="max-w-screen-2xl mx-auto px-4 h-16 flex items-center justify-between">
            
            <!-- LOGO -->
            <div class="flex items-center gap-x-3">
                <div class="lek-logo flex items-center justify-center w-11 h-11 bg-red-600 rounded-2xl shadow-lg shadow-red-600/30 relative overflow-hidden">
                    <!-- Custom Emergency Logo SVG -->
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Outer shield / pin shape -->
                        <path d="M13 2C13 2 21 6.5 21 13.5C21 19.5 17.5 24 13 24C8.5 24 5 19.5 5 13.5C5 6.5 13 2 13 2Z" fill="#fff"/>
                        <!-- Inner location pin -->
                        <path d="M13 7.5C15.4853 7.5 17.5 9.51472 17.5 12C17.5 14.5 15 17.5 13 19.5C11 17.5 8.5 14.5 8.5 12C8.5 9.51472 10.5147 7.5 13 7.5Z" fill="#DC2626"/>
                        <!-- White cross / medical -->
                        <rect x="11.5" y="10.5" width="3" height="3" rx="0.5" fill="#fff"/>
                        <rect x="10.25" y="11.75" width="5.5" height="0.5" rx="0.25" fill="#fff"/>
                    </svg>
                </div>
                <div class="leading-none">
                    <div class="flex items-baseline">
                        <span class="font-black text-3xl tracking-[-1.8px] text-white">LEK</span>
                        <span class="font-black text-3xl tracking-[-0.5px] text-red-500">MEDAN</span>
                    </div>
                    <div class="text-[9px] font-bold text-red-500/70 -mt-1 tracking-[1.5px]">EMERGENCY RESPONSE</div>
                </div>
            </div>

            <!-- SEARCH -->
            <div class="flex-1 max-w-md mx-6 relative hidden md:block">
                <div class="relative group">
                    <input 
                        type="text" 
                        id="searchInput" 
                        onkeyup="searchFacility()" 
                        class="w-full bg-slate-800 border border-slate-700 focus:border-red-500 transition-colors text-white placeholder:text-slate-400 pl-11 pr-4 py-2.5 text-sm rounded-2xl outline-none"
                        placeholder="Cari rumah sakit, polsek, atau pos damkar...">
                    <div class="absolute left-4 top-3 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                
                <!-- Search Results -->
                <div id="searchResults" 
                     class="hidden absolute mt-2 w-full bg-slate-900 border border-slate-700 rounded-3xl shadow-2xl max-h-[280px] overflow-y-auto z-[9999] text-sm">
                </div>
            </div>

            <!-- RIGHT ACTIONS -->
            <div class="flex items-center gap-x-2">
                
                <!-- Quick Emergency Dials -->
                <div class="hidden lg:flex items-center bg-slate-800 rounded-2xl p-1 border border-slate-700">
                    <a href="tel:119" 
                       class="flex items-center gap-x-1.5 px-4 py-1.5 text-xs font-bold bg-red-600 hover:bg-red-700 transition-colors text-white rounded-xl">
                        <span>119</span>
                        <span class="text-red-200 text-[10px]">AMBULANS</span>
                    </a>
                    <a href="tel:113" 
                       class="flex items-center gap-x-1.5 px-4 py-1.5 text-xs font-bold hover:bg-slate-700 transition-colors text-white rounded-xl">
                        <span>113</span>
                        <span class="text-slate-400 text-[10px]">DAMKAR</span>
                    </a>
                    <a href="tel:110" 
                       class="flex items-center gap-x-1.5 px-4 py-1.5 text-xs font-bold hover:bg-slate-700 transition-colors text-white rounded-xl">
                        <span>110</span>
                        <span class="text-slate-400 text-[10px]">POLISI</span>
                    </a>
                </div>

                <!-- Mobile Quick Dial -->
                <div class="lg:hidden flex items-center gap-x-1">
                    <a href="tel:119" class="w-9 h-9 flex items-center justify-center bg-red-600 text-white text-xs font-black rounded-2xl active:scale-95 transition">119</a>
                    <a href="tel:113" class="w-9 h-9 flex items-center justify-center bg-orange-600 text-white text-xs font-black rounded-2xl active:scale-95 transition">113</a>
                </div>

                <!-- User / Admin -->
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center justify-center w-9 h-9 bg-slate-700 hover:bg-slate-600 transition rounded-2xl text-xs font-bold border border-slate-600">
                        A
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="text-xs font-bold px-4 py-2 bg-white text-slate-900 rounded-2xl hover:bg-slate-100 transition flex items-center gap-x-2">
                        <span>Masuk</span>
                    </a>
                @endauth

                <!-- Theme Toggle -->
                <button onclick="toggleTheme()" 
                        class="w-9 h-9 flex items-center justify-center text-slate-300 hover:text-white hover:bg-slate-800 transition rounded-2xl border border-slate-700">
                    <span id="theme-icon">🌙</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MAP -->
    <div id="map" class="absolute inset-0 pt-16"></div>

    <!-- FLOATING ACTION BUTTON - REPORT INCIDENT -->
    <div class="fixed bottom-6 right-6 z-[60]">
        <button onclick="openReportModal()"
                class="group flex items-center gap-x-3 bg-red-600 hover:bg-red-700 active:bg-red-800 transition-all shadow-xl shadow-red-600/40 text-white pl-5 pr-6 py-3.5 rounded-3xl font-bold text-sm tracking-wider">
            <div class="flex items-center justify-center w-5 h-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <span class="font-extrabold tracking-[0.5px]">LAPOR INSIDEN</span>
        </button>
    </div>

    <!-- MAIN CONTROL PANEL (Floating) -->
    <div id="floating-panel" 
         class="fixed bottom-6 left-1/2 -translate-x-1/2 md:left-6 md:translate-x-0 z-[55] w-[94%] max-w-[380px] md:w-[360px] panel border border-slate-200 dark:border-slate-700 shadow-2xl rounded-3xl overflow-hidden">
        
        <!-- Drag handle (mobile only) -->
        <div class="md:hidden flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
        </div>

        <!-- Header -->
        <div class="px-5 pt-4 pb-3 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="section-header text-red-600 dark:text-red-400">MODE OPERASI</div>
                    <div class="text-lg font-extrabold tracking-tight text-slate-900 dark:text-white">Pilih Kategori Darurat</div>
                </div>
                <div class="flex items-center gap-x-1 text-[10px] font-mono text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-full">
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full status-dot"></div>
                    <span class="font-bold">LIVE</span>
                </div>
            </div>
        </div>

        <!-- Mode Tabs -->
        <div class="px-5 pt-4 pb-2">
            <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl">
                <button id="tab-medis" 
                        onclick="setMode('rumah_sakit')"
                        class="flex items-center justify-center gap-x-2 py-3 text-sm font-extrabold rounded-xl bg-white dark:bg-slate-900 shadow text-blue-700 dark:text-blue-400 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <span>KESEHATAN</span>
                </button>
                <button id="tab-keamanan" 
                        onclick="setMode('keamanan')"
                        class="flex items-center justify-center gap-x-2 py-3 text-sm font-extrabold rounded-xl text-slate-600 dark:text-slate-300 hover:bg-white/70 dark:hover:bg-slate-900/70 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 8.932 11.623 1.072.546 2.355.546 3.428 0 5.107-1.333 8.932-6.03 8.932-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.249-8.269-3.268z" />
                    </svg>
                    <span>KEAMANAN</span>
                </button>
            </div>
        </div>

        <!-- ROUTE RESULT CARD -->
        <div id="result-card" class="hidden mx-5 mb-4 p-4 bg-gradient-to-br from-slate-50 to-white dark:from-slate-800 dark:to-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl route-info">
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-bold text-slate-500 dark:text-slate-400">TUJUAN TERDEKAT</div>
                    <div id="res-nama" class="font-extrabold text-lg leading-tight text-slate-900 dark:text-white pr-2"></div>
                </div>
            </div>
            
            <div class="flex gap-x-6 mb-4">
                <div>
                    <div class="text-xs text-slate-400 font-medium">JARAK</div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white tabular-nums"><span id="res-km">0</span><span class="text-base align-super font-normal text-slate-400">km</span></div>
                </div>
                <div>
                    <div class="text-xs text-slate-400 font-medium">EST. WAKTU</div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white tabular-nums"><span id="res-min">0</span><span class="text-base align-super font-normal text-slate-400">menit</span></div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <button onclick="startGoogleNavigation()" 
                        class="flex items-center justify-center gap-x-2 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 transition text-white text-xs font-extrabold rounded-2xl tracking-widest">
                    <span>BUKA NAVIGASI</span>
                </button>
                <button onclick="shareToWhatsApp()" 
                        class="flex items-center justify-center gap-x-2 py-3 bg-white hover:bg-slate-50 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-xs font-extrabold rounded-2xl tracking-widest">
                    <span>SHARE WA</span>
                </button>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="px-5 pb-5">
            <button onclick="getUserLocation()" 
                    class="w-full flex items-center justify-center gap-x-2 py-3 text-sm font-extrabold bg-slate-900 hover:bg-black transition text-white rounded-2xl active:scale-[0.985]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                </svg>
                <span>TENTUKAN LOKASI SAYA</span>
            </button>
            
            <div class="mt-3 text-center">
                <button onclick="clearRoute()" 
                        class="text-xs font-medium text-slate-400 hover:text-red-500 transition">
                    Hapus rute aktif
                </button>
            </div>
        </div>
    </div>

    <!-- REPORT INCIDENT MODAL -->
    <div id="report-modal" onclick="if (event.target.id === 'report-modal') closeReportModal()" 
         class="hidden fixed inset-0 z-[70] bg-black/70 flex items-end md:items-center justify-center">
        <div onclick="event.stopImmediatePropagation()" 
             class="w-full md:w-[420px] bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl p-6 shadow-2xl border border-slate-100 dark:border-slate-700">
            
            <div class="flex justify-between items-center mb-5">
                <div>
                    <div class="font-extrabold text-xl text-slate-900 dark:text-white">Laporkan Insiden</div>
                    <div class="text-xs text-red-600 font-bold">Data akan langsung masuk ke sistem real-time</div>
                </div>
                <button onclick="closeReportModal()" class="text-3xl leading-none text-slate-300 hover:text-slate-500">×</button>
            </div>

            <div class="space-y-3 mb-6">
                <div class="text-xs font-bold text-slate-500 px-1">JENIS INSIDEN</div>
                
                <div class="grid grid-cols-2 gap-2" id="incident-types">
                    <button onclick="selectIncidentType(this, 'Kecelakaan Parah')" class="incident-type-btn flex items-center gap-x-3 px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-2xl text-left active:scale-[0.985]">
                        <span class="text-2xl">💥</span>
                        <div class="text-sm font-bold">Kecelakaan</div>
                    </button>
                    <button onclick="selectIncidentType(this, 'Banjir Luapan')" class="incident-type-btn flex items-center gap-x-3 px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-2xl text-left active:scale-[0.985]">
                        <span class="text-2xl">🌊</span>
                        <div class="text-sm font-bold">Banjir</div>
                    </button>
                    <button onclick="selectIncidentType(this, 'Pohon Tumbang')" class="incident-type-btn flex items-center gap-x-3 px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-2xl text-left active:scale-[0.985]">
                        <span class="text-2xl">🌳</span>
                        <div class="text-sm font-bold">Pohon Tumbang</div>
                    </button>
                    <button onclick="selectIncidentType(this, 'Jalan Rusak / Lubang')" class="incident-type-btn flex items-center gap-x-3 px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-2xl text-left active:scale-[0.985]">
                        <span class="text-2xl">🚧</span>
                        <div class="text-sm font-bold">Jalan Rusak</div>
                    </button>
                </div>
            </div>

            <button onclick="submitReportFromModal()" 
                    class="w-full py-4 bg-red-600 hover:bg-red-700 active:bg-red-800 transition text-white font-extrabold text-sm tracking-[1px] rounded-2xl">
                KIRIM LAPORAN KE PUSAT
            </button>
            
            <p class="text-center text-[10px] text-slate-400 mt-3">Laporan ini akan terlihat oleh petugas dan pengguna lain secara langsung.</p>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    
    <script>
        // ==================== DATA & STATE ====================
        const facilities = JSON.parse('{!! json_encode($facilities) !!}');
        
        let map;
        let routingControl = null;
        let eventMarker = null;
        let destMarker = null;
        let currentMode = 'rumah_sakit';
        let reportMarkers = {};
        let selectedIncidentType = null;
        let currentReportLat = null;
        let currentReportLng = null;

        // ==================== INIT MAP ====================
        function initMap() {
            map = L.map('map', { 
                zoomControl: false,
                attributionControl: false 
            }).setView([3.5952, 98.6722], 13);

            // Light tiles (clean)
            const lightTiles = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 20
            });
            
            // Dark tiles
            const darkTiles = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 20
            });
            
            lightTiles.addTo(map);
            window.lightTiles = lightTiles;
            window.darkTiles = darkTiles;

            // Add facilities markers
            facilities.forEach(f => {
                const color = f.kategori === 'rumah_sakit' ? '#2563eb' : '#334155';
                const icon = L.divIcon({
                    html: `<div style="background-color: ${color}; width: 11px; height: 11px; border: 2px solid white; border-radius: 9999px; box-shadow: 0 0 0 3px rgba(0,0,0,0.15);"></div>`,
                    className: '',
                    iconSize: [15, 15],
                    iconAnchor: [7.5, 7.5]
                });
                
                const marker = L.marker([f.lat, f.lng], { icon: icon }).addTo(map);
                marker.bindPopup(`
                    <div class="font-sans p-1">
                        <div class="font-extrabold text-base">${f.nama}</div>
                        <div class="text-xs text-slate-500 mt-0.5">${f.alamat || ''}</div>
                        <div class="mt-2 text-[10px] font-mono text-slate-400">${f.lat}, ${f.lng}</div>
                    </div>
                `, { closeButton: false });
            });

            // Click on map to report
            map.on('click', function(e) {
                // Only trigger if not clicking on existing markers or controls
                if (!e.originalEvent.target.closest('.leaflet-marker-icon') && 
                    !e.originalEvent.target.closest('.leaflet-popup')) {
                    currentReportLat = e.latlng.lat;
                    currentReportLng = e.latlng.lng;
                    openReportModal();
                }
            });

            // Load initial reports
            fetchActiveReports();
            
            // Auto refresh reports every 8 seconds
            setInterval(fetchActiveReports, 8000);
        }

        // ==================== THEME ====================
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            document.getElementById('theme-icon').innerText = isDark ? '☀️' : '🌙';
            
            if (isDark && window.lightTiles) {
                map.removeLayer(window.lightTiles);
                window.darkTiles.addTo(map);
            } else if (window.darkTiles) {
                map.removeLayer(window.darkTiles);
                window.lightTiles.addTo(map);
            }
        }

        // ==================== USER LOCATION ====================
        function getUserLocation() {
            if (!navigator.geolocation) {
                alert("Browser Anda tidak mendukung geolokasi.");
                return;
            }
            
            navigator.geolocation.getCurrentPosition(position => {
                const { latitude, longitude } = position.coords;
                map.flyTo([latitude, longitude], 16, { duration: 1.2 });
                
                setUserPinpoint(latitude, longitude);
            }, () => {
                alert("Gagal mendapatkan lokasi. Pastikan izin lokasi diaktifkan.");
            });
        }

        function setUserPinpoint(lat, lng) {
            if (eventMarker) map.removeLayer(eventMarker);
            
            const userIcon = L.divIcon({
                html: `
                    <div style="width: 28px; height: 28px; background: #DC2626; border: 3px solid white; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); box-shadow: 0 4px 12px rgb(220 38 38 / 0.5); display: flex; align-items: center; justify-content: center;">
                        <div style="width: 8px; height: 8px; background: white; border-radius: 50%; transform: rotate(45deg);"></div>
                    </div>
                `,
                className: '',
                iconSize: [28, 28],
                iconAnchor: [14, 28]
            });
            
            eventMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map);
            
            // Auto calculate route if mode is set
            setTimeout(() => {
                calculateBestRoute(lat, lng);
            }, 400);
        }

        // ==================== ROUTING ====================
        function calculateBestRoute(userLat, userLng, manualTarget = null) {
            let target = manualTarget;
            
            if (!target) {
                const filtered = facilities.filter(f => f.kategori === currentMode);
                if (filtered.length === 0) return;
                
                let closest = filtered[0];
                let minDist = Infinity;
                
                filtered.forEach(f => {
                    const dist = L.latLng(userLat, userLng).distanceTo([f.lat, f.lng]);
                    if (dist < minDist) {
                        minDist = dist;
                        closest = f;
                    }
                });
                target = closest;
            }

            // Remove old markers
            if (destMarker) map.removeLayer(destMarker);
            if (routingControl) map.removeControl(routingControl);

            // Destination marker
            const isHealth = target.kategori === 'rumah_sakit';
            const destIcon = L.divIcon({
                html: `
                    <div style="display: flex; flex-direction: column; align-items: center; width: 140px;">
                        <div style="background: white; padding: 2px 10px; border-radius: 9999px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid #e2e8f0; font-size: 11px; font-weight: 800; color: #0f172a; white-space: nowrap; margin-bottom: 4px;">
                            ${target.nama}
                        </div>
                        <div style="width: 34px; height: 34px; background: ${isHealth ? '#2563eb' : '#334155'}; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid white; box-shadow: 0 4px 10px rgb(0 0 0 / 0.25); display: flex; align-items: center; justify-content: center;">
                            <span style="transform: rotate(45deg); color: white; font-size: 15px;">📍</span>
                        </div>
                    </div>
                `,
                className: '',
                iconSize: [140, 70],
                iconAnchor: [70, 65]
            });
            
            destMarker = L.marker([target.lat, target.lng], { icon: destIcon }).addTo(map);

            // Routing
            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(userLat, userLng),
                    L.latLng(target.lat, target.lng)
                ],
                routeWhileDragging: false,
                addWaypoints: false,
                show: true,
                lineOptions: {
                    styles: [{ 
                        color: isHealth ? '#2563eb' : '#475569', 
                        weight: 7, 
                        opacity: 0.85 
                    }]
                },
                createMarker: () => null
            }).addTo(map);

            routingControl.on('routesfound', function(e) {
                const summary = e.routes[0].summary;
                const km = (summary.totalDistance / 1000).toFixed(1);
                const minutes = Math.ceil(summary.totalTime / 60);

                // Show result card
                document.getElementById('res-nama').innerText = target.nama;
                document.getElementById('res-km').innerText = km;
                document.getElementById('res-min').innerText = minutes;
                document.getElementById('result-card').classList.remove('hidden');
                
                // Store current target for navigation/share
                window.currentTarget = { ...target, userLat, userLng, km, minutes };
            });

            map.flyTo([target.lat, target.lng], 14, { duration: 1.1 });
        }

        function startGoogleNavigation() {
            if (!window.currentTarget) return;
            const t = window.currentTarget;
            const url = `https://www.google.com/maps/dir/?api=1&origin=${t.userLat},${t.userLng}&destination=${t.lat},${t.lng}&travelmode=driving`;
            window.open(url, '_blank');
        }

        function shareToWhatsApp() {
            if (!window.currentTarget) return;
            const t = window.currentTarget;
            
            const message = encodeURIComponent(
                `🚨 *DARURAT LEK MEDAN* 🚨\n\n` +
                `Saya membutuhkan bantuan segera!\n\n` +
                `📍 Lokasi saya: https://www.google.com/maps?q=${t.userLat},${t.userLng}\n` +
                `🏥 Menuju: ${t.nama}\n` +
                `📏 Jarak: ${t.km} km\n` +
                `⏱ Estimasi: ${t.minutes} menit\n\n` +
                `Mohon bantuan atau doa. Terima kasih.`
            );
            
            window.open(`https://api.whatsapp.com/send?text=${message}`, '_blank');
        }

        function clearRoute() {
            if (routingControl) {
                map.removeControl(routingControl);
                routingControl = null;
            }
            if (destMarker) {
                map.removeLayer(destMarker);
                destMarker = null;
            }
            document.getElementById('result-card').classList.add('hidden');
            window.currentTarget = null;
        }

        // ==================== MODE ====================
        function setMode(mode) {
            currentMode = mode;
            
            const medisBtn = document.getElementById('tab-medis');
            const keamananBtn = document.getElementById('tab-keamanan');
            
            if (mode === 'rumah_sakit') {
                medisBtn.className = `flex items-center justify-center gap-x-2 py-3 text-sm font-extrabold rounded-xl bg-white dark:bg-slate-900 shadow text-blue-700 dark:text-blue-400 transition-all`;
                keamananBtn.className = `flex items-center justify-center gap-x-2 py-3 text-sm font-extrabold rounded-xl text-slate-600 dark:text-slate-300 hover:bg-white/70 dark:hover:bg-slate-900/70 transition-all`;
            } else {
                keamananBtn.className = `flex items-center justify-center gap-x-2 py-3 text-sm font-extrabold rounded-xl bg-white dark:bg-slate-900 shadow text-slate-800 dark:text-white transition-all`;
                medisBtn.className = `flex items-center justify-center gap-x-2 py-3 text-sm font-extrabold rounded-xl text-slate-600 dark:text-slate-300 hover:bg-white/70 dark:hover:bg-slate-900/70 transition-all`;
            }
            
            // Recalculate if user already has location
            if (eventMarker) {
                const pos = eventMarker.getLatLng();
                calculateBestRoute(pos.lat, pos.lng);
            }
        }

        // ==================== SEARCH ====================
        function searchFacility() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase().trim();
            const resultsDiv = document.getElementById('searchResults');
            
            if (!filter) {
                resultsDiv.classList.add('hidden');
                return;
            }
            
            const filtered = facilities.filter(f => 
                f.nama.toLowerCase().includes(filter) || 
                (f.alamat && f.alamat.toLowerCase().includes(filter))
            );
            
            if (filtered.length > 0) {
                resultsDiv.innerHTML = filtered.map(f => `
                    <div onclick="selectFacilityFromSearch('${f.nama.replace(/'/g, "\\'")}', ${f.lat}, ${f.lng}, '${f.kategori}')" 
                         class="px-5 py-3.5 hover:bg-slate-800 cursor-pointer border-b border-slate-700 last:border-none flex items-center gap-x-3 text-sm">
                        <div class="w-2 h-2 rounded-full flex-shrink-0 ${f.kategori === 'rumah_sakit' ? 'bg-blue-500' : 'bg-slate-400'}"></div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-white">${f.nama}</div>
                            <div class="text-xs text-slate-400 truncate">${f.alamat || ''}</div>
                        </div>
                    </div>
                `).join('');
                resultsDiv.classList.remove('hidden');
            } else {
                resultsDiv.innerHTML = `<div class="px-5 py-4 text-sm text-slate-400">Tidak ditemukan.</div>`;
                resultsDiv.classList.remove('hidden');
            }
        }

        function selectFacilityFromSearch(nama, lat, lng, kategori) {
            document.getElementById('searchResults').classList.add('hidden');
            document.getElementById('searchInput').value = nama;
            
            if (!eventMarker) {
                alert("Silakan tentukan lokasi Anda terlebih dahulu dengan tombol 'TENTUKAN LOKASI SAYA'");
                return;
            }
            
            const userPos = eventMarker.getLatLng();
            const target = { nama, lat, lng, kategori };
            
            calculateBestRoute(userPos.lat, userPos.lng, target);
        }

        // ==================== REPORTING (CROWDSOURCING) ====================
        function openReportModal() {
            document.getElementById('report-modal').classList.remove('hidden');
            document.getElementById('report-modal').classList.add('flex');
            selectedIncidentType = null;
            
            // Clear previous selections
            document.querySelectorAll('#incident-types button').forEach(btn => {
                btn.classList.remove('active', 'border-red-600', 'bg-red-50');
                btn.classList.add('border-slate-200', 'dark:border-slate-600');
            });
        }

        function closeReportModal() {
            document.getElementById('report-modal').classList.remove('flex');
            document.getElementById('report-modal').classList.add('hidden');
        }

        function selectIncidentType(element, type) {
            // Deselect all
            document.querySelectorAll('#incident-types button').forEach(btn => {
                btn.classList.remove('active', 'border-red-600', 'bg-red-50', 'dark:bg-red-900/20');
                btn.classList.add('border-slate-200', 'dark:border-slate-600');
            });
            
            // Select current
            element.classList.add('active', 'border-red-600', 'bg-red-50', 'dark:bg-red-900/20');
            element.classList.remove('border-slate-200', 'dark:border-slate-600');
            
            selectedIncidentType = type;
        }

        function submitReportFromModal() {
            if (!selectedIncidentType) {
                alert("Silakan pilih jenis insiden terlebih dahulu.");
                return;
            }
            
            if (!currentReportLat || !currentReportLng) {
                // If opened from FAB without map click, ask for location
                if (eventMarker) {
                    const pos = eventMarker.getLatLng();
                    currentReportLat = pos.lat;
                    currentReportLng = pos.lng;
                } else {
                    alert("Silakan klik lokasi insiden di peta terlebih dahulu, atau tentukan lokasi Anda.");
                    closeReportModal();
                    return;
                }
            }
            
            const reportId = 'rep_' + Date.now();
            
            fetch('/api/reports', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: reportId,
                    tipe: selectedIncidentType,
                    lat: currentReportLat,
                    lng: currentReportLng
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderReportMarker(reportId, selectedIncidentType, currentReportLat, currentReportLng);
                    closeReportModal();
                    
                    // Reset
                    currentReportLat = null;
                    currentReportLng = null;
                    selectedIncidentType = null;
                    
                    // Toast
                    showToast("Laporan berhasil dikirim ke pusat kendali.");
                } else {
                    alert("Gagal mengirim laporan: " + (data.error || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error(err);
                alert("Terjadi kesalahan saat mengirim laporan.");
            });
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-24 left-1/2 -translate-x-1/2 bg-emerald-600 text-white px-5 py-3 rounded-2xl text-sm font-medium shadow-xl flex items-center gap-x-2 z-[80]`;
            toast.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7"/></svg>
                <span>${message}</span>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.transition = 'all 0.3s ease';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 200);
            }, 2400);
        }

        function renderReportMarker(id, tipe, lat, lng) {
            if (reportMarkers[id]) {
                map.removeLayer(reportMarkers[id]);
            }
            
            const warningIcon = L.divIcon({
                html: `
                    <div style="width: 32px; height: 32px; background: #F59E0B; border: 3px solid white; border-radius: 9999px; box-shadow: 0 4px 12px rgb(245 158 11 / 0.4); display: flex; align-items: center; justify-content: center; animation: bounce 1.5s infinite;">
                        <span style="font-size: 15px; line-height: 1;">⚠︎</span>
                    </div>
                `,
                className: '',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });
            
            const marker = L.marker([lat, lng], { icon: warningIcon }).addTo(map);
            
            marker.bindPopup(`
                <div class="p-3 w-[210px]">
                    <div class="font-extrabold text-amber-600 text-sm mb-1">${tipe}</div>
                    <div class="text-xs text-slate-500 mb-3">Dilaporkan warga • Real-time</div>
                    
                    <button onclick="resolveReport('${id}', this)" 
                            class="w-full py-2 text-xs font-extrabold tracking-wider bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl transition">
                        TANDAI SELESAI
                    </button>
                </div>
            `, { closeButton: false, offset: [0, -5] });
            
            reportMarkers[id] = marker;
        }

        function resolveReport(id, buttonElement) {
            fetch(`/api/reports/resolve/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (reportMarkers[id]) {
                        map.removeLayer(reportMarkers[id]);
                        delete reportMarkers[id];
                    }
                }
            });
        }

        function fetchActiveReports() {
            fetch('/api/reports')
                .then(res => res.json())
                .then(data => {
                    // Clear old markers
                    Object.keys(reportMarkers).forEach(id => {
                        if (reportMarkers[id]) map.removeLayer(reportMarkers[id]);
                    });
                    reportMarkers = {};
                    
                    data.forEach(report => {
                        renderReportMarker(report.id, report.tipe, report.lat, report.lng);
                    });
                })
                .catch(err => console.log('Gagal sinkronisasi laporan'));
        }

        // ==================== INIT EVERYTHING ====================
        function initializeApp() {
            initMap();
            
            // Default mode
            setMode('rumah_sakit');
            
            // Make panel draggable on mobile
            if (window.innerWidth < 768) {
                interact('#floating-panel').draggable({
                    allowFrom: '.md\\:hidden',
                    listeners: {
                        move(event) {
                            const target = event.target;
                            const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                            const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
                            
                            target.style.transform = `translate(calc(-50% + ${x}px), ${y}px)`;
                            target.setAttribute('data-x', x);
                            target.setAttribute('data-y', y);
                        }
                    }
                });
            }
            
            // Keyboard support for search
            document.getElementById('searchInput').addEventListener('focus', function() {
                if (window.innerWidth < 768) {
                    document.getElementById('floating-panel').style.display = 'none';
                }
            });
            
            // Demo: Auto locate user after 2.5s if no interaction (optional, can remove)
            // setTimeout(() => {
            //     if (!eventMarker) console.log('%c[LEK] Ready for location', 'color:#64748b');
            // }, 2500);
            
            // Initial toast for first time users (optional)
            // setTimeout(() => {
            //     if (!localStorage.getItem('lekmedan_welcome')) {
            //         showToast("Klik peta untuk melaporkan insiden");
            //         localStorage.setItem('lekmedan_welcome', '1');
            //     }
            // }, 4500);
        }

        // Boot
        window.onload = initializeApp;
        
        // Expose some functions if needed for debugging
        window.LEKMEDAN = { setMode, getUserLocation, clearRoute };
    </script>
</body>
</html>