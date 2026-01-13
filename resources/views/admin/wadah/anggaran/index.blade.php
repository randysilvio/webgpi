@extends('admin.layout')

@section('title', 'Keuangan (RAB)')
@section('header-title', 'Anggaran & Realisasi Wadah')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div class="text-gray-700 text-sm">
            Kelola Rencana Anggaran Belanja (RAB) dan catat transaksi harian.
        </div>
        <a href="{{ route('admin.wadah.anggaran.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm transition">
            <i class="fas fa-plus mr-2"></i> Buat Pos Anggaran
        </a>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
        <form method="GET" action="{{ route('admin.wadah.anggaran.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tahun</label>
                <select name="tahun" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-primary focus:ring-primary">
                    <option value="">- Semua -</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Wadah</label>
                <select name="wadah" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-primary focus:ring-primary">
                    <option value="">- Semua -</option>
                    @foreach($jenisWadahs as $w)
                        <option value="{{ $w->id }}" {{ request('wadah') == $w->id ? 'selected' : '' }}>{{ $w->nama_wadah }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tingkat</label>
                <select name="tingkat" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-primary focus:ring-primary">
                    <option value="">- Semua -</option>
                    <option value="sinode" {{ request('tingkat') == 'sinode' ? 'selected' : '' }}>Sinode</option>
                    <option value="klasis" {{ request('tingkat') == 'klasis' ? 'selected' : '' }}>Klasis</option>
                    <option value="jemaat" {{ request('tingkat') == 'jemaat' ? 'selected' : '' }}>Jemaat</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Jenis</label>
                <select name="jenis" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-primary focus:ring-primary">
                    <option value="">- Semua -</option>
                    <option value="penerimaan" {{ request('jenis') == 'penerimaan' ? 'selected' : '' }}>Penerimaan</option>
                    <option value="pengeluaran" {{ request('jenis') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm h-[38px] transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pos Anggaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target (Rp)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Realisasi (Rp)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capaian</th>
                        <th class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($anggarans as $a)
                        @php
                            $persen = $a->jumlah_target > 0 ? ($a->jumlah_realisasi / $a->jumlah_target) * 100 : 0;
                            $color = $persen >= 100 ? 'green' : ($persen >= 50 ? 'yellow' : 'red');
                            if($a->jenis_anggaran == 'pengeluaran') {
                                $color = $persen > 100 ? 'red' : ($persen >= 80 ? 'yellow' : 'green');
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-bold">{{ $a->nama_pos_anggaran }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    {{ $a->tahun_anggaran }} | {{ $a->jenisWadah->nama_wadah }} | {{ strtoupper($a->tingkat) }}
                                    @if($a->programKerja)
                                        <span class="text-blue-600 block mt-0.5"><i class="fas fa-link mr-1"></i> {{ Str::limit($a->programKerja->nama_program, 30) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $a->jenis_anggaran == 'penerimaan' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                    {{ ucfirst($a->jenis_anggaran) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-mono">{{ number_format($a->jumlah_target, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm font-mono font-bold">{{ number_format($a->jumlah_realisasi, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm w-1/6">
                                <div class="flex items-center">
                                    <span class="mr-2 text-xs font-bold text-{{ $color }}-600">{{ round($persen) }}%</span>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-{{ $color }}-500 h-2.5 rounded-full" style="width: {{ min($persen, 100) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                <a href="{{ route('admin.wadah.anggaran.show', $a->id) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="Transaksi & Detail"><i class="fas fa-list-alt"></i></a>
                                <a href="{{ route('admin.wadah.anggaran.edit', $a->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.wadah.anggaran.destroy', $a->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus pos anggaran ini? Transaksi terkait juga akan terhapus.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">Belum ada pos anggaran yang dibuat.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-100">{{ $anggarans->links() }}</div>
        </div>
    </div>
@endsection