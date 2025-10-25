@extends('admin.layout')

@section('title', 'Edit Berita/Kegiatan')
@section('header-title', 'Edit Berita/Kegiatan')

@section('content')

    {{-- Display Validation Errors --}}
     @if ($errors->any())
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            <p class="font-bold">Oops! Ada kesalahan:</p>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM EDIT POST --}}
    <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white shadow rounded-lg p-6 border border-gray-200 space-y-6">

            {{-- Judul --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-600">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm @error('title') border-red-500 @enderror">
                 @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Konten --}}
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Konten <span class="text-red-600">*</span></label>
                <textarea id="content" name="content" rows="10" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm @error('content') border-red-500 @enderror"
                >{{ old('content', $post->content) }}</textarea>
                 @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Gambar Utama --}}
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar Utama</label>
                <input type="file" id="image" name="image" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 @error('image') border-red-500 @enderror"
                       onchange="previewImage(event, 'image-preview')">
                 <p class="mt-1 text-xs text-gray-500">Opsional. JPG, PNG maks 2MB. Kosongkan jika tidak ingin mengubah gambar.</p>
                 @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                 
                 {{-- Tampilkan Gambar Saat Ini --}}
                 @if($post->image_path && Storage::disk('public')->exists($post->image_path))
                    <div class="mt-2">
                        <p class="text-xs text-gray-600 mb-1">Gambar saat ini:</p>
                        <img src="{{ Storage::url($post->image_path) }}" alt="Gambar {{ $post->title }}" class="image-preview !max-h-40">
                        {{-- Checkbox Hapus Gambar --}}
                        <div class="flex items-center mt-2">
                            <input type="checkbox" id="remove_image" name="remove_image" value="1" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <label for="remove_image" class="ml-2 block text-sm font-medium text-red-700">Hapus gambar saat ini</label>
                        </div>
                    </div>
                 @endif
                 
                 {{-- Image Preview untuk gambar baru --}}
                <img id="image-preview" src="#" alt="Preview Gambar Baru" class="image-preview hidden">
            </div>

             {{-- Checkbox Publish Sekarang --}}
            <div class="flex items-center">
                 {{-- Cek apakah post sudah dipublish DAN tanggalnya sudah lewat --}}
                <input type="checkbox" id="publish_now" name="publish_now" value="1"
                       {{ old('publish_now', ($post->published_at && $post->published_at <= now()) ? 'checked' : '') }}
                       class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                <label for="publish_now" class="ml-2 block text-sm font-medium text-gray-700">Publish Sekarang?</label>
            </div>

            {{-- Tanggal & Waktu Publish --}}
            <div>
                <label for="published_at_date" class="block text-sm font-medium text-gray-700 mb-1">Jadwalkan Publish (Opsional)</label>
                <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
                    {{-- Format tanggal untuk input date --}}
                    <input type="date" id="published_at_date" name="published_at_date" 
                           value="{{ old('published_at_date', $post->published_at ? $post->published_at->format('Y-m-d') : '') }}"
                           class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm @error('published_at_date') border-red-500 @enderror">
                     {{-- Format waktu untuk input time --}}
                    <input type="time" id="published_at_time" name="published_at_time" 
                           value="{{ old('published_at_time', $post->published_at ? $post->published_at->format('H:i') : '') }}"
                           class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm @error('published_at_time') border-red-500 @enderror">
                </div>
                <p class="mt-1 text-xs text-gray-500">Ubah untuk menjadwalkan ulang. Kosongkan tanggal/waktu untuk menjadikannya Draft (jika "Publish Sekarang" tidak dicentang).</p>
                 @error('published_at_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                 @error('published_at_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

        </div>

        {{-- Tombol Aksi --}}
         <div class="mt-8 flex justify-end space-x-3">
             <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                 Batal
             </a>
            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Update
            </button>
        </div>

    </form>
@endsection