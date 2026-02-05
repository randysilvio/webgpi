@extends('admin.layout')

@section('title', 'Edit User')
@section('header-title', 'Edit Data User: ' . $user->name)

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8 max-w-3xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Formulir Edit User</h2>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-6">

            {{-- 1. Info Akun --}}
            <section class="border rounded-lg p-6 bg-gray-50">
                 <h3 class="text-lg font-semibold text-gray-700 mb-4">1. Informasi Akun</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-600">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" id="password" name="password" 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Biarkan kosong jika tidak ingin mengubah password">
                        <p class="text-xs text-gray-500 mt-1">* Minimal 8 karakter.</p>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                 </div>
            </section>

            {{-- 2. Roles --}}
            <section class="border rounded-lg p-6 bg-white">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">2. Peran & Hak Akses</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Role User:</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3" id="roles-container">
                        @foreach($roles as $role)
                            <div class="flex items-center p-2 border rounded hover:bg-gray-50 cursor-pointer">
                                {{-- PERBAIKAN: Tambahkan data-role agar JS bisa baca --}}
                                <input type="checkbox" name="roles[]" 
                                       value="{{ $role->name }}" 
                                       id="role_{{ $role->id }}"
                                       data-role="{{ $role->name }}" 
                                       class="role-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer"
                                       {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                                <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-700 cursor-pointer select-none font-medium">
                                    {{ $role->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('roles') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Area Konteks (Muncul sesuai Role) --}}
                <div id="context-section" class="mt-6 pt-4 border-t space-y-4 hidden">
                    <h4 class="text-md font-medium text-blue-800">Detail Wilayah & Penugasan</h4>

                    {{-- Link ke Pegawai --}}
                    <div id="pegawai-select-div" class="hidden">
                        <label for="pegawai_id" class="block text-sm font-medium text-gray-700 mb-1">Tautkan ke Data Pegawai</label>
                        <select id="pegawai_id" name="pegawai_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}" {{ old('pegawai_id', $user->pegawai_id) == $pegawai->id ? 'selected' : '' }}>
                                    {{ $pegawai->nama_lengkap }} ({{ $pegawai->jenis_pegawai ?? 'Pegawai' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Link ke Wadah --}}
                    <div id="wadah-select-div" class="hidden">
                        <label for="jenis_wadah_id" class="block text-sm font-medium text-gray-700 mb-1">Jenis Wadah Kategorial</label>
                        <select id="jenis_wadah_id" name="jenis_wadah_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">-- Pilih Wadah --</option>
                            @foreach($jenisWadahs as $wadah)
                                <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id', $user->jenis_wadah_id) == $wadah->id ? 'selected' : '' }}>
                                    {{ $wadah->nama_wadah }} ({{ $wadah->singkatan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Klasis --}}
                        <div id="klasis-select-div" class="hidden">
                            <label for="klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Klasis</label>
                            <select id="klasis_id" name="klasis_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">-- Pilih Klasis --</option>
                                @foreach($klasisList as $klasis)
                                    <option value="{{ $klasis->id }}" {{ old('klasis_id', $user->klasis_id) == $klasis->id ? 'selected' : '' }}>
                                        {{ $klasis->nama_klasis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jemaat --}}
                        <div id="jemaat-select-div" class="hidden">
                            <label for="jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Jemaat</label>
                            <select id="jemaat_id" name="jemaat_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">-- Pilih Jemaat --</option>
                                @foreach($jemaatList as $jemaat)
                                    <option value="{{ $jemaat->id }}" {{ old('jemaat_id', $user->jemaat_id) == $jemaat->id ? 'selected' : '' }}>
                                        {{ $jemaat->nama_jemaat }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-blue-600 mt-1 hidden" id="loading-jemaat">Memuat data jemaat...</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition text-sm font-medium">Batal</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition shadow-lg text-sm font-medium">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rolesContainer = document.getElementById('roles-container');
        const contextSection = document.getElementById('context-section');
        
        const pegawaiSelectDiv = document.getElementById('pegawai-select-div');
        const wadahSelectDiv = document.getElementById('wadah-select-div');
        const klasisSelectDiv = document.getElementById('klasis-select-div');
        const jemaatSelectDiv = document.getElementById('jemaat-select-div');

        const klasisSelect = document.getElementById('klasis_id');
        const jemaatSelect = document.getElementById('jemaat_id');
        const loadingJemaat = document.getElementById('loading-jemaat');

        function checkRoles() {
            let hasPegawaiRole = false;
            let hasKlasisRole = false;
            let hasJemaatRole = false;
            let hasWadahRole = false;

            rolesContainer.querySelectorAll('.role-checkbox:checked').forEach(checkbox => {
                const role = checkbox.dataset.role; // PENTING: Baca data-role
                
                // Logika deteksi role (sama persis dengan create.blade.php)
                if (role === 'Pendeta' || role === 'Pegawai') hasPegawaiRole = true;
                if (role === 'Admin Klasis') hasKlasisRole = true;
                if (role === 'Admin Jemaat') hasJemaatRole = true;
                if (role && role.includes('Wadah')) { // Deteksi semua jenis wadah
                    hasWadahRole = true;
                    hasKlasisRole = true; // Wadah butuh lokasi
                    hasJemaatRole = true;
                }
            });

            // Toggle Tampilan
            if (hasPegawaiRole || hasKlasisRole || hasJemaatRole || hasWadahRole) {
                contextSection.classList.remove('hidden');
            } else {
                contextSection.classList.add('hidden');
            }

            // Toggle per Item
            if (hasPegawaiRole) pegawaiSelectDiv.classList.remove('hidden'); else pegawaiSelectDiv.classList.add('hidden');
            if (hasWadahRole) wadahSelectDiv.classList.remove('hidden'); else wadahSelectDiv.classList.add('hidden');
            if (hasKlasisRole) klasisSelectDiv.classList.remove('hidden'); else klasisSelectDiv.classList.add('hidden');
            if (hasJemaatRole) jemaatSelectDiv.classList.remove('hidden'); else jemaatSelectDiv.classList.add('hidden');
        }

        checkRoles();

        rolesContainer.addEventListener('change', function (event) {
            if (event.target.classList.contains('role-checkbox')) {
                checkRoles();
            }
        });

        // AJAX Jemaat
        if (klasisSelect) {
            klasisSelect.addEventListener('change', function() {
                const klasisId = this.value;
                jemaatSelect.innerHTML = '<option value="">-- Pilih Jemaat --</option>'; 
                
                if (klasisId) {
                    loadingJemaat.classList.remove('hidden');
                    jemaatSelect.disabled = true;

                    fetch(`/api/jemaat-by-klasis/${klasisId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(jemaat => {
                                const option = document.createElement('option');
                                option.value = jemaat.id;
                                option.textContent = jemaat.nama_jemaat;
                                jemaatSelect.appendChild(option);
                            });
                            loadingJemaat.classList.add('hidden');
                            jemaatSelect.disabled = false;
                        });
                }
            });
        }
    });
</script>
@endpush

@endsection