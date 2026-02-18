@extends('layouts.app')

@section('title', 'Detail User: ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto">
    
    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="p-6 md:p-8 flex flex-col md:flex-row items-center gap-6">
            <div class="h-24 w-24 rounded-full bg-slate-100 border-4 border-white shadow-lg flex items-center justify-center text-4xl font-black text-slate-300 uppercase">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="text-center md:text-left flex-grow">
                <h1 class="text-2xl font-bold text-slate-800">{{ $user->name }}</h1>
                <p class="text-slate-500 font-medium">{{ $user->email }}</p>
                <div class="mt-3 flex flex-wrap gap-2 justify-center md:justify-start">
                    @forelse($user->roles as $role)
                        <span class="px-3 py-1 bg-slate-800 text-white text-xs font-bold uppercase tracking-wider rounded-full">{{ $role->name }}</span>
                    @empty
                        <span class="text-xs text-slate-400 italic">Tanpa Role</span>
                    @endforelse
                </div>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-bold uppercase shadow-sm transition">
                    <i class="fas fa-edit mr-1"></i> Edit User
                </a>
            </div>
        </div>
    </div>

    {{-- Grid Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- Konteks / Relasi --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">Konteks Wilayah & Penugasan</h3>
            <ul class="space-y-4 text-sm">
                <li class="flex items-start">
                    <span class="w-8 text-center text-slate-400"><i class="fas fa-user-tie"></i></span>
                    <div>
                        <span class="block text-xs font-bold text-slate-500 uppercase">Data Pegawai</span>
                        @if($user->pendeta)
                            <a href="#" class="font-bold text-purple-700 hover:underline">{{ $user->pendeta->nama_lengkap }}</a>
                        @else
                            <span class="text-slate-400 italic">- Tidak terhubung -</span>
                        @endif
                    </div>
                </li>
                <li class="flex items-start">
                    <span class="w-8 text-center text-slate-400"><i class="fas fa-map"></i></span>
                    <div>
                        <span class="block text-xs font-bold text-slate-500 uppercase">Lingkup Klasis</span>
                        @if($user->klasisTugas)
                            <span class="font-bold text-slate-800">{{ $user->klasisTugas->nama_klasis }}</span>
                        @else
                            <span class="text-slate-400 italic">-</span>
                        @endif
                    </div>
                </li>
                <li class="flex items-start">
                    <span class="w-8 text-center text-slate-400"><i class="fas fa-church"></i></span>
                    <div>
                        <span class="block text-xs font-bold text-slate-500 uppercase">Lingkup Jemaat</span>
                        @if($user->jemaatTugas)
                            <span class="font-bold text-slate-800">{{ $user->jemaatTugas->nama_jemaat }}</span>
                        @else
                            <span class="text-slate-400 italic">-</span>
                        @endif
                    </div>
                </li>
            </ul>
        </div>

        {{-- Meta Data --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">Meta Data Akun</h3>
            <ul class="space-y-4 text-sm">
                <li class="flex justify-between">
                    <span class="text-slate-500">ID System</span>
                    <span class="font-mono text-slate-800">#{{ $user->id }}</span>
                </li>
                <li class="flex justify-between">
                    <span class="text-slate-500">Status Email</span>
                    @if($user->email_verified_at)
                        <span class="text-green-600 font-bold text-xs uppercase"><i class="fas fa-check-circle mr-1"></i> Terverifikasi</span>
                    @else
                        <span class="text-red-500 font-bold text-xs uppercase">Belum Verifikasi</span>
                    @endif
                </li>
                <li class="flex justify-between">
                    <span class="text-slate-500">Terdaftar Sejak</span>
                    <span class="font-medium text-slate-800">{{ $user->created_at->isoFormat('D MMM YYYY') }}</span>
                </li>
                <li class="flex justify-between">
                    <span class="text-slate-500">Update Terakhir</span>
                    <span class="font-medium text-slate-800">{{ $user->updated_at->diffForHumans() }}</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.users.index') }}" class="text-slate-500 hover:text-slate-800 text-sm font-medium">
            &larr; Kembali ke Daftar User
        </a>
    </div>
</div>
@endsection