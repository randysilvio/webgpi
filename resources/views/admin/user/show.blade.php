@extends('admin.layout')

@section('title', 'Detail User: ' . $user->name)
@section('header-title', 'Detail Pengguna Sistem')

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8 max-w-3xl mx-auto">
    {{-- Header Detail --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4">
        <div class="flex items-center space-x-4">
             <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center text-gray-600">
                <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
            </div>
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">Email: {{ $user->email }}</p>
                <p class="text-sm text-gray-500">
                    Roles:
                    @forelse($user->roles as $role)
                         <span class="ml-1 px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $role->name }}</span>
                    @empty
                         <span class="ml-1 italic text-gray-400">Tidak ada</span>
                    @endforelse
                </p>
            </div>
        </div>
        <div class="mt-3 sm:mt-0 flex space-x-2">
            {{-- @hasrole('Super Admin') --}}
            <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap">
                Edit User
            </a>
            {{-- @endhasrole --}}
            <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow text-sm transition duration-150 ease-in-out whitespace-nowrap">
                &larr; Kembali
            </a>
        </div>
    </div>

    {{-- Grid Detail Data --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 text-sm mt-6">

        {{-- Kolom 1: Relasi --}}
        <div class="space-y-3 bg-white shadow rounded-lg p-6 border">
            <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Relasi Data</h3>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Terhubung ke Pendeta:</strong></p>
             @if($user->pendeta)
                <a href="{{ route('admin.pendeta.show', $user->pendeta_id) }}" class="text-primary hover:underline ml-4">{{ $user->pendeta->nama_lengkap }} (NIPG: {{ $user->pendeta->nipg }})</a>
             @else
                <p class="ml-4 text-gray-500 italic">- Tidak terhubung -</p>
             @endif

             <p><strong class="font-medium text-gray-600 w-32 inline-block">Terhubung ke Klasis:</strong></p>
             @if($user->klasisTugas)
                 <a href="{{ route('admin.klasis.show', $user->klasis_id) }}" class="text-primary hover:underline ml-4">{{ $user->klasisTugas->nama_klasis }}</a>
             @else
                <p class="ml-4 text-gray-500 italic">- Tidak terhubung -</p>
             @endif

             <p><strong class="font-medium text-gray-600 w-32 inline-block">Terhubung ke Jemaat:</strong></p>
             @if($user->jemaatTugas)
                 <a href="{{ route('admin.jemaat.show', $user->jemaat_id) }}" class="text-primary hover:underline ml-4">{{ $user->jemaatTugas->nama_jemaat }}</a>
             @else
                <p class="ml-4 text-gray-500 italic">- Tidak terhubung -</p>
             @endif
        </div>

        {{-- Kolom 2: Info Akun --}}
        <div class="space-y-3 bg-white shadow rounded-lg p-6 border">
             <h3 class="text-base font-semibold text-gray-700 mb-2 border-b pb-1">Info Akun</h3>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">ID User:</strong> {{ $user->id }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Email Terverifikasi:</strong>
                @if($user->email_verified_at)
                    <span class="text-green-600">{{ $user->email_verified_at->isoFormat('DD MMMM YYYY, HH:mm') }}</span>
                @else
                    <span class="text-red-600">Belum</span>
                @endif
             </p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Dibuat pada:</strong> {{ $user->created_at->isoFormat('DD MMMM YYYY, HH:mm') }}</p>
             <p><strong class="font-medium text-gray-600 w-32 inline-block">Update terakhir:</strong> {{ $user->updated_at->isoFormat('DD MMMM YYYY, HH:mm') }}</p>
        </div>
    </div>
</div>
@endsection