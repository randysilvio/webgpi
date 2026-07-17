<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk tabel Transaksi Induk (Buku Kas Umum).
     */
    public function up(): void
    {
        Schema::create('transaksi_induk', function (Blueprint $table) {
            $table->id();

            // Relasi ke Mata Anggaran (Penerimaan/Belanja apa?)
            $table->foreignId('mata_anggaran_id')->constrained('mata_anggaran')->onDelete('cascade');

            // Lokasi Transaksi (Jemaat atau Klasis mana?)
            $table->foreignId('klasis_id')->nullable()->constrained('klasis')->onDelete('cascade');
            $table->foreignId('jemaat_id')->nullable()->constrained('jemaat')->onDelete('cascade');

            // Detail Transaksi
            $table->date('tanggal_transaksi');
            $table->string('nomor_bukti')->nullable(); // No. Kwitansi/Nota
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan');
            
            // Upload Bukti Fisik
            $table->string('file_bukti_path')->nullable(); 

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users'); // User yang input
            $table->timestamps();
            $table->softDeletes();

            // Index untuk laporan cepat
            $table->index(['tanggal_transaksi', 'klasis_id', 'jemaat_id']);
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_induk');
    }
};