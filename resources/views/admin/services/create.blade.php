@extends('layouts.app')

@section('title', 'Tambah Layanan')

@section('content')
    <x-admin-form 
        title="Tambah Layanan Baru" 
        action="{{ route('admin.services.store') }}" 
        back-route="{{ route('admin.services.index') }}"
    >
        <div class="space-y-6">
            
            {{-- Judul --}}
            <x-form-input label="Judul Layanan" name="title" required placeholder="Contoh: Diakonia & Sosial" />

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Deskripsi Singkat</label>
                <textarea name="description" rows="3" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 placeholder-slate-400">{{ old('description') }}</textarea>
            </div>

            {{-- List Poin --}}
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Daftar Poin (List Items)</label>
                <textarea name="list_items" rows="4" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 placeholder-slate-400" 
                    placeholder="• Bantuan Bencana&#10;• Pelayanan Kesehatan&#10;• Beasiswa Pendidikan">{{ old('list_items') }}</textarea>
                <p class="mt-1 text-[10px] text-slate-400">Tekan <strong>Enter</strong> untuk membuat baris baru. Gunakan simbol • untuk mempercantik.</p>
            </div>

            {{-- Grid untuk Opsi --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-slate-50 p-4 rounded border border-slate-100">
                
                {{-- Ikon --}}
                <x-form-select label="Ikon" name="icon">
                    <option value="">-- Pilih Ikon --</option>
                    @foreach ($icons as $icon)
                        <option value="{{ $icon }}" {{ old('icon') == $icon ? 'selected' : '' }}>{{ ucfirst($icon) }}</option>
                    @endforeach
                </x-form-select>

                {{-- Tema Warna --}}
                <x-form-select label="Tema Warna" name="color_theme" required>
                    @foreach ($themes as $theme)
                        <option value="{{ $theme }}" {{ old('color_theme', 'blue') == $theme ? 'selected' : '' }}>{{ ucfirst($theme) }}</option>
                    @endforeach
                </x-form-select>

                {{-- Urutan --}}
                <x-form-input type="number" label="Urutan Tampil" name="order" value="{{ old('order', 0) }}" required />
            </div>

        </div>
    </x-admin-form>
@endsection