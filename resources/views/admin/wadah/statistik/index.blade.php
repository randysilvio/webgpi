@extends('admin.layout')

@section('title', 'Statistik Anggota')
@section('header-title', 'Statistik Anggota Wadah Kategorial')

@section('content')
    <div class="mb-6">
        <p class="text-gray-600">
            Data potensi anggota berdasarkan rentang usia dan jenis kelamin dari database Anggota Jemaat.
        </p>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
        <form method="GET" action="{{ route('admin.wadah.statistik.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
            
            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Sinode'))
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Klasis</label>
                    <select name="klasis_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-primary focus:border-primary" onchange="this.form.submit()">
                        <option value="">- Semua Klasis -</option>
                        @foreach($klasisList as $klasis)
                            <option value="{{ $klasis->id }}" {{ request('klasis_id') == $klasis->id ? 'selected' : '' }}>
                                {{ $klasis->nama_klasis }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if(auth()->user()->hasRole(['Super Admin', 'Admin Sinode', 'Admin Klasis']))
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Jemaat</label>
                    <select name="jemaat_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-primary focus:border-primary" onchange="this.form.submit()">
                        <option value="">- Semua Jemaat -</option>
                        @foreach($jemaatList as $jemaat)
                            <option value="{{ $jemaat->id }}" {{ request('jemaat_id') == $jemaat->id ? 'selected' : '' }}>
                                {{ $jemaat->nama_jemaat }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="w-full md:w-auto">
                <a href="{{ route('admin.wadah.statistik.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow-sm inline-block text-sm">
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach($statistik as $stat)
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl border-t-4 
                {{ $stat['wadah']->nama_wadah == 'PAR' ? 'border-green-500' : '' }}
                {{ $stat['wadah']->nama_wadah == 'PP' ? 'border-blue-500' : '' }}
                {{ $stat['wadah']->nama_wadah == 'PERWATA' ? 'border-pink-500' : '' }}
                {{ $stat['wadah']->nama_wadah == 'PERPRI' ? 'border-gray-800' : '' }}
                {{ $stat['wadah']->nama_wadah == 'PERLANSIA' ? 'border-yellow-500' : '' }}
                ">
                <div class="p-6 text-center">
                    <h3 class="text-lg font-bold text-gray-700">{{ $stat['wadah']->nama_wadah }}</h3>
                    <p class="text-xs text-gray-500 mb-4">{{ $stat['keterangan'] }}</p>
                    
                    <div class="text-4xl font-extrabold text-gray-900 my-2">
                        {{ number_format($stat['jumlah']) }}
                    </div>
                    <p class="text-sm text-gray-600">Anggota Potensial</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-sm">
        <i class="fas fa-info-circle mr-1"></i> <strong>Info:</strong> Data di atas dihitung otomatis berdasarkan Tanggal Lahir dan Jenis Kelamin dari database Anggota Jemaat.
    </div>
@endsection