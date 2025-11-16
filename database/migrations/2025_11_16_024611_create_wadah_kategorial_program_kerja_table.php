<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wadah_kategorial_program_kerja', function (Blueprint $table) {
            $table->id();

            // Relasi ke Jenis Wadah (PAR, PAM, dll)
            $table->foreignId('jenis_wadah_id')->constrained('jenis_wadah_kategorial')->onDelete('cascade');

            // Lingkup & Lokasi
            $table->enum('tingkat', ['sinode', 'klasis', 'jemaat']);
            $table->foreignId('klasis_id')->nullable()->constrained('klasis')->onDelete('cascade');
            $table->foreignId('jemaat_id')->nullable()->constrained('jemaat')->onDelete('cascade');

            // Detail Program
            $table->year('tahun_program'); // Contoh: 2025
            $table->string('nama_program');
            $table->text('deskripsi')->nullable();
            $table->text('tujuan')->nullable(); // Output yang diharapkan
            $table->string('penanggung_jawab')->nullable(); // Seksi atau Jabatan (misal: Seksi Kerohanian)

            // Relasi Hierarki (Program Turunan)
            // Program di Jemaat bisa merujuk ke Program Klasis/Sinode sebagai induknya
            $table->foreignId('parent_program_id')
                  ->nullable()
                  ->constrained('wadah_kategorial_program_kerja')
                  ->onDelete('set null');
            
            // Status & Target
            // Status pelaksanaan: 0=Rencana, 1=Berjalan, 2=Selesai, 3=Ditunda, 4=Dibatalkan
            $table->tinyInteger('status_pelaksanaan')->default(0); 
            
            // Target Anggaran (Estimasi Biaya) - Realisasi ada di tabel transaksi nanti
            $table->decimal('target_anggaran', 15, 2)->default(0);

            $table->timestamps();

            // --- PERBAIKAN ERROR DISINI ---
            // Memberikan nama index kustom yang pendek agar tidak error di MySQL
            $table->index(['jenis_wadah_id', 'tahun_program'], 'idx_wkpk_jenis_tahun');
            $table->index(['tingkat', 'klasis_id', 'jemaat_id'], 'idx_wkpk_lokasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wadah_kategorial_program_kerja');
    }
};