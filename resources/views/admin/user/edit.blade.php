@extends('layouts.app')

@section('title', 'Modifikasi Akun Pengguna')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Modifikasi Akses Pengguna</h2>
            <p class="text-xs text-gray-600 mt-1">Sistem kontrol hak akses dan autentikasi untuk <strong>{{ $user->name }}</strong>.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Indeks
        </a>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            {{-- PANEL INFORMASI AKUN & PASSWORD --}}
            <div class="bg-white border border-gray-200 p-5 rounded shadow-sm">
                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-user-shield mr-2 text-blue-800"></i> Informasi Kredensial Akun</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Nama Lengkap Pemegang Akun <span class="text-red-600">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Alamat Surel (Email Login) <span class="text-red-600">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                            class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-gray-50">
                        @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- FIELD PASSWORD YANG DIPERBAIKI (HARUS ADA CONFIRMATION) --}}
                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded mb-2">
                    <p class="text-[10px] text-yellow-800 font-bold uppercase mb-3"><i class="fas fa-key mr-1"></i> Area Perubahan Kata Sandi</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Kata Sandi Baru</label>
                            <input type="password" name="password" placeholder="Kosongkan jika tidak ada perubahan..." 
                                class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm">
                            @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" placeholder="Ketik ulang kata sandi baru..." 
                                class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm">
                        </div>
                    </div>
                    <p class="text-[9px] text-gray-500 mt-2 italic">Perhatian: Untuk pengguna bertipe Pendeta/Pegawai yang dibuat otomatis via menu Kepegawaian, sandi awal adalah NIPG mereka.</p>
                </div>
            </div>

            {{-- PANEL OTORISASI & ROLE --}}
            <div class="bg-white border border-gray-200 p-5 rounded shadow-sm">
                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-4"><i class="fas fa-sitemap mr-2 text-blue-800"></i> Penetapan Otoritas (Role) & Penugasan</h4>
                
                <div id="roles-container" class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    @foreach($roles as $role)
                        <label class="flex items-center p-3 border border-gray-200 rounded cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" data-role="{{ $role->name }}"
                                   class="role-checkbox rounded border-gray-300 text-blue-800 focus:ring-blue-800 shadow-sm mr-3 cursor-pointer"
                                   {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                            <span class="text-[11px] font-bold text-gray-700 uppercase">{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>

                {{-- Konteks Dinamis Wilayah Kerja --}}
                <div id="context-section" class="hidden bg-gray-50 p-4 rounded border border-gray-200 space-y-4">
                    <h4 class="text-xs font-bold text-gray-700 uppercase mb-2 border-b border-gray-200 pb-2">Detail Integrasi Wilayah & Pangkalan Data</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Pegawai --}}
                        <div id="pegawai-select-div" class="hidden">
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tautkan ke Buku Induk Kepegawaian</label>
                            <select name="pegawai_id" class="w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                                <option value="">-- Tanpa Tautan Pegawai --</option>
                                @foreach($pegawais as $pegawai)
                                    <option value="{{ $pegawai->id }}" {{ old('pegawai_id', $user->pegawai_id) == $pegawai->id ? 'selected' : '' }}>
                                        [{{ $pegawai->nipg }}] - {{ $pegawai->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Wadah --}}
                        <div id="wadah-select-div" class="hidden">
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tautkan ke Wadah Kategorial</label>
                            <select name="jenis_wadah_id" class="w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                                <option value="">-- Tanpa Tautan Wadah --</option>
                                @foreach($jenisWadahs as $wadah)
                                    <option value="{{ $wadah->id }}" {{ old('jenis_wadah_id', $user->jenis_wadah_id) == $wadah->id ? 'selected' : '' }}>
                                        {{ $wadah->nama_wadah }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Klasis --}}
                        <div id="klasis-select-div" class="hidden">
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Otoritas Wilayah Klasis</label>
                            <select id="klasis_id" name="klasis_id" class="w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                                <option value="">-- Seluruh Wilayah Sinode --</option>
                                @foreach($klasisList as $klasis)
                                    <option value="{{ $klasis->id }}" {{ old('klasis_id', $user->klasis_id) == $klasis->id ? 'selected' : '' }}>
                                        {{ $klasis->nama_klasis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jemaat --}}
                        <div id="jemaat-select-div" class="hidden">
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Otoritas Wilayah Jemaat</label>
                            <select id="jemaat_id" name="jemaat_id" class="w-full border border-gray-300 rounded text-xs focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                                <option value="">-- Pilihan Jemaat Berdasarkan Klasis --</option>
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

            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded text-xs font-bold uppercase tracking-widest shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Terapkan Perubahan
                </button>
            </div>
        </div>
    </form>

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
            
            checkRoles(); // Eksekusi saat pertama muat

            // DYNAMIC DROPDOWN KLASIS -> JEMAAT
            if (klasisSelect) {
                klasisSelect.addEventListener('change', function() {
                    const klasisId = this.value;
                    jemaatSelect.innerHTML = '<option value="">-- Menunggu Pangkalan Data --</option>';
                    if (klasisId) {
                        jemaatSelect.disabled = true;
                        fetch(`/api/jemaat-by-klasis/${klasisId}`)
                            .then(res => res.json())
                            .then(data => {
                                jemaatSelect.innerHTML = '<option value="">-- Pilih Wilayah Jemaat --</option>';
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