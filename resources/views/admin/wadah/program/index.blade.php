@extends('admin.layout')

@section('title', 'Program Kerja')
@section('header-title', 'Program Kerja Wadah Kategorial')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div class="text-gray-700 text-sm">
            Daftar rencana dan realisasi program kerja tahunan.
        </div>
        <a href="{{ route('admin.wadah.program.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm">
            <i class="fas fa-plus mr-2"></i> Buat Program Baru
        </a>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
        <form method="GET" action="{{ route('admin.wadah.program.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tahun</label>
                <select name="tahun" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">- Semua -</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Wadah</label>
                <select name="jenis_wadah_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">- Semua -</option>
                    @foreach($jenisWadahs as $wadah)
                        <option value="{{ $wadah->id }}" {{ request('jenis_wadah_id') == $wadah->id ? 'selected' : '' }}>
                            {{ $wadah->nama_wadah }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tingkat</label>
                <select name="tingkat" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">- Semua -</option>
                    <option value="sinode" {{ request('tingkat') == 'sinode' ? 'selected' : '' }}>Sinode</option>
                    <option value="klasis" {{ request('tingkat') == 'klasis' ? 'selected' : '' }}>Klasis</option>
                    <option value="jemaat" {{ request('tingkat') == 'jemaat' ? 'selected' : '' }}>Jemaat</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Cari Program</label>
                <input type="text" name="search" value="{{ request('search') }}" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Nama Program...">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm h-[38px]">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun & Wadah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tingkat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggaran (Est)</th>
                        <th class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($programs as $p)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-bold">{{ $p->tahun_program }}</div>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">{{ $p->jenisWadah->nama_wadah }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $p->nama_program }}</div>
                                @if($p->parentProgram)
                                    <div class="text-xs text-gray-500 mt-1 flex items-center">
                                        <i class="fas fa-level-up-alt mr-1"></i> Induk: {{ $p->parentProgram->nama_program }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 uppercase">
                                {{ $p->tingkat }}
                                @if($p->tingkat == 'jemaat')
                                    <div class="text-xs normal-case">{{ $p->jemaat->nama_jemaat ?? '-' }}</div>
                                @elseif($p->tingkat == 'klasis')
                                    <div class="text-xs normal-case">{{ $p->klasis->nama_klasis ?? '-' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $p->status_color }}-100 text-{{ $p->status_color }}-800">
                                    {{ $p->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($p->target_anggaran, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                <a href="{{ route('admin.wadah.program.edit', $p->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.wadah.program.destroy', $p->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus program ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-6 text-center text-gray-500">Belum ada program kerja.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $programs->links() }}</div>
        </div>
    </div>
@endsection