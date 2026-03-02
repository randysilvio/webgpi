@extends('layouts.app')

@section('title', 'Catat Aset Baru')

@section('content')
    <x-admin-form 
        title="Registrasi Inventaris Aset" 
        action="{{ route('admin.perbendaharaan.aset.store') }}" 
        back-route="{{ route('admin.perbendaharaan.aset.index') }}"
        has-file="true"
    >
        <div class="space-y-6">
            
            {{-- INFORMASI UTAMA --}}
            <div class="bg-slate-50 p-4 rounded border border-slate-200">
                <h3 class="text-xs font-bold text-blue-600 uppercase mb-4 border-b border-blue-100 pb-2">Informasi Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form-input label="Nama Aset" name="nama_aset" required placeholder="Contoh: Gedung Gereja Utama" />
                    <x-form-input label="Kode Aset (Opsional)" name="kode_aset" placeholder="Kosongkan untuk auto-generate" />
                    
                    <x-form-select label="Kategori Aset" name="kategori" required>
                        @foreach(['Tanah', 'Gedung', 'Kendaraan', 'Peralatan Elektronik', 'Meubelair', 'Alat Musik', 'Lainnya'] as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </x-form-select>

                    <x-form-select label="Kondisi Fisik" name="kondisi" required>
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </x-form-select>
                </div>
            </div>

            {{-- NILAI & LEGALITAS --}}
            <div class="bg-slate-50 p-4 rounded border border-slate-200">
                <h3 class="text-xs font-bold text-blue-600 uppercase mb-4 border-b border-blue-100 pb-2">Nilai & Legalitas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form-input type="date" label="Tanggal Perolehan" name="tanggal_perolehan" />
                    <x-form-input type="number" label="Nilai Perolehan (Rp)" name="nilai_perolehan" placeholder="0" />
                    
                    <x-form-select label="Status Kepemilikan" name="status_kepemilikan">
                        <option value="Milik Sendiri">Milik Sendiri</option>
                        <option value="Sewa">Sewa</option>
                        <option value="Pinjam Pakai">Pinjam Pakai</option>
                    </x-form-select>

                    <x-form-input label="No. Sertifikat / BPKB / Dokumen" name="nomor_dokumen" />
                </div>
            </div>

            {{-- LOKASI & BERKAS --}}
            <div class="bg-slate-50 p-4 rounded border border-slate-200">
                <h3 class="text-xs font-bold text-blue-600 uppercase mb-4 border-b border-blue-100 pb-2">Lokasi & Berkas Pendukung</h3>
                <div class="grid grid-cols-1 gap-4">
                    <x-form-input label="Lokasi Fisik (Ruangan/Tempat)" name="lokasi_fisik" placeholder="Contoh: Ruang Kantor Jemaat" />
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Scan Dokumen (PDF/JPG)</label>
                            <input type="file" name="file_dokumen" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Foto Fisik Aset</label>
                            <input type="file" name="foto_aset" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer" onchange="previewImage(event, 'foto-preview')">
                            <img id="foto-preview" src="#" alt="Preview" class="mt-3 h-32 hidden rounded border shadow-sm object-cover">
                        </div>
                    </div>
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