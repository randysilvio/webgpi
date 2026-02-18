@extends('layouts.app')

@section('title', 'Risalah Persidangan')

@section('content')
<div class="space-y-8">

    {{-- BAGIAN 1: FORM INPUT RISALAH (CARD ATAS) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest">
                <i class="fas fa-gavel mr-2 text-primary"></i> Arsipkan Hasil Sidang Baru
            </h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.tata-gereja.sidang.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    {{-- Judul Sidang --}}
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Judul / Nama Persidangan <span class="text-red-500">*</span></label>
                        <input type="text" name="judul_sidang" value="{{ old('judul_sidang') }}" required 
                            placeholder="Contoh: Sidang Jemaat Ke-XXIII Tahun 2026" 
                            class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 placeholder-slate-400">
                        @error('judul_sidang') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Tanggal Pelaksanaan <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_sidang" value="{{ old('tanggal_sidang') }}" required 
                            class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-600">
                        @error('tanggal_sidang') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tingkat --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Tingkat Persidangan <span class="text-red-500">*</span></label>
                        <select name="tingkat_sidang" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-600">
                            <option value="Jemaat">Jemaat</option>
                            <option value="Klasis">Klasis</option>
                            <option value="Sinode">Sinode</option>
                        </select>
                    </div>

                    {{-- File Upload --}}
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">File Dokumen Keputusan (PDF)</label>
                        <input type="file" name="file_risalah" accept=".pdf" 
                            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        <p class="text-[10px] text-slate-400 mt-1">* Maksimal ukuran file 5MB.</p>
                        @error('file_risalah') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Ringkasan --}}
                    <div class="md:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Ringkasan / Poin Penting Keputusan</label>
                        <textarea name="ringkasan_keputusan" rows="3" 
                            class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 placeholder-slate-400">{{ old('ringkasan_keputusan') }}</textarea>
                        @error('ringkasan_keputusan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="md:col-span-3 flex justify-end pt-2 border-t border-slate-100">
                        <button type="submit" class="bg-slate-800 text-white px-6 py-2.5 rounded shadow-md text-xs font-bold uppercase tracking-wider hover:bg-slate-900 transition flex items-center">
                            <i class="fas fa-save mr-2"></i> Simpan Arsip
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- BAGIAN 2: TABEL DATA (CARD BAWAH) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-white px-6 py-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700">Daftar Arsip Persidangan</h3>
        </div>

        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Detail Persidangan</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Tingkat</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 text-center">Dokumen</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sidangs as $s)
                <tr class="hover:bg-slate-50 transition duration-150">
                    <td class="px-6 py-4 align-top">
                        <span class="block font-bold text-slate-800 text-sm mb-1">{{ $s->judul_sidang }}</span>
                        <div class="flex items-center text-xs text-slate-500">
                            <i class="far fa-calendar-alt mr-1.5"></i>
                            {{ \Carbon\Carbon::parse($s->tanggal_sidang)->isoFormat('D MMMM Y') }}
                        </div>
                        @if($s->ringkasan_keputusan)
                            <p class="text-xs text-slate-400 mt-2 line-clamp-2 italic">"{{ Str::limit($s->ringkasan_keputusan, 100) }}"</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 align-top">
                        @php
                            $badgeColor = match($s->tingkat_sidang) {
                                'Sinode' => 'bg-purple-100 text-purple-700 border-purple-200',
                                'Klasis' => 'bg-blue-100 text-blue-700 border-blue-200',
                                default => 'bg-yellow-100 text-yellow-700 border-yellow-200'
                            };
                        @endphp
                        <span class="px-2.5 py-1 border rounded text-[10px] font-bold uppercase {{ $badgeColor }}">
                            {{ $s->tingkat_sidang }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center align-top">
                        @if($s->file_risalah)
                            <a href="{{ Storage::url($s->file_risalah) }}" target="_blank" class="inline-flex flex-col items-center group">
                                <div class="w-8 h-8 rounded bg-red-50 text-red-500 flex items-center justify-center border border-red-100 group-hover:bg-red-100 transition">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <span class="text-[9px] text-red-500 font-bold mt-1 group-hover:underline">Unduh</span>
                            </a>
                        @else
                            <span class="text-slate-300 text-xs italic">- Tidak ada file -</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center align-top">
                        {{-- Tombol Hapus --}}
                        <form action="{{ route('admin.tata-gereja.sidang.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip sidang ini? File yang terlampir juga akan dihapus.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Hapus Data">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-3">
                                <i class="fas fa-folder-open text-xl"></i>
                            </div>
                            <p class="text-slate-500 text-sm font-medium">Belum ada arsip sidang yang dicatat.</p>
                            <p class="text-slate-400 text-xs">Silakan isi formulir di atas untuk menambahkan data.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($sidangs->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $sidangs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection