@extends('layouts.app')

@section('title', 'Penyusunan Jurnal Pastoral')

@section('content')
    {{-- PANEL DRAF OFFLINE (Akan muncul jika ada data tersimpan di HP) --}}
    <div id="offline-draft-panel" class="hidden mb-6 bg-yellow-50 border border-yellow-300 p-5 rounded shadow-sm">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center">
                <i class="fas fa-hdd text-yellow-600 text-3xl mr-4"></i>
                <div>
                    <h4 class="font-black text-yellow-900 text-sm uppercase tracking-widest">Terdapat Dokumen Belum Disinkronisasi</h4>
                    <p class="text-xs text-yellow-700 mt-1">Sistem mendeteksi ada <b id="draft-count" class="text-yellow-900 text-sm">0</b> jurnal yang ditulis saat offline dan belum masuk ke Pangkalan Data Sinode.</p>
                </div>
            </div>
            <button type="button" onclick="syncOfflineData()" id="btn-sync" class="bg-yellow-700 hover:bg-yellow-800 text-white px-5 py-2.5 rounded text-xs font-bold uppercase tracking-wider shadow transition flex items-center">
                <i class="fas fa-cloud-upload-alt mr-2"></i> Sinkronisasi Sekarang
            </button>
        </div>
    </div>

    <x-admin-form 
        title="Formulir Rekam Jejak Pelayanan (Jurnal)" 
        action="{{ route('admin.jurnal.store') }}" 
        back-route="{{ route('admin.jurnal.index') }}"
    >
        {{-- Kita tambahkan ID 'jurnal-form' pada tag pembungkus form melalui atribut slot atau langsung modifikasi elemen JS --}}
        <div class="space-y-6" id="form-container">
            {{-- PANEL INFORMASI OTORISASI --}}
            <div class="bg-gray-50 border border-gray-200 p-4 rounded shadow-sm">
                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-200 pb-2 mb-3"><i class="fas fa-info-circle mr-2 text-blue-800"></i> Informasi Identifikasi Dokumen</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-[10px] font-bold text-gray-500 uppercase">Lokasi Penugasan (Pemilik Dokumen)</span>
                        <span class="font-bold text-gray-900">{{ $jemaat->nama_jemaat ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-500 uppercase">Penanggung Jawab (Penulis)</span>
                        <span class="font-bold text-gray-900">{{ auth()->user()->pegawai->nama_lengkap ?? auth()->user()->name }}</span>
                    </div>
                </div>
                <p class="text-[10px] text-gray-500 mt-3 italic">Formulir ini telah dilengkapi teknologi <b>PWA Offline Storage</b>. Anda dapat menulis dan menyimpan jurnal meski berada di pedalaman tanpa sinyal internet.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Input Tanggal Kegiatan --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Tanggal Pelaksanaan / Observasi <span class="text-red-600">*</span></label>
                    <input type="date" id="input_tanggal" name="tanggal_kegiatan" value="{{ old('tanggal_kegiatan', date('Y-m-d')) }}" required 
                        class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm">
                    @error('tanggal_kegiatan') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Input Kategori --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Kategori Pelayanan <span class="text-red-600">*</span></label>
                    <select id="input_kategori" name="kategori" required class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 shadow-sm bg-white">
                        <option value="">-- Pilih Klasifikasi Kategori --</option>
                        <option value="Diakonia & Sosial" {{ old('kategori') == 'Diakonia & Sosial' ? 'selected' : '' }}>Diakonia & Sosial</option>
                        <option value="Resolusi Konflik" {{ old('kategori') == 'Resolusi Konflik' ? 'selected' : '' }}>Resolusi Konflik</option>
                        <option value="Pembangunan Fisik" {{ old('kategori') == 'Pembangunan Fisik' ? 'selected' : '' }}>Pembangunan Fisik / Aset</option>
                        <option value="Pembinaan Teologi" {{ old('kategori') == 'Pembinaan Teologi' ? 'selected' : '' }}>Pembinaan Teologi / Katekisasi</option>
                        <option value="Evaluasi Umum" {{ old('kategori') == 'Evaluasi Umum' ? 'selected' : '' }}>Evaluasi Jemaat Umum</option>
                    </select>
                    @error('kategori') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Input Konteks Situasi --}}
            <div class="mt-6">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Analisis Konteks & Situasi Jemaat <span class="text-red-600">*</span></label>
                <textarea id="input_konteks" name="konteks_situasi" rows="8" required 
                    class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 placeholder-gray-400 leading-relaxed shadow-sm"
                    placeholder="Uraikan secara objektif situasi pelayanan, masalah yang dihadapi, atau hasil observasi Anda...">{{ old('konteks_situasi') }}</textarea>
                <p class="text-[10px] text-gray-500 mt-1">Gunakan bahasa baku yang mendeskripsikan fakta dan analisis pastoral Anda.</p>
                @error('konteks_situasi') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Input Tindak Lanjut --}}
            <div class="mt-6">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Rekomendasi Tindak Lanjut / Catatan Penerus</label>
                <textarea id="input_tindak_lanjut" name="tindak_lanjut" rows="5" 
                    class="w-full border border-gray-300 rounded text-sm focus:ring-blue-800 focus:border-blue-800 placeholder-gray-400 leading-relaxed shadow-sm"
                    placeholder="Tuliskan langkah-langkah yang harus diambil selanjutnya, atau catatan penting bagi pendeta yang akan menggantikan Anda kelak...">{{ old('tindak_lanjut') }}</textarea>
                <p class="text-[10px] text-gray-500 mt-1">Bagian ini sangat krusial untuk menjaga kesinambungan pelayanan ketika terjadi mutasi.</p>
                @error('tindak_lanjut') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mt-8 border-t border-gray-200 pt-5">
                <p class="text-xs text-gray-600 font-bold mb-4"><i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i> Pernyataan Integritas:</p>
                <p class="text-[10px] text-gray-500 leading-relaxed">Dengan menyimpan dokumen ini, saya menyatakan bahwa laporan yang disusun adalah benar, objektif, dan dapat dipertanggungjawabkan di hadapan Majelis Pekerja Sinode GPI Papua sebagai bahan penyusunan Rencana Strategis (Renstra).</p>
            </div>
        </div>
    </x-admin-form>

    @push('scripts')
    <script>
        // INISIALISASI DATABASE LOKAL (IndexedDB)
        const dbName = "SIMGPI_Database";
        const storeName = "jurnalOffline";
        let db;

        const request = indexedDB.open(dbName, 1);
        
        request.onupgradeneeded = function(event) {
            db = event.target.result;
            if (!db.objectStoreNames.contains(storeName)) {
                db.createObjectStore(storeName, { keyPath: "id", autoIncrement: true });
            }
        };

        request.onsuccess = function(event) {
            db = event.target.result;
            checkOfflineDrafts();
        };

        request.onerror = function(event) {
            console.error("Gagal membuka IndexedDB", event);
        };

        // CEK JUMLAH DRAF TERSIMPAN
        function checkOfflineDrafts() {
            const transaction = db.transaction([storeName], "readonly");
            const store = transaction.objectStore(storeName);
            const countRequest = store.count();

            countRequest.onsuccess = function() {
                const count = countRequest.result;
                const panel = document.getElementById('offline-draft-panel');
                const countText = document.getElementById('draft-count');
                
                if (count > 0) {
                    countText.innerText = count;
                    panel.classList.remove('hidden');
                } else {
                    panel.classList.add('hidden');
                }
            };
        }

        // AMBIL ALIH TOMBOL SUBMIT (INTERCEPTOR)
        document.addEventListener("DOMContentLoaded", function() {
            // Karena kita menggunakan komponen x-admin-form, kita target form terdekat dari input
            const form = document.getElementById('input_tanggal').closest('form');
            
            form.addEventListener('submit', function(e) {
                // JIKA TIDAK ADA SINYAL (OFFLINE)
                if (!navigator.onLine) {
                    e.preventDefault(); // Hentikan form agar tidak error "No Internet"
                    
                    const dataJurnal = {
                        tanggal_kegiatan: document.getElementById('input_tanggal').value,
                        kategori: document.getElementById('input_kategori').value,
                        konteks_situasi: document.getElementById('input_konteks').value,
                        tindak_lanjut: document.getElementById('input_tindak_lanjut').value,
                        _token: document.querySelector('input[name="_token"]').value
                    };

                    const transaction = db.transaction([storeName], "readwrite");
                    const store = transaction.objectStore(storeName);
                    store.add(dataJurnal);

                    transaction.oncomplete = function() {
                        alert("KONEKSI TERPUTUS!\n\nJurnal berhasil disimpan di memori Perangkat/HP Anda secara offline. Pastikan menekan tombol 'Sinkronisasi' saat jaringan internet kembali normal.");
                        form.reset();
                        checkOfflineDrafts();
                    };
                }
                // Jika online, form akan submit seperti biasa ke server
            });
        });

        // FUNGSI SINKRONISASI KE SERVER (SAAT KEMBALI ONLINE)
        function syncOfflineData() {
            if (!navigator.onLine) {
                alert("Anda masih Offline. Silakan cari sinyal terlebih dahulu sebelum melakukan sinkronisasi.");
                return;
            }

            const btnSync = document.getElementById('btn-sync');
            btnSync.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Mengunggah...';
            btnSync.disabled = true;

            const transaction = db.transaction([storeName], "readwrite");
            const store = transaction.objectStore(storeName);
            const getAllRequest = store.getAll();

            getAllRequest.onsuccess = function() {
                const drafts = getAllRequest.result;
                let syncCount = 0;
                let hasError = false;

                if (drafts.length === 0) return;

                drafts.forEach(draft => {
                    fetch('{{ route("admin.jurnal.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': draft._token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(draft)
                    })
                    .then(response => {
                        if (response.ok) {
                            // Hapus dari IndexedDB jika berhasil terkirim
                            const delTransaction = db.transaction([storeName], "readwrite");
                            delTransaction.objectStore(storeName).delete(draft.id);
                        } else {
                            hasError = true;
                        }
                    })
                    .catch(error => {
                        console.error('Sync error:', error);
                        hasError = true;
                    })
                    .finally(() => {
                        syncCount++;
                        // Jika semua draft selesai diproses
                        if (syncCount === drafts.length) {
                            if (hasError) {
                                alert("Beberapa jurnal gagal disinkronkan. Mohon coba lagi.");
                            } else {
                                alert("Sinkronisasi Berhasil! Seluruh draf offline telah masuk ke Pangkalan Data Sinode.");
                            }
                            checkOfflineDrafts();
                            btnSync.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i> Sinkronisasi Sekarang';
                            btnSync.disabled = false;
                            
                            // Muat ulang halaman untuk memperbarui daftar indeks jika diperlukan
                            window.location.href = '{{ route("admin.jurnal.index") }}';
                        }
                    });
                });
            };
        }
    </script>
    @endpush
@endsection