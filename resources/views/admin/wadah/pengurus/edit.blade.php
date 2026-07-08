@extends('layouts.app')

@section('title', 'Modifikasi Pengurus Kategorial')

@section('content')
    <x-admin-form 
        title="Formulir Pembaruan Data Pengurus: {{ $pengurus->jabatan }}" 
        action="{{ route('admin.wadah.pengurus.update', $pengurus->id) }}" 
        method="PUT"
        back-route="{{ route('admin.wadah.pengurus.index') }}"
    >
        <div class="space-y-6">
            {{-- PANEL INFORMASI STRUKTURAL --}}
            <div class="bg-gray-50 border border-gray-200 p-4 rounded shadow-sm">
                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-3"><i class="fas fa-sitemap mr-2 text-blue-800"></i> Kedudukan & Wilayah Kerja</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="jenis_wadah_id" class="block text-xs font-bold text-gray-700 uppercase mb-2">Jenis Wadah Kategorial <span class="text-red-600">*</span></label>
                        <select id="jenis_wadah_id" name="jenis_wadah_id" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" required>
                            <option value="">-- Pilih Wadah Kategorial --</option>
                            @foreach($jenisWadahs as $wadah)
                                <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id', $pengurus->jenis_wadah_id) == $wadah->id ? 'selected' : '' }}>
                                    {{ $wadah->nama_wadah }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_wadah_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tingkat" class="block text-xs font-bold text-gray-700 uppercase mb-2">Tingkat Kepengurusan <span class="text-red-600">*</span></label>
                        <select id="tingkat" name="tingkat" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" required onchange="handleTingkatChange()">
                            <option value="sinode" {{ old('tingkat', $pengurus->tingkat) == 'sinode' ? 'selected' : '' }}>Tingkat Sinode</option>
                            <option value="klasis" {{ old('tingkat', $pengurus->tingkat) == 'klasis' ? 'selected' : '' }}>Tingkat Klasis</option>
                            <option value="jemaat" {{ old('tingkat', $pengurus->tingkat) == 'jemaat' ? 'selected' : '' }}>Tingkat Jemaat</option>
                        </select>
                        @error('tingkat') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div id="div_klasis" class="{{ $pengurus->tingkat == 'sinode' ? 'hidden' : '' }}">
                        <label for="klasis_id" class="block text-xs font-bold text-gray-700 uppercase mb-2">Wilayah Klasis <span class="text-red-600">*</span></label>
                        <select id="klasis_id" name="klasis_id" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white" onchange="loadJemaat(this.value)">
                            <option value="">-- Pilih Wilayah Klasis --</option>
                            @foreach($klasisList as $klasis)
                                <option value="{{ $klasis->id }}" {{ old('klasis_id', $pengurus->klasis_id) == $klasis->id ? 'selected' : '' }}>
                                    {{ $klasis->nama_klasis }}
                                </option>
                            @endforeach
                        </select>
                        @error('klasis_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div id="div_jemaat" class="{{ $pengurus->tingkat == 'jemaat' ? '' : 'hidden' }}">
                        <label for="jemaat_id" class="block text-xs font-bold text-gray-700 uppercase mb-2">Jemaat Lokal <span class="text-red-600">*</span></label>
                        <select id="jemaat_id" name="jemaat_id" class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                            <option value="">-- Pilih Jemaat Lokal --</option>
                            @foreach($jemaatList as $jemaat)
                                <option value="{{ $jemaat->id }}" {{ old('jemaat_id', $pengurus->jemaat_id) == $jemaat->id ? 'selected' : '' }}>
                                    {{ $jemaat->nama_jemaat }}
                                </option>
                            @endforeach
                        </select>
                        @error('jemaat_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- PANEL INFORMASI PERSONAL --}}
            <div class="bg-white border border-gray-200 p-5 rounded shadow-sm">
                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-id-badge mr-2 text-blue-800"></i> Identitas Personal & SK</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="jabatan" class="block text-xs font-bold text-gray-700 uppercase mb-2">Nama Jabatan <span class="text-red-600">*</span></label>
                        <input id="jabatan" type="text" name="jabatan" value="{{ old('jabatan', $pengurus->jabatan) }}" required 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm" />
                        @error('jabatan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="anggota_jemaat_id" class="block text-xs font-bold text-gray-700 uppercase mb-2">ID Register Anggota (Opsional)</label>
                        <input id="anggota_jemaat_id" type="number" name="anggota_jemaat_id" value="{{ old('anggota_jemaat_id', $pengurus->anggota_jemaat_id) }}" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50" />
                        @if($pengurus->anggotaJemaat)
                            <p class="text-[10px] font-bold text-blue-800 mt-1 uppercase tracking-wide"><i class="fas fa-check-circle mr-1"></i> Tertaut dengan: {{ $pengurus->anggotaJemaat->nama }}</p>
                        @endif
                        @error('anggota_jemaat_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="nomor_sk" class="block text-xs font-bold text-gray-700 uppercase mb-2">Nomor Surat Keputusan (SK)</label>
                        <input id="nomor_sk" type="text" name="nomor_sk" value="{{ old('nomor_sk', $pengurus->nomor_sk) }}" 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm" />
                    </div>
                    <div>
                        <label for="periode_mulai" class="block text-xs font-bold text-gray-700 uppercase mb-2">Tanggal Mulai Jabatan <span class="text-red-600">*</span></label>
                        <input id="periode_mulai" type="date" name="periode_mulai" value="{{ old('periode_mulai', $pengurus->periode_mulai ? $pengurus->periode_mulai->format('Y-m-d') : '') }}" required 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm" />
                    </div>
                    <div>
                        <label for="periode_selesai" class="block text-xs font-bold text-gray-700 uppercase mb-2">Tanggal Akhir Jabatan <span class="text-red-600">*</span></label>
                        <input id="periode_selesai" type="date" name="periode_selesai" value="{{ old('periode_selesai', $pengurus->periode_selesai ? $pengurus->periode_selesai->format('Y-m-d') : '') }}" required 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm" />
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <label for="is_active" class="inline-flex items-center cursor-pointer">
                        <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', $pengurus->is_active) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-blue-800 focus:ring-blue-800 shadow-sm cursor-pointer">
                        <span class="ml-2 text-xs font-bold text-gray-700 uppercase">Status Personil Aktif (Sedang Menjabat)</span>
                    </label>
                </div>
            </div>
        </div>
    </x-admin-form>

    @push('scripts')
    <script>
        function handleTingkatChange() {
            const tingkat = document.getElementById('tingkat').value;
            const divKlasis = document.getElementById('div_klasis');
            const divJemaat = document.getElementById('div_jemaat');
            const inputKlasis = document.getElementById('klasis_id');
            const inputJemaat = document.getElementById('jemaat_id');

            divKlasis.classList.add('hidden');
            divJemaat.classList.add('hidden');
            inputKlasis.removeAttribute('required');
            inputJemaat.removeAttribute('required');

            if (tingkat === 'klasis') {
                divKlasis.classList.remove('hidden');
                inputKlasis.setAttribute('required', 'required');
            } else if (tingkat === 'jemaat') {
                divKlasis.classList.remove('hidden');
                divJemaat.classList.remove('hidden');
                inputKlasis.setAttribute('required', 'required');
                inputJemaat.setAttribute('required', 'required');
            }
        }

        function loadJemaat(klasisId) {
            const tingkat = document.getElementById('tingkat').value;
            if (tingkat !== 'jemaat') return; 
            
            const jemaatSelect = document.getElementById('jemaat_id');
            const currentJemaatId = "{{ $pengurus->jemaat_id }}"; 

            jemaatSelect.innerHTML = '<option value="">Memuat Pangkalan Data...</option>';

            if (klasisId) {
                fetch(`/api/jemaat-by-klasis/${klasisId}`)
                    .then(response => response.json())
                    .then(data => {
                        jemaatSelect.innerHTML = '<option value="">-- Pilih Jemaat Lokal --</option>';
                        data.forEach(jemaat => {
                            const selected = jemaat.id == currentJemaatId ? 'selected' : '';
                            jemaatSelect.innerHTML += `<option value="${jemaat.id}" ${selected}>${jemaat.nama_jemaat}</option>`;
                        });
                    })
                    .catch(error => {
                        jemaatSelect.innerHTML = '<option value="">Gagal merespon jaringan</option>';
                    });
            }
        }

        // Jalankan saat pertama kali halaman dirender
        document.addEventListener('DOMContentLoaded', function() {
            handleTingkatChange();
        });
    </script>
    @endpush
@endsection