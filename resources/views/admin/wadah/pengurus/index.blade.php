@extends('admin.layout')

@section('title', 'Manajemen Pengurus')

@section('header-title', 'Manajemen Pengurus Wadah Kategorial')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div class="text-gray-700">
            Kelola data pengurus Wadah Kategorial di semua tingkatan.
        </div>
        <a href="{{ route('admin.wadah.pengurus.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm">
            <i class="fas fa-plus mr-2"></i> Tambah Pengurus
        </a>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
        <form method="GET" action="{{ route('admin.wadah.pengurus.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <div>
                <label for="jenis_wadah_id" class="block text-sm font-medium text-gray-700">Wadah</label>
                <select name="jenis_wadah_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                    <option value="">- Semua Wadah -</option>
                    @foreach($jenisWadahs as $wadah)
                        <option value="{{ $wadah->id }}" {{ request('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>
                            {{ $wadah->nama_wadah }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tingkat" class="block text-sm font-medium text-gray-700">Tingkat</label>
                <select name="tingkat" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                    <option value="">- Semua Tingkat -</option>
                    <option value="sinode" {{ request('tingkat') == 'sinode' ? 'selected' : '' }}>Sinode</option>
                    <option value="klasis" {{ request('tingkat') == 'klasis' ? 'selected' : '' }}>Klasis</option>
                    <option value="jemaat" {{ request('tingkat') == 'jemaat' ? 'selected' : '' }}>Jemaat</option>
                </select>
            </div>

            <div>
                <label for="klasis_id" class="block text-sm font-medium text-gray-700">Klasis</label>
                <select name="klasis_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                    <option value="">- Semua Klasis -</option>
                    @foreach($klasisList as $klasis)
                        <option value="{{ $klasis->id }}" {{ request('klasis_id') == $klasis->id ? 'selected' : '' }}>
                            {{ $klasis->nama_klasis }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <div class="w-full">
                    <label for="search" class="block text-sm font-medium text-gray-700">Cari Nama/Jabatan</label>
                    <input id="search" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Jabatan..." />
                </div>
                <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm h-[38px]">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pengurus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan & Wadah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lingkup Pelayanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pengurus as $p)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $p->anggotaJemaat->nama ?? 'Non-Anggota/Manual' }}
                                </div>
                                @if($p->nomor_sk)
                                    <div class="text-xs text-gray-500">SK: {{ $p->nomor_sk }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-semibold">{{ $p->jabatan }}</div>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $p->jenisWadah->nama_wadah }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="font-bold uppercase">{{ $p->tingkat }}</div>
                                @if($p->tingkat == 'klasis')
                                    <div class="text-xs">{{ $p->klasis->nama_klasis ?? '-' }}</div>
                                @elseif($p->tingkat == 'jemaat')
                                    <div class="text-xs">{{ $p->jemaat->nama_jemaat ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">({{ $p->klasis->nama_klasis ?? '-' }})</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $p->periode_mulai->format('Y') }} - {{ $p->periode_selesai->format('Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($p->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                <a href="{{ route('admin.wadah.pengurus.edit', $p->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.wadah.pengurus.destroy', $p->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data pengurus.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="p-4">
                {{ $pengurus->links() }}
            </div>
        </div>
    </div>
@endsection