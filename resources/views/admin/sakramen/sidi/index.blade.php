@extends('admin.layout')

@section('title', 'Buku Besar Sidi')
@section('header-title', 'Registrasi: Sakramen Sidi')

@section('content')
<div class="space-y-6">

    {{-- 1. Blok Validasi Error & Notifikasi Sukses --}}
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 shadow-md rounded-lg">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span class="font-bold uppercase text-xs tracking-wider">Terjadi Kesalahan Input</span>
            </div>
            <ul class="text-xs list-disc ml-8">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-md rounded-lg flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- 2. Form Input Sidi --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-sm font-black text-gray-800 mb-6 uppercase tracking-widest border-b pb-2">
            <i class="fas fa-bible mr-2 text-primary"></i> Pencatatan Pengakuan Iman (Sidi)
        </h3>
        <form action="{{ route('admin.sakramen.sidi.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1 tracking-wider">Pilih Warga Jemaat</label>
                <select name="anggota_jemaat_id" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary select2">
                    <option value="">-- Cari Nama Anggota --</option>
                    @foreach($anggotaTanpaSidi as $a)
                        <option value="{{ $a->id }}">{{ $a->nama_lengkap }} ({{ $a->jemaat->nama_jemaat }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">No. Akta Sidi</label>
                <input type="text" name="no_akta_sidi" required placeholder="Contoh: 045/SD/2023" class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Tanggal Sidi</label>
                <input type="date" name="tanggal_sidi" required class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Tempat / Gedung Gereja</label>
                <input type="text" name="tempat_sidi" required class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Pendeta Pelayan</label>
                <input type="text" name="pendeta_pelayan" required class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="bg-primary text-white px-8 py-3 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-blue-800 transition shadow-lg flex items-center transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i> Simpan Data Sidi
                </button>
            </div>
        </form>
    </div>

    {{-- 3. Tabel Register Sidi --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <h4 class="text-xs font-black text-gray-700 uppercase tracking-tighter">Arsip Digital Sidi</h4>
            {{-- Form Search Optional (Jika Controller mendukung) --}}
            <form method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..." class="pl-10 pr-4 py-2 border-gray-300 rounded-lg text-xs w-64 focus:ring-primary">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
            </form>
        </div>
        
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-500 border-b">
                <tr>
                    <th class="px-6 py-4">Informasi Akta</th>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">Gereja / Klasis</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sidi as $s)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="block font-black text-primary text-xs">{{ $s->no_akta_sidi }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">{{ \Carbon\Carbon::parse($s->tanggal_sidi)->isoFormat('D MMM Y') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="block font-bold text-gray-900 uppercase not-italic">{{ $s->anggotaJemaat->nama_lengkap }}</span>
                        <span class="text-[10px] text-gray-500 font-medium italic">Layanan: {{ $s->pendeta_pelayan }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="block font-medium text-gray-700 text-xs">{{ $s->anggotaJemaat->jemaat->nama_jemaat }}</span>
                        <span class="text-[10px] text-gray-400 uppercase tracking-tighter">Klasis: {{ $s->anggotaJemaat->jemaat->klasis->nama_klasis }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{-- TOMBOL CETAK SURAT SIDI --}}
                        <a href="{{ route('admin.sakramen.sidi.cetak', $s->id) }}" target="_blank" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-800 rounded-lg transition inline-block" title="Cetak Surat Sidi">
                            <i class="fas fa-print"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 uppercase tracking-widest text-xs font-bold">
                        Buku Sidi Masih Kosong
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($sidi->hasPages()) 
            <div class="px-6 py-4 bg-gray-50 border-t">
                {{ $sidi->links() }}
            </div> 
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- Load CSS & JS Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 pada class .select2
        $('.select2').select2({
            width: '100%',
            placeholder: "-- Pilih Data --",
            allowClear: true
        });
    });
</script>
@endpush