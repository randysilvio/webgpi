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
                     {{-- Nama User --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('name') border-red-500 @enderror">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email <span class="text-red-600">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('email') border-red-500 @enderror">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-600">*</span></label>
                        <input type="password" id="password" name="password" required autocomplete="new-password"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('password') border-red-500 @enderror">
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                     {{-- Konfirmasi Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password <span class="text-red-600">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
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
                            @if($roles->isEmpty())
                                <p class="text-sm text-gray-500 italic col-span-full">Role belum dibuat di database.</p>
                            @else
                                @foreach($roles as $roleName => $roleLabel)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="roles[]" value="{{ $roleName }}"
                                               data-role="{{ $roleName }}"
                                               class="role-checkbox rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                               {{ (is_array(old('roles')) && in_array($roleName, old('roles'))) ? 'checked' : '' }}>
                                        <span class="text-sm text-gray-700">{{ $roleLabel }}</span>
                                    </label>
                                @endforeach
                            @endif
                        </div>
                         @error('roles') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <p class="text-sm text-gray-500 italic border-t pt-4">Jika user adalah 'Admin Klasis', 'Admin Jemaat', atau 'Pendeta', hubungkan dengan data di bawah ini:</p>

                    {{-- Relasi Pegawai (Pendeta/Staff) --}}
                    {{-- PERBAIKAN 1: ID div disesuaikan --}}
                    <div id="pegawai-select-div" class="{{ old('pegawai_id') || (is_array(old('roles')) && in_array('Pendeta', old('roles'))) ? '' : 'hidden' }}">
                        {{-- PERBAIKAN 2: Label & For --}}
                        <label for="pegawai_id" class="block text-sm font-medium text-gray-700 mb-1">Hubungkan ke Personil <span class="italic text-gray-500">(Jika user adalah Pendeta/Pegawai)</span></label>
                        {{-- PERBAIKAN 3: Name & ID menjadi 'pegawai_id' --}}
                        <select id="pegawai_id" name="pegawai_id"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('pegawai_id') border-red-500 @enderror">
                            <option value="">-- Pilih Nama Personil --</option>
                            {{-- PERBAIKAN 4: Variabel loop diganti jadi $pegawaiOptions (sesuai Controller) --}}
                            @foreach ($pegawaiOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('pegawai_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                         <p class="mt-1 text-xs text-gray-500 italic">Data ini diambil dari Data Induk Pegawai. Wajib untuk user Pendeta.</p>
                        @error('pegawai_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Relasi Klasis --}}
                    <div id="klasis-select-div" class="{{ old('klasis_id') || (is_array(old('roles')) && in_array('Admin Klasis', old('roles'))) ? '' : 'hidden' }}">
                        <label for="klasis_id" class="block text-sm font-medium text-gray-700 mb-1">Hubungkan ke Klasis <span class="italic text-gray-500">(Jika user adalah Admin Klasis)</span></label>
                        <select id="klasis_id" name="klasis_id"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('klasis_id') border-red-500 @enderror">
                            <option value="">-- Tidak terhubung ke Klasis --</option>
                            @foreach ($klasisOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('klasis_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                         <p class="mt-1 text-xs text-gray-500 italic">Hanya untuk user dengan role 'Admin Klasis'.</p>
                        @error('klasis_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Relasi Jemaat --}}
                    <div id="jemaat-select-div" class="{{ old('jemaat_id') || (is_array(old('roles')) && in_array('Admin Jemaat', old('roles'))) ? '' : 'hidden' }}">
                        <label for="jemaat_id" class="block text-sm font-medium text-gray-700 mb-1">Hubungkan ke Jemaat <span class="italic text-gray-500">(Jika user adalah Admin Jemaat)</span></label>
                        <select id="jemaat_id" name="jemaat_id"
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('jemaat_id') border-red-500 @enderror">
                            <option value="">-- Tidak terhubung ke Jemaat --</option>
                            @foreach ($jemaatOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('jemaat_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                         <p class="mt-1 text-xs text-gray-500 italic">Hanya untuk user dengan role 'Admin Jemaat'.</p>
                        @error('jemaat_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                 </div>
            </section>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                Batal
            </a>
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                Simpan User
            </button>
        </div>
    </form>
</div>

@push('styles')
<style>
    .error-message { margin-top: 0.25rem; font-size: 0.75rem; color: #DC2626; }
    input.border-red-500, select.border-red-500, textarea.border-red-500 { border-color: #EF4444 !important; }
    input.border-red-500:focus, select.border-red-500:focus, textarea.border-red-500:focus { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rolesContainer = document.getElementById('roles-container');
        // PERBAIKAN 5: Referensi elemen di JS diperbarui
        const pegawaiSelectDiv = document.getElementById('pegawai-select-div');
        const klasisSelectDiv = document.getElementById('klasis-select-div');
        const jemaatSelectDiv = document.getElementById('jemaat-select-div');
        const pegawaiSelect = document.getElementById('pegawai_id');
        const klasisSelect = document.getElementById('klasis_id');
        const jemaatSelect = document.getElementById('jemaat_id');

        function checkRoles() {
            let hasPendetaRole = false; // Logic tetap sama: Jika Pendeta, buka dropdown Pegawai
            let hasKlasisRole = false;
            let hasJemaatRole = false;

            rolesContainer.querySelectorAll('.role-checkbox:checked').forEach(checkbox => {
                const role = checkbox.dataset.role;
                if (role === 'Pendeta' || role === 'Pegawai') hasPendetaRole = true; // Tambahan: Role 'Pegawai' juga bisa memicu ini
                if (role === 'Admin Klasis') hasKlasisRole = true;
                if (role === 'Admin Jemaat') hasJemaatRole = true;
            });

            // Tampilkan/sembunyikan dropdown Pegawai (ex Pendeta)
            if (hasPendetaRole) {
                pegawaiSelectDiv.classList.remove('hidden');
            } else {
                pegawaiSelectDiv.classList.add('hidden');
                pegawaiSelect.value = ''; 
            }

            if (hasKlasisRole) {
                klasisSelectDiv.classList.remove('hidden');
            } else {
                klasisSelectDiv.classList.add('hidden');
                klasisSelect.value = '';
            }

            if (hasJemaatRole) {
                jemaatSelectDiv.classList.remove('hidden');
            } else {
                jemaatSelectDiv.classList.add('hidden');
                jemaatSelect.value = '';
            }
        }

        checkRoles();

        rolesContainer.addEventListener('change', function (event) {
            if (event.target.classList.contains('role-checkbox')) {
                checkRoles();
            }
        });
    });
</script>
@endpush

@endsection