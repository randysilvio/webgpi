@extends('layouts.app')

@section('title', 'Edit Anggaran')

@section('content')
    <x-admin-form 
        title="Edit Data Pos Anggaran" 
        action="{{ route('admin.wadah.anggaran.update', $anggaran->id) }}" 
        method="PUT"
        back-route="{{ route('admin.wadah.anggaran.index') }}"
    >
        <div class="space-y-6">

            {{-- INFO READONLY --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100 flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                <div class="text-xs text-blue-800">
                    <strong>Informasi Konteks (Tidak dapat diubah):</strong><br>
                    Tahun Anggaran: {{ $anggaran->tahun_anggaran }} <br>
                    Wadah: {{ $anggaran->jenisWadah->nama_wadah }} <br>
                    Tingkat: {{ ucfirst($anggaran->tingkat) }}
                </div>
            </div>

            <x-form-input label="Nama Pos Anggaran" name="nama_pos_anggaran" value="{{ $anggaran->nama_pos_anggaran }}" required />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jenis Anggaran</label>
                    <select name="jenis_anggaran" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                        <option value="penerimaan" {{ $anggaran->jenis_anggaran == 'penerimaan' ? 'selected' : '' }}>Penerimaan</option>
                        <option value="pengeluaran" {{ $anggaran->jenis_anggaran == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <x-form-input type="number" label="Target Jumlah (Rp)" name="jumlah_target" value="{{ $anggaran->jumlah_target }}" required />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">{{ old('keterangan', $anggaran->keterangan) }}</textarea>
            </div>

        </div>
    </x-admin-form>
@endsection