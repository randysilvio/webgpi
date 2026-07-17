<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambah relasi pohon keluarga.
     */
    public function up(): void
    {
        Schema::table('anggota_jemaat', function (Blueprint $table) {
            // 1. Menambahkan kolom relasi orang tua (Self-Referencing)
            // Diletakkan setelah jemaat_id agar struktur database rapi
            $table->unsignedBigInteger('ayah_id')->nullable()->after('jemaat_id');
            $table->unsignedBigInteger('ibu_id')->nullable()->after('ayah_id');
            
            // 2. Menambahkan status dalam keluarga (Enum)
            $table->enum('status_keluarga', ['Kepala Keluarga', 'Istri', 'Anak', 'Famili Lain'])->nullable()->after('ibu_id');
            
            // 3. Kolom pendukung otomatisasi KK (Penting untuk sinkronisasi internal)
            $table->string('kode_keluarga_internal')->nullable()->unique()->after('nomor_kk');
            
            // 4. Definisi Foreign Key ke tabel itu sendiri (Pohon Keluarga)
            $table->foreign('ayah_id')->references('id')->on('anggota_jemaat')->onDelete('set null');
            $table->foreign('ibu_id')->references('id')->on('anggota_jemaat')->onDelete('set null');
        });
    }

    /**
     * Membatalkan migrasi (Rollback).
     */
    public function down(): void
    {
        Schema::table('anggota_jemaat', function (Blueprint $table) {
            // Drop foreign keys terlebih dahulu sebelum kolomnya dihapus
            $table->dropForeign(['ayah_id']);
            $table->dropForeign(['ibu_id']);
            
            // Hapus kolom-kolom yang ditambahkan
            $table->dropColumn([
                'ayah_id', 
                'ibu_id', 
                'status_keluarga', 
                'kode_keluarga_internal'
            ]);
        });
    }
};