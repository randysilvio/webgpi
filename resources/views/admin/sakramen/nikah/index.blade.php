@extends('admin.layout')

@section('title', 'Buku Besar Pernikahan')
@section('header-title', 'Registrasi: Sakramen Nikah')

@section('content')
<div class="space-y-6">
    
    {{-- Notifikasi --}}
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 shadow-md rounded-lg">
            <ul class="text-xs list-disc ml-8 uppercase font-bold">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-md rounded-lg flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            <span class="text-sm font-semibold uppercase">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Form Registrasi Nikah --}}
    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center mb-6 border-b pb-4">
            <i class="fas fa-ring mr-3 text-primary text-xl"></i>
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Registrasi Pemberkatan Nikah</h3>
                <p class="text-[10px] text-gray-400 font-bold uppercase italic">Sumber data pendeta: Tabel Pegawai (Jenis: Pendeta)</p>
            </div>
        </div>

        <form action="{{ route('admin.sakramen.nikah.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @csrf
            
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-blue-600 uppercase mb-1">Mempelai Pria</label>
                <select name="suami_id" required class="w-full border-gray-300 rounded-lg text-sm select2">
                    <option value="">-- Pilih Nama Anggota --</option>
                    @foreach($pria as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_lengkap }} ({{ $p->jemaat->nama_jemaat ?? '-' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-pink-600 uppercase mb-1">Mempelai Wanita</label>
                <select name="istri_id" required class="w-full border-gray-300 rounded-lg text-sm select2">
                    <option value="">-- Pilih Nama Anggota --</option>
                    @foreach($wanita as $w)
                        <option value="{{ $w->id }}">{{ $w->nama_lengkap }} ({{ $w->jemaat->nama_jemaat ?? '-' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">No. Akta Nikah</label>
                <input type="text" name="no_akta_nikah" required class="w-full border-gray-300 rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Tanggal Nikah</label>
                <input type="date" name="tanggal_nikah" required class="w-full border-gray-300 rounded-lg text-sm">
            </div>

            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Tempat / Gedung Gereja</label>
                <input type="text" name="tempat_nikah" required class="w-full border-gray-300 rounded-lg text-sm">
            </div>

            <div class="md:col-span-4">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Pendeta Pelayan</label>
                <select name="pendeta_pelayan" required class="w-full border-gray-300 rounded-lg text-sm select2">
                    <option value="">-- Pilih Pendeta Pelayan --</option>
                    @foreach($pendetas as $pendeta)
                        {{-- Menggunakan nama_lengkap dari model Pegawai --}}
                        <option value="{{ $pendeta->nama_lengkap }}">{{ $pendeta->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex justify-end pt-4 border-t">
                <button type="submit" class="bg-primary text-white px-10 py-3 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-blue-800 shadow-lg">
                    <i class="fas fa-save mr-2"></i> Simpan & Generate KK Baru
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel Register Pernikahan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-500 border-b">
                <tr>
                    <th class="px-6 py-4">No. Akta & Tgl</th>
                    <th class="px-6 py-4 text-center">Mempelai Pria</th>
                    <th class="px-6 py-4 text-center">Ikon</th>
                    <th class="px-6 py-4 text-center">Mempelai Wanita</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 italic">
                @forelse($nikahs as $n)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="block font-black text-primary text-xs">{{ $n->no_akta_nikah }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">{{ \Carbon\Carbon::parse($n->tanggal_nikah)->isoFormat('D MMM Y') }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="block font-bold text-gray-900 uppercase not-italic text-xs">{{ $n->suami->nama_lengkap ?? 'DATA DIHAPUS' }}</span>
                        <span class="text-[10px] text-gray-500 uppercase">{{ $n->suami?->jemaat?->nama_jemaat ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 text-center text-red-500"><i class="fas fa-heart animate-pulse"></i></td>
                    <td class="px-6 py-4 text-center">
                        <span class="block font-bold text-gray-900 uppercase not-italic text-xs">{{ $n->istri->nama_lengkap ?? 'DATA DIHAPUS' }}</span>
                        <span class="text-[10px] text-gray-500 uppercase">{{ $n->istri?->jemaat?->nama_jemaat ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 text-center not-italic">
                        <div class="flex justify-center space-x-2">
                            <form action="{{ route('admin.sakramen.nikah.destroy', $n->id) }}" method="POST" onsubmit="return confirm('Hapus arsip ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-gray-100 text-red-500 rounded-lg transition" title="Hapus Data"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 uppercase text-xs font-bold">Belum ada data pernikahan tercatat.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($nikahs->hasPages()) <div class="px-6 py-4 bg-gray-50 border-t">{{ $nikahs->links() }}</div> @endif
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%', placeholder: "-- Pilih --" });
    });
</script>
@endpush