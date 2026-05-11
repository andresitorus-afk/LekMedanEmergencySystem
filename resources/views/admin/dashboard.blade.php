<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-extrabold text-xl text-slate-800 leading-tight tracking-tight uppercase">
                Pusat Kendali <span class="text-red-600">LekMedan</span>
            </h2>
            
            <a href="{{ route('admin.create') }}" class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all shadow-lg flex items-center gap-2">
                <span>+</span> Tambah Lokasi Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm" role="alert">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-slate-100">
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
                                        <form action="{{ route('admin.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lokasi {{ $f->nama }}? Data yang dihapus tidak bisa dikembalikan.');">
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
                                        Database masih kosong. Silakan tambah data lokasi pertama Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>