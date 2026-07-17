@extends('layouts.app')

@section('title', 'Verifikasi & Edit Publikasi')

@section('content')
    <x-admin-form 
        title="Formulir Pembaruan Dokumen Publikasi" 
        action="{{ route('admin.posts.update', $post) }}" 
        method="PUT"
        back-route="{{ route('admin.posts.index') }}"
        has-file="true"
    >
        <div class="space-y-6">

            {{-- ======================================================== --}}
            {{-- PANEL STATUS & OTORISASI (FORMAL)                        --}}
            {{-- ======================================================== --}}
            @if($post->status == 'pending')
                <div class="border-l-4 border-yellow-500 bg-yellow-50 p-4 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h4 class="font-bold text-yellow-800 text-sm uppercase tracking-wide">Status Dokumen: Menunggu Verifikasi</h4>
                            <p class="text-xs text-yellow-700 mt-1">Draf ini diajukan oleh <b>{{ $post->author->name ?? 'Admin Sistem' }}</b> dan membutuhkan verifikasi sebelum diterbitkan ke portal publik.</p>
                        </div>
                        @hasanyrole('Super Admin|Admin Bidang 4')
                        <div class="flex flex-wrap gap-2">
                            <button type="submit" name="action_type" value="publish" class="bg-blue-800 hover:bg-blue-900 text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wide shadow-sm transition flex items-center">
                                <i class="fas fa-check mr-2"></i> Verifikasi & Terbitkan
                            </button>
                            <button type="submit" name="action_type" value="draft" class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded text-xs font-bold uppercase tracking-wide shadow-sm transition flex items-center">
                                <i class="fas fa-times mr-2 text-red-600"></i> Tolak Dokumen
                            </button>
                        </div>
                        @endhasanyrole
                    </div>
                </div>
            @elseif($post->status == 'published')
                <div class="border-l-4 border-green-600 bg-green-50 p-4 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h4 class="font-bold text-green-800 text-sm uppercase tracking-wide">Status Dokumen: Terpublikasi</h4>
                            <p class="text-xs text-green-700 mt-1">Dokumen ini telah sah ditayangkan pada portal publik sejak {{ $post->published_at ? $post->published_at->format('d M Y, H:i') : '-' }} WIT.</p>
                        </div>
                        @hasanyrole('Super Admin|Admin Bidang 4')
                        <button type="submit" name="action_type" value="draft" class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded text-xs font-bold uppercase tracking-wide shadow-sm transition flex items-center">
                            <i class="fas fa-ban mr-2 text-red-600"></i> Batalkan Publikasi
                        </button>
                        @endhasanyrole
                    </div>
                </div>
            @else
                <div class="border-l-4 border-gray-400 bg-gray-100 p-4 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h4 class="font-bold text-gray-700 text-sm uppercase tracking-wide">Status Dokumen: Draf Konseptual</h4>
                            <p class="text-xs text-gray-600 mt-1">Dokumen ini bersifat draf internal dan belum diajukan untuk proses verifikasi publikasi.</p>
                        </div>
                        @hasanyrole('Super Admin|Admin Bidang 4')
                        <button type="submit" name="action_type" value="publish" class="bg-blue-800 hover:bg-blue-900 text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wide shadow-sm transition flex items-center">
                            <i class="fas fa-upload mr-2"></i> Terbitkan Dokumen
                        </button>
                        @endhasanyrole
                    </div>
                </div>
            @endif
            {{-- ======================================================== --}}

            {{-- Input Judul --}}
            <div class="mt-6">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Judul Publikasi <span class="text-red-600">*</span></label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" required 
                    class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm"
                    placeholder="Masukkan judul publikasi...">
                @error('title') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Input Konten --}}
            <div class="mt-6">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Uraian Konten <span class="text-red-600">*</span></label>
                <textarea name="content" rows="12" required 
                    class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 placeholder-gray-400 leading-relaxed shadow-sm">{{ old('content', $post->content) }}</textarea>
                @error('content') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                
                {{-- Kelola Gambar --}}
                <div class="bg-white p-5 rounded border border-gray-200 shadow-sm">
                    <label class="block text-xs font-bold text-gray-800 uppercase mb-4 border-b border-gray-200 pb-2">Pembaruan Gambar Lampiran</label>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(event, 'preview-edit')"
                           class="block w-full text-xs text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-800 hover:file:bg-gray-200 transition cursor-pointer border border-gray-200 p-1">
                    
                    @if($post->image_path)
                        <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded">
                            <p class="text-[10px] text-gray-500 uppercase font-bold mb-2">Lampiran Saat Ini:</p>
                            <img src="{{ Storage::url($post->image_path) }}" id="preview-edit" class="h-32 w-full object-cover border border-gray-300">
                            <div class="flex items-center mt-3 gap-2">
                                <input type="checkbox" id="remove_image" name="remove_image" value="1" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-600 cursor-pointer">
                                <label for="remove_image" class="text-xs font-bold text-red-600 cursor-pointer">Hapus Lampiran Ini</label>
                            </div>
                        </div>
                    @else
                        <img id="preview-edit" src="#" class="mt-4 h-32 hidden border border-gray-300 object-cover w-full">
                    @endif
                </div>

                {{-- Panel Informasi Administratif --}}
                <div class="bg-white p-5 rounded border border-gray-200 shadow-sm">
                    <label class="block text-xs font-bold text-gray-800 uppercase mb-4 border-b border-gray-200 pb-2">Informasi Otorisasi Sistem</label>
                    
                    <div class="text-sm text-gray-700 leading-relaxed">
                        @hasanyrole('Super Admin|Admin Bidang 4')
                            <p class="mb-3">Berdasarkan hak akses struktural Anda, Anda memiliki kewenangan penuh untuk melakukan verifikasi, modifikasi, dan publikasi langsung atas dokumen ini.</p>
                            <p>Status tayang dapat diubah melalui panel otorisasi di bagian atas formulir ini.</p>
                        @else
                            <p class="mb-3 font-bold text-yellow-700">Perhatian:</p>
                            <p>Perubahan yang disimpan hanya akan memperbarui dokumen draf Anda. Sesuai prosedur administrasi, publikasi ke portal utama memerlukan peninjauan dan verifikasi dari Admin Bidang 4 Sinode.</p>
                        @endhasanyrole
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
                    if(preview.classList.contains('hidden')) preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
@endsection