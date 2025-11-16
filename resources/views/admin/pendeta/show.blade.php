@extends('admin.layout')

{{-- Define Section Title --}}
@section('title', 'Detail Pendeta: ' . $pendeta->nama_lengkap)

{{-- Define Section Header Title --}}
@section('header-title', 'Detail Data Pendeta')

{{-- Start Section Content --}}
@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8">
    {{-- Header Detail --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4">
        <div class="flex items-center space-x-4 mb-4 sm:mb-0">
             {{-- Foto Pendeta --}}
             @if ($pendeta->foto_path && Storage::disk('public')->exists($pendeta->foto_path))
                <img src="{{ Storage::url($pendeta->foto_path) }}" alt="Foto {{ $pendeta->nama_lengkap }}" class="w-16 h-16 rounded-full object-cover border border-gray-200 shadow-sm">
            @else
                 <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center text-gray-600">
                    <i class="fas fa-user fa-2x"></i>
                </div>
            @endif
            {{-- Info Nama, NIPG, Status --}}
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">{{ $pendeta->nama_lengkap }}</h2>
                <p class="text-sm text-gray-500">
                    NIPG: {{ $pendeta->nipg }} |
                    Status: <span class="font-medium">{{ $pendeta->status_kepegawaian ?? '-' }}</span>
                </p>
                 <p class="text-sm text-gray-500">
                    Jabatan: {{ $pendeta->jabatan_saat_ini ?: '-' }}
                </p>
            </div>
        </div>
        {{-- Tombol Aksi Header --}}
        <div class="flex flex-wrap gap-2">
            @hasanyrole('Super Admin|Admin Bidang 3')
            <a href="{{ route('admin.pendeta.edit', $pendeta->id) }}" class="btn-primary-outline text-sm whitespace-nowrap">
                <i class="fas fa-edit mr-1"></i> Edit Data
            </a>
            <a href="{{ route('admin.pendeta.mutasi.create', $pendeta->id) }}" class="btn-primary text-sm whitespace-nowrap">
                 <i class="fas fa-exchange-alt mr-1"></i> Tambah Mutasi
            </a>
            @endhasanyrole
            <a href="{{ route('admin.pendeta.index') }}" class="btn-secondary text-sm whitespace-nowrap">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </div>

     {{-- Flash Message --}}
    @if (session('success'))
        <div class="flash-message mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
           <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Grid Detail Data --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm mt-6">
        {{-- Kolom 1: Data Pribadi --}}
        <div class="space-y-3 bg-gray-50 shadow-sm rounded-lg p-5 border border-gray-200">
            <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Data Pribadi</h3>
            <p><strong class="font-medium text-gray-500 w-28 inline-block">NIK:</strong> {{ $pendeta->nik ?: '-' }}</p>
            <p><strong class="font-medium text-gray-500 w-28 inline-block">Tempat Lahir:</strong> {{ $pendeta->tempat_lahir ?: '-' }}</p>
            <p><strong class="font-medium text-gray-500 w-28 inline-block">Tanggal Lahir:</strong> {{ optional($pendeta->tanggal_lahir)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
            <p><strong class="font-medium text-gray-500 w-28 inline-block">Jenis Kelamin:</strong> {{ $pendeta->jenis_kelamin ?: '-' }}</p>
            <p><strong class="font-medium text-gray-500 w-28 inline-block">Status Nikah:</strong> {{ $pendeta->status_pernikahan ?: '-' }}</p>
            <p><strong class="font-medium text-gray-500 w-28 inline-block">Pasangan:</strong> {{ $pendeta->nama_pasangan ?: '-' }}</p>
            <p><strong class="font-medium text-gray-500 w-28 inline-block">Telepon:</strong> {{ $pendeta->telepon ?: '-' }}</p>
            <p><strong class="font-medium text-gray-500 w-28 inline-block">Email Akun:</strong> {{ optional($pendeta->user)->email ?: '-' }}</p>
            <div>
                <strong class="font-medium text-gray-500 block mb-1">Alamat:</strong>
                <p class="pl-4 whitespace-pre-line text-gray-700">{{ $pendeta->alamat_domisili ?: '-' }}</p>
            </div>
        </div>

        {{-- Kolom 2: Kependetaan & Kepegawaian --}}
        <div class="space-y-3 bg-gray-50 shadow-sm rounded-lg p-5 border border-gray-200">
             <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Kependetaan & Kepegawaian</h3>
             <p><strong class="font-medium text-gray-500 w-32 inline-block">NIPG:</strong> <span class="text-gray-900 font-semibold">{{ $pendeta->nipg }}</span></p>
             <p><strong class="font-medium text-gray-500 w-32 inline-block">Tgl Tahbisan:</strong> {{ optional($pendeta->tanggal_tahbisan)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
             <p><strong class="font-medium text-gray-500 w-32 inline-block">Tempat Tahbisan:</strong> {{ $pendeta->tempat_tahbisan ?: '-' }}</p>
             <p><strong class="font-medium text-gray-500 w-32 inline-block">No SK Pendeta:</strong> {{ $pendeta->nomor_sk_kependetaan ?: '-' }}</p>
             <hr class="my-2 border-gray-200">
             <p><strong class="font-medium text-gray-500 w-32 inline-block">Status Pegawai:</strong> <span class="font-medium text-blue-700">{{ $pendeta->status_kepegawaian }}</span></p>
             <p><strong class="font-medium text-gray-500 w-32 inline-block">Tgl Masuk GPI:</strong> {{ optional($pendeta->tanggal_mulai_masuk_gpi)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
             <p><strong class="font-medium text-gray-500 w-32 inline-block">Gol./Pangkat:</strong> {{ $pendeta->golongan_pangkat_terakhir ?: '-' }}</p>
             <hr class="my-2 border-gray-200">
             <p><strong class="font-medium text-gray-500 w-32 inline-block">Pend. Teologi:</strong> {{ $pendeta->pendidikan_teologi_terakhir ?: '-' }}</p>
             <p><strong class="font-medium text-gray-500 w-32 inline-block">Institusi:</strong> {{ $pendeta->institusi_pendidikan_teologi ?: '-' }}</p>
        </div>

        {{-- Kolom 3: Penempatan & Catatan --}}
        <div class="space-y-3 bg-gray-50 shadow-sm rounded-lg p-5 border border-gray-200">
            <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Penempatan Saat Ini</h3>
            <p><strong class="font-medium text-gray-500 w-36 inline-block">Klasis Penempatan:</strong> <span class="text-gray-900 font-semibold">{{ optional($pendeta->klasisPenempatan)->nama_klasis ?? '-' }}</span></p>
            <p><strong class="font-medium text-gray-500 w-36 inline-block">Jemaat Penempatan:</strong> <span class="text-gray-900 font-semibold">{{ optional($pendeta->jemaatPenempatan)->nama_jemaat ?? '-' }}</span></p>
            <p><strong class="font-medium text-gray-500 w-36 inline-block">Jabatan Saat Ini:</strong> {{ $pendeta->jabatan_saat_ini ?: '-' }}</p>
            <p><strong class="font-medium text-gray-500 w-36 inline-block">Tgl Mulai Jabatan:</strong> {{ optional($pendeta->tanggal_mulai_jabatan_saat_ini)->isoFormat('DD MMMM YYYY') ?: '-' }}</p>
             <hr class="my-2 border-gray-200">
             <div>
                <strong class="font-medium text-gray-500 block mb-1">Catatan:</strong>
                <p class="whitespace-pre-line text-gray-700">{{ $pendeta->catatan ?: '-' }}</p>
             </div>
        </div>
    </div>

    {{-- Riwayat Mutasi --}}
    <div class="mt-8 bg-white shadow rounded-lg p-6 md:p-8 border border-gray-200">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Mutasi</h3>
            {{-- Tombol tambah sudah di atas --}}
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-600 uppercase">Tgl SK</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600 uppercase">No SK</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600 uppercase">Jenis</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600 uppercase">Asal</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600 uppercase">Tujuan</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600 uppercase">Tgl Efektif</th>
                        <th class="px-4 py-2 text-right font-medium text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        // Eager load relasi untuk tabel mutasi
                        $pendeta->loadMissing([
                            'mutasiHistory' => function ($query) {
                                $query->with(['asalKlasis', 'asalJemaat', 'tujuanKlasis', 'tujuanJemaat'])
                                      ->orderBy('tanggal_sk', 'desc'); // Urutkan lagi di sini jika perlu
                            }
                        ]);
                    @endphp
                    @forelse ($pendeta->mutasiHistory as $mutasi)
                    <tr class="hover:bg-gray-50"> {{-- Tambahkan hover effect --}}
                        <td class="px-4 py-2 whitespace-nowrap text-gray-600">{{ $mutasi->tanggal_sk->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-gray-600">{{ $mutasi->nomor_sk }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-gray-700">{{ $mutasi->jenis_mutasi }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-gray-600">
                            {{ optional($mutasi->asalJemaat)->nama_jemaat ?? (optional($mutasi->asalKlasis)->nama_klasis ?? '-') }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-gray-600">
                             {{ optional($mutasi->tujuanJemaat)->nama_jemaat ?? (optional($mutasi->tujuanKlasis)->nama_klasis ?? '-') }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-gray-600">{{ optional($mutasi->tanggal_efektif)->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-right font-medium">
                            {{-- Jika Anda mengaktifkan route resource 'mutasi' --}}
                            {{-- <a href="{{ route('admin.mutasi.show', $mutasi->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2" title="Lihat Detail Mutasi"><i class="fas fa-eye"></i></a> --}}
                            {{-- @hasanyrole('Super Admin|Admin Bidang 3')
                            <a href="{{ route('admin.mutasi.edit', $mutasi->id) }}" class="text-blue-600 hover:text-blue-900 mr-2" title="Edit Mutasi"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.mutasi.destroy', $mutasi->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus riwayat mutasi ini?')"> @csrf @method('DELETE') <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus Mutasi"><i class="fas fa-trash"></i></button> </form>
                            @endhasanyrole --}}
                            <span class="text-gray-400 italic text-xs">Riwayat</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-500 italic">
                            Belum ada riwayat mutasi untuk pendeta ini.
                        </td>
                    </tr>
                    @endforelse {{-- <<< Ini baris penutup @forelse --}}
                </tbody>
            </table>
        </div>
         {{-- Pagination untuk riwayat mutasi (jika Anda load dengan paginate di controller) --}}
         {{-- <div class="mt-4"> {{ $mutasiHistory->links() }} </div> --}} {{-- Ganti $mutasiHistory dengan variabel paginator jika berbeda --}}
    </div>

</div> {{-- Penutup div utama content --}}

{{-- Style umum --}}
@push('styles')
<style>
    /* ... (Style dari create mutasi blade) ... */
    .btn-primary { background-color: #3B82F6; color: white; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: background-color 150ms ease-in-out; }
    .btn-primary:hover { background-color: #2563EB; }
    .btn-primary-outline { background-color: white; color: #3B82F6; border: 1px solid #3B82F6; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: background-color 150ms ease-in-out; }
    .btn-primary-outline:hover { background-color: #EFF6FF; } /* bg-blue-50 */
    .btn-secondary { background-color: #E5E7EB; color: #1F2937; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: background-color 150ms ease-in-out; }
    .btn-secondary:hover { background-color: #D1D5DB; }
    .flash-message { animation: fadeOut 5s forwards; }
    @keyframes fadeOut { 0% { opacity: 1; } 90% { opacity: 1; } 100% { opacity: 0; display: none; } }
</style>
@endpush

{{-- End Section Content --}}
@endsection