@extends('layouts.app')

@section('title', 'Mata Anggaran')

@section('content')
    <x-admin-index 
        title="Mata Anggaran (COA)" 
        subtitle="Daftar Kode Akun Standar untuk klasifikasi Pendapatan dan Belanja."
        create-route="{{ route('admin.perbendaharaan.mata-anggaran.create') }}"
        create-label="Tambah Akun"
        :pagination="$mataAnggarans"
    >
        {{-- SLOT FILTERS --}}
        <x-slot name="filters">
            <form action="{{ route('admin.perbendaharaan.mata-anggaran.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                {{-- Filter Jenis --}}
                <x-form-select name="jenis" onchange="this.form.submit()">
                    <option value="">- Semua Jenis -</option>
                    <option value="Pendapatan" {{ request('jenis') == 'Pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                    <option value="Belanja" {{ request('jenis') == 'Belanja' ? 'selected' : '' }}>Belanja</option>
                </x-form-select>

                {{-- Search --}}
                <div class="md:col-span-2 relative">
                    <x-form-input name="search" value="{{ request('search') }}" placeholder="Cari Kode atau Nama Akun..." />
                    <button type="submit" class="absolute right-3 top-2 text-slate-400 hover:text-blue-600">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </x-slot>

        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4 w-32">Kode Akun</th>
            <th class="px-6 py-4">Nama Mata Anggaran</th>
            <th class="px-6 py-4 text-center">Jenis</th>
            <th class="px-6 py-4">Kelompok</th>
            <th class="px-6 py-4 text-center">Status</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse($mataAnggarans as $ma)
            <tr class="hover:bg-slate-50 transition group">
                <x-td>
                    <span class="font-mono font-bold text-blue-700 bg-blue-50 px-2 py-1 rounded text-xs border border-blue-100">
                        {{ $ma->kode }}
                    </span>
                </x-td>
                <x-td>
                    <div class="font-bold text-slate-800 text-sm">{{ $ma->nama_mata_anggaran }}</div>
                    @if($ma->deskripsi)
                        <div class="text-[10px] text-slate-400 mt-0.5 truncate max-w-xs">{{ $ma->deskripsi }}</div>
                    @endif
                </x-td>
                <x-td class="text-center">
                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase border {{ $ma->jenis == 'Pendapatan' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-red-100 text-red-700 border-red-200' }}">
                        {{ $ma->jenis }}
                    </span>
                </x-td>
                <x-td>
                    <span class="text-xs text-slate-600 font-medium">{{ $ma->kelompok ?? '-' }}</span>
                </x-td>
                <x-td class="text-center">
                    @if($ma->is_active)
                        <i class="fas fa-check-circle text-green-500" title="Aktif"></i>
                    @else
                        <i class="fas fa-times-circle text-slate-300" title="Tidak Aktif"></i>
                    @endif
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.perbendaharaan.mata-anggaran.edit', $ma->id) }}" class="text-slate-400 hover:text-yellow-600 transition" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        @hasanyrole('Super Admin')
                        <form action="{{ route('admin.perbendaharaan.mata-anggaran.destroy', $ma->id) }}" method="POST" class="inline" onsubmit="return confirm('Non-aktifkan akun ini? Data historis tidak akan hilang, namun akun tidak bisa dipilih untuk transaksi baru.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Hapus / Non-aktifkan">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                        @endhasanyrole
                    </div>
                </x-td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada mata anggaran yang terdaftar.</td>
            </tr>
        @endforelse

        {{-- INFO FOOTER --}}
        <div class="mt-4 bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
            <div class="text-xs text-blue-800">
                <strong>Catatan:</strong> Mata Anggaran (COA) ini menjadi referensi utama dalam penyusunan <strong>Rencana APB</strong> dan pencatatan <strong>Buku Kas Umum</strong>. Pastikan kode akun sesuai dengan standarisasi Sinode.
            </div>
        </div>

    </x-admin-index>
@endsection