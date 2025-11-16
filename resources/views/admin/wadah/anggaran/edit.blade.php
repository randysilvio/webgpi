<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pos Anggaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.wadah.anggaran.update', $anggaran->id) }}">
                    @csrf @method('PUT')

                    <div class="bg-gray-50 p-4 mb-4 rounded text-sm text-gray-600">
                        <strong>Info:</strong> Pos ini untuk Tahun {{ $anggaran->tahun_anggaran }}, Wadah {{ $anggaran->jenisWadah->nama_wadah }}.
                    </div>

                    <div class="mb-4">
                        <x-input-label for="nama_pos_anggaran" :value="__('Nama Pos Anggaran')" />
                        <x-text-input id="nama_pos_anggaran" class="block mt-1 w-full font-bold" type="text" name="nama_pos_anggaran" :value="old('nama_pos_anggaran', $anggaran->nama_pos_anggaran)" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="jenis_anggaran" :value="__('Jenis Anggaran')" />
                            <select id="jenis_anggaran" name="jenis_anggaran" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="penerimaan" {{ $anggaran->jenis_anggaran == 'penerimaan' ? 'selected' : '' }}>Penerimaan</option>
                                <option value="pengeluaran" {{ $anggaran->jenis_anggaran == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="jumlah_target" :value="__('Target Jumlah (Rp)')" />
                            <x-text-input id="jumlah_target" class="block mt-1 w-full" type="number" name="jumlah_target" :value="old('jumlah_target', $anggaran->jumlah_target)" required />
                        </div>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="keterangan" :value="__('Keterangan')" />
                        <textarea id="keterangan" name="keterangan" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('keterangan', $anggaran->keterangan) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('admin.wadah.anggaran.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow-sm">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>