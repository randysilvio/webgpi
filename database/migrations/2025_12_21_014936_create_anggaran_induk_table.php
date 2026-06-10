<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk tabel Anggaran Induk (RAPB).
     */
    public function up(): void
    {
        Schema::create('anggaran_induk', function (Blueprint $table) {
            $table->id();

            // Relasi ke Mata Anggaran (COA)
            $table->foreignId('mata_anggaran_id')->constrained('mata_anggaran')->onDelete('cascade');

            // Lingkup Anggaran (Sinode, Klasis, atau Jemaat)
            $table->foreignId('klasis_id')->nullable()->constrained('klasis')->onDelete('cascade');
            $table->foreignId('jemaat_id')->nullable()->constrained('jemaat')->onDelete('cascade');

            // Detail Anggaran
            $table->year('tahun_anggaran');
            $table->decimal('jumlah_target', 15, 2)->default(0); // Nilai yang direncanakan (RAPB)
            $table->decimal('jumlah_realisasi', 15, 2)->default(0); // Akan terisi otomatis dari transaksi harian

            // Status Pengesahan (Draft, Diajukan, Disetujui, Ditolak)
            $table->enum('status_anggaran', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak'])->default('Draft');
            
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index untuk laporan cepat
            $table->index(['tahun_anggaran', 'klasis_id', 'jemaat_id'], 'idx_anggaran_lokasi_tahun');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggaran_induk');
    }
};