@extends('layouts.app')

@section('title', 'Manajemen Info Popup')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manajemen Info Popup</h2>
            <p class="text-sm text-slate-500">Atur pengumuman visual atau banner yang muncul otomatis saat pengguna mengakses portal.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- 2. KOLOM KIRI: FORM INPUT (Card Style) --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden sticky top-20">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest flex items-center">
                        <i class="fas fa-plus-circle text-blue-600 mr-2"></i> Buat Iklan / Banner Baru
                    </h3>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('admin.popup.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        
                        {{-- Judul --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Judul / Nama Event <span class="text-red-500">*</span></label>
                            <input type="text" name="judul" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 placeholder-slate-400 shadow-sm" placeholder="Contoh: Ucapan Selamat Natal 2026" required>
                        </div>

                        {{-- Upload Gambar --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Gambar Banner / Poster <span class="text-red-500">*</span></label>
                            <div class="relative border-2 border-dashed border-slate-200 rounded-xl p-4 flex flex-col items-center justify-center text-center hover:bg-slate-50 transition cursor-pointer group">
                                <input type="file" name="gambar" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(event)" required>
                                
                                <div id="upload-placeholder" class="space-y-2 py-4">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto group-hover:scale-110 transition group-hover:text-blue-500 group-hover:bg-blue-50">
                                        <i class="fas fa-cloud-upload-alt text-xl"></i>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Klik atau seret gambar ke sini</p>
                                    <p class="text-[9px] text-slate-400 italic">Maksimal ukuran file 2MB</p>
                                </div>
                                <img id="img-preview" class="hidden w-full rounded-lg shadow-sm mt-2 object-cover max-h-48 border border-slate-100">
                            </div>
                        </div>

                        {{-- Tanggal --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Tayang Mulai</label>
                                <input type="date" name="mulai_tanggal" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-600 shadow-sm" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Berakhir Pada</label>
                                <input type="date" name="selesai_tanggal" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-600 shadow-sm" required>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-lg text-xs font-bold uppercase tracking-widest shadow-lg transition transform hover:-translate-y-0.5 flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> Tayangkan Popup
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. KOLOM KANAN: TABEL LIST --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase font-bold text-slate-500 tracking-widest">
                                <th class="px-6 py-4">Visual Preview</th>
                                <th class="px-6 py-4">Informasi Penayangan</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($popups as $popup)
                            <tr class="hover:bg-slate-50 transition duration-150 group">
                                {{-- Kolom Gambar --}}
                                <td class="px-6 py-4 align-middle">
                                    <div class="h-16 w-28 rounded-lg overflow-hidden border border-slate-200 shadow-sm relative bg-slate-100">
                                        <img src="{{ asset('storage/' . $popup->gambar_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                    </div>
                                </td>

                                {{-- Kolom Info --}}
                                <td class="px-6 py-4 align-middle">
                                    <p class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition">{{ $popup->judul }}</p>
                                    <div class="flex items-center mt-1.5 text-[11px] text-slate-500 font-medium">
                                        <i class="far fa-calendar-alt mr-2 text-slate-400"></i>
                                        {{ date('d M', strtotime($popup->mulai_tanggal)) }} — {{ date('d M Y', strtotime($popup->selesai_tanggal)) }}
                                    </div>
                                </td>

                                {{-- Kolom Status --}}
                                <td class="px-6 py-4 align-middle text-center">
                                    @if($popup->is_active && now()->between($popup->mulai_tanggal, $popup->selesai_tanggal))
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-green-100 text-green-700 border border-green-200">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                            Running
                                        </span>
                                    @elseif(!$popup->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-500 border border-slate-200">
                                            Disabled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-orange-50 text-orange-600 border border-orange-100">
                                            Expired
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom Aksi --}}
                                <td class="px-6 py-4 align-middle text-center">
                                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        {{-- Toggle --}}
                                        <form action="{{ route('admin.popup.toggle', $popup->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="p-2 rounded-lg transition shadow-sm border border-transparent {{ $popup->is_active ? 'bg-green-50 text-green-600 hover:bg-green-600 hover:text-white' : 'bg-slate-100 text-slate-400 hover:bg-slate-600 hover:text-white' }}" title="Aktif/Non-aktifkan">
                                                <i class="fas fa-power-off text-sm"></i>
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form action="{{ route('admin.popup.destroy', $popup->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus popup ini secara permanen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-600 hover:text-white transition shadow-sm border border-transparent" title="Hapus Permanen">
                                                <i class="fas fa-trash-alt text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                            <i class="fas fa-images text-2xl text-slate-300"></i>
                                        </div>
                                        <p class="text-slate-500 text-sm font-bold uppercase tracking-widest">Belum Ada Popup Iklan</p>
                                        <p class="text-slate-400 text-xs mt-1">Gunakan panel di sebelah kiri untuk membuat pengumuman visual baru.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('img-preview');
                const placeholder = document.getElementById('upload-placeholder');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection