@extends('admin.layout')

@section('title', $pegawai->nama_lengkap)
@section('header-title', 'Profil Pegawai')

@section('content')

<div x-data="{ activeTab: 'profil' }">
    
    {{-- Header Profil --}}
    <div class="bg-white shadow-sm rounded-xl p-6 mb-6 border border-gray-200 flex flex-col md:flex-row gap-6 items-center md:items-start">
        <div class="flex-shrink-0">
            @if($pegawai->foto_diri)
                <img src="{{ Storage::url($pegawai->foto_diri) }}" alt="Foto" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-md">
            @else
                <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 text-4xl">
                    <i class="fas fa-user"></i>
                </div>
            @endif
        </div>
        <div class="flex-grow text-center md:text-left">
            <h2 class="text-2xl font-bold text-gray-900">{{ $pegawai->nama_gelar }}</h2>
            <p class="text-gray-500 text-sm font-medium mb-2">{{ $pegawai->nipg }}</p>
            <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-4">
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $pegawai->jenis_pegawai }}</span>
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">{{ $pegawai->status_kepegawaian }}</span>
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">{{ $pegawai->status_aktif }}</span>
            </div>
            <div class="text-sm text-gray-600 space-y-1">
                <p><i class="fas fa-map-marker-alt w-5 text-center"></i> {{ $pegawai->jemaat->nama_jemaat ?? '-' }} ({{ $pegawai->klasis->nama_klasis ?? 'Sinode' }})</p>
                <p><i class="fas fa-phone w-5 text-center"></i> {{ $pegawai->no_hp ?? '-' }}</p>
                <p><i class="fas fa-envelope w-5 text-center"></i> {{ $pegawai->email ?? '-' }}</p>
            </div>
        </div>
        
        {{-- Bagian Tombol Aksi di Header --}}
        <div class="flex-shrink-0 flex flex-col gap-2">
            {{-- TOMBOL CETAK PDF --}}
            <a href="{{ route('admin.kepegawaian.pegawai.print', $pegawai->id) }}" target="_blank" class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium transition text-center">
                <i class="fas fa-file-pdf mr-2"></i> Cetak Biodata
            </a>

            <a href="{{ route('admin.kepegawaian.pegawai.edit', $pegawai->id) }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-md shadow-sm text-sm font-medium transition text-center">
                <i class="fas fa-edit mr-2"></i> Edit Profil
            </a>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex overflow-x-auto border-b border-gray-200 bg-white rounded-t-lg shadow-sm mb-0">
        <button @click="activeTab = 'profil'" :class="activeTab === 'profil' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition">
            Data Diri
        </button>
        <button @click="activeTab = 'keluarga'" :class="activeTab === 'keluarga' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition">
            Data Keluarga
        </button>
        <button @click="activeTab = 'pendidikan'" :class="activeTab === 'pendidikan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition">
            Riwayat Pendidikan
        </button>
        <button @click="activeTab = 'sk'" :class="activeTab === 'sk' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition">
            Riwayat SK & Pangkat
        </button>
    </div>

    {{-- Tab Contents --}}
    <div class="bg-white shadow-sm rounded-b-lg p-6 border border-t-0 border-gray-200 min-h-[400px]">
        
        {{-- 1. Tab Data Diri --}}
        <div x-show="activeTab === 'profil'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-bold text-gray-500 uppercase mb-4 border-b pb-1">Informasi Pribadi</h4>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500">Tempat Lahir</dt><dd class="mt-1 text-sm text-gray-900">{{ $pegawai->tempat_lahir ?? '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500">Tanggal Lahir</dt><dd class="mt-1 text-sm text-gray-900">{{ $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->isoFormat('D MMMM Y') : '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500">Jenis Kelamin</dt><dd class="mt-1 text-sm text-gray-900">{{ $pegawai->jenis_kelamin }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500">Gol. Darah</dt><dd class="mt-1 text-sm text-gray-900">{{ $pegawai->golongan_darah ?? '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500">Status Pernikahan</dt><dd class="mt-1 text-sm text-gray-900">{{ $pegawai->status_pernikahan ?? '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500">NIK KTP</dt><dd class="mt-1 text-sm text-gray-900">{{ $pegawai->nik_ktp ?? '-' }}</dd></div>
                    </dl>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-500 uppercase mb-4 border-b pb-1">Administrasi</h4>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500">NPWP</dt><dd class="mt-1 text-sm text-gray-900">{{ $pegawai->npwp ?? '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500">BPJS Kesehatan</dt><dd class="mt-1 text-sm text-gray-900">{{ $pegawai->no_bpjs_kesehatan ?? '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500">BPJS Ketenagakerjaan</dt><dd class="mt-1 text-sm text-gray-900">{{ $pegawai->no_bpjs_ketenagakerjaan ?? '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500">TMT Pegawai</dt><dd class="mt-1 text-sm text-gray-900">{{ $pegawai->tmt_pegawai ? $pegawai->tmt_pegawai->format('d/m/Y') : '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500">Est. Pensiun</dt><dd class="mt-1 text-sm font-bold text-blue-600">{{ $pegawai->tanggal_pensiun ? $pegawai->tanggal_pensiun->format('d/m/Y') : '-' }}</dd></div>
                    </dl>
                </div>
                <div class="col-span-2">
                    <h4 class="text-sm font-bold text-gray-500 uppercase mb-2 border-b pb-1">Alamat Domisili</h4>
                    <p class="text-sm text-gray-900">{{ $pegawai->alamat_domisili ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- 2. Tab Keluarga --}}
        <div x-show="activeTab === 'keluarga'" class="space-y-6">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold text-gray-700">Daftar Anggota Keluarga</h4>
                <button onclick="document.getElementById('formKeluarga').classList.toggle('hidden')" class="bg-green-100 text-green-700 px-3 py-1 rounded text-sm font-medium hover:bg-green-200 transition">+ Tambah</button>
            </div>

            <div id="formKeluarga" class="hidden bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                <form action="{{ route('admin.kepegawaian.keluarga.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
                    <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required class="text-sm border-gray-300 rounded">
                    <select name="hubungan" required class="text-sm border-gray-300 rounded">
                        <option value="Suami">Suami</option>
                        <option value="Istri">Istri</option>
                        <option value="Anak">Anak</option>
                    </select>
                    <input type="date" name="tanggal_lahir" class="text-sm border-gray-300 rounded">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Simpan</button>
                </form>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hubungan</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tgl Lahir</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tunjangan?</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawai->keluarga as $k)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $k->nama_lengkap }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $k->hubungan }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $k->tanggal_lahir ? $k->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($k->status_tunjangan) <span class="text-green-600 font-bold text-xs">YA</span> @else <span class="text-gray-400 text-xs">TIDAK</span> @endif
                        </td>
                        <td class="px-4 py-3 text-right text-sm">
                            <form action="{{ route('admin.kepegawaian.keluarga.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500 text-sm">Belum ada data keluarga.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 3. Tab Pendidikan --}}
        <div x-show="activeTab === 'pendidikan'" class="space-y-6">
             <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold text-gray-700">Riwayat Pendidikan</h4>
                <button onclick="document.getElementById('formPendidikan').classList.toggle('hidden')" class="bg-green-100 text-green-700 px-3 py-1 rounded text-sm font-medium hover:bg-green-200 transition">+ Tambah</button>
            </div>

            <div id="formPendidikan" class="hidden bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                <form action="{{ route('admin.kepegawaian.pendidikan.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
                    <select name="jenjang" required class="text-sm border-gray-300 rounded"><option value="">Pilih Jenjang</option><option>SD</option><option>SMP</option><option>SMA</option><option>D3</option><option>S1</option><option>S2</option><option>S3</option></select>
                    <input type="text" name="nama_institusi" placeholder="Nama Institusi" required class="text-sm border-gray-300 rounded">
                    <input type="text" name="jurusan" placeholder="Jurusan" class="text-sm border-gray-300 rounded">
                    <input type="number" name="tahun_lulus" placeholder="Tahun Lulus" required class="text-sm border-gray-300 rounded">
                    <div class="col-span-2">
                        <label class="block text-xs mb-1">Upload Ijazah (PDF/Img)</label>
                        <input type="file" name="file_ijazah" class="text-sm">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm col-span-2">Simpan Pendidikan</button>
                </form>
            </div>

            <ul class="space-y-4">
                @forelse($pegawai->pendidikan as $edu)
                <li class="bg-gray-50 p-4 rounded border border-gray-200 flex justify-between items-start">
                    <div>
                        <h5 class="font-bold text-gray-800">{{ $edu->jenjang }} - {{ $edu->nama_institusi }}</h5>
                        <p class="text-sm text-gray-600">{{ $edu->jurusan ?? 'Umum' }} (Lulus: {{ $edu->tahun_lulus }})</p>
                        @if($edu->file_ijazah)
                            <a href="{{ Storage::url($edu->file_ijazah) }}" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 inline-block"><i class="fas fa-paperclip"></i> Lihat Ijazah</a>
                        @endif
                    </div>
                    <form action="{{ route('admin.kepegawaian.pendidikan.destroy', $edu->id) }}" method="POST" onsubmit="return confirm('Hapus?');">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                    </form>
                </li>
                @empty
                <p class="text-center text-gray-500 text-sm">Belum ada data pendidikan.</p>
                @endforelse
            </ul>
        </div>

        {{-- 4. Tab Riwayat SK --}}
        <div x-show="activeTab === 'sk'" class="space-y-6">
             <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold text-gray-700">Riwayat Kepangkatan & SK</h4>
                <button onclick="document.getElementById('formSk').classList.toggle('hidden')" class="bg-green-100 text-green-700 px-3 py-1 rounded text-sm font-medium hover:bg-green-200 transition">+ Catat SK Baru</button>
            </div>

             <div id="formSk" class="hidden bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                <form action="{{ route('admin.kepegawaian.sk.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
                    <input type="text" name="nomor_sk" placeholder="Nomor SK" required class="text-sm border-gray-300 rounded">
                    <select name="jenis_sk" required class="text-sm border-gray-300 rounded">
                        <option value="">Jenis SK</option>
                        <option>Pengangkatan</option><option>Kenaikan Pangkat</option><option>KGB</option><option>Mutasi</option><option>Pensiun</option>
                    </select>
                    <div>
                        <label class="text-xs">TMT (Terhitung Mulai Tanggal)</label>
                        <input type="date" name="tmt_sk" required class="w-full text-sm border-gray-300 rounded">
                    </div>
                     <div>
                        <label class="text-xs">File SK (PDF)</label>
                        <input type="file" name="file_sk" class="w-full text-sm">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm col-span-2">Simpan SK</button>
                </form>
            </div>

            <div class="relative border-l-2 border-gray-200 ml-3 space-y-8">
                @forelse($pegawai->riwayatSk as $sk)
                <div class="relative pl-6">
                    <span class="absolute top-0 -left-2.5 bg-blue-500 h-5 w-5 rounded-full border-4 border-white"></span>
                    <h5 class="font-bold text-gray-800">{{ $sk->jenis_sk }} ({{ $sk->tmt_sk->format('d/m/Y') }})</h5>
                    <p class="text-sm text-gray-600 mb-1">No. SK: {{ $sk->nomor_sk }}</p>
                    <p class="text-xs text-gray-500 mb-2">
                        @if($sk->golongan_baru) Gol: <strong>{{ $sk->golongan_baru }}</strong> @endif
                        @if($sk->jabatan_baru) | Jabatan: <strong>{{ $sk->jabatan_baru }}</strong> @endif
                    </p>
                    <div class="flex gap-3 text-xs">
                         @if($sk->file_sk)
                            <a href="{{ Storage::url($sk->file_sk) }}" target="_blank" class="text-blue-600 hover:underline"><i class="fas fa-file-pdf"></i> Lihat Dokumen</a>
                        @endif
                        <form action="{{ route('admin.kepegawaian.sk.destroy', $sk->id) }}" method="POST" onsubmit="return confirm('Hapus?');" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="ml-6 text-gray-500 text-sm italic">Belum ada riwayat SK.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- Alpine.js untuk Tabs --}}
<script src="//unpkg.com/alpinejs" defer></script>
@endsection