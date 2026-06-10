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
        Schema::create('mutasi_pendeta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendeta_id')->constrained('pendeta')->cascadeOnDelete(); // Relasi ke pendeta, hapus riwayat jika pendeta dihapus
            $table->date('tanggal_sk'); // Tanggal Surat Keputusan Mutasi
            $table->string('nomor_sk')->unique(); // Nomor SK, unik
            $table->string('jenis_mutasi'); // Mis: Penempatan Awal, Pindah Tugas, Emeritus, Keluar, Lainnya
            
            // Kolom Asal (nullable karena penempatan awal mungkin tidak punya asal)
            $table->foreignId('asal_klasis_id')->nullable()->constrained('klasis')->nullOnDelete();
            $table->foreignId('asal_jemaat_id')->nullable()->constrained('jemaat')->nullOnDelete();

            // Kolom Tujuan (nullable karena status Emeritus/Keluar mungkin tidak punya tujuan)
            $table->foreignId('tujuan_klasis_id')->nullable()->constrained('klasis')->nullOnDelete();
            $table->foreignId('tujuan_jemaat_id')->nullable()->constrained('jemaat')->nullOnDelete();

            $table->date('tanggal_efektif')->nullable(); // Tanggal mulai efektif mutasi (opsional)
            $table->text('keterangan')->nullable(); // Catatan tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_pendeta');
    }
};