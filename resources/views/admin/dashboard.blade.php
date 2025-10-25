{{-- resources/views/admin/dashboard.blade.php --}}
@extends('admin.layout') {{-- Mewarisi layout admin --}}

@section('title', 'Dashboard') {{-- Mengisi judul halaman --}}
@section('header-title', 'Dashboard Utama') {{-- Mengisi judul header --}}

@section('content') {{-- Mulai mengisi konten utama --}}

    {{-- Pesan Selamat Datang & Info User --}}
    <div class="p-4 md:p-6 bg-white rounded-lg shadow-md mb-6 border-l-4 border-primary">
        {{-- Gunakan null coalescing operator (??) untuk fallback jika $user null --}}
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Selamat Datang, {{ $user->name ?? 'Pengguna' }}!</h2>
        @if ($user) {{-- Hanya tampilkan info role jika user ada --}}
            <p class="text-gray-600 text-sm mb-1">Anda login sebagai:
                @forelse ($user->getRoleNames() as $role)
                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $role }}</span>
                @empty
                    <span class="text-gray-500 italic">(Tidak ada role)</span>
                @endforelse
            </p>
            {{-- Tampilkan info Klasis/Jemaat jika ada --}}
            @if ($user->klasisTugas)
                <p class="text-gray-600 text-sm">Klasis Tugas: <span class="font-medium">{{ $user->klasisTugas->nama_klasis }}</span></p>
            @endif
            @if ($user->jemaatTugas)
                <p class="text-gray-600 text-sm">Jemaat Tugas: <span class="font-medium">{{ $user->jemaatTugas->nama_jemaat }}</span></p>
            @endif
        @endif
    </div>

    {{-- Ringkasan Statistik (Menggunakan data dari $stats) --}}
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Ringkasan Statistik</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        {{-- Card Anggota Jemaat --}}
        <div class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 border-l-4 border-primary">
            <div class="p-3 rounded-full bg-blue-100 text-primary"><svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg></div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Anggota Aktif</p>
                {{-- Tampilkan statistik jika ada --}}
                <p class="text-2xl font-bold text-gray-900">
                    {{ $stats['total_anggota'] ?? ($stats['total_anggota_di_klasis'] ?? ($stats['total_anggota_di_jemaat'] ?? '-')) }}
                </p>
            </div>
        </div>
        {{-- Card Gereja Lokal (Jemaat) --}}
        <div class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 border-l-4 border-accent"> <div class="p-3 rounded-full bg-green-100 text-accent"><svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" /></svg></div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Jemaat</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_jemaat'] ?? ($stats['total_jemaat_di_klasis'] ?? '-') }}</p>
            </div>
        </div>
        {{-- Card Pendeta --}}
         <div class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 border-l-4 border-secondary"> <div class="p-3 rounded-full bg-orange-100 text-secondary"><svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg></div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Pendeta Aktif</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pendeta'] ?? '-' }}</p>
            </div>
        </div>
        {{-- Card Klasis --}}
         <div class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 border-l-4 border-purple-500"> <div class="p-3 rounded-full bg-purple-100 text-purple-600"><svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6M9 11.25h6m-6 4.5h6M6.75 21v-2.25a2.25 2.25 0 0 1 2.25-2.25h6a2.25 2.25 0 0 1 2.25 2.25V21" /></svg></div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Klasis</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_klasis'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Panel Aksi Cepat / Informasi per Role --}}
    <h2 class="text-xl font-semibold text-gray-800 mb-4 mt-8">Akses Cepat & Informasi</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- === KONTEN KHUSUS SUPER ADMIN === --}}
        @hasrole('Super Admin')
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-6 rounded-lg shadow-lg text-white">
            <h3 class="text-lg font-semibold mb-3 border-b border-purple-300 pb-2">Panel Super Admin</h3>
            <p class="text-sm mb-4">Akses penuh ke semua modul sistem.</p>
            <div class="space-y-2 text-sm">
                <a href="{{ route('admin.users.index') }}" class="block hover:underline">- Manajemen User & Roles</a>
                <a href="{{ route('admin.settings') }}" class="block hover:underline">- Pengaturan Situs</a>
                <a href="{{ route('admin.pendeta.index') }}" class="block hover:underline">- Kelola Data Pendeta</a>
                <a href="{{ route('admin.klasis.index') }}" class="block hover:underline">- Kelola Data Klasis</a>
            </div>
        </div>
        @endhasrole

        {{-- === KONTEN KHUSUS ADMIN BIDANG 3 === --}}
        @hasrole('Admin Bidang 3')
        <div class="bg-gradient-to-r from-blue-500 to-cyan-500 p-6 rounded-lg shadow-lg text-white">
            <h3 class="text-lg font-semibold mb-3 border-b border-blue-300 pb-2">Panel Admin Bidang 3</h3>
            <p class="text-sm mb-4">Akses ke modul data kepegawaian dan struktur gereja.</p>
            <div class="space-y-2 text-sm">
                <a href="{{ route('admin.pendeta.index') }}" class="block hover:underline">- Manajemen Pendeta</a>
                <a href="{{ route('admin.klasis.index') }}" class="block hover:underline">- Manajemen Klasis</a>
                <a href="{{ route('admin.jemaat.index') }}" class="block hover:underline">- Manajemen Jemaat</a>
                <a href="{{ route('admin.anggota-jemaat.index') }}" class="block hover:underline">- Lihat/Ekspor Anggota</a>
            </div>
        </div>
        @endhasrole

        {{-- === KONTEN KHUSUS ADMIN BIDANG 4 === --}}
        @hasrole('Admin Bidang 4')
        <div class="bg-gradient-to-r from-emerald-500 to-green-600 p-6 rounded-lg shadow-lg text-white">
            <h3 class="text-lg font-semibold mb-3 border-b border-emerald-300 pb-2">Panel Admin Bidang 4</h3>
            <p class="text-sm mb-4">Akses ke modul konten website dan komunikasi.</p>
            <div class="space-y-2 text-sm">
                <a href="{{ route('admin.posts.index') }}" class="block hover:underline">- Manajemen Berita</a>
                <a href="{{ route('admin.services.index') }}" class="block hover:underline">- Manajemen Layanan</a>
                <a href="{{ route('admin.messages.index') }}" class="block hover:underline">- Pesan Masuk</a>
                <a href="{{ route('admin.settings') }}" class="block hover:underline">- Pengaturan Situs</a>
            </div>
        </div>
        @endhasrole

         {{-- === KONTEN KHUSUS ADMIN KLASIS === --}}
        @hasrole('Admin Klasis')
        <div class="bg-gradient-to-r from-sky-500 to-blue-500 p-6 rounded-lg shadow-lg text-white">
            <h3 class="text-lg font-semibold mb-3 border-b border-sky-300 pb-2">Panel Admin Klasis</h3>
             @if ($user?->klasisTugas)
                <p class="mb-1 text-sm">Klasis: <span class="font-medium">{{ $user->klasisTugas->nama_klasis }}</span></p>
             @else
                 <p class="mb-1 text-yellow-300 font-semibold text-sm">Peringatan: Akun Anda belum terhubung ke Klasis.</p>
             @endif
            <p class="text-sm mb-4">Kelola data Jemaat dan Anggota Jemaat dalam Klasis Anda.</p>
            <div class="space-y-2 text-sm">
                <a href="{{ route('admin.jemaat.index') }}" class="block hover:underline">- Manajemen Jemaat</a>
                <a href="{{ route('admin.anggota-jemaat.index') }}" class="block hover:underline">- Manajemen Anggota Jemaat</a>
                @if ($user?->klasisTugas)
                 <a href="{{ route('admin.klasis.edit', $user->klasis_id) }}" class="block hover:underline">- Edit Info Kontak Klasis</a>
                @endif
            </div>
        </div>
        @endhasrole

        {{-- === KONTEN KHUSUS ADMIN JEMAAT === --}}
        @hasrole('Admin Jemaat')
        <div class="bg-gradient-to-r from-teal-500 to-cyan-600 p-6 rounded-lg shadow-lg text-white">
            <h3 class="text-lg font-semibold mb-3 border-b border-teal-300 pb-2">Panel Admin Jemaat</h3>
             @if ($user?->jemaatTugas)
                <p class="mb-1 text-sm">Jemaat: <span class="font-medium">{{ $user->jemaatTugas->nama_jemaat }}</span></p>
                @if ($user->jemaatTugas->klasis)
                 <p class="mb-1 text-xs opacity-90">Klasis: {{ $user->jemaatTugas->klasis->nama_klasis }}</p>
                @endif
             @else
                 <p class="mb-1 text-yellow-300 font-semibold text-sm">Peringatan: Akun Anda belum terhubung ke Jemaat.</p>
             @endif
            <p class="text-sm mb-4">Kelola data Anggota Jemaat dalam Jemaat Anda.</p>
            <div class="space-y-2 text-sm">
                <a href="{{ route('admin.anggota-jemaat.index') }}" class="block hover:underline">- Manajemen Anggota Jemaat</a>
                @if ($user?->jemaatTugas)
                <a href="{{ route('admin.jemaat.edit', $user->jemaat_id) }}" class="block hover:underline">- Edit Data Jemaat Anda</a>
                @endif
                <a href="{{ route('admin.anggota-jemaat.create') }}" class="block hover:underline">- Tambah Anggota Baru</a>
            </div>
        </div>
        @endhasrole

        {{-- === KONTEN KHUSUS PENDETA === --}}
        @hasrole('Pendeta')
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 p-6 rounded-lg shadow-lg text-white">
            <h3 class="text-lg font-semibold mb-3 border-b border-gray-600 pb-2">Panel Pendeta</h3>
            <p class="text-sm mb-4">Akses lihat data dan profil.</p>
            <div class="space-y-2 text-sm">
                 @if($user?->pendeta)
                    <a href="{{ route('admin.pendeta.show', $user->pendeta->id) }}" class="block hover:underline">- Lihat Profil Pendeta Anda</a>
                 @else
                     <span class="text-yellow-400 text-sm">Data Pendeta Anda belum terhubung.</span>
                 @endif
                 <a href="{{ route('admin.klasis.index') }}" class="block hover:underline">- Lihat Daftar Klasis</a>
                <a href="{{ route('admin.jemaat.index') }}" class="block hover:underline">- Lihat Daftar Jemaat</a>
                <a href="{{ route('admin.anggota-jemaat.index') }}" class="block hover:underline">- Lihat Daftar Anggota Jemaat</a>
            </div>
        </div>
        @endhasrole

        {{-- === KONTEN UMUM / FALLBACK JIKA TIDAK ADA ROLE DI ATAS === --}}
        @if($user && !$user->hasAnyRole(['Super Admin', 'Admin Bidang 3', 'Admin Bidang 4', 'Admin Klasis', 'Admin Jemaat', 'Pendeta']))
        <div class="bg-white p-6 rounded-lg shadow md:col-span-2 lg:col-span-3">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Informasi</h3>
            <p class="text-gray-600 text-sm">Dashboard Anda sedang dalam pengembangan atau Anda belum memiliki akses ke modul khusus.</p>
        </div>
        @endif

    </div> {{-- Akhir Grid Panel Aksi Cepat --}}

    {{-- Kolom Aktivitas & Grafik (placeholder) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
         <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>
            {{-- TODO: Tampilkan log aktivitas sistem jika ada --}}
            <p class="text-gray-600 text-sm">Belum ada aktivitas terbaru untuk ditampilkan.</p>
        </div>
         <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Pertumbuhan</h3>
            {{-- TODO: Tambahkan chart/grafik jika diperlukan --}}
            <p class="text-gray-600 text-sm">Fitur grafik akan segera hadir.</p>
        </div>
    </div>

@endsection {{-- Akhir bagian konten --}}

@push('scripts')
{{-- Tambahkan script khusus dashboard jika perlu, misal untuk Chart.js --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
{{-- <script> /* Logika Chart */ </script> --}}
@endpush