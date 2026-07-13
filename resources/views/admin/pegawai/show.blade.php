@extends('layouts.app')

@section('title', 'Arsip Induk Personel')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ activeTab: 'profil' }">

    {{-- HEADER PROFILE --}}
    <div class="bg-white rounded border border-gray-300 shadow-sm p-6 flex flex-col md:flex-row items-center md:items-start gap-6 mb-6 border-l-4 border-l-gray-800">
        <div class="flex-shrink-0">
            <div class="h-32 w-32 rounded border-4 border-gray-100 overflow-hidden shadow-inner bg-gray-200 flex items-center justify-center">
                @if($pegawai->foto_diri)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($pegawai->foto_diri) }}" class="h-full w-full object-cover">
                @else
                    <i class="fas fa-user text-4xl text-gray-400"></i>
                @endif
            </div>
        </div>
        <div class="flex-grow text-center md:text-left">
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-widest">{{ $pegawai->gelar_depan }} {{ $pegawai->nama_lengkap }} {{ $pegawai->gelar_belakang }}</h1>
            <p class="text-xs font-mono font-bold text-gray-600 mt-1">NIPG / NIP: {{ $pegawai->nipg ?? $pegawai->nip ?? '-' }}</p>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-2 mt-4">
                <span class="px-3 py-1 bg-blue-800 text-white rounded text-[10px] font-bold uppercase tracking-widest">{{ $pegawai->jenis_pegawai }}</span>
                @if($pegawai->status_aktif == 'Aktif')
                    <span class="px-3 py-1 bg-green-100 border border-green-300 text-green-800 rounded text-[10px] font-bold uppercase tracking-widest">Berstatus Aktif</span>
                @else
                    <span class="px-3 py-1 bg-red-100 border border-red-300 text-red-800 rounded text-[10px] font-bold uppercase tracking-widest">{{ $pegawai->status_aktif }}</span>
                @endif
            </div>

            <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-4 text-xs font-bold uppercase text-gray-600">
                <span><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> {{ $pegawai->jemaat->nama_jemaat ?? 'Tanpa Jemaat' }}</span>
                <span><i class="fas fa-map text-gray-400 mr-1"></i> {{ $pegawai->klasis->nama_klasis ?? 'Pusat Sinode' }}</span>
            </div>
        </div>
        <div class="flex-shrink-0 flex flex-col md:flex-row gap-2">
            <a href="{{ route('admin.kepegawaian.pegawai.print', $pegawai->id) }}" target="_blank" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase shadow-sm transition">
                <i class="fas fa-print mr-1"></i> Cetak Berkas
            </a>
            <a href="{{ route('admin.kepegawaian.pegawai.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded text-[10px] font-bold uppercase shadow-sm transition flex items-center justify-center">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    {{-- MENU TAB FORMAL --}}
    <div class="bg-white rounded border border-gray-300 shadow-sm overflow-hidden mb-10">
        <div class="flex border-b border-gray-200 bg-gray-50 overflow-x-auto">
            <button @click="activeTab = 'profil'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'profil', 'text-gray-600 hover:text-gray-900': activeTab !== 'profil'}" class="px-5 py-4 text-[11px] font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                1. Data Pribadi
            </button>
            <button @click="activeTab = 'sk'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'sk', 'text-gray-600 hover:text-gray-900': activeTab !== 'sk'}" class="px-5 py-4 text-[11px] font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                2. Kepangkatan (SK)
            </button>
            <button @click="activeTab = 'pendidikan'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'pendidikan', 'text-gray-600 hover:text-gray-900': activeTab !== 'pendidikan'}" class="px-5 py-4 text-[11px] font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                3. Riwayat Studi
            </button>
            <button @click="activeTab = 'keluarga'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'keluarga', 'text-gray-600 hover:text-gray-900': activeTab !== 'keluarga'}" class="px-5 py-4 text-[11px] font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                4. Data Keluarga
            </button>
            <button @click="activeTab = 'mutasi'" :class="{'border-b-2 border-blue-800 text-blue-800 bg-white': activeTab === 'mutasi', 'text-gray-600 hover:text-gray-900': activeTab !== 'mutasi'}" class="px-5 py-4 text-[11px] font-bold uppercase tracking-widest focus:outline-none transition whitespace-nowrap">
                5. Riwayat Mutasi
            </button>
        </div>

        <div class="p-8">
            {{-- TAB 1: PROFIL --}}
            <div x-show="activeTab === 'profil'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Informasi Kelahiran & Sipil</h4>
                        <table class="w-full text-sm text-gray-700">
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Tempat, Tgl Lahir</td><td class="py-2">{{ $pegawai->tempat_lahir ?? '-' }}, {{ $pegawai->tanggal_lahir ? \Carbon\Carbon::parse($pegawai->tanggal_lahir)->format('d M Y') : '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Jenis Kelamin</td><td class="py-2">{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Golongan Darah</td><td class="py-2 font-bold text-red-600">{{ $pegawai->golongan_darah ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Status Sipil</td><td class="py-2">{{ $pegawai->status_pernikahan ?? '-' }}</td></tr>
                            <tr><td class="py-2 font-bold w-1/3 uppercase text-[10px]">NIK KTP Nasional</td><td class="py-2 font-mono text-xs">{{ $pegawai->nik_ktp ?? '-' }}</td></tr>
                        </table>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Informasi Kontak & Kedinasan</h4>
                        <table class="w-full text-sm text-gray-700">
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Alamat Saat Ini</td><td class="py-2">{{ $pegawai->alamat_domisili ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Ponsel / WhatsApp</td><td class="py-2 font-mono text-xs">{{ $pegawai->no_hp ?? '-' }}</td></tr>
                            <tr class="border-b border-gray-50"><td class="py-2 font-bold w-1/3 uppercase text-[10px]">Surat Elektronik</td><td class="py-2">{{ $pegawai->email ?? '-' }}</td></tr>
                            @if($pegawai->jenis_pegawai == 'Pendeta')
                            <tr><td class="py-2 font-bold w-1/3 uppercase text-[10px] text-blue-800">Tanggal Tahbisan</td><td class="py-2 font-bold">{{ $pegawai->tanggal_tahbisan ? \Carbon\Carbon::parse($pegawai->tanggal_tahbisan)->format('d F Y') : '-' }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAB 2: SK --}}
            <div x-show="activeTab === 'sk'" x-cloak>
                <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
                    <h4 class="text-sm font-black uppercase text-gray-800 tracking-widest">Kepangkatan & Jabatan</h4>
                    @can('manage pendeta')
                    <button onclick="toggleModal('modalSK')" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm transition"><i class="fas fa-plus mr-1"></i> Tambah SK</button>
                    @endcan
                </div>
                <div class="border-l-2 border-gray-200 pl-6 space-y-6">
                    @forelse($pegawai->riwayatSk ?? [] as $sk)
                    <div class="relative group">
                        <div class="absolute -left-[33px] top-1 h-4 w-4 rounded-full bg-blue-800 border-4 border-white"></div>
                        
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">TMT: {{ \Carbon\Carbon::parse($sk->tmt_sk)->format('d M Y') }}</div>
                                <h3 class="text-sm font-black text-gray-800 uppercase">{{ $sk->jenis_sk }}</h3>
                                <p class="text-xs font-mono text-gray-500 mt-1">No Register SK: {{ $sk->nomor_sk }}</p>
                            </div>
                            @can('manage pendeta')
                            <form action="{{ route('admin.kepegawaian.sk.destroy', $sk->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat SK ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-300 hover:text-red-600 transition"><i class="fas fa-trash-alt text-sm"></i></button>
                            </form>
                            @endcan
                        </div>

                        <div class="mt-3 text-xs bg-gray-50 p-4 rounded border border-gray-200 flex flex-col md:flex-row gap-6">
                            <div><span class="block text-[9px] uppercase font-bold text-gray-400 mb-1">Ditetapkan Sebagai</span> <span class="font-bold text-gray-900">{{ $sk->jabatan_baru ?? '-' }}</span></div>
                            <div><span class="block text-[9px] uppercase font-bold text-gray-400 mb-1">Golongan Ruang</span> <span class="font-bold text-gray-900">{{ $sk->golongan_baru ?? '-' }}</span></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-400 text-xs italic font-bold">Catatan historis kepangkatan belum terekam di Pangkalan Data.</p>
                    @endforelse
                </div>
            </div>

            {{-- TAB 3: PENDIDIKAN --}}
            <div x-show="activeTab === 'pendidikan'" x-cloak>
                <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
                    <h4 class="text-sm font-black uppercase text-gray-800 tracking-widest">Latar Belakang Studi</h4>
                    <button onclick="toggleModal('modalPendidikan')" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm transition"><i class="fas fa-graduation-cap mr-1"></i> Tambah Pendidikan</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                                <th class="px-4 py-3">Tingkat</th>
                                <th class="px-4 py-3">Nama Institusi / Universitas</th>
                                <th class="px-4 py-3">Fakultas / Program Studi</th>
                                <th class="px-4 py-3 text-center">Lulus</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-200">
                            @forelse($pegawai->riwayatPendidikan ?? [] as $pend)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-bold text-gray-800">{{ $pend->tingkat }}</td>
                                <td class="px-4 py-3 text-gray-700 uppercase font-bold text-xs">{{ $pend->nama_institusi }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $pend->jurusan ?? '-' }}</td>
                                <td class="px-4 py-3 text-center font-mono font-bold">{{ $pend->tahun_lulus }}</td>
                                <td class="px-4 py-3 text-center">
                                    <form action="{{ route('admin.kepegawaian.pendidikan.destroy', $pend->id) }}" method="POST" onsubmit="return confirm('Hapus data pendidikan ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition"><i class="fas fa-trash-alt text-xs"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500 text-xs italic">Data riwayat pendidikan belum dimasukkan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 4: KELUARGA --}}
            <div x-show="activeTab === 'keluarga'" x-cloak>
                <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
                    <h4 class="text-sm font-black uppercase text-gray-800 tracking-widest">Tanggungan Keluarga</h4>
                    <button onclick="toggleModal('modalKeluarga')" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm transition"><i class="fas fa-users mr-1"></i> Tambah Tanggungan</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-800 text-[10px] text-gray-700 uppercase tracking-wider font-bold">
                                <th class="px-4 py-3">Nama Anggota Keluarga</th>
                                <th class="px-4 py-3">Status Sipil</th>
                                <th class="px-4 py-3">Tempat, Tgl Lahir</th>
                                <th class="px-4 py-3">Pekerjaan</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-200">
                            @forelse($pegawai->keluarga ?? [] as $kel)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-bold text-gray-800 uppercase text-xs">{{ $kel->nama }}</td>
                                <td class="px-4 py-3">
                                    <span class="bg-blue-50 text-blue-800 border border-blue-200 px-2 py-0.5 rounded text-[10px] font-bold uppercase">{{ $kel->hubungan }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $kel->tempat_lahir }}, {{ \Carbon\Carbon::parse($kel->tanggal_lahir)->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $kel->pekerjaan ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <form action="{{ route('admin.kepegawaian.keluarga.destroy', $kel->id) }}" method="POST" onsubmit="return confirm('Hapus data tanggungan keluarga ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition"><i class="fas fa-trash-alt text-xs"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500 text-xs italic">Data tanggungan keluarga belum dimasukkan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 5: MUTASI --}}
            <div x-show="activeTab === 'mutasi'" x-cloak>
                <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
                    <h4 class="text-sm font-black uppercase text-gray-800 tracking-widest">Jejak Penugasan</h4>
                    @can('manage pendeta')
                    <a href="{{ route('admin.kepegawaian.pegawai.mutasi.create', $pegawai->id) }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm transition"><i class="fas fa-route mr-1"></i> Catat Mutasi Baru</a>
                    @endcan
                </div>
                <div class="border-l-2 border-gray-200 pl-6 space-y-6">
                    @forelse($pegawai->mutasiHistory ?? [] as $mutasi)
                    <div class="relative group">
                        <div class="absolute -left-[33px] top-1 h-4 w-4 rounded-full bg-yellow-600 border-4 border-white"></div>
                        <div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">TMT: {{ \Carbon\Carbon::parse($mutasi->tanggal_efektif)->format('d M Y') }}</div>
                        <h3 class="text-sm font-black text-gray-800 uppercase">Mutasi Ke: {{ $mutasi->tujuan_instansi }}</h3>
                        <p class="text-xs font-mono text-gray-500 mt-1">No SK: {{ $mutasi->nomor_sk }}</p>
                        
                        <div class="mt-3 text-xs bg-yellow-50 p-4 rounded border border-yellow-200 flex flex-col md:flex-row gap-6">
                            <div><span class="block text-[9px] uppercase font-bold text-yellow-700 mb-1">Dari Instansi Asal</span> <span class="font-bold text-gray-900 uppercase">{{ $mutasi->asal_instansi ?? '-' }}</span></div>
                            <div><span class="block text-[9px] uppercase font-bold text-yellow-700 mb-1">Ditetapkan Tanggal</span> <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($mutasi->tanggal_sk)->format('d/m/Y') }}</span></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-400 text-xs italic font-bold">Pegawai ini belum pernah mengalami pergerakan mutasi.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL TAMBAH SK --}}
<div id="modalSK" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-gray-900/70 backdrop-blur-sm p-4">
    <div class="bg-white rounded shadow-2xl w-full max-w-lg border border-gray-300 overflow-hidden">
        <div class="bg-gray-100 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest">Catat Riwayat SK</h3>
            <button type="button" onclick="toggleModal('modalSK')" class="text-gray-400 hover:text-red-600"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.kepegawaian.sk.store') }}" method="POST">
            @csrf
            <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Jenis SK <span class="text-red-600">*</span></label>
                    <input type="text" name="jenis_sk" required placeholder="Cth: SK Berkala / SK Jabatan" class="w-full border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nomor Registrasi SK <span class="text-red-600">*</span></label>
                    <input type="text" name="nomor_sk" required class="w-full border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 font-mono">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">TMT SK <span class="text-red-600">*</span></label>
                        <input type="date" name="tmt_sk" required class="w-full border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Golongan Ruang</label>
                        <input type="text" name="golongan_baru" placeholder="Cth: III/a" class="w-full border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Jabatan Baru (Ditetapkan Sebagai)</label>
                    <input type="text" name="jabatan_baru" class="w-full border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800">
                </div>
            </div>
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalSK')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm">Batal</button>
                <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm">Simpan SK</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL PENDIDIKAN --}}
<div id="modalPendidikan" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-gray-900/70 backdrop-blur-sm p-4">
    <div class="bg-white rounded shadow-2xl w-full max-w-lg border border-gray-300 overflow-hidden">
        <div class="bg-gray-100 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest">Tambah Studi Akademik</h3>
            <button type="button" onclick="toggleModal('modalPendidikan')" class="text-gray-400 hover:text-red-600"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.kepegawaian.pendidikan.store') }}" method="POST">
            @csrf
            <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Jenjang/Tingkat <span class="text-red-600">*</span></label>
                        <select name="tingkat" required class="w-full border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800">
                            <option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA">SMA/SMK</option>
                            <option value="D3">Diploma III (D3)</option><option value="S1">Strata I (S1)</option><option value="S2">Strata II (S2)</option><option value="S3">Strata III (S3)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tahun Lulus <span class="text-red-600">*</span></label>
                        <input type="number" name="tahun_lulus" required placeholder="YYYY" min="1950" class="w-full border-gray-300 rounded text-sm focus:ring-blue-800 font-mono">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nama Universitas / Institusi <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_institusi" required class="w-full border-gray-300 rounded text-sm focus:ring-blue-800">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fakultas / Jurusan</label>
                    <input type="text" name="jurusan" class="w-full border-gray-300 rounded text-sm focus:ring-blue-800">
                </div>
            </div>
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalPendidikan')" class="bg-white border border-gray-300 px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm">Batal</button>
                <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm">Simpan Studi</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KELUARGA --}}
<div id="modalKeluarga" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-gray-900/70 backdrop-blur-sm p-4">
    <div class="bg-white rounded shadow-2xl w-full max-w-lg border border-gray-300 overflow-hidden">
        <div class="bg-gray-100 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest">Tambah Tanggungan Keluarga</h3>
            <button type="button" onclick="toggleModal('modalKeluarga')" class="text-gray-400 hover:text-red-600"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.kepegawaian.keluarga.store') }}" method="POST">
            @csrf
            <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Nama Lengkap Anggota Keluarga <span class="text-red-600">*</span></label>
                    <input type="text" name="nama" required class="w-full border-gray-300 rounded text-sm focus:ring-blue-800 uppercase">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Hubungan Sipil <span class="text-red-600">*</span></label>
                        <select name="hubungan" required class="w-full border-gray-300 rounded text-sm focus:ring-blue-800">
                            <option value="Suami">Suami</option><option value="Istri">Istri</option><option value="Anak">Anak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="w-full border-gray-300 rounded text-sm focus:ring-blue-800">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tempat Lahir <span class="text-red-600">*</span></label>
                        <input type="text" name="tempat_lahir" required class="w-full border-gray-300 rounded text-sm focus:ring-blue-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tanggal Lahir <span class="text-red-600">*</span></label>
                        <input type="date" name="tanggal_lahir" required class="w-full border-gray-300 rounded text-sm focus:ring-blue-800">
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalKeluarga')" class="bg-white border border-gray-300 px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm">Batal</button>
                <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm">Simpan Tanggungan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleModal(modalID) {
        const modal = document.getElementById(modalID);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection