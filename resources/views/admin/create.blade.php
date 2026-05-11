<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight tracking-tight uppercase">
            {{ __('Tambah Lokasi Fasilitas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-slate-100 p-6 sm:p-8">
                <form action="{{ route('admin.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Nama Fasilitas</label>
                                <input type="text" name="nama" class="mt-2 block w-full border-slate-200 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Contoh: Polsek Medan Baru" required>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Kategori</label>
                                <select name="kategori" class="mt-2 block w-full border-slate-200 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="rumah_sakit">Kesehatan (RS / Klinik)</option>
                                    <option value="keamanan">Keamanan (Polsek / Polrestabes)</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-blue-600 uppercase tracking-widest">Latitude</label>
                                    <input type="text" name="lat" id="lat" oninput="updateMapFromInput()" class="mt-2 block w-full bg-white border-blue-200 text-slate-900 rounded-xl shadow-sm font-mono text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 3.595244" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-blue-600 uppercase tracking-widest">Longitude</label>
                                    <input type="text" name="lng" id="lng" oninput="updateMapFromInput()" class="mt-2 block w-full bg-white border-blue-200 text-slate-900 rounded-xl shadow-sm font-mono text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 98.672223" required>
                                </div>
                            </div>
                            
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 flex justify-between">
                                    <span>Alamat Otomatis</span>
                                    <span id="status-alamat" class="text-[9px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full hidden italic">Melacak...</span>
                                </label>
                                <textarea name="alamat" id="alamatInput" rows="3" class="block w-full border-slate-200 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all text-sm" placeholder="Alamat akan terisi sendiri saat koordinat dimasukkan..." required></textarea>
                            </div>
                            
                            <button type="submit" class="w-full bg-slate-900 text-white py-4 mt-4 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-black transition-all shadow-xl active:scale-[0.98]">
                                Simpan ke Database
                            </button>
                        </div>

                        <div class="space-y-2 h-full min-h-[400px] flex flex-col">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Preview Lokasi</label>
                            <div id="map-picker" class="w-full flex-1 rounded-[2rem] border-4 border-slate-100 shadow-inner overflow-hidden min-h-[300px]"></div>
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        const map = L.map('map-picker').setView([3.5952, 98.6722], 13);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(map);

        let marker;

        // 1. Fungsi Utama: Update Map saat Input diisi manual
        function updateMapFromInput() {
            const latValue = document.getElementById('lat').value;
            const lngValue = document.getElementById('lng').value;

            // Validasi apakah input adalah angka yang valid
            if (latValue && lngValue && !isNaN(latValue) && !isNaN(lngValue)) {
                const lat = parseFloat(latValue);
                const lng = parseFloat(lngValue);

                setMarker(lat, lng, "Titik dari Input Manual");
                reverseGeocode(lat, lng); // Sekalian cari alamatnya biar admin ga ngetik lagi
            }
        }

        // 2. Fungsi: Klik Peta (Jika ingin koreksi manual)
        map.on('click', function(e) {
            const { lat, lng } = e.latlng;
            document.getElementById('lat').value = lat.toFixed(6);
            document.getElementById('lng').value = lng.toFixed(6);
            setMarker(lat, lng, "Titik Dipilih");
            reverseGeocode(lat, lng);
        });

        function setMarker(lat, lng, popupText) {
            if (marker) map.removeLayer(marker);
            
            const icon = L.divIcon({
                html: `<div class="w-4 h-4 bg-red-600 border-2 border-white rounded-full shadow-lg pulse"></div>`,
                className: 'custom-icon', iconSize: [16, 16], iconAnchor: [8, 8]
            });

            marker = L.marker([lat, lng], {icon: icon}).addTo(map).bindPopup(`<b class="text-xs">${popupText}</b>`).openPopup();
            map.panTo([lat, lng]); 
        }

        async function reverseGeocode(lat, lng) {
            const alamatInput = document.getElementById('alamatInput');
            const statusLabel = document.getElementById('status-alamat');
            statusLabel.classList.remove('hidden');

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
                const data = await response.json();
                if (data && data.display_name) {
                    alamatInput.value = data.display_name;
                }
            } catch (error) {
                console.log("Geocoding gagal");
            } finally {
                statusLabel.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>