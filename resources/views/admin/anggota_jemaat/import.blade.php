@extends('admin.layout')

@section('title', 'Import Anggota Jemaat')
@section('header-title', 'Import Data Anggota Jemaat')

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8 max-w-2xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Import dari Excel/CSV</h2>

    {{-- Pesan Error/Warning --}}
    @if (session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm">
           <p class="font-bold">Gagal Import:</p>
           <p class="mt-1 text-sm">{{ session('error') }}</p>
        </div>
    @endif
    @if (session('warning'))
        <div class="mb-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md shadow-sm">
           <p class="font-bold">Peringatan Import:</p>
           <pre class="mt-2 text-xs whitespace-pre-wrap">{{ session('warning') }}</pre>
        </div>
    @endif

    <form action="{{ route('admin.anggota-jemaat.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-6">
            
            {{-- PILIHAN LOKASI (PENTING AGAR DATA MASUK KE TEMPAT YANG BENAR) --}}
            <div class="bg-blue-50 p-4 rounded-md border border-blue-100">
                <h3 class="text-sm font-bold text-blue-800 mb-3">Tujuan Import Data</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Dropdown Klasis (Hanya muncul jika Super Admin / Admin Klasis) --}}
                    @if(isset($klasisOptions) && $klasisOptions->isNotEmpty())
                    <div>
                        <label for="klasis_id" class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Klasis</label>
                        <select id="klasis_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">-- Pilih Klasis --</option>
                            @foreach($klasisOptions as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_klasis }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Dropdown Jemaat (WAJIB DIPILIH) --}}
                    <div>
                        <label for="jemaat_id" class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Jemaat <span class="text-red-500">*</span></label>
                        <select name="jemaat_id" id="jemaat_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">-- Pilih Jemaat --</option>
                            {{-- Jika user adalah Admin Jemaat, otomatis terisi satu opsi --}}
                            @if(isset($jemaatOptions) && $jemaatOptions->isNotEmpty())
                                @foreach($jemaatOptions as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama_jemaat }}</option>
                                @endforeach
                            @endif
                        </select>
                         <p class="text-[10px] text-gray-400 mt-1 italic">Semua data di file CSV akan dimasukkan ke jemaat ini.</p>
                    </div>
                </div>
            </div>

            {{-- Upload File --}}
            <div>
                <label for="import_file" class="block text-sm font-medium text-gray-700 mb-1">File Excel/CSV <span class="text-red-600">*</span></label>
                <input type="file" id="import_file" name="import_file" required accept=".xlsx, .xls, .csv"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-300 rounded-md p-1">
                <p class="mt-2 text-xs text-gray-500">
                    Pastikan header kolom di baris pertama: <code>nama_lengkap</code>, <code>tanggal_lahir</code>, <code>jenis_kelamin_kode</code>, dll.
                </p>
            </div>

        </div>

        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            <a href="{{ route('admin.anggota-jemaat.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                Batal
            </a>
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                Mulai Import
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Script sederhana untuk load jemaat berdasarkan klasis (jika Super Admin)
    document.getElementById('klasis_id')?.addEventListener('change', function() {
        const klasisId = this.value;
        const jemaatSelect = document.getElementById('jemaat_id');
        
        jemaatSelect.innerHTML = '<option value="">Memuat...</option>';
        
        if (klasisId) {
            fetch(`/api/jemaat-by-klasis/${klasisId}`)
                .then(response => response.json())
                .then(data => {
                    jemaatSelect.innerHTML = '<option value="">-- Pilih Jemaat --</option>';
                    data.forEach(item => {
                        jemaatSelect.innerHTML += `<option value="${item.id}">${item.nama_jemaat}</option>`;
                    });
                });
        } else {
            jemaatSelect.innerHTML = '<option value="">-- Pilih Jemaat --</option>';
        }
    });
</script>
@endpush