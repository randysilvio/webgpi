@extends('layouts.app')

@section('title', 'Edit Anggota')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <x-admin-form 
        title="Edit Data: {{ $anggotaJemaat->nama_lengkap }}" 
        action="{{ route('admin.anggota-jemaat.update', $anggotaJemaat->id) }}" 
        method="PUT"
        back-route="{{ route('admin.anggota-jemaat.index') }}"
    >
        {{-- SECTION 1: DATA PRIBADI --}}
        <div class="space-y-4 mb-8">
            <h3 class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">I. Data Pribadi & Kontak</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <x-form-input label="Nama Lengkap" name="nama_lengkap" value="{{ $anggotaJemaat->nama_lengkap }}" required />
                <x-form-input label="NIK (KTP)" name="nik" value="{{ $anggotaJemaat->nik }}" />
                <x-form-input label="No. Buku Induk" name="nomor_buku_induk" value="{{ $anggotaJemaat->nomor_buku_induk }}" />
                
                <x-form-input label="Tempat Lahir" name="tempat_lahir" value="{{ $anggotaJemaat->tempat_lahir }}" />
                <x-form-input type="date" label="Tanggal Lahir" name="tanggal_lahir" value="{{ optional($anggotaJemaat->tanggal_lahir)->format('Y-m-d') }}" />
                
                <x-form-select label="Jenis Kelamin" name="jenis_kelamin">
                    <option value="Laki-laki" {{ $anggotaJemaat->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ $anggotaJemaat->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </x-form-select>

                <x-form-select label="Gol. Darah" name="golongan_darah">
                    @foreach(['A','B','AB','O','-'] as $g)
                        <option value="{{ $g }}" {{ $anggotaJemaat->golongan_darah == $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </x-form-select>

                <x-form-select label="Status Pernikahan" name="status_pernikahan">
                    @foreach(['Belum Menikah','Menikah','Cerai Hidup','Cerai Mati'] as $s)
                        <option value="{{ $s }}" {{ $anggotaJemaat->status_pernikahan == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </x-form-select>

                <x-form-input label="No. HP / WA" name="telepon" value="{{ $anggotaJemaat->telepon }}" />
                
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" rows="2" class="w-full border-slate-300 rounded text-sm">{{ $anggotaJemaat->alamat_lengkap }}</textarea>
                </div>
            </div>
        </div>

        {{-- SECTION 2: KEANGGOTAAN GEREJA --}}
        <div class="space-y-4 mb-8">
            <h3 class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4 uppercase tracking-wide">II. Keanggotaan Gereja</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                {{-- Disabled Select for Safety --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Jemaat</label>
                    <select name="jemaat_id" class="w-full border-slate-300 rounded text-sm bg-slate-50" {{ !Auth::user()->hasRole('Super Admin') ? 'disabled' : '' }}>
                        @foreach ($jemaatOptions as $id => $nama)
                            <option value="{{ $id }}" {{ $anggotaJemaat->jemaat_id == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                    @if(!Auth::user()->hasRole('Super Admin')) <input type="hidden" name="jemaat_id" value="{{ $anggotaJemaat->jemaat_id }}"> @endif
                </div>

                <x-form-select label="Status Keanggotaan" name="status_keanggotaan" required>
                    @foreach(['Aktif','Tidak Aktif','Pindah','Meninggal'] as $s)
                        <option value="{{ $s }}" {{ $anggotaJemaat->status_keanggotaan == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </x-form-select>

                <x-form-input label="Nomor KK" name="nomor_kk" value="{{ $anggotaJemaat->nomor_kk }}" />
                <x-form-input label="Sektor Pelayanan" name="sektor_pelayanan" value="{{ $anggotaJemaat->sektor_pelayanan }}" />
                <x-form-input label="Unit Pelayanan" name="unit_pelayanan" value="{{ $anggotaJemaat->unit_pelayanan }}" />

                <x-form-input type="date" label="Tanggal Baptis" name="tanggal_baptis" value="{{ optional($anggotaJemaat->tanggal_baptis)->format('Y-m-d') }}" />
                <x-form-input label="Tempat Baptis" name="tempat_baptis" value="{{ $anggotaJemaat->tempat_baptis }}" />
                
                <x-form-input type="date" label="Tanggal Sidi" name="tanggal_sidi" value="{{ optional($anggotaJemaat->tanggal_sidi)->format('Y-m-d') }}" />
                <x-form-input label="Tempat Sidi" name="tempat_sidi" value="{{ $anggotaJemaat->tempat_sidi }}" />
            </div>
        </div>

        {{-- SECTION 3: HUBUNGAN KELUARGA --}}
        <div class="bg-blue-50 p-5 rounded border border-blue-100 mb-8">
            <h3 class="text-xs font-bold text-blue-800 border-b border-blue-200 pb-2 mb-4 uppercase tracking-wide">III. Pohon Keluarga</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ayah</label>
                    <select name="ayah_id" id="select-ayah" class="w-full"></select>
                    <input type="text" name="nama_ayah" value="{{ $anggotaJemaat->nama_ayah }}" class="w-full mt-2 border-slate-300 rounded text-xs" placeholder="Nama Manual">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ibu</label>
                    <select name="ibu_id" id="select-ibu" class="w-full"></select>
                    <input type="text" name="nama_ibu" value="{{ $anggotaJemaat->nama_ibu }}" class="w-full mt-2 border-slate-300 rounded text-xs" placeholder="Nama Manual">
                </div>
                <div class="md:col-span-2">
                    <x-form-select label="Status dalam Keluarga" name="status_dalam_keluarga">
                        @foreach(['Kepala Keluarga','Istri','Anak','Famili Lain'] as $s)
                            <option value="{{ $s }}" {{ $anggotaJemaat->status_dalam_keluarga == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </x-form-select>
                </div>
            </div>
        </div>

    </x-admin-form>

    {{-- SCRIPTS SELECT2 --}}
    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            function initSelect2(id, initialData) {
                $(id).select2({
                    placeholder: 'Cari Nama...',
                    ajax: {
                        url: "{{ route('admin.anggota-jemaat.search') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) { return { q: params.term }; },
                        processResults: function (data) { return { results: data }; },
                        cache: true
                    }
                });
                if(initialData) {
                    var option = new Option(initialData.text, initialData.id, true, true);
                    $(id).append(option).trigger('change');
                }
            }

            @if($anggotaJemaat->ayah_id)
                initSelect2('#select-ayah', { id: '{{ $anggotaJemaat->ayah_id }}', text: '{{ $anggotaJemaat->ayah->nama_lengkap }}' });
            @else
                initSelect2('#select-ayah');
            @endif

            @if($anggotaJemaat->ibu_id)
                initSelect2('#select-ibu', { id: '{{ $anggotaJemaat->ibu_id }}', text: '{{ $anggotaJemaat->ibu->nama_lengkap }}' });
            @else
                initSelect2('#select-ibu');
            @endif
        });
    </script>
    @endpush
    @push('styles')
    <style>.select2-container .select2-selection--single { height: 38px; border-color: #cbd5e1; }</style>
    @endpush
@endsection