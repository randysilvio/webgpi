@extends('layouts.app')

@section('title', 'Arsip Surat Masuk')

@section('content')
<div class="space-y-8">

    {{-- BAGIAN 1: FORM PENCATATAN SURAT (CARD ATAS) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center">
            <i class="fas fa-envelope-open-text mr-3 text-primary"></i>
            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest">Registrasi Surat Masuk Baru</h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.e-office.surat-masuk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    
                    {{-- Nomor Surat --}}
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Nomor Surat Asal <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" required 
                            placeholder="Contoh: 123/ORG/II/2026" 
                            class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                        @error('nomor_surat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tanggal Terima --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Tanggal Terima <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_terima" value="{{ old('tanggal_terima', date('Y-m-d')) }}" required 
                            class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-600">
                        @error('tanggal_terima') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Asal Surat --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Asal Instansi / Pengirim <span class="text-red-500">*</span></label>
                        <input type="text" name="asal_surat" value="{{ old('asal_surat') }}" required 
                            placeholder="Contoh: Klasis GPI Papua Fakfak"
                            class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                        @error('asal_surat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Perihal --}}
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Perihal / Ringkasan <span class="text-red-500">*</span></label>
                        <input type="text" name="perihal" value="{{ old('perihal') }}" required 
                            placeholder="Isi singkat perihal surat..."
                            class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                        @error('perihal') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- File Upload --}}
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Scan Dokumen (PDF)</label>
                        <input type="file" name="file_surat" accept=".pdf" 
                            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        @error('file_surat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="md:col-span-4 flex justify-end pt-2 border-t border-slate-100">
                        <button type="submit" class="bg-slate-800 text-white px-8 py-2.5 rounded shadow-md text-xs font-bold uppercase tracking-wider hover:bg-slate-900 transition flex items-center">
                            <i class="fas fa-save mr-2"></i> Arsipkan Surat
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- BAGIAN 2: TABEL DATA (MENGGUNAKAN KONSEP ADMIN-INDEX) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-white px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-sm font-bold text-slate-700">Daftar Arsip Surat Masuk</h3>
            
            {{-- Search & Filter Simple --}}
            <form action="{{ route('admin.e-office.surat-masuk.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor/asal/perihal..." 
                    class="border-slate-300 rounded text-xs focus:ring-slate-500 w-64">
                <button type="submit" class="bg-slate-100 text-slate-600 px-3 py-1 rounded border border-slate-200 hover:bg-slate-200 transition">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 w-1/3">No. Surat & Pengirim</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Perihal</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 text-center">Tgl Terima</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 text-center">Dokumen</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($surats as $s)
                    <tr class="hover:bg-slate-50 transition duration-150 group">
                        <td class="px-6 py-4">
                            <span class="block font-bold text-primary text-sm mb-0.5">{{ $s->nomor_surat }}</span>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-tight">{{ $s->asal_surat }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-slate-600 line-clamp-2">{{ $s->perihal }}</p>
                            @if($s->status_disposisi)
                                <span class="inline-block mt-2 px-2 py-0.5 bg-green-50 text-green-600 border border-green-100 rounded text-[9px] font-bold uppercase">Sudah Disposisi</span>
                            @else
                                <span class="inline-block mt-2 px-2 py-0.5 bg-slate-100 text-slate-400 border border-slate-200 rounded text-[9px] font-bold uppercase">Belum Disposisi</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-xs font-medium text-slate-600">
                            {{ \Carbon\Carbon::parse($s->tanggal_terima)->isoFormat('D MMM Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($s->file_surat)
                                <a href="{{ Storage::url($s->file_surat) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded bg-red-50 text-red-500 border border-red-100 hover:bg-red-100 transition shadow-sm" title="Buka PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            @else
                                <span class="text-slate-300 text-[10px] italic">No File</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                {{-- Tombol Disposisi --}}
                                <button class="text-slate-400 hover:text-blue-600 transition" title="Disposisi Surat">
                                    <i class="fas fa-share-square"></i>
                                </button>
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.e-office.surat-masuk.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip surat masuk ini?');">
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
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-3">
                                    <i class="fas fa-envelope-open text-xl"></i>
                                </div>
                                <p class="text-slate-500 text-sm font-medium">Belum ada arsip surat masuk yang dicatat.</p>
                                <p class="text-slate-400 text-xs">Silakan gunakan formulir di atas untuk mendaftarkan surat.</p>
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