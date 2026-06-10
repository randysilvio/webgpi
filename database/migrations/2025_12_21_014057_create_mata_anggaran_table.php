<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk tabel Mata Anggaran.
     */
    public function up(): void
    {
        Schema::create('mata_anggaran', function (Blueprint $table) {
            $table->id();
            
            // Kode Akun (Cth: 1.1 untuk Pendapatan, 2.1 untuk Belanja Pegawai)
            $table->string('kode')->unique();
            $table->string('nama_mata_anggaran');
            
            // Jenis: Pendapatan atau Belanja
            $table->enum('jenis', ['Pendapatan', 'Belanja']);
            
            // Kategori Kelompok (Cth: Rutin, Pembangunan, Sentralisasi, dll)
            $table->string('kelompok')->nullable();
            
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            // Index untuk pencarian cepat
            $table->index(['kode', 'jenis']);
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_anggaran');
    }
};