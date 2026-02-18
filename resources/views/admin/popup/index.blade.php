@extends('admin.layout')

@section('title', 'Manajemen Popup Iklan')
@section('header-title', 'Inforkom & Publikasi')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-black text-gray-800 tracking-tight uppercase">Manajemen Info Popup</h2>
            <p class="text-sm text-gray-500">Atur iklan atau pengumuman yang muncul saat pengguna login.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- 2. KOLOM KIRI: FORM INPUT (Card Style) --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-700 flex items-center">
                        <i class="fas fa-plus-circle text-primary mr-2"></i> Buat Iklan Baru
                    </h3>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('admin.popup.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        
                        {{-- Judul --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Judul Event</label>
                            <input type="text" name="judul" class="w-full border-gray-200 rounded-lg focus:ring-primary focus:border-primary text-sm" placeholder="Contoh: Ucapan Natal 2026" required>
                        </div>

                        {{-- Upload Gambar --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Gambar Banner</label>
                            <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 flex flex-col items-center justify-center text-center hover:bg-gray-50 transition cursor-pointer group">
                                <input type="file" name="gambar" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(event)" required>
                                
                                <div id="upload-placeholder" class="space-y-2">
                                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mx-auto group-hover:scale-110 transition">
                                        <i class="fas fa-cloud-upload-alt text-xl"></i>
                                    </div>
                                    <p class="text-xs text-gray-500">Klik untuk upload (Max 2MB)</p>
                                </div>
                                <img id="img-preview" class="hidden w-full rounded-lg shadow-sm mt-2 object-cover max-h-40">
                            </div>
                        </div>

                        {{-- Tanggal --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Mulai</label>
                                <input type="date" name="mulai_tanggal" class="w-full border-gray-200 rounded-lg focus:ring-primary focus:border-primary text-sm" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Selesai</label>
                                <input type="date" name="selesai_tanggal" class="w-full border-gray-200 rounded-lg focus:ring-primary focus:border-primary text-sm" required>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-primary hover:bg-blue-800 text-white py-3 rounded-lg text-sm font-bold uppercase tracking-wider shadow-lg transition transform hover:-translate-y-0.5 flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> Tayangkan Iklan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. KOLOM KANAN: TABEL LIST (Style Reference) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase font-bold text-gray-500 tracking-wider">
                                <th class="px-6 py-4">Preview</th>
                                <th class="px-6 py-4">Info Iklan</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($popups as $popup)
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out group">
                                {{-- Kolom Gambar --}}
                                <td class="px-6 py-4 align-middle">
                                    <div class="h-16 w-24 rounded-lg overflow-hidden border border-gray-200 shadow-sm relative">
                                        <img src="{{ asset('storage/' . $popup->gambar_path) }}" class="w-full h-full object-cover">
                                    </div>
                                </td>

                                {{-- Kolom Info --}}
                                <td class="px-6 py-4 align-middle">
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-primary transition">{{ $popup->judul }}</p>
                                    <div class="flex items-center mt-1 text-xs text-gray-500">
                                        <i class="far fa-calendar-alt mr-1.5 text-gray-400"></i>
                                        {{ date('d M', strtotime($popup->mulai_tanggal)) }} - {{ date('d M Y', strtotime($popup->selesai_tanggal)) }}
                                    </div>
                                </td>

                                {{-- Kolom Status (Badge Style) --}}
                                <td class="px-6 py-4 align-middle text-center">
                                    @if($popup->is_active && now()->between($popup->mulai_tanggal, $popup->selesai_tanggal))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @elseif(!$popup->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                            Non-Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                            Expired
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom Aksi (Button Style) --}}
                                <td class="px-6 py-4 align-middle text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        {{-- Toggle --}}
                                        <form action="{{ route('admin.popup.toggle', $popup->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="p-2 rounded-lg transition shadow-sm border border-transparent {{ $popup->is_active ? 'bg-green-50 text-green-600 hover:bg-green-500 hover:text-white' : 'bg-gray-50 text-gray-400 hover:bg-gray-500 hover:text-white' }}" title="Ubah Status">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form action="{{ route('admin.popup.destroy', $popup->id) }}" method="POST" onsubmit="return confirm('Hapus permanen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-500 hover:text-white transition shadow-sm border border-transparent" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-images text-2xl text-gray-300"></i>
                                        </div>
                                        <span class="text-sm font-medium">Belum ada popup iklan yang dibuat.</span>
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