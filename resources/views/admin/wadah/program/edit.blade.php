@extends('layouts.app')

@section('title', 'Edit Program Kerja')

@section('content')
    <x-admin-form 
        title="Edit Program Kerja" 
        action="{{ route('admin.wadah.program.update', $program->id) }}" 
        method="PUT"
        back-route="{{ route('admin.wadah.program.index') }}"
    >
        <div class="space-y-6">

            {{-- INFO READONLY --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100 flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                <div class="text-xs text-blue-800">
                    <strong>Konteks Program (Tetap):</strong><br>
                    Wadah: {{ $program->jenisWadah->nama_wadah }} <br>
                    Tingkat: {{ ucfirst($program->tingkat) }}
                    @if($program->klasis) | Klasis: {{ $program->klasis->nama_klasis }} @endif
                    @if($program->jemaat) | Jemaat: {{ $program->jemaat->nama_jemaat }} @endif
                    <input type="hidden" name="jenis_wadah_id" value="{{ $program->jenis_wadah_id }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input type="number" label="Tahun Program" name="tahun_program" value="{{ $program->tahun_program }}" required />
                
                <x-form-select label="Status Pelaksanaan" name="status_pelaksanaan">
                    <option value="0" {{ $program->status_pelaksanaan == 0 ? 'selected' : '' }}>Direncanakan</option>
                    <option value="1" {{ $program->status_pelaksanaan == 1 ? 'selected' : '' }}>Sedang Berjalan</option>
                    <option value="2" {{ $program->status_pelaksanaan == 2 ? 'selected' : '' }}>Selesai</option>
                    <option value="3" {{ $program->status_pelaksanaan == 3 ? 'selected' : '' }}>Ditunda</option>
                    <option value="4" {{ $program->status_pelaksanaan == 4 ? 'selected' : '' }}>Dibatalkan</option>
                </x-form-select>
            </div>

            <x-form-input label="Nama Program" name="nama_program" value="{{ $program->nama_program }}" required />

            {{-- PARENT EDIT --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Program Induk</label>
                <select name="parent_program_id" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                    <option value="">-- Tidak Ada / Mandiri --</option>
                    @foreach($potentialParents as $parent)
                        <option value="{{ $parent->id }}" {{ $program->parent_program_id == $parent->id ? 'selected' : '' }}>
                            {{ $parent->nama_program }} ({{ ucfirst($parent->tingkat) }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Ubah hanya jika diperlukan.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tujuan / Output</label>
                <textarea name="tujuan" rows="2" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">{{ old('tujuan', $program->tujuan) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Deskripsi Kegiatan</label>
                <textarea name="deskripsi" rows="3" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">{{ old('deskripsi', $program->deskripsi) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Penanggung Jawab" name="penanggung_jawab" value="{{ $program->penanggung_jawab }}" />
                <x-form-input type="number" label="Target Anggaran (Rp)" name="target_anggaran" value="{{ $program->target_anggaran }}" />
            </div>

        </div>
    </x-admin-form>
@endsection