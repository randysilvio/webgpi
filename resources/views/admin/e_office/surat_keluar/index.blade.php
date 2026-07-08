@extends('layouts.app')

@section('title', 'Buku Agenda Surat Keluar')

@section('content')
<div class="space-y-8">

    {{-- HEADER SISTEM E-OFFICE --}}
    <div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between border-b-2 border-gray-800 pb-4">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Buku Agenda Surat Keluar</h2>
            <p class="text-xs text-gray-600 mt-1">Sistem Penerbitan dan Pengarsipan Persuratan Resmi Organisasi.</p>
        </div>
    </div>

    {{-- BAGIAN 1: FORM PENCATATAN SURAT KELUAR --}}
    <div class="bg-white border border-gray-300 p-5 rounded shadow-sm mb-8">
        <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-paper-plane mr-2 text-blue-800"></i> Registrasi Penerbitan Surat Baru</h4>
        
        <form action="{{ route('admin.e-office.surat-keluar.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                
                {{-- Nomor Surat --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomor Registrasi Keluar <span class="text-red-600">*</span></label>
                    <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" required 
                        placeholder="Contoh: 001/A/GPI-P/I/2026" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm font-mono bg-blue-50">
                    @error('nomor_surat') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal Surat --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tanggal Terbit Dokumen <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', date('Y-m-d')) }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    @error('tanggal_surat') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Sifat Surat --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Klasifikasi Sifat Dokumen <span class="text-red-600">*</span></label>
                    <select name="sifat_surat" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="Biasa">Biasa / Terbuka</option>
                        <option value="Penting">Penting</option>
                        <option value="Rahasia">Sangat Rahasia</option>
                        <option value="Segera">Segera / Cito</option>
                    </select>
                </div>

                {{-- Tujuan Surat --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Instansi Tujuan / Penerima <span class="text-red-600">*</span></label>
                    <input type="text" name="tujuan" value="{{ old('tujuan') }}" required 
                        placeholder="Contoh: Majelis Jemaat GPI Papua Fakfak"
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 uppercase">
                    @error('tujuan') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- File Upload --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Unggah Softcopy Final (PDF)</label>
                    <div class="flex items-center">
                        <input type="file" name="file_surat" accept=".pdf" 
                            class="w-full text-[10px] text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-gray-800 file:text-white hover:file:bg-gray-900 cursor-pointer">
                    </div>
                    @error('file_surat') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Perihal --}}
                <div class="md:col-span-4">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Uraian Perihal / Maksud Penerbitan <span class="text-red-600">*</span></label>
                    <textarea name="perihal" rows="2" required 
                        placeholder="Jelaskan secara singkat isi atau maksud penerbitan dokumen persuratan..."
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">{{ old('perihal') }}</textarea>
                    @error('perihal') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tombol Simpan --}}
                <div class="md:col-span-4 flex justify-end pt-4 border-t border-gray-200 mt-2">
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-2.5 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                        <i class="fas fa-save mr-2"></i> Terbitkan & Arsipkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- BAGIAN 2: TABEL DAFTAR ARSIP --}}
    <div class="bg-white border border-gray-300 rounded shadow-sm overflow-hidden border-t-4 border-t-gray-800">
        <div class="bg-gray-50 px-5 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest flex items-center"><i class="fas fa-list-alt mr-2 text-gray-500"></i> Register Arsip Keluar</h3>
            
            {{-- Pencarian --}}
            <form action="{{ route('admin.e-office.surat-keluar.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                <div class="relative flex-grow md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Ref / Tujuan / Perihal..." 
                        class="pl-9 w-full border border-gray-300 rounded text-[10px] uppercase font-bold focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                </div>
                <button type="submit" class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded text-[10px] font-bold uppercase transition flex-shrink-0">
                    Saring
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                        <th class="px-5 py-3 w-1/4">Ref. Nomor & Tgl Terbit</th>
                        <th class="px-5 py-3">Tujuan & Uraian Perihal</th>
                        <th class="px-5 py-3 text-center w-28">Klasifikasi</th>
                        <th class="px-5 py-3 text-center">Berkas Dokumen</th>
                        <th class="px-5 py-3 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($surats as $s)
                    <tr class="hover:bg-gray-50 transition group">
                        <td class="px-5 py-4">
                            <span class="block font-mono font-bold text-gray-900 text-xs mb-1">{{ $s->nomor_surat }}</span>
                            <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest inline-block border border-gray-200 bg-white px-1.5 py-0.5 rounded">
                                {{ \Carbon\Carbon::parse($s->tanggal_surat)->format('d M Y') }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-bold text-blue-800 text-xs mb-0.5 uppercase tracking-wide">{{ $s->tujuan }}</div>
                            <p class="text-xs text-gray-600 line-clamp-2 italic leading-relaxed">"{{ $s->perihal }}"</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @php
                                $badgeColor = match($s->sifat_surat) {
                                    'Rahasia' => 'bg-red-50 text-red-800 border-red-200',
                                    'Penting' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                                    'Segera'  => 'bg-orange-50 text-orange-800 border-orange-200',
                                    default   => 'bg-gray-100 text-gray-600 border-gray-300'
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border {{ $badgeColor }}">
                                {{ $s->sifat_surat }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($s->file_surat)
                                <a href="{{ Storage::url($s->file_surat) }}" target="_blank" class="text-red-700 hover:text-red-900 transition" title="Buka Dokumen">
                                    <i class="fas fa-file-pdf text-lg"></i>
                                </a>
                            @else
                                <span class="text-gray-300 text-[9px] uppercase font-bold italic tracking-widest">N/A</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex justify-center gap-3">
                                @can('manage e-office')
                                <form action="{{ route('admin.e-office.surat-keluar.destroy', $s->id) }}" method="POST" class="inline" onsubmit="return confirm('Perhatian: Pemusnahan data arsip ini bersifat permanen. Lanjutkan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-700 transition text-sm" title="Musnahkan Arsip">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <i class="fas fa-paper-plane text-3xl mb-3 block text-gray-300"></i>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Buku Agenda Kosong</p>
                            <p class="text-gray-400 text-[10px] mt-1">Belum ada dokumen persuratan keluar yang diterbitkan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($surats) && $surats->hasPages())
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                {{ $surats->links() }}
            </div>
        @endif
    </div>
</div>
@endsection