@extends('layouts.app')

@section('title', 'Manajemen Layanan')

@section('content')
    <x-admin-index 
        title="Manajemen Layanan" 
        subtitle="Kelola daftar layanan holistik yang ditampilkan di halaman depan."
        create-route="{{ route('admin.services.create') }}"
        create-label="Tambah Layanan"
        :pagination="$services"
    >
        {{-- SLOT TABLE HEAD --}}
        <x-slot name="tableHead">
            <th class="px-6 py-4 w-16 text-center">Urutan</th>
            <th class="px-6 py-4">Judul Layanan</th>
            <th class="px-6 py-4">Tema Warna</th>
            <th class="px-6 py-4">Ikon</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- LOOP DATA --}}
        @forelse ($services as $service)
            <tr class="hover:bg-slate-50 transition">
                <x-td class="text-center">
                    <span class="bg-slate-100 text-slate-600 font-bold px-2 py-1 rounded text-xs">
                        {{ $service->order }}
                    </span>
                </x-td>
                <x-td class="font-bold text-slate-800">
                    {{ $service->title }}
                </x-td>
                <x-td>
                    {{-- Badge Warna --}}
                    @php
                        $colorClass = match($service->color_theme) {
                            'green' => 'bg-green-100 text-green-700',
                            'orange' => 'bg-orange-100 text-orange-700',
                            'purple' => 'bg-purple-100 text-purple-700',
                            'red' => 'bg-red-100 text-red-700',
                            'indigo' => 'bg-indigo-100 text-indigo-700',
                            default => 'bg-blue-100 text-blue-700',
                        };
                    @endphp
                    <span class="{{ $colorClass }} px-2 py-1 rounded text-xs font-bold capitalize">
                        {{ $service->color_theme }}
                    </span>
                </x-td>
                <x-td class="text-slate-500 capitalize">
                    @if($service->icon)
                        <i class="fas fa-{{ $service->icon == 'hands-helping' ? 'hand-holding-heart' : $service->icon }} mr-2"></i> 
                        {{ $service->icon }}
                    @else
                        -
                    @endif
                </x-td>
                <x-td class="text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.services.edit', $service) }}" class="text-slate-400 hover:text-yellow-600 transition">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus layanan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </x-td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data layanan.</td>
            </tr>
        @endforelse

    </x-admin-index>
@endsection