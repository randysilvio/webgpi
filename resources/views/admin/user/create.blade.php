@extends('admin.layout')

@section('title', 'Tambah User Baru')
@section('header-title', 'Tambah Pengguna Sistem Baru')

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8 max-w-3xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir User Baru</h2>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="space-y-6">

            {{-- Info Akun --}}
            <section class="border rounded-lg p-6">
                 <h3 class="text-lg font-semibold text-gray-700 mb-4">1. Informasi Akun</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-600">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-600">*</span></label>
                        <input type="password" id="password" name="password" required autocomplete="new-password"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password <span class="text-red-600">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                 </div>
            </section>

            {{-- Roles & Relasi --}}
             <section class="border rounded-lg p-6">
                 <h3 class="text-lg font-semibold text-gray-700 mb-4">2. Peran (Role) & Relasi</h3>
                 <div class="space-y-4">
                     
                    {{-- Roles --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peran (Roles) <span class="text-red-600">*</span></label>
                        <div id="roles-container" class="grid grid-cols-2 md:grid-cols-4 gap-2 border p-3 rounded-md">
                            @foreach($roles as $role)
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                           data-role="{{ $role->name }}"
                                           class="role-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="text-sm text-gray-700">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('roles') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <p class="text-sm text-gray-500 italic border-t pt-4">Detail Wilayah & Penugasan (Muncul otomatis sesuai Role):</p>

                    {{-- 1. PEGAWAI --}}
                    <div id="pegawai-select-div" class="hidden">
                        <label for="pegawai_id" class="block text-sm font-medium text-gray-700 mb-1">Hubungkan ke Data Pegawai/Pendeta</label>
                        <select id="pegawai_id" name="pegawai_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach ($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}" {{ old('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                    {{ $pegawai->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. WADAH (Baru Ditambahkan) --}}
                    <div id="wadah-select-div" class="hidden">
                        <label for="jenis_wadah_id" class="block text-sm font-medium text-gray-700 mb-1">Jenis Wadah Kategorial</label>
                        <select id="jenis_wadah_id" name="jenis_wadah_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">-- Pilih Wadah --</option>
                            @foreach ($jenisWadahs as $wadah)
                                <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>
                                    {{ $wadah->nama_wadah }} ({{ $wadah->singkatan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- 3. KLASIS --}}
                        <div id="klasis-select-div" class="hidden">
                            <label for="klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Klasis</label>
                            <select id="klasis_id" name="klasis_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">-- Pilih Klasis --</option>
                                @foreach ($klasisList as $klasis)
                                    <option value="{{ $klasis->id }}" {{ old('klasis_id') == $klasis->id ? 'selected' : '' }}>
                                        {{ $klasis->nama_klasis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 4. JEMAAT --}}
                        <div id="jemaat-select-div" class="hidden">
                            <label for="jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Jemaat</label>
                            <select id="jemaat_id" name="jemaat_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">-- Pilih Jemaat --</option>
                                @foreach ($jemaatList as $jemaat)
                                    <option value="{{ $jemaat->id }}" {{ old('jemaat_id') == $jemaat->id ? 'selected' : '' }}>
                                        {{ $jemaat->nama_jemaat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                 </div>
            </section>
        </div>

        {{-- Tombol --}}
        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md transition">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow transition">Simpan User</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rolesContainer = document.getElementById('roles-container');
        
        const pegawaiDiv = document.getElementById('pegawai-select-div');
        const wadahDiv   = document.getElementById('wadah-select-div');
        const klasisDiv  = document.getElementById('klasis-select-div');
        const jemaatDiv  = document.getElementById('jemaat-select-div');

        const klasisSelect = document.getElementById('klasis_id');
        const jemaatSelect = document.getElementById('jemaat_id');

        function checkRoles() {
            let showPegawai = false;
            let showWadah   = false;
            let showKlasis  = false;
            let showJemaat  = false;

            rolesContainer.querySelectorAll('.role-checkbox:checked').forEach(checkbox => {
                const role = checkbox.dataset.role;

                if (role === 'Pendeta' || role === 'Pegawai') showPegawai = true;
                if (role === 'Admin Klasis') showKlasis = true;
                if (role === 'Admin Jemaat') { showJemaat = true; showKlasis = true; }
                if (role && role.includes('Wadah')) { showWadah = true; showKlasis = true; showJemaat = true; }
            });

            if(showPegawai) pegawaiDiv.classList.remove('hidden'); else pegawaiDiv.classList.add('hidden');
            if(showWadah) wadahDiv.classList.remove('hidden'); else wadahDiv.classList.add('hidden');
            if(showKlasis) klasisDiv.classList.remove('hidden'); else klasisDiv.classList.add('hidden');
            if(showJemaat) jemaatDiv.classList.remove('hidden'); else jemaatDiv.classList.add('hidden');
        }

        rolesContainer.addEventListener('change', function (e) {
            if (e.target.classList.contains('role-checkbox')) checkRoles();
        });
        
        // Cek awal
        checkRoles();

        // AJAX Klasis -> Jemaat
        if (klasisSelect) {
            klasisSelect.addEventListener('change', function() {
                const klasisId = this.value;
                jemaatSelect.innerHTML = '<option value="">-- Pilih Jemaat --</option>';
                if (klasisId) {
                    jemaatSelect.disabled = true;
                    fetch(`/api/jemaat-by-klasis/${klasisId}`)
                        .then(res => res.json())
                        .then(data => {
                            data.forEach(j => {
                                jemaatSelect.innerHTML += `<option value="${j.id}">${j.nama_jemaat}</option>`;
                            });
                            jemaatSelect.disabled = false;
                        });
                }
            });
        }
    });
</script>
@endpush

@endsection