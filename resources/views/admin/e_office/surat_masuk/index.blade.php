@extends('layouts.app')

@section('title', 'Buku Agenda Surat Masuk')

@section('content')
<div class="space-y-6">

    {{-- HEADER SISTEM E-OFFICE --}}
    <div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between border-b-2 border-gray-800 pb-4">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Buku Agenda Surat Masuk</h2>
            <p class="text-xs text-gray-600 mt-1">Sistem Pendaftaran dan Disposisi Dokumen Persuratan Eksternal.</p>
        </div>
    </div>

    {{-- BAGIAN 1: FORM PENCATATAN SURAT --}}
    <div class="bg-white border border-gray-300 p-5 rounded shadow-sm mb-8">
        <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-file-import mr-2 text-blue-800"></i> Registrasi Surat Masuk Baru</h4>
        
        <form action="{{ route('admin.e-office.surat-masuk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                
                {{-- Nomor Surat --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomor Registrasi Surat / Ref. <span class="text-red-600">*</span></label>
                    <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" required 
                        placeholder="Contoh: 123/ORG/II/2026" 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm font-mono bg-blue-50">
                    @error('nomor_surat') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal Terima --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tanggal Terima Dokumen <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal_terima" value="{{ old('tanggal_terima', date('Y-m-d')) }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    @error('tanggal_terima') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Asal Surat --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Instansi / Organisasi Pengirim <span class="text-red-600">*</span></label>
                    <input type="text" name="asal_surat" value="{{ old('asal_surat') }}" required 
                        placeholder="Contoh: Klasis GPI Papua Fakfak"
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 uppercase">
                    @error('asal_surat') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Perihal --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Uraian Perihal / Ringkasan Isi <span class="text-red-600">*</span></label>
                    <input type="text" name="perihal" value="{{ old('perihal') }}" required 
                        placeholder="Isi singkat perihal surat..."
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                    @error('perihal') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- File Upload --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Pindaian Dokumen Resmi (PDF)</label>
                    <div class="flex items-center">
                        <input type="file" name="file_surat" accept=".pdf" 
                            class="w-full text-[10px] text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-gray-800 file:text-white hover:file:bg-gray-900 cursor-pointer">
                    </div>
                    @error('file_surat') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tombol Simpan --}}
                <div class="md:col-span-4 flex justify-end pt-4 border-t border-gray-200 mt-2">
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-2.5 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                        <i class="fas fa-save mr-2"></i> Arsipkan Dokumen
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- BAGIAN 2: TABEL DATA --}}
    <div class="bg-white border border-gray-300 rounded shadow-sm overflow-hidden border-t-4 border-t-gray-800">
        <div class="bg-gray-50 px-5 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest flex items-center"><i class="fas fa-list-alt mr-2 text-gray-500"></i> Register Arsip Masuk</h3>
            
            {{-- Search & Filter Simple --}}
            <form action="{{ route('admin.e-office.surat-masuk.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                <div class="relative flex-grow md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Ref / Asal / Perihal..." 
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
                        <th class="px-5 py-3 w-1/4">Ref. Surat & Instansi</th>
                        <th class="px-5 py-3">Uraian Perihal</th>
                        <th class="px-5 py-3 text-center">Tanggal Terima</th>
                        <th class="px-5 py-3 text-center">Berkas</th>
                        <th class="px-5 py-3 text-center">Status & Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($surats as $s)
                    <tr class="hover:bg-gray-50 transition group">
                        <td class="px-5 py-4">
                            <span class="block font-mono font-bold text-gray-900 text-xs mb-1">{{ $s->nomor_surat }}</span>
                            <span class="text-[9px] font-bold text-blue-800 uppercase tracking-widest bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 inline-block">{{ $s->asal_surat }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-700 font-medium leading-relaxed">{{ $s->perihal }}</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-[10px] font-mono font-bold text-gray-600">
                                {{ \Carbon\Carbon::parse($s->tanggal_terima)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($s->file_surat)
                                <a href="{{ Storage::url($s->file_surat) }}" target="_blank" class="text-red-700 hover:text-red-900 transition" title="Buka Pindaian Dokumen">
                                    <i class="fas fa-file-pdf text-lg"></i>
                                </a>
                            @else
                                <span class="text-gray-300 text-[9px] uppercase font-bold italic tracking-widest">N/A</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                @if($s->status_disposisi)
                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 border border-green-300 rounded text-[9px] font-bold uppercase tracking-widest"><i class="fas fa-check-circle mr-1"></i> Didisposisi</span>
                                @else
                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 border border-yellow-300 rounded text-[9px] font-bold uppercase tracking-widest"><i class="fas fa-clock mr-1"></i> Menunggu</span>
                                @endif

                                <div class="flex gap-3 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button class="text-gray-400 hover:text-blue-800 transition text-xs" title="Proses Disposisi Surat">
                                        <i class="fas fa-share"></i>
                                    </button>
                                    @hasanyrole('Super Admin|Admin Bidang 4')
                                    <form action="{{ route('admin.e-office.surat-masuk.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Perhatian: Pemusnahan data arsip ini bersifat permanen. Lanjutkan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-700 transition text-xs" title="Musnahkan Arsip">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endhasanyrole
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <i class="fas fa-inbox text-3xl mb-3 block text-gray-300"></i>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Buku Agenda Kosong</p>
                            <p class="text-gray-400 text-[10px] mt-1">Gunakan formulir di atas untuk meregistrasi persuratan masuk.</p>
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