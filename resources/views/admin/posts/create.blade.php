@extends('layouts.app')

@section('title', 'Tambah Berita Baru')

@section('content')
    <x-admin-form 
        title="Tulis Berita / Kegiatan" 
        action="{{ route('admin.posts.store') }}" 
        back-route="{{ route('admin.posts.index') }}"
        has-file="true"
    >
        <div class="space-y-6">
            {{-- Input Judul --}}
            <x-form-input label="Judul Utama" name="title" required placeholder="Masukkan judul berita yang menarik..." />

            {{-- Input Konten --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Isi Konten Berita <span class="text-red-500">*</span></label>
                <textarea name="content" rows="12" required 
                    class="w-full border-slate-300 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500 placeholder-slate-400 leading-relaxed"
                    placeholder="Tuliskan isi berita secara lengkap di sini...">{{ old('content') }}</textarea>
                @error('content') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Upload Gambar --}}
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Gambar Sampul (Thumbnail)</label>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(event, 'preview-new')"
                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-white file:text-blue-700 hover:file:bg-blue-50 transition cursor-pointer">
                    <p class="mt-2 text-[10px] text-slate-400 italic">Format: JPG, PNG (Maks 2MB).</p>
                    <img id="preview-new" src="#" alt="Preview" class="mt-4 h-32 hidden rounded-lg border shadow-sm object-cover">
                </div>

                {{-- Penjadwalan --}}
                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 space-y-4">
                    <label class="block text-xs font-bold text-blue-700 uppercase mb-1">Pengaturan Publikasi</label>
                    
                    <div class="flex items-center gap-3 py-2">
                        <input type="checkbox" id="publish_now" name="publish_now" value="1" {{ old('publish_now') ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="publish_now" class="text-sm font-bold text-slate-700 cursor-pointer">Publish Sekarang?</label>
                    </div>

                    <div class="border-t border-blue-100 pt-3">
                        <p class="text-[10px] font-bold text-blue-600 uppercase mb-2">Atau Jadwalkan Otomatis:</p>
                        <div class="flex gap-2">
                            <input type="date" name="published_at_date" value="{{ old('published_at_date') }}" 
                                   class="w-1/2 border-slate-300 rounded text-xs focus:ring-blue-500">
                            <input type="time" name="published_at_time" value="{{ old('published_at_time') }}" 
                                   class="w-1/2 border-slate-300 rounded text-xs focus:ring-blue-500">
                        </div>
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