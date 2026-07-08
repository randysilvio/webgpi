@extends('layouts.app')

@section('title', 'Pusat Distribusi Dokumen Pelayanan')

@section('content')
    <div class="mb-6 flex justify-between items-end border-b-2 border-gray-800 pb-4">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Pusat Distribusi Dokumen Bidang 1</h2>
            <p class="text-xs text-gray-600 mt-1">Portal resmi permohonan dan pengunduhan materi khotbah serta liturgi GPI Papua.</p>
        </div>
        <a href="{{ route('admin.bursa.transaksi.index') }}" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-800 px-4 py-2 rounded text-xs font-bold uppercase tracking-wide transition shadow-sm">
            <i class="fas fa-history mr-2"></i> Riwayat Permohonan Saya
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($materis as $materi)
            <div class="bg-white border border-gray-300 rounded flex flex-col shadow-sm hover:shadow transition overflow-hidden">
                @if($materi->cover_path)
                    <img src="{{ Storage::url($materi->cover_path) }}" class="h-40 w-full object-cover border-b border-gray-200">
                @else
                    <div class="h-40 w-full bg-gray-100 flex items-center justify-center border-b border-gray-200">
                        <i class="fas fa-file-pdf text-4xl text-gray-300"></i>
                    </div>
                @endif
                
                <div class="p-4 flex-1 flex flex-col">
                    <span class="text-[9px] font-bold text-blue-800 uppercase tracking-wider mb-1">{{ $materi->kategori }}</span>
                    <h3 class="text-sm font-bold text-gray-900 leading-snug mb-2">{{ $materi->judul_dokumen }}</h3>
                    <p class="text-[10px] text-gray-600 line-clamp-2 mb-4 flex-1">{{ $materi->deskripsi_singkat }}</p>
                    
                    <div class="border-t border-gray-200 pt-3 mt-auto">
                        <p class="text-xs font-bold text-gray-900 mb-3 text-center">
                            {{ $materi->harga_dokumen == 0 ? 'DOKUMEN BEBAS BIAYA (GRATIS)' : 'BIAYA INFAQ: Rp ' . number_format($materi->harga_dokumen, 0, ',', '.') }}
                        </p>

                        @if($materi->harga_dokumen == 0)
                            <a href="{{ route('admin.bursa.download', $materi) }}" class="block w-full text-center bg-blue-800 hover:bg-blue-900 text-white py-2 rounded text-[10px] font-bold uppercase tracking-wide transition">
                                <i class="fas fa-download mr-1"></i> Unduh Dokumen
                            </a>
                        @else
                            <form action="{{ route('admin.bursa.transaksi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                                @csrf
                                <input type="hidden" name="materi_khotbah_id" value="{{ $materi->id }}">
                                <div>
                                    <label class="block text-[9px] font-bold text-gray-600 uppercase mb-1">Unggah Bukti Transfer Kas Jemaat:</label>
                                    <input type="file" name="bukti_transfer" required accept="image/*" class="block w-full text-[10px] text-gray-700 border border-gray-300 p-1 rounded bg-gray-50">
                                </div>
                                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white py-2 rounded text-[10px] font-bold uppercase tracking-wide transition">
                                    <i class="fas fa-paper-plane mr-1"></i> Ajukan Otorisasi
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500 text-sm border border-dashed border-gray-300 bg-gray-50">
                Katalog dokumen belum tersedia.
            </div>
        @endforelse
    </div>
    
    <div class="mt-6">{{ $materis->links() }}</div>
@endsection