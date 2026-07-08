@extends('layouts.app')

@section('title', 'Penyusunan Publikasi Baru')

@section('content')
    <x-admin-form 
        title="Formulir Pengajuan Dokumen Publikasi" 
        action="{{ route('admin.posts.store') }}" 
        back-route="{{ route('admin.posts.index') }}"
        has-file="true"
    >
        <div class="space-y-6">
            {{-- Input Judul --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Judul Publikasi <span class="text-red-600">*</span></label>
                <input type="text" name="title" required 
                    class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm"
                    placeholder="Masukkan judul resmi publikasi...">
                @error('title') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Input Konten --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Uraian Konten <span class="text-red-600">*</span></label>
                <textarea name="content" rows="12" required 
                    class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 placeholder-gray-400 leading-relaxed shadow-sm"
                    placeholder="Susun uraian berita atau pengumuman secara terperinci di sini...">{{ old('content') }}</textarea>
                @error('content') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                {{-- Upload Gambar --}}
                <div class="bg-white p-5 rounded border border-gray-200 shadow-sm">
                    <label class="block text-xs font-bold text-gray-800 uppercase mb-4 border-b border-gray-200 pb-2">Lampiran Dokumen (Visual)</label>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(event, 'preview-new')"
                           class="block w-full text-xs text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-800 hover:file:bg-gray-200 transition cursor-pointer border border-gray-200 p-1">
                    <p class="mt-2 text-[10px] text-gray-500 font-medium">Ketentuan Format: JPG, PNG (Kapasitas Maksimal 2MB).</p>
                    <img id="preview-new" src="#" alt="Pratinjau" class="mt-4 h-32 hidden border border-gray-300 object-cover w-full">
                </div>

                {{-- Penjadwalan & Tombol Submit Dinamis Berdasarkan Role --}}
                <div class="bg-white p-5 rounded border border-gray-200 shadow-sm space-y-4">
                    <label class="block text-xs font-bold text-gray-800 uppercase mb-4 border-b border-gray-200 pb-2">Otorisasi & Penjadwalan Sistem</label>
                    
                    <div class="space-y-4">
                        @hasanyrole('Super Admin|Admin Bidang 4')
                            {{-- Jika Admin Pusat: Bebas Publish --}}
                            <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded">
                                <input type="checkbox" id="publish_now" name="publish_now" value="1" checked
                                       class="h-4 w-4 text-blue-800 border-gray-300 rounded focus:ring-blue-800 cursor-pointer">
                                <label for="publish_now" class="text-xs font-bold text-blue-900 cursor-pointer">Otorisasi Penerbitan Langsung ke Portal Publik</label>
                            </div>
                            <div class="pt-2">
                                <p class="text-[10px] font-bold text-gray-600 uppercase mb-2">Penjadwalan Otomatis (Opsional):</p>
                                <div class="flex gap-2">
                                    <input type="date" name="published_at_date" class="w-1/2 border-gray-300 rounded text-xs focus:ring-blue-800 shadow-sm">
                                    <input type="time" name="published_at_time" class="w-1/2 border-gray-300 rounded text-xs focus:ring-blue-800 shadow-sm">
                                </div>
                            </div>
                        @else
                            {{-- Jika Jemaat/Klasis: Hanya bisa kirim draft --}}
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded">
                                <p class="text-xs font-bold text-yellow-800 mb-2">Prosedur Administrasi Berjenjang</p>
                                <p class="text-[11px] text-yellow-700 leading-relaxed">Dokumen yang Anda ajukan akan didaftarkan dengan status <strong>Menunggu Verifikasi (Pending)</strong>. Penerbitan ke portal utama memerlukan persetujuan dan tinjauan akhir dari Biro Inforkom (Admin Bidang 4 Sinode).</p>
                                <input type="hidden" name="publish_now" value="1"> {{-- Trigger pengiriman draft otomatis --}}
                            </div>
                        @endrole
                    </div>
                </div>
            </div>
        </div>
    </x-admin-form>

    @push('scripts')
    <script>
        function previewImage(event, id) {
            const input = event.target;
            const preview = document.getElementById(id);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
@endsection