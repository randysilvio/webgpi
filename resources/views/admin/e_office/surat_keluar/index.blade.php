@extends('admin.layout')

@section('title', 'Arsip Surat Keluar')
@section('header-title', 'E-Office: Surat Keluar')

@section('content')
<div class="space-y-6">
    
    {{-- 1. Blok Pesan Error & Validasi --}}
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 shadow-md rounded-lg" role="alert">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <p class="font-bold text-sm">Gagal Mengarsipkan Surat:</p>
            </div>
            <ul class="text-xs list-disc ml-8">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 2. Form Registrasi Surat Keluar --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center mb-6 border-b pb-3">
            <i class="fas fa-paper-plane mr-3 text-primary text-xl"></i>
            <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">
                Registrasi Surat Keluar Baru
            </h3>
        </div>

        <form action="{{ route('admin.e-office.surat-keluar.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @csrf
            
            {{-- Nomor Surat --}}
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1 tracking-wider">Nomor Surat</label>
                <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" required 
                       placeholder="Contoh: 123/GPI-P/KLS-Y/XII/2025"
                       class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary shadow-sm">
            </div>

            {{-- Tanggal Surat --}}
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Tanggal Keluar</label>
                <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat') }}" required 
                       class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary shadow-sm">
            </div>

            {{-- Klasifikasi --}}
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Sifat Surat</label>
                <select name="sifat_surat" class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="Biasa">Biasa</option>
                    <option value="Penting">Penting</option>
                    <option value="Rahasia">Rahasia</option>
                    <option value="Segera">Segera</option>
                </select>
            </div>

            {{-- Tujuan Surat --}}
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Tujuan / Penerima</label>
                <input type="text" name="tujuan" value="{{ old('tujuan') }}" required 
                       placeholder="Contoh: Ketua Klasis / Jemaat ..."
                       class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary shadow-sm">
            </div>

            {{-- Lampiran PDF --}}
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Unggah Dokumen (PDF)</label>
                <input type="file" name="file_surat" accept="application/pdf"
                       class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-primary hover:file:bg-blue-100">
            </div>

            {{-- Perihal --}}
            <div class="md:col-span-4">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Perihal / Ringkasan Isi</label>
                <textarea name="perihal" rows="3" required placeholder="Jelaskan secara singkat isi surat..."
                          class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary shadow-sm">{{ old('perihal') }}</textarea>
            </div>

            {{-- Tombol Simpan --}}
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="bg-primary text-white px-10 py-3 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-blue-800 transition shadow-lg flex items-center">
                    <i class="fas fa-save mr-2"></i> Arsipkan Surat Keluar
                </button>
            </div>
        </form>
    </div>

    {{-- 3. Tabel Arsip Surat Keluar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Buku Agenda Surat Keluar</h4>
            <span class="text-[10px] bg-blue-100 text-primary px-2 py-1 rounded-md font-bold">Total: {{ $surats->total() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-500 border-b">
                    <tr>
                        <th class="px-6 py-4">No. Surat & Tgl</th>
                        <th class="px-6 py-4">Tujuan</th>
                        <th class="px-6 py-4">Perihal</th>
                        <th class="px-6 py-4 text-center">Dokumen</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($surats as $s)
                    <tr class="hover:bg-gray-50 transition-colors italic">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="block font-black text-primary text-xs">{{ $s->nomor_surat }}</span>
                            <span class="text-[10px] text-gray-400 font-bold uppercase">{{ \Carbon\Carbon::parse($s->tanggal_surat)->isoFormat('D MMM Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-800 not-italic uppercase text-xs">{{ $s->tujuan }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-500 line-clamp-2 max-w-xs">{{ $s->perihal }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($s->file_surat)
                                <a href="{{ Storage::url($s->file_surat) }}" target="_blank" class="text-red-500 hover:text-red-700 transition" title="Lihat PDF">
                                    <i class="fas fa-file-pdf fa-2x"></i>
                                </a>
                            @else
                                <span class="text-gray-300 text-[10px] font-bold uppercase tracking-widest">No File</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('admin.e-office.surat-keluar.edit', $s->id) }}" class="p-2 bg-gray-100 text-gray-400 hover:text-primary rounded-lg transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.e-office.surat-keluar.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus arsip surat ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-gray-100 text-gray-400 hover:text-red-500 rounded-lg transition">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-300">
                                <i class="fas fa-envelope-open fa-3x mb-4"></i>
                                <span class="uppercase tracking-widest text-xs font-bold">Belum Ada Surat Keluar Tercatat</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($surats->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $surats->links() }}
        </div>
        @endif
    </div>
</div>
@endsection