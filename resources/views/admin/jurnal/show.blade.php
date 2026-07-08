@extends('layouts.app')

@section('title', 'Tinjauan Dokumen Jurnal')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        
        {{-- Header Tindakan --}}
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('admin.jurnal.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
            </a>
            <button onclick="window.print()" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wide shadow-sm transition flex items-center">
                <i class="fas fa-print mr-2"></i> Cetak Dokumen
            </button>
        </div>

        {{-- Kertas Dokumen --}}
        <div class="bg-white border border-gray-300 rounded shadow-sm p-8 md:p-12 print:shadow-none print:border-none print:p-0">
            
            {{-- Kop Dokumen Intern --}}
            <div class="border-b-2 border-gray-800 pb-6 mb-6 text-center">
                <h1 class="text-xl font-black text-gray-900 uppercase tracking-widest">Jurnal Pelayanan Pastoral</h1>
                <h2 class="text-sm font-bold text-gray-700 uppercase mt-1">Gereja Protestan Indonesia di Papua (GPI Papua)</h2>
                <p class="text-xs text-gray-500 mt-2 font-mono">ID Register: JRNL-{{ str_pad($jurnal->id, 5, '0', STR_PAD_LEFT) }} | Klasifikasi: Terbatas</p>
            </div>

            {{-- Metadata --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 border-b border-gray-200 pb-6">
                <div>
                    <span class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tanggal Kegiatan</span>
                    <span class="text-sm font-bold text-gray-900">{{ $jurnal->tanggal_kegiatan->isoFormat('D MMMM YYYY') }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Kategori Pelayanan</span>
                    <span class="text-sm font-bold text-gray-900">{{ $jurnal->kategori }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Lokasi Jemaat</span>
                    <span class="text-sm font-bold text-blue-800 uppercase">{{ $jurnal->jemaat->nama_jemaat ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Penulis (Author)</span>
                    <span class="text-sm font-bold text-gray-900">{{ $jurnal->pendeta->nama_lengkap ?? '-' }}</span>
                </div>
            </div>

            {{-- Isi Jurnal --}}
            <div class="space-y-8">
                <div>
                    <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-3 bg-gray-100 p-2 border-l-4 border-blue-800">I. Analisis Konteks & Situasi</h3>
                    <div class="text-sm text-gray-800 leading-loose whitespace-pre-line text-justify">
                        {{ $jurnal->konteks_situasi }}
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-3 bg-gray-100 p-2 border-l-4 border-yellow-600">II. Rekomendasi & Tindak Lanjut</h3>
                    @if($jurnal->tindak_lanjut)
                        <div class="text-sm text-gray-800 leading-loose whitespace-pre-line text-justify italic">
                            {{ $jurnal->tindak_lanjut }}
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">Tidak ada catatan tindak lanjut yang diberikan untuk sesi ini.</p>
                    @endif
                </div>
            </div>

            {{-- Footer Tanda Tangan --}}
            <div class="mt-16 pt-8 border-t border-gray-200 flex justify-end">
                <div class="text-center w-64">
                    <p class="text-xs text-gray-600 mb-16">Disahkan secara digital oleh,</p>
                    <p class="text-sm font-bold text-gray-900 underline">{{ $jurnal->pendeta->nama_lengkap ?? '-' }}</p>
                    <p class="text-[10px] text-gray-500 mt-1 uppercase">Pelayan Firman & Sakramen</p>
                </div>
            </div>

        </div>
    </div>
@endsection