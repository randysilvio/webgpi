@extends('admin.layout')

@section('title', 'Import Data Jemaat')
@section('header-title', 'Import Data Jemaat')

@section('content')
<div class="bg-white shadow rounded-lg p-6 md:p-8 max-w-2xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Import Jemaat dari Excel/CSV</h2>

    {{-- Tampilkan Pesan Error/Warning --}}
    @if (session('error') && is_string(session('error')) && str_contains(session('error'), 'kesalahan validasi'))
        <div class="flash-message mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert"> <p class="font-bold">Gagal Import:</p> <pre class="mt-2 text-xs whitespace-pre-wrap font-mono">{{ session('error') }}</pre> </div>
    @elseif (session('error'))
        <div class="flash-message mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert"> <p class="font-bold">Gagal Import:</p> <p class="mt-1 text-sm">{{ session('error') }}</p> </div>
    @endif
     @if (session('warning'))
        <div class="flash-message mb-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md shadow-sm" role="alert"> <p class="font-bold">Peringatan Import:</p> <pre class="mt-2 text-xs whitespace-pre-wrap font-mono">{{ session('warning') }}</pre> </div>
    @endif

    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md text-sm text-blue-700">
        <h4 class="font-semibold mb-2">Petunjuk Import Data Jemaat:</h4>
        <ol class="list-decimal list-inside space-y-1">
            <li>Unduh template file di bawah ini.</li>
            <li>Isi data jemaat sesuai kolom. Kolom **Nama Jemaat**, **ID Klasis**, **Status Jemaat**, **Jenis Jemaat** wajib diisi.</li>
            <li>Pastikan **ID Klasis** yang dimasukkan valid (ada di sistem).</li>
            <li>Jika **Kode Jemaat** atau **Email Jemaat** diisi, pastikan nilainya unik.</li>
            <li>Simpan file dalam format **.xlsx** (disarankan) atau .csv.</li>
            <li>Pilih file dan klik "Import Data".</li>
        </ol>
        <p class="mt-3">
            <a href="{{ route('admin.jemaat.export', ['template' => 'yes']) }}"
               class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md shadow text-xs transition duration-150 ease-in-out">
                Unduh Template Import Jemaat (.xlsx)
            </a>
        </p>
    </div>

    <form action="{{ route('admin.jemaat.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label for="import_file" class="block text-sm font-medium text-gray-700 mb-1">Pilih File (Excel/CSV) <span class="text-red-600">*</span></label>
            <input type="file" id="import_file" name="import_file" required accept=".xlsx, .xls, .csv"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 @error('import_file') border-red-500 @enderror">
            @error('import_file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
            <a href="{{ route('admin.jemaat.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                Batal
            </a>
            <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow hover:shadow-md transition duration-150 ease-in-out">
                Import Data Jemaat
            </button>
        </div>
    </form>
</div>
@endsection