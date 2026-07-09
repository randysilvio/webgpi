@extends('layouts.app')

@section('title', 'Pembaruan Dokumen Program')

@section('content')
<div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between border-b-2 border-gray-800 pb-4">
    <div>
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Penyuntingan Dokumen Program</h2>
        <p class="text-xs text-gray-600 mt-1">Modifikasi arsip rancangan kegiatan kategorial.</p>
    </div>
    <a href="{{ route('admin.wadah.program.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center mt-3 md:mt-0">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
    </a>
</div>

<form action="{{ route('admin.wadah.program.update', $program->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="space-y-6 max-w-5xl mx-auto">

        {{-- INFO READONLY (STRUKTURAL TETAP) --}}
        <div class="bg-gray-100 border border-gray-300 p-5 rounded shadow-sm border-l-4 border-l-gray-800 flex items-start gap-4">
            <i class="fas fa-lock text-gray-500 text-2xl mt-1"></i>
            <div>
                <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-1">Parameter Kedudukan (Bersifat Tetap)</h4>
                <p class="text-[10px] text-gray-600 leading-relaxed font-bold uppercase">
                    Klasifikasi Wadah: <span class="text-blue-800">{{ $program->jenisWadah->nama_wadah }}</span> <br>
                    Hierarki Pelaksanaan: <span class="text-gray-900">{{ strtoupper($program->tingkat) }}</span>
                    @if($program->klasis) <span class="mx-1">|</span> Yurisdiksi Klasis: <span class="text-gray-900">{{ strtoupper($program->klasis->nama_klasis) }}</span> @endif
                    @if($program->jemaat) <span class="mx-1">|</span> Lokal Jemaat: <span class="text-gray-900">{{ strtoupper($program->jemaat->nama_jemaat) }}</span> @endif
                </p>
                <input type="hidden" name="jenis_wadah_id" value="{{ $program->jenis_wadah_id }}">
            </div>
        </div>

        {{-- STATUS & RINCIAN --}}
        <div class="bg-white border border-gray-300 p-5 rounded shadow-sm border-t-4 border-t-blue-800">
            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-file-alt mr-2 text-blue-800"></i> I. Progres & Rincian Teknis Program</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tahun Anggaran Berjalan <span class="text-red-600">*</span></label>
                    <input type="number" name="tahun_program" value="{{ old('tahun_program', $program->tahun_program) }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 font-mono font-bold">
                    @error('tahun_program') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Indikator Eksekusi (Status) <span class="text-red-600">*</span></label>
                    <select name="status_pelaksanaan" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white font-bold">
                        <option value="0" {{ $program->status_pelaksanaan == 0 ? 'selected' : '' }}>Fase Perencanaan</option>
                        <option value="1" {{ $program->status_pelaksanaan == 1 ? 'selected' : '' }}>Eksekusi Sedang Berjalan</option>
                        <option value="2" {{ $program->status_pelaksanaan == 2 ? 'selected' : '' }}>Selesai / Purna</option>
                        <option value="3" {{ $program->status_pelaksanaan == 3 ? 'selected' : '' }}>Ditunda Pelaksanaannya</option>
                        <option value="4" {{ $program->status_pelaksanaan == 4 ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomenklatur Program / Kegiatan <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_program" value="{{ old('nama_program', $program->nama_program) }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm uppercase bg-gray-50">
                    @error('nama_program') <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 p-4 rounded">
                    <label class="block text-[10px] font-bold text-blue-900 uppercase mb-1">Modifikasi Tautan Program Induk (Opsional)</label>
                    <select name="parent_program_id" class="w-full border border-blue-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">-- Bukan Program Turunan (Mandiri) --</option>
                        @foreach($potentialParents as $parent)
                            <option value="{{ $parent->id }}" {{ $program->parent_program_id == $parent->id ? 'selected' : '' }}>
                                [{{ strtoupper($parent->tingkat) }}] {{ $parent->nama_program }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Target / Capaian Output</label>
                    <textarea name="tujuan" rows="2" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">{{ old('tujuan', $program->tujuan) }}</textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Deskripsi & Metode Pelaksanaan</label>
                    <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">{{ old('deskripsi', $program->deskripsi) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 pt-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Unit Penanggung Jawab (PIC)</label>
                        <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab', $program->penanggung_jawab) }}" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Estimasi Kebutuhan Anggaran (RAB)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold text-[10px]">Rp</span>
                            <input type="number" name="target_anggaran" value="{{ old('target_anggaran', (int)$program->target_anggaran) }}" 
                                class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded text-sm font-mono text-right focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50 font-bold">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 pb-10">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Perbarui Rencana Program
            </button>
        </div>

    </div>
</form>
@endsection