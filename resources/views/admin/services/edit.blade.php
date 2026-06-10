@extends('layouts.app')

@section('title', 'Edit Layanan')

@section('content')
    <x-admin-form 
        title="Edit Layanan: {{ $service->title }}" 
        action="{{ route('admin.services.update', $service) }}" 
        method="PUT"
        back-route="{{ route('admin.services.index') }}"
    >
        <div class="space-y-6">
            
            {{-- Judul --}}
            <x-form-input label="Judul Layanan" name="title" value="{{ $service->title }}" required />

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Deskripsi Singkat</label>
                <textarea name="description" rows="3" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">{{ old('description', $service->description) }}</textarea>
            </div>

            {{-- List Poin --}}
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Daftar Poin (List Items)</label>
                <textarea name="list_items" rows="4" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">{{ old('list_items', $service->list_items) }}</textarea>
                <p class="mt-1 text-[10px] text-slate-400">Tekan Enter untuk baris baru.</p>
            </div>

            {{-- Grid untuk Opsi --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-slate-50 p-4 rounded border border-slate-100">
                
                {{-- Ikon --}}
                <x-form-select label="Ikon" name="icon">
                    <option value="">-- Pilih Ikon --</option>
                    @foreach ($icons as $icon)
                        <option value="{{ $icon }}" {{ old('icon', $service->icon) == $icon ? 'selected' : '' }}>{{ ucfirst($icon) }}</option>
                    @endforeach
                </x-form-select>

                {{-- Tema Warna --}}
                <x-form-select label="Tema Warna" name="color_theme" required>
                    @foreach ($themes as $theme)
                        <option value="{{ $theme }}" {{ old('color_theme', $service->color_theme) == $theme ? 'selected' : '' }}>{{ ucfirst($theme) }}</option>
                    @endforeach
                </x-form-select>

                {{-- Urutan --}}
                <x-form-input type="number" label="Urutan Tampil" name="order" value="{{ $service->order }}" required />
            </div>

        </div>
    </x-admin-form>
@endsection