@extends('admin.layout')

@section('title', 'Risalah Persidangan')
@section('header-title', 'Keputusan: Risalah Sidang')

@section('content')
<div class="space-y-6">
    {{-- Form Input Risalah --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-xs font-black text-gray-800 mb-6 uppercase tracking-widest border-b pb-2">
            <i class="fas fa-gavel mr-2 text-primary"></i> Arsipkan Hasil Sidang
        </h3>
        <form action="{{ route('admin.tata-gereja.sidang.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Judul / Nama Persidangan</label>
                <input type="text" name="judul_sidang" value="{{ old('judul_sidang') }}" required placeholder="Contoh: Sidang Jemaat Ke-XXIII" class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Tanggal</label>
                <input type="date" name="tanggal_sidang" value="{{ old('tanggal_sidang') }}" required class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Tingkat</label>
                <select name="tingkat_sidang" class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="Jemaat">Jemaat</option>
                    <option value="Klasis">Klasis</option>
                    <option value="Sinode">Sinode</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">File Keputusan (PDF)</label>
                <input type="file" name="file_risalah" class="w-full text-xs text-gray-400">
            </div>
            <div class="md:col-span-3">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Ringkasan Keputusan</label>
                <textarea name="ringkasan_keputusan" rows="3" class="w-full border-gray-300 rounded-lg text-sm">{{ old('ringkasan_keputusan') }}</textarea>
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="bg-primary text-white px-8 py-2.5 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-blue-800 transition shadow-lg">
                    Simpan Arsip
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel Arsip --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-500 border-b">
                <tr>
                    <th class="px-6 py-4">Informasi Sidang</th>
                    <th class="px-6 py-4">Tingkat</th>
                    <th class="px-6 py-4 text-center">Dokumen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sidangs as $s)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="block font-bold text-gray-900 uppercase text-xs">{{ $s->judul_sidang }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">{{ \Carbon\Carbon::parse($s->tanggal_sidang)->isoFormat('D MMMM Y') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-md text-[10px] font-black">{{ $s->tingkat_sidang }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($s->file_risalah)
                            <a href="{{ Storage::url($s->file_risalah) }}" target="_blank" class="text-primary hover:text-blue-800"><i class="fas fa-file-pdf fa-lg"></i></a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 text-xs font-bold uppercase">Belum ada risalah sidang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection