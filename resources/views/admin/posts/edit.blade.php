@extends('layouts.app')

@section('title', 'Edit Berita')

@section('content')
    <x-admin-form 
        title="Ubah Konten: {{ Str::limit($post->title, 40) }}" 
        action="{{ route('admin.posts.update', $post) }}" 
        method="PUT"
        back-route="{{ route('admin.posts.index') }}"
        has-file="true"
    >
        <div class="space-y-6">
            {{-- Judul --}}
            <x-form-input label="Judul Utama" name="title" value="{{ $post->title }}" required />

            {{-- Konten --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Isi Konten Berita <span class="text-red-500">*</span></label>
                <textarea name="content" rows="12" required 
                    class="w-full border-slate-300 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500">{{ old('content', $post->content) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Kelola Gambar --}}
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Ganti Gambar Sampul</label>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(event, 'preview-edit')"
                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-white file:text-blue-700 hover:file:bg-blue-50 transition cursor-pointer">
                    
                    @if($post->image_path)
                        <div class="mt-4 p-2 bg-white rounded-lg border border-slate-200">
                            <p class="text-[10px] text-slate-400 uppercase font-bold mb-2">Gambar Saat Ini:</p>
                            <img src="{{ Storage::url($post->image_path) }}" id="preview-edit" class="h-32 w-full object-cover rounded shadow-sm">
                            <div class="flex items-center mt-2 gap-2">
                                <input type="checkbox" id="remove_image" name="remove_image" value="1" class="rounded text-red-600">
                                <label for="remove_image" class="text-[10px] font-bold text-red-600 uppercase cursor-pointer">Hapus Gambar</label>
                            </div>
                        </div>
                    @else
                        <img id="preview-edit" src="#" class="mt-4 h-32 hidden rounded-lg border shadow-sm object-cover">
                    @endif
                </div>

                {{-- Status & Publish --}}
                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 space-y-4">
                    <label class="block text-xs font-bold text-blue-700 uppercase mb-1">Status Publikasi</label>
                    
                    <div class="flex items-center gap-3 py-2">
                        <input type="checkbox" id="publish_now" name="publish_now" value="1" 
                               {{ ($post->published_at && $post->published_at <= now()) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="publish_now" class="text-sm font-bold text-slate-700 cursor-pointer">Publish Sekarang</label>
                    </div>

                    <div class="border-t border-blue-100 pt-3">
                        <p class="text-[10px] font-bold text-blue-600 uppercase mb-2">Atur Tanggal Publish:</p>
                        <div class="flex gap-2">
                            <input type="date" name="published_at_date" 
                                   value="{{ $post->published_at ? $post->published_at->format('Y-m-d') : '' }}" 
                                   class="w-1/2 border-slate-300 rounded text-xs">
                            <input type="time" name="published_at_time" 
                                   value="{{ $post->published_at ? $post->published_at->format('H:i') : '' }}" 
                                   class="w-1/2 border-slate-300 rounded text-xs">
                        </div>
                        <p class="text-[9px] text-slate-400 mt-2 italic">*Kosongkan jika ingin menjadikannya Draft.</p>
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