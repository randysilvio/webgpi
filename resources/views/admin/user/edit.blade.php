@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-slate-500 hover:text-slate-800 text-xs font-bold uppercase tracking-wide mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
        <h2 class="text-xl font-bold text-slate-800">Edit Data: {{ $user->name }}</h2>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-6">

            {{-- BAGIAN 1 --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">Informasi Akun</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nama Lengkap</label>
                        {{-- PERBAIKAN: Tambah class border --}}
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Password Baru (Opsional)</label>
                        <input type="password" name="password" placeholder="Isi hanya jika ingin mengubah password..." class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                    </div>
                </div>
            </div>

            {{-- BAGIAN 2: ROLE --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">Peran & Hak Akses</h3>
                
                <div id="roles-container" class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    @foreach($roles as $role)
                        <label class="flex items-center p-3 border border-slate-200 rounded cursor-pointer hover:bg-slate-50 transition">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" data-role="{{ $role->name }}"
                                   class="role-checkbox rounded border-gray-300 text-slate-800 focus:ring-slate-500 mr-3"
                                   {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                            <span class="text-sm font-bold text-slate-700">{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>

                {{-- Konteks Dinamis --}}
                <div id="context-section" class="hidden bg-slate-50 p-4 rounded border border-slate-200 space-y-4">
                    <h4 class="text-xs font-bold text-blue-600 uppercase mb-2">Detail Wilayah & Penugasan</h4>

                    {{-- Pegawai --}}
                    <div id="pegawai-select-div" class="hidden">
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tautkan ke Data Pegawai</label>
                        <select name="pegawai_id" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}" {{ old('pegawai_id', $user->pegawai_id) == $pegawai->id ? 'selected' : '' }}>
                                    {{ $pegawai->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Wadah --}}
                    <div id="wadah-select-div" class="hidden">
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Wadah Kategorial</label>
                        <select name="jenis_wadah_id" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                            <option value="">-- Pilih Wadah --</option>
                            @foreach($jenisWadahs as $wadah)
                                <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id', $user->jenis_wadah_id) == $wadah->id ? 'selected' : '' }}>
                                    {{ $wadah->nama_wadah }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Klasis --}}
                        <div id="klasis-select-div" class="hidden">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Klasis</label>
                            <select id="klasis_id" name="klasis_id" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
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
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jemaat</label>
                            <select id="jemaat_id" name="jemaat_id" class="w-full border border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500">
                                <option value="">-- Pilih Jemaat --</option>
                                @foreach($jemaatList as $jemaat)
                                    <option value="{{ $jemaat->id }}" {{ old('jemaat_id', $user->jemaat_id) == $jemaat->id ? 'selected' : '' }}>
                                        {{ $jemaat->nama_jemaat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-bold uppercase tracking-wide shadow-lg transition">
                    Update Data User
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
            let hasAnyContext = false;

            rolesContainer.querySelectorAll('.role-checkbox:checked').forEach(checkbox => {
                const role = checkbox.dataset.role;
                if (role === 'Pendeta' || role === 'Pegawai') showPegawai = true;
                if (role === 'Admin Klasis') showKlasis = true;
                if (role === 'Admin Jemaat') { showJemaat = true; showKlasis = true; }
                if (role && role.includes('Wadah')) { showWadah = true; showKlasis = true; showJemaat = true; }
            });

            if (showPegawai || showWadah || showKlasis || showJemaat) hasAnyContext = true;

            contextSection.classList.toggle('hidden', !hasAnyContext);
            pegawaiDiv.classList.toggle('hidden', !showPegawai);
            wadahDiv.classList.toggle('hidden', !showWadah);
            klasisDiv.classList.toggle('hidden', !showKlasis);
            jemaatDiv.classList.toggle('hidden', !showJemaat);
        }

        rolesContainer.addEventListener('change', function (e) {
            if (e.target.classList.contains('role-checkbox')) checkRoles();
        });
        
        checkRoles();

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