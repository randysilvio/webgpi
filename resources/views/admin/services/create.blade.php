@extends('admin.layout')

@section('title', 'Tambah Layanan Baru')
@section('header-title', 'Tambah Layanan Baru')

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

    {{-- FORM TAMBAH LAYANAN --}}
    <form action="{{ route('admin.services.store') }}" method="POST">
        @csrf

        <div class="bg-white shadow rounded-lg p-6 border border-gray-200 space-y-6">

            {{-- Judul Layanan --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Layanan <span class="text-red-600">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm @error('title') border-red-500 @enderror">
                 @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi Singkat --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm @error('description') border-red-500 @enderror"
                >{{ old('description') }}</textarea>
                 @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

             {{-- Daftar Poin (List Items) --}}
            <div>
                <label for="list_items" class="block text-sm font-medium text-gray-700 mb-1">Daftar Poin (satu per baris)</label>
                <textarea id="list_items" name="list_items" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm @error('list_items') border-red-500 @enderror"
                          placeholder="• Poin pertama
• Poin kedua
• Poin ketiga"
                >{{ old('list_items') }}</textarea>
                 <p class="mt-1 text-xs text-gray-500">Gunakan tanda • (opsional) dan tekan Enter untuk setiap poin baru.</p>
                 @error('list_items') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Ikon --}}
                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Ikon</label>
                    <select id="icon" name="icon" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm bg-white @error('icon') border-red-500 @enderror">
                        <option value="">-- Pilih Ikon --</option>
                        @foreach ($icons as $icon)
                            <option value="{{ $icon }}" {{ old('icon') == $icon ? 'selected' : '' }}>{{ ucfirst($icon) }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Opsional. Pilih ikon yang relevan.</p>
                     @error('icon') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Tema Warna --}}
                <div>
                    <label for="color_theme" class="block text-sm font-medium text-gray-700 mb-1">Tema Warna <span class="text-red-600">*</span></label>
                    <select id="color_theme" name="color_theme" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm bg-white @error('color_theme') border-red-500 @enderror">
                        @foreach ($themes as $theme)
                            <option value="{{ $theme }}" {{ old('color_theme', 'blue') == $theme ? 'selected' : '' }} class="capitalize">{{ ucfirst($theme) }}</option>
                        @endforeach
                    </select>
                     @error('color_theme') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                 {{-- Urutan Tampil --}}
                 <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil <span class="text-red-600">*</span></label>
                    <input type="number" id="order" name="order" value="{{ old('order', 0) }}" min="0" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary shadow-sm text-sm @error('order') border-red-500 @enderror">
                     <p class="mt-1 text-xs text-gray-500">Angka lebih kecil tampil lebih dulu.</p>
                     @error('order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

        </div>

        {{-- Tombol Aksi --}}
         <div class="mt-8 flex justify-end space-x-3">
             <a href="{{ route('admin.services.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                 Batal
             </a>
            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Simpan Layanan
            </button>
        </div>

    </form>
@endsection