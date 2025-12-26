<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Program Kerja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form method="POST" action="{{ route('admin.wadah.program.update', $program->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Wadah:</span>
                                <span class="font-bold block">{{ $program->jenisWadah->nama_wadah }}</span>
                                <input type="hidden" name="jenis_wadah_id" value="{{ $program->jenis_wadah_id }}">
                            </div>
                            <div>
                                <span class="text-gray-500">Tingkat:</span>
                                <span class="font-bold block uppercase">{{ $program->tingkat }}</span>
                                @if($program->klasis)
                                    <span class="text-xs text-gray-600">Klasis: {{ $program->klasis->nama_klasis }}</span>
                                @endif
                                @if($program->jemaat)
                                    <span class="text-xs text-gray-600 block">Jemaat: {{ $program->jemaat->nama_jemaat }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="tahun_program" :value="__('Tahun Program')" />
                            <x-text-input id="tahun_program" class="block mt-1 w-full" type="number" name="tahun_program" :value="old('tahun_program', $program->tahun_program)" required />
                        </div>
                        <div>
                            <x-input-label for="status_pelaksanaan" :value="__('Status Pelaksanaan')" />
                            <select id="status_pelaksanaan" name="status_pelaksanaan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="0" {{ $program->status_pelaksanaan == 0 ? 'selected' : '' }}>Direncanakan</option>
                                <option value="1" {{ $program->status_pelaksanaan == 1 ? 'selected' : '' }}>Sedang Berjalan</option>
                                <option value="2" {{ $program->status_pelaksanaan == 2 ? 'selected' : '' }}>Selesai</option>
                                <option value="3" {{ $program->status_pelaksanaan == 3 ? 'selected' : '' }}>Ditunda</option>
                                <option value="4" {{ $program->status_pelaksanaan == 4 ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="nama_program" :value="__('Nama Program')" />
                        <x-text-input id="nama_program" class="block mt-1 w-full font-bold" type="text" name="nama_program" :value="old('nama_program', $program->nama_program)" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="parent_program_id" :value="__('Program Induk')" />
                        <select id="parent_program_id" name="parent_program_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Tidak Ada / Mandiri --</option>
                            @foreach($potentialParents as $parent)
                                <option value="{{ $parent->id }}" {{ $program->parent_program_id == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->nama_program }} ({{ ucfirst($parent->tingkat) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Ubah hanya jika diperlukan.</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="tujuan" :value="__('Tujuan / Output')" />
                        <textarea id="tujuan" name="tujuan" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('tujuan', $program->tujuan) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="deskripsi" :value="__('Deskripsi Kegiatan')" />
                        <textarea id="deskripsi" name="deskripsi" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('deskripsi', $program->deskripsi) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="penanggung_jawab" :value="__('Penanggung Jawab')" />
                            <x-text-input id="penanggung_jawab" class="block mt-1 w-full" type="text" name="penanggung_jawab" :value="old('penanggung_jawab', $program->penanggung_jawab)" />
                        </div>
                        <div>
                            <x-input-label for="target_anggaran" :value="__('Target Anggaran (Rp)')" />
                            <x-text-input id="target_anggaran" class="block mt-1 w-full" type="number" name="target_anggaran" :value="old('target_anggaran', $program->target_anggaran)" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('admin.wadah.program.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                            Perbarui Program
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>