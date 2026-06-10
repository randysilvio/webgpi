@extends('layouts.app')

@section('title', 'Arsip Surat Keluar')

@section('content')
<div class="space-y-8">

    {{-- BAGIAN 1: FORM PENCATATAN SURAT KELUAR (CARD ATAS) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center">
            <i class="fas fa-paper-plane mr-3 text-primary"></i>
            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest">Registrasi Surat Keluar Baru</h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.e-office.surat-keluar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    
                    {{-- Nomor Surat --}}
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Nomor Surat <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" required 
                            placeholder="Contoh: 001/A/GPI-P/I/2026" 
                            class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                        @error('nomor_surat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tanggal Surat --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Tanggal Surat <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', date('Y-m-d')) }}" required 
                            class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-600 shadow-sm">
                        @error('tanggal_surat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Sifat Surat --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Sifat Surat <span class="text-red-500">*</span></label>
                        <select name="sifat_surat" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-600 shadow-sm">
                            <option value="Biasa">Biasa</option>
                            <option value="Penting">Penting</option>
                            <option value="Rahasia">Rahasia</option>
                            <option value="Segera">Segera</option>
                        </select>
                    </div>

                    {{-- Tujuan Surat --}}
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Tujuan / Penerima <span class="text-red-500">*</span></label>
                        <input type="text" name="tujuan" value="{{ old('tujuan') }}" required 
                            placeholder="Contoh: Majelis Jemaat GPI Papua Fakfak"
                            class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">
                        @error('tujuan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- File Upload --}}
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Unggah Dokumen (PDF)</label>
                        <input type="file" name="file_surat" accept=".pdf" 
                            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        @error('file_surat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Perihal --}}
                    <div class="md:col-span-4">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Perihal / Ringkasan Isi <span class="text-red-500">*</span></label>
                        <textarea name="perihal" rows="2" required 
                            placeholder="Jelaskan secara singkat isi atau maksud surat..."
                            class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 shadow-sm">{{ old('perihal') }}</textarea>
                        @error('perihal') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="md:col-span-4 flex justify-end pt-2 border-t border-slate-100">
                        <button type="submit" class="bg-slate-800 text-white px-8 py-2.5 rounded shadow-md text-xs font-bold uppercase tracking-wider hover:bg-slate-900 transition flex items-center">
                            <i class="fas fa-save mr-2"></i> Simpan Arsip
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- BAGIAN 2: TABEL DAFTAR ARSIP --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-white px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-sm font-bold text-slate-700">Buku Agenda Surat Keluar</h3>
            
            {{-- Pencarian --}}
            <form action="{{ route('admin.e-office.surat-keluar.index') }}" method="GET" class="flex gap-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor/tujuan/perihal..." 
                        class="border-slate-300 rounded text-xs focus:ring-slate-500 w-64 pl-8">
                    <i class="fas fa-search absolute left-2.5 top-2 text-slate-400"></i>
                </div>
                <button type="submit" class="bg-slate-100 text-slate-600 px-4 py-1.5 rounded border border-slate-200 hover:bg-slate-200 transition text-xs font-bold uppercase">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 w-40">Nomor & Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Tujuan & Perihal</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 text-center">Sifat</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 text-center">Berkas</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($surats as $s)
                    <tr class="hover:bg-slate-50 transition duration-150 group">
                        <td class="px-6 py-4">
                            <span class="block font-bold text-primary text-xs mb-1">{{ $s->nomor_surat }}</span>
                            <div class="flex items-center text-[10px] text-slate-500 font-medium uppercase tracking-tight">
                                <i class="far fa-calendar-alt mr-1.5"></i>
                                {{ \Carbon\Carbon::parse($s->tanggal_surat)->isoFormat('D MMM Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800 text-sm mb-0.5 line-clamp-1">{{ $s->tujuan }}</div>
                            <p class="text-xs text-slate-500 line-clamp-2 italic leading-relaxed">"{{ $s->perihal }}"</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $badgeColor = match($s->sifat_surat) {
                                    'Rahasia' => 'bg-red-100 text-red-700 border-red-200',
                                    'Penting' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    'Segera'  => 'bg-orange-100 text-orange-700 border-orange-200',
                                    default   => 'bg-slate-100 text-slate-600 border-slate-200'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded text-[9px] font-black uppercase border {{ $badgeColor }}">
                                {{ $s->sifat_surat }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($s->file_surat)
                                <a href="{{ Storage::url($s->file_surat) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded bg-blue-50 text-blue-600 border border-blue-100 hover:bg-blue-100 transition shadow-sm" title="Buka Dokumen">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            @else
                                <span class="text-slate-300 text-[10px] italic font-medium">Kosong</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                {{-- Edit --}}
                                <a href="{{ route('admin.e-office.surat-keluar.edit', $s->id) }}" class="text-slate-400 hover:text-yellow-600 transition" title="Edit Arsip">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Hapus --}}
                                <form action="{{ route('admin.e-office.surat-keluar.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip surat ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Hapus Data">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-300">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                    <i class="fas fa-envelope-open text-2xl"></i>
                                </div>
                                <p class="text-slate-500 text-sm font-bold uppercase tracking-widest">Belum Ada Arsip Surat Keluar</p>
                                <p class="text-slate-400 text-xs mt-1">Gunakan formulir di atas untuk mencatat surat baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($surats->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $surats->links() }}
            </div>
        @endif
    </div>
</div>
@endsection