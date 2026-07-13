@extends('layouts.app')

@section('title', 'Detail Mutasi')
@section('header-title', 'Detail SK Mutasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- BUTTON BACK --}}
    <div class="flex items-center justify-between border-b-2 border-gray-800 pb-4">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Detail Dokumen Mutasi</h2>
            <p class="text-xs text-gray-600 mt-1">Evaluasi rincian Surat Keputusan pemindahan personel.</p>
        </div>
        <a href="{{ route('admin.mutasi.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Indeks Jurnal
        </a>
    </div>

    {{-- MAIN CARD --}}
    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden relative">
        
        {{-- Header Card --}}
        <div class="bg-gray-800 px-8 py-6 text-white flex justify-between items-start">
            <div>
                <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1">Nomor Registrasi Surat Keputusan</p>
                <h1 class="text-2xl font-black font-mono tracking-widest">{{ $mutasi->nomor_sk }}</h1>
            </div>
            <div class="text-right">
                <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1">TMT (Tanggal Efektif)</p>
                <p class="text-sm font-bold uppercase tracking-widest">{{ $mutasi->tanggal_efektif ? \Carbon\Carbon::parse($mutasi->tanggal_efektif)->format('d M Y') : 'Belum Ditentukan' }}</p>
            </div>
        </div>

        <div class="p-8">
            {{-- Profile Section --}}
            <div class="flex items-center mb-8 pb-8 border-b border-gray-200">
                <div class="h-16 w-16 rounded border border-gray-300 bg-gray-100 flex items-center justify-center text-gray-400 text-xl font-bold uppercase shadow-inner">
                    @if(isset($mutasi->pegawai) && $mutasi->pegawai->foto_diri)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($mutasi->pegawai->foto_diri) }}" class="h-full w-full object-cover">
                    @else
                        {{ substr($mutasi->pegawai->nama_lengkap ?? 'X', 0, 1) }}
                    @endif
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest">{{ $mutasi->pegawai->nama_lengkap ?? 'Personel Telah Dihapus' }}</h3>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">NIP/NIPG: {{ $mutasi->pegawai->nipg ?? $mutasi->pegawai->nip ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <span class="bg-gray-100 text-gray-600 border border-gray-300 px-3 py-1 rounded text-[10px] font-black uppercase tracking-widest">
                        Mutasi: {{ $mutasi->jenis_mutasi }}
                    </span>
                </div>
            </div>

            {{-- Movement Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 relative">
                {{-- Connector Line (Desktop) --}}
                <div class="hidden md:block absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-0">
                    <i class="fas fa-arrow-right text-gray-300 text-2xl"></i>
                </div>

                {{-- From --}}
                <div class="relative z-10 bg-gray-50 p-6 rounded border border-gray-200">
                    <span class="text-[10px] font-black uppercase text-gray-500 tracking-widest mb-3 block"><i class="far fa-circle mr-2"></i> Kedudukan Instansi Lama</span>
                    <p class="text-sm font-bold text-gray-900 uppercase leading-relaxed">{{ $mutasi->asalJemaat->nama_jemaat ?? 'Tanpa Jemaat' }}</p>
                    <p class="text-[10px] font-bold text-gray-500 uppercase mt-1 tracking-widest">{{ $mutasi->asalKlasis->nama_klasis ?? 'Kantor Sinode' }}</p>
                </div>

                {{-- To --}}
                <div class="relative z-10 bg-blue-50 p-6 rounded border border-blue-200">
                    <span class="text-[10px] font-black uppercase text-blue-800 tracking-widest mb-3 block"><i class="fas fa-arrow-right mr-2"></i> Penempatan Instansi Baru</span>
                    <p class="text-sm font-black text-blue-900 uppercase leading-relaxed">{{ $mutasi->tujuanJemaat->nama_jemaat ?? 'Tanpa Jemaat' }}</p>
                    <p class="text-[10px] font-bold text-blue-700 uppercase mt-1 tracking-widest">{{ $mutasi->tujuanKlasis->nama_klasis ?? 'Ditarik Ke Pusat' }}</p>
                </div>
            </div>

            {{-- Details --}}
            <div class="grid grid-cols-2 gap-6 text-sm border-t border-gray-200 pt-6">
                <div>
                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest">Tanggal Pengesahan SK</span>
                    <span class="block font-bold text-gray-900 mt-1 uppercase">{{ $mutasi->tanggal_sk ? \Carbon\Carbon::parse($mutasi->tanggal_sk)->format('d F Y') : '-' }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest">ID Rekaman Sistem</span>
                    <span class="block font-mono font-bold text-gray-900 mt-1">SYS-MUT-{{ str_pad($mutasi->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="col-span-2">
                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Keterangan / Catatan Tambahan</span>
                    <div class="bg-gray-50 p-4 rounded border border-gray-200 text-xs text-gray-700 leading-relaxed italic">
                        "{{ $mutasi->keterangan ?? 'Tanpa catatan tambahan dari pihak otoritas.' }}"
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="bg-gray-50 px-8 py-5 border-t border-gray-200 flex justify-between items-center">
            <span class="text-[9px] font-bold uppercase tracking-widest text-gray-500">Terekam: {{ $mutasi->created_at->format('d/m/Y H:i') }}</span>
            
            <div class="flex gap-2">
                @can('manage pendeta')
                <form action="{{ route('admin.mutasi.destroy', $mutasi->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Membatalkan/menghapus SK Mutasi ini tidak akan mengembalikan pegawai ke lokasi sebelumnya secara otomatis. Yakin?');" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-white border border-gray-300 text-red-700 text-[10px] font-bold uppercase tracking-widest rounded shadow-sm hover:bg-red-50 transition">
                        Hapus SK
                    </button>
                </form>
                {{-- <a href="{{ route('admin.mutasi.edit', $mutasi->id) }}" class="px-4 py-2 bg-gray-800 text-white text-[10px] font-bold uppercase tracking-widest rounded shadow-sm hover:bg-gray-900 transition">
                    Modifikasi Data
                </a> --}}
                @endcan
            </div>
        </div>

    </div>
</div>
@endsection