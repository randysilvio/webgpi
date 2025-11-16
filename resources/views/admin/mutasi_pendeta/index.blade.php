@extends('admin.layout')

@section('title', 'Riwayat Mutasi Pendeta')
@section('header-title', 'Riwayat Mutasi Pendeta')

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8">
    <div class="flex justify-between items-center mb-6 border-b pb-3">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Riwayat Mutasi</h2>
        {{-- Tombol Tambah Mutasi mungkin lebih cocok di halaman detail Pendeta --}}
    </div>

    {{-- Filter (Opsional) --}}
    {{-- <form method="GET" action="{{ route('admin.mutasi.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="text" name="search" placeholder="Cari No SK / Nama Pendeta..." value="{{ request('search') }}" class="input-field">
        <select name="klasis_id" class="input-field"> Klasis... </select>
        <select name="jenis_mutasi" class="input-field"> Jenis... </select>
        <button type="submit" class="btn-primary col-start-4">Filter</button>
    </form> --}}

    @if (session('success'))
        <div class="flash-message mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl SK</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No SK</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendeta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tujuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Efektif</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($mutasiHistory as $mutasi)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $mutasi->tanggal_sk->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $mutasi->nomor_sk }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <a href="{{ route('admin.pendeta.show', $mutasi->pendeta_id) }}" class="text-blue-600 hover:underline">
                            {{ $mutasi->pendeta->nama_lengkap ?? 'N/A' }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $mutasi->jenis_mutasi }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $mutasi->asalJemaat->nama_jemaat ?? ($mutasi->asalKlasis->nama_klasis ?? '-') }}
                    </td>
                     <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $mutasi->tujuanJemaat->nama_jemaat ?? ($mutasi->tujuanKlasis->nama_klasis ?? '-') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ optional($mutasi->tanggal_efektif)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        {{-- <a href="{{ route('admin.mutasi.show', $mutasi->id) }}" class="text-gray-600 hover:text-gray-900 mr-3">Detail</a> --}}
                        {{-- <a href="{{ route('admin.mutasi.edit', $mutasi->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a> --}}
                        {{-- <form action="{{ route('admin.mutasi.destroy', $mutasi->id) }}" method="POST" class="inline"> @csrf @method('DELETE') <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin?')">Hapus</button> </form> --}}
                         <span class="text-gray-400 italic">No actions</span> {{-- Aksi mungkin tidak relevan jika riwayat --}}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-10 text-gray-500">
                        Belum ada riwayat mutasi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-6">
        {{-- $mutasiHistory->links() --}} {{-- Aktifkan jika $mutasiHistory adalah Paginator --}}
    </div>

</div>
{{-- Style & Flash Message --}}
@push('styles') <style> /* ... (style dari create.blade.php) ... */ </style> @endpush
@endsection