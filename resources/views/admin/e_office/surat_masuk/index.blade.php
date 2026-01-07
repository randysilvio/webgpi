@extends('admin.layout')

@section('title', 'Arsip Surat Masuk')
@section('header-title', 'E-Office: Surat Masuk')

@section('content')
<div class="space-y-6">
    {{-- Penanganan Error --}}
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 shadow-md rounded-lg" role="alert">
            <p class="font-bold text-sm">Gagal Mengarsipkan Surat Masuk:</p>
            <ul class="text-xs list-disc ml-8">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center mb-6 border-b pb-3">
            <i class="fas fa-envelope-open-text mr-3 text-primary text-xl"></i>
            <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Pencatatan Surat Masuk</h3>
        </div>

        <form action="{{ route('admin.e-office.surat-masuk.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Nomor Surat Asal</label>
                <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" required class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Tanggal Terima</label>
                <input type="date" name="tanggal_terima" value="{{ old('tanggal_terima') }}" required class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Asal Instansi/Pengirim</label>
                <input type="text" name="asal_surat" value="{{ old('asal_surat') }}" required class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Ringkasan Perihal</label>
                <input type="text" name="perihal" value="{{ old('perihal') }}" required class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">File Scan Surat (PDF)</label>
                <input type="file" name="file_surat" accept="application/pdf" class="w-full text-xs text-gray-400">
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="bg-primary text-white px-10 py-3 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-blue-800 transition shadow-lg">
                    <i class="fas fa-download mr-2"></i> Simpan Surat Masuk
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel Arsip --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-500 border-b">
                <tr>
                    <th class="px-6 py-4">No. Surat & Pengirim</th>
                    <th class="px-6 py-4">Tgl Terima</th>
                    <th class="px-6 py-4 text-center">Status Disposisi</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 italic">
                @forelse($surats as $s)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="block font-black text-primary text-xs">{{ $s->nomor_surat }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $s->asal_surat }}</span>
                    </td>
                    <td class="px-6 py-4 text-xs font-bold text-gray-600 not-italic">{{ \Carbon\Carbon::parse($s->tanggal_terima)->isoFormat('D MMM Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 {{ $s->status_disposisi ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }} rounded text-[9px] font-black uppercase">
                            {{ $s->status_disposisi ? 'Sudah Disposisi' : 'Belum Disposisi' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center flex justify-center space-x-2">
                        @if($s->file_surat)
                            <a href="{{ Storage::url($s->file_surat) }}" target="_blank" class="text-primary hover:text-blue-800"><i class="fas fa-file-pdf"></i></a>
                        @endif
                        <button class="text-gray-400 hover:text-primary"><i class="fas fa-share-square"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-gray-300 uppercase text-xs font-bold">Belum ada surat masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection