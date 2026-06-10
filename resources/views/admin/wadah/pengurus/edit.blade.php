<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pengurus Wadah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form method="POST" action="{{ route('admin.wadah.pengurus.update', $pengurus->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="jenis_wadah_id" :value="__('Jenis Wadah Kategorial')" />
                        <select id="jenis_wadah_id" name="jenis_wadah_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">-- Pilih Wadah --</option>
                            @foreach($jenisWadahs as $wadah)
                                <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id', $pengurus->jenis_wadah_id) == $wadah->id ? 'selected' : '' }}>
                                    {{ $wadah->nama_wadah }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="tingkat" :value="__('Tingkat Kepengurusan')" />
                            <select id="tingkat" name="tingkat" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required onchange="handleTingkatChange()">
                                <option value="sinode" {{ old('tingkat', $pengurus->tingkat) == 'sinode' ? 'selected' : '' }}>Sinode</option>
                                <option value="klasis" {{ old('tingkat', $pengurus->tingkat) == 'klasis' ? 'selected' : '' }}>Klasis</option>
                                <option value="jemaat" {{ old('tingkat', $pengurus->tingkat) == 'jemaat' ? 'selected' : '' }}>Jemaat</option>
                            </select>
                        </div>

                        <div id="div_klasis" class="{{ $pengurus->tingkat == 'sinode' ? 'hidden' : '' }}">
                            <x-input-label for="klasis_id" :value="__('Pilih Klasis')" />
                            <select id="klasis_id" name="klasis_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" onchange="loadJemaat(this.value)">
                                <option value="">-- Pilih Klasis --</option>
                                @foreach($klasisList as $klasis)
                                    <option value="{{ $klasis->id }}" {{ old('klasis_id', $pengurus->klasis_id) == $klasis->id ? 'selected' : '' }}>
                                        {{ $klasis->nama_klasis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="div_jemaat" class="mb-4 {{ $pengurus->tingkat == 'jemaat' ? '' : 'hidden' }}">
                        <x-input-label for="jemaat_id" :value="__('Pilih Jemaat')" />
                        <select id="jemaat_id" name="jemaat_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Pilih Jemaat --</option>
                            @foreach($jemaatList as $jemaat)
                                <option value="{{ $jemaat->id }}" {{ old('jemaat_id', $pengurus->jemaat_id) == $jemaat->id ? 'selected' : '' }}>
                                    {{ $jemaat->nama_jemaat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="anggota_jemaat_id" :value="__('ID Anggota Jemaat')" />
                            <x-text-input id="anggota_jemaat_id" class="block mt-1 w-full bg-gray-100" type="number" name="anggota_jemaat_id" :value="old('anggota_jemaat_id', $pengurus->anggota_jemaat_id)" placeholder="ID Anggota" />
                            @if($pengurus->anggotaJemaat)
                                <p class="text-xs text-blue-600 mt-1">Saat ini: {{ $pengurus->anggotaJemaat->nama }}</p>
                            @endif
                        </div>

                        <div>
                            <x-input-label for="jabatan" :value="__('Jabatan')" />
                            <x-text-input id="jabatan" class="block mt-1 w-full" type="text" name="jabatan" :value="old('jabatan', $pengurus->jabatan)" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <x-input-label for="nomor_sk" :value="__('Nomor SK')" />
                            <x-text-input id="nomor_sk" class="block mt-1 w-full" type="text" name="nomor_sk" :value="old('nomor_sk', $pengurus->nomor_sk)" />
                        </div>
                        <div>
                            <x-input-label for="periode_mulai" :value="__('Mulai Periode')" />
                            <x-text-input id="periode_mulai" class="block mt-1 w-full" type="date" name="periode_mulai" :value="old('periode_mulai', $pengurus->periode_mulai->format('Y-m-d'))" required />
                        </div>
                        <div>
                            <x-input-label for="periode_selesai" :value="__('Selesai Periode')" />
                            <x-text-input id="periode_selesai" class="block mt-1 w-full" type="date" name="periode_selesai" :value="old('periode_selesai', $pengurus->periode_selesai->format('Y-m-d'))" required />
                        </div>
                    </div>

                    <div class="block mt-4">
                        <label for="is_active" class="inline-flex items-center">
                            <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_active" value="1" {{ old('is_active', $pengurus->is_active) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-600">{{ __('Status Aktif (Sedang Menjabat)') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('admin.wadah.pengurus.index') }}" class="text-gray-600 hover:text-gray-900 mr-4 text-sm">Batal</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                            Perbarui Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Logic JS sama dengan create, hanya memastikan nilai awal ter-handle oleh Blade 'old' atau value DB
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
            
            // Jika sedang edit dan klasis tidak berubah, jangan reset (optional optimization, but simple fetch is safer)
            const jemaatSelect = document.getElementById('jemaat_id');
            const currentJemaatId = "{{ $pengurus->jemaat_id }}"; 

            jemaatSelect.innerHTML = '<option value="">Memuat...</option>';

            if (klasisId) {
                fetch(`/api/jemaat-by-klasis/${klasisId}`)
                    .then(response => response.json())
                    .then(data => {
                        jemaatSelect.innerHTML = '<option value="">-- Pilih Jemaat --</option>';
                        data.forEach(jemaat => {
                            const selected = jemaat.id == currentJemaatId ? 'selected' : '';
                            jemaatSelect.innerHTML += `<option value="${jemaat.id}" ${selected}>${jemaat.nama_jemaat}</option>`;
                        });
                    });
            }
        }
    </script>
</x-app-layout>