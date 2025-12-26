<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk tabel Aset Gereja.
     */
    public function up(): void
    {
        Schema::create('aset_gereja', function (Blueprint $table) {
            $table->id();
            
            // Relasi Lokasi Aset
            $table->foreignId('klasis_id')->nullable()->constrained('klasis')->onDelete('cascade');
            $table->foreignId('jemaat_id')->nullable()->constrained('jemaat')->onDelete('cascade');
            
            // Detail Aset
            $table->string('nama_aset'); // Contoh: Gedung Pastori, Mobil Dinas, Tanah Hibah
            $table->string('kode_aset')->unique()->nullable();
            
            // Kategori Aset
            $table->enum('kategori', [
                'Tanah', 
                'Gedung', 
                'Kendaraan', 
                'Peralatan Elektronik', 
                'Meubelair', 
                'Lainnya'
            ]);

            // Informasi Perolehan
            $table->date('tanggal_perolehan')->nullable();
            $table->decimal('nilai_perolehan', 15, 2)->default(0); // Harga beli/nilai saat didapat
            $table->string('sumber_perolehan')->nullable(); // Contoh: Hibah, APB Jemaat, Sumbangan
            
            // Kondisi & Status
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('status_kepemilikan', ['Milik Sendiri', 'Sewa', 'Pinjam Pakai'])->default('Milik Sendiri');
            
            // Dokumentasi & Lokasi Fisik
            $table->string('lokasi_fisik')->nullable(); // Alamat atau ruang spesifik
            $table->string('nomor_dokumen')->nullable(); // No Sertifikat / BPKB
            $table->string('file_dokumen_path')->nullable(); // Scan Sertifikat/Surat
            $table->string('foto_aset_path')->nullable();
            
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Untuk menjaga history jika aset dihapus/dijual
            
            // Index untuk pencarian cepat
            $table->index(['kategori', 'kondisi']);
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset_gereja');
    }
};