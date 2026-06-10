@extends('layouts.app')

@section('title', 'Edit Aset')

@section('content')
    <x-admin-form 
        title="Edit Data Aset: {{ $aset->nama_aset }}" 
        action="{{ route('admin.perbendaharaan.aset.update', $aset->id) }}" 
        method="PUT"
        back-route="{{ route('admin.perbendaharaan.aset.index') }}"
        has-file="true"
    >
        <div class="space-y-6">
            
            {{-- INFORMASI UTAMA --}}
            <div class="bg-slate-50 p-4 rounded border border-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form-input label="Nama Aset" name="nama_aset" value="{{ $aset->nama_aset }}" required />
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kode Aset</label>
                        <input type="text" name="kode_aset" value="{{ $aset->kode_aset }}" class="w-full border-slate-300 rounded text-sm bg-slate-100 text-slate-500 cursor-not-allowed" readonly>
                    </div>
                    
                    <x-form-select label="Kategori Aset" name="kategori" required>
                        @foreach(['Tanah', 'Gedung', 'Kendaraan', 'Peralatan Elektronik', 'Meubelair', 'Alat Musik', 'Lainnya'] as $cat)
                            <option value="{{ $cat }}" {{ $aset->kategori == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </x-form-select>

                    <x-form-select label="Kondisi Fisik" name="kondisi" required>
                        @foreach(['Baik', 'Rusak Ringan', 'Rusak Berat'] as $kon)
                            <option value="{{ $kon }}" {{ $aset->kondisi == $kon ? 'selected' : '' }}>{{ $kon }}</option>
                        @endforeach
                    </x-form-select>
                </div>
            </div>

            {{-- NILAI & LOKASI --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form-input label="No. Dokumen / Sertifikat" name="nomor_dokumen" value="{{ $aset->nomor_dokumen }}" />
                <x-form-input type="number" label="Nilai Perolehan (Rp)" name="nilai_perolehan" value="{{ (int)$aset->nilai_perolehan }}" />
                
                <div class="md:col-span-2">
                    <x-form-input label="Lokasi Fisik" name="lokasi_fisik" value="{{ $aset->lokasi_fisik }}" />
                </div>
            </div>

            {{-- UPLOAD FILE --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-blue-700 uppercase mb-1">Ganti Dokumen (PDF)</label>
                    <input type="file" name="file_dokumen" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-white file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer">
                    @if($aset->file_dokumen_path) 
                        <p class="text-[10px] text-green-600 mt-2 flex items-center"><i class="fas fa-check-circle mr-1"></i> File dokumen saat ini tersedia.</p> 
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-bold text-blue-700 uppercase mb-1">Ganti Foto Aset</label>
                    <input type="file" name="foto_aset" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-white file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer" onchange="previewImage(event, 'preview-edit')">
                    <img id="preview-edit" src="{{ $aset->foto_aset_path ? Storage::url($aset->foto_aset_path) : '#' }}" class="mt-3 h-24 rounded border shadow-sm object-cover {{ $aset->foto_aset_path ? '' : 'hidden' }}">
                </div>
            </div>

        </div>
    </x-admin-form>

    @push('scripts')
    <script>
        function previewImage(event, id) {
            const input = event.target;
            const preview = document.getElementById(id);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
@endsection