@extends('admin.layout')

@section('title', 'Manajemen Penatua & Diaken')
@section('header-title', 'Struktur: Pejabat Gerejawi')

@section('content')
<div class="space-y-6">
    {{-- Pesan Error & Validasi --}}
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 shadow-md rounded-lg" role="alert">
            <p class="font-bold text-sm">Gagal Menambah Pejabat:</p>
            <ul class="text-xs list-disc ml-8">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Registrasi Pejabat --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-sm font-black text-gray-800 mb-6 uppercase tracking-widest border-b pb-2">
            <i class="fas fa-user-shield mr-2 text-primary"></i> Registrasi Pejabat Gerejawi Baru
        </h3>
        <form action="{{ route('admin.tata-gereja.pejabat.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Nama Warga Jemaat</label>
                <select name="anggota_jemaat_id" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary">
                    <option value="">-- Cari Nama Anggota --</option>
                    @foreach($anggotas as $a)
                        <option value="{{ $a->id }}" {{ old('anggota_jemaat_id') == $a->id ? 'selected' : '' }}>
                            {{ $a->nama_lengkap }} ({{ $a->jemaat->nama_jemaat }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Jabatan</label>
                <select name="jabatan" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary">
                    <option value="Penatua" {{ old('jabatan') == 'Penatua' ? 'selected' : '' }}>Penatua</option>
                    <option value="Diaken" {{ old('jabatan') == 'Diaken' ? 'selected' : '' }}>Diaken</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Status Aktif</label>
                <select name="status_aktif" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary">
                    <option value="Aktif">Aktif</option>
                    <option value="Demisioner">Demisioner</option>
                    <option value="Emeritus">Emeritus</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Periode Mulai</label>
                <input type="text" name="periode_mulai" value="{{ old('periode_mulai') }}" required placeholder="Contoh: 2022" class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Periode Selesai</label>
                <input type="text" name="periode_selesai" value="{{ old('periode_selesai') }}" required placeholder="Contoh: 2027" class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Nomor SK Pelantikan</label>
                <input type="text" name="no_sk_pelantikan" value="{{ old('no_sk_pelantikan') }}" placeholder="Opsional" class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="bg-primary text-white px-8 py-2.5 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-blue-800 shadow-lg transition-all">
                    Simpan Data Pejabat
                </button>
            </div>
        </form>
    </div>

    {{-- Daftar Pejabat --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-500 border-b">
                <tr>
                    <th class="px-6 py-4">Pejabat</th>
                    <th class="px-6 py-4">Jabatan</th>
                    <th class="px-6 py-4">Periode Bakti</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pejabats as $p)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="block font-bold text-gray-900 uppercase text-xs">{{ $p->anggotaJemaat->nama_lengkap }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $p->anggotaJemaat->jemaat->nama_jemaat }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-[10px] font-black uppercase">{{ $p->jabatan }}</span>
                    </td>
                    <td class="px-6 py-4 text-xs font-medium text-gray-600">
                        {{ $p->periode_mulai }} — {{ $p->periode_selesai }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 {{ $p->status_aktif == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }} rounded-full text-[9px] font-black uppercase">
                            {{ $p->status_aktif }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                         <form action="{{ route('admin.tata-gereja.pejabat.destroy', $p->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600" onclick="return confirm('Hapus data pejabat?')"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 uppercase tracking-widest text-xs font-bold italic">Belum ada pejabat gerejawi terdaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50">
            {{ $pejabats->links() }}
        </div>
    </div>
</div>
@endsection