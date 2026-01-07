@extends('admin.layout')

@section('title', 'Buku Besar Baptisan')
@section('header-title', 'Registrasi: Sakramen Baptis')

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

    {{-- 2. Form Registrasi Baru --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-sm font-black text-gray-800 mb-6 uppercase tracking-widest border-b pb-2">
            <i class="fas fa-water mr-2 text-primary"></i> Input Data Baptisan Baru
        </h3>
        <form action="{{ route('admin.sakramen.baptis.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Nama Warga Jemaat</label>
                <select name="anggota_jemaat_id" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary select2">
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($anggotaTanpaBaptis as $a)
                        <option value="{{ $a->id }}">{{ $a->nama_lengkap }} ({{ $a->jemaat->nama_jemaat }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Nomor Akta Baptis</label>
                <input type="text" name="no_akta_baptis" required placeholder="Contoh: 123/BAP/2023" class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Tanggal Layanan</label>
                <input type="date" name="tanggal_baptis" required class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Tempat / Gedung Gereja</label>
                <input type="text" name="tempat_baptis" required class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Pendeta Pelayan</label>
                <input type="text" name="pendeta_pelayan" required class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="bg-primary text-white px-8 py-2.5 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-blue-800 shadow-lg transition-all">
                    Simpan Ke Buku Besar
                </button>
            </div>
        </form>
    </div>

    {{-- 3. Daftar Registrasi (Arsip) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <h4 class="text-xs font-black text-gray-700 uppercase tracking-tighter">Arsip Digital Buku Baptis</h4>
            <form method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau nomor akta..." class="pl-10 pr-4 py-2 border-gray-300 rounded-lg text-xs w-64 focus:ring-primary">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
            </form>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-500 border-b">
                <tr>
                    <th class="px-6 py-4">Informasi Akta</th>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">Pelayanan</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($baptis as $b)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="block font-black text-primary text-xs">{{ $b->no_akta_baptis }}</span>
                        <span class="text-[10px] text-gray-400 italic">{{ \Carbon\Carbon::parse($b->tanggal_baptis)->isoFormat('D MMMM Y') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="block font-bold text-gray-900 uppercase">{{ $b->anggotaJemaat->nama_lengkap }}</span>
                        <span class="text-[10px] text-gray-500">{{ $b->anggotaJemaat->jemaat->nama_jemaat }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="block font-medium text-gray-700 text-xs">{{ $b->pendeta_pelayan }}</span>
                        <span class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $b->tempat_baptis }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{-- TOMBOL CETAK SURAT BAPTIS --}}
                        <a href="{{ route('admin.sakramen.baptis.cetak', $b->id) }}" target="_blank" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-800 rounded-lg transition inline-block" title="Cetak Surat Baptis">
                            <i class="fas fa-print"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center italic text-gray-400 uppercase tracking-widest text-xs">Belum ada data baptisan yang tercatat.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50">
            {{ $baptis->links() }}
        </div>
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
            placeholder: "-- Pilih Anggota --",
            allowClear: true
        });
    });
</script>
@endpush