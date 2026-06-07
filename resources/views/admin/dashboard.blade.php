<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 leading-tight tracking-tight uppercase">
            Pusat Kendali <span class="text-red-600">LekMedan</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm" role="alert">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-slate-100 p-6 sm:p-8">
                <form action="{{ route('admin.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        <div class="space-y-5">
                            <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider border-b pb-2">Tambah Lokasi Fasilitas Baru</h3>
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Nama Fasilitas</label>
                                <input type="text" name="nama" class="mt-2 block w-full border-slate-200 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Contoh: RS Umum Siloam" required>
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
                                    <input type="text" name="lat" id="lat" oninput="updateMapFromInput()" class="mt-2 block w-full bg-white border-blue-200 text-slate-900 rounded-xl shadow-sm font-mono text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="3.5952" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-blue-600 uppercase tracking-widest">Longitude</label>
                                    <input type="text" name="lng" id="lng" oninput="updateMapFromInput()" class="mt-2 block w-full bg-white border-blue-200 text-slate-900 rounded-xl shadow-sm font-mono text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="98.6722" required>
                                </div>
                            </div>
                            
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 flex justify-between">
                                    <span>Alamat Otomatis</span>
                                    <span id="status-alamat" class="text-[9px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full hidden italic">Melacak...</span>
                                </label>
                                <textarea name="alamat" id="alamatInput" rows="2" class="block w-full border-slate-200 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all text-sm" placeholder="Klik pada peta atau ketik koordinat agar alamat terisi otomatis..." required></textarea>
                            </div>
                            
                            <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-black transition-all shadow-xl active:scale-[0.98]">
                                Simpan Lokasi Baru
                            </button>
                        </div>

                        <div class="space-y-2 h-full min-h-[350px] flex flex-col">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Klik Pada Peta Untuk Memilih Koordinat</label>
                            <div id="map-picker" class="w-full flex-1 rounded-[2rem] border-4 border-slate-100 shadow-inner overflow-hidden min-h-[300px]"></div>
                        </div>
                        
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-slate-100">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider">Daftar Fasilitas Terdaftar</h3>
                </div>
                <div class="p-1 sm:p-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-widest text-slate-400 font-bold border-b border-slate-100">
                                <th class="p-4 rounded-tl-xl">Nama Instansi / Fasilitas</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Alamat Terdaftar</th>
                                <th class="p-4">Koordinat (Lat, Lng)</th>
                                <th class="p-4 rounded-tr-xl text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm">
                            @forelse ($facilities as $f)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="p-4 font-bold text-slate-800">{{ $f->nama }}</td>
                                    <td class="p-4">
                                        @if($f->kategori === 'rumah_sakit')
                                            <span class="bg-blue-50 text-blue-600 border border-blue-100 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Kesehatan</span>
                                        @else
                                            <span class="bg-slate-800 text-white border border-slate-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Keamanan</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-500 truncate max-w-[200px]">{{ $f->alamat }}</td>
                                    <td class="p-4 text-slate-400 font-mono text-xs opacity-70 group-hover:opacity-100 transition-opacity">
                                        {{ $f->lat }}, {{ $f->lng }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <form action="{{ route('admin.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lokasi {{ $f->nama }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-colors shadow-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-slate-400 italic text-sm">
                                        Database masih kosong. Silakan tambah data lokasi pertama Anda melalui form di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        const map = L.map('map-picker').setView([3.5952, 98.6722], 12);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(map);

        let marker;

        function updateMapFromInput() {
            const latValue = document.getElementById('lat').value;
            const lngValue = document.getElementById('lng').value;

            if (latValue && lngValue && !isNaN(latValue) && !isNaN(lngValue)) {
                const lat = parseFloat(latValue);
                const lng = parseFloat(lngValue);

                setMarker(lat, lng, "Titik dari Input Manual");
                reverseGeocode(lat, lng);
            }
        }

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