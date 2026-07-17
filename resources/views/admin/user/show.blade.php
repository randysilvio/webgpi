@extends('layouts.app')

@section('title', 'Tinjauan Detail Pengguna: ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    {{-- Action Bar --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-blue-800 font-bold text-xs uppercase transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Indeks Pengguna
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wide shadow-sm transition flex items-center">
                <i class="fas fa-edit mr-2"></i> Modifikasi Kredensial
            </a>
        </div>
    </div>

    {{-- Kertas Dokumen Profil --}}
    <div class="bg-white border border-gray-300 rounded shadow-sm overflow-hidden">
        
        <div class="bg-gray-100 border-b-2 border-gray-800 p-6 md:p-8 flex flex-col md:flex-row items-center gap-6">
            <div class="h-24 w-24 rounded bg-white border border-gray-300 shadow-sm flex items-center justify-center text-4xl font-black text-gray-400 uppercase">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="text-center md:text-left flex-grow">
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-widest">{{ $user->name }}</h1>
                <p class="text-sm font-medium text-gray-600 mt-1">{{ $user->email }}</p>
                <div class="mt-3 flex flex-wrap gap-2 justify-center md:justify-start">
                    @forelse($user->roles as $role)
                        <span class="px-3 py-1 bg-blue-900 text-white text-[10px] font-bold uppercase tracking-widest rounded">{{ $role->name }}</span>
                    @empty
                        <span class="text-[10px] text-gray-500 border border-gray-300 px-3 py-1 rounded uppercase font-bold">Akses Terbatas / Kosong</span>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Panel Administratif --}}
            <div>
                <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Status Administratif</h3>
                <ul class="space-y-4 text-sm">
                    <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                        <span class="text-gray-500 text-xs font-bold uppercase">ID Pangkalan Data</span>
                        <span class="font-mono text-gray-900 font-bold">#USR-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </li>
                    <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                        <span class="text-gray-500 text-xs font-bold uppercase">Validasi Email</span>
                        @if($user->email_verified_at)
                            <span class="text-green-700 font-bold text-[10px] uppercase bg-green-100 px-2 py-1 rounded"><i class="fas fa-check mr-1"></i> Terverifikasi</span>
                        @else
                            <span class="text-red-600 font-bold text-[10px] uppercase bg-red-100 px-2 py-1 rounded">Belum Verifikasi</span>
                        @endif
                    </li>
                    <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                        <span class="text-gray-500 text-xs font-bold uppercase">Tanggal Registrasi</span>
                        <span class="font-bold text-gray-800">{{ $user->created_at->isoFormat('D MMMM YYYY') }}</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-gray-500 text-xs font-bold uppercase">Update Terakhir</span>
                        <span class="font-bold text-gray-800">{{ $user->updated_at->diffForHumans() }}</span>
                    </li>
                </ul>
            </div>

            {{-- Panel Penugasan --}}
            <div>
                <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Tautan & Penugasan Wilayah</h3>
                
                @if($user->pegawai || $user->klasis_id || $user->jemaat_id || $user->jenis_wadah_id)
                    <ul class="space-y-4 text-sm">
                        @if($user->pegawai)
                        <li class="bg-blue-50 border border-blue-200 p-3 rounded">
                            <span class="block text-[10px] font-bold text-blue-800 uppercase mb-1">Tautan Induk Kepegawaian</span>
                            <a href="{{ route('admin.kepegawaian.pegawai.show', $user->pegawai->id) }}" class="font-bold text-blue-900 hover:underline">
                                <i class="fas fa-id-card mr-2 text-blue-500"></i> {{ $user->pegawai->nipg }} - {{ $user->pegawai->nama_lengkap }}
                            </a>
                        </li>
                        @endif

                        @if($user->klasis_id)
                        <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                            <span class="text-gray-500 text-xs font-bold uppercase"><i class="fas fa-map-marker-alt w-5 text-center mr-1"></i> Otoritas Klasis</span>
                            <span class="font-bold text-gray-900">{{ $user->klasisTugas->nama_klasis ?? '-' }}</span>
                        </li>
                        @endif

                        @if($user->jemaat_id)
                        <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                            <span class="text-gray-500 text-xs font-bold uppercase"><i class="fas fa-church w-5 text-center mr-1"></i> Otoritas Jemaat</span>
                            <span class="font-bold text-gray-900">{{ $user->jemaatTugas->nama_jemaat ?? '-' }}</span>
                        </li>
                        @endif

                        @if($user->jenis_wadah_id)
                        <li class="flex justify-between items-center">
                            <span class="text-gray-500 text-xs font-bold uppercase"><i class="fas fa-users w-5 text-center mr-1"></i> Otoritas Wadah</span>
                            <span class="font-bold text-gray-900">{{ $user->jenisWadah->nama_wadah ?? '-' }}</span>
                        </li>
                        @endif
                    </ul>
                @else
                    <div class="bg-gray-50 border border-gray-200 p-6 rounded text-center">
                        <i class="fas fa-link text-gray-400 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-600 font-bold uppercase">Akun Independen</p>
                        <p class="text-[10px] text-gray-500 mt-1">Tidak terikat pada struktur kepegawaian atau wilayah tertentu (Tingkat Sinode).</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection