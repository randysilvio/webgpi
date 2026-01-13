<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi dengan pengecekan kolom untuk menghindari error duplicate.
     */
    public function up(): void
    {
        Schema::table('anggota_jemaat', function (Blueprint $table) {
            // Cek dan tambah kolom ayah_id
            if (!Schema::hasColumn('anggota_jemaat', 'ayah_id')) {
                $table->unsignedBigInteger('ayah_id')->nullable()->after('jemaat_id');
                $table->foreign('ayah_id')->references('id')->on('anggota_jemaat')->onDelete('set null');
            }

            // Cek dan tambah kolom ibu_id
            if (!Schema::hasColumn('anggota_jemaat', 'ibu_id')) {
                $table->unsignedBigInteger('ibu_id')->nullable()->after('ayah_id');
                $table->foreign('ibu_id')->references('id')->on('anggota_jemaat')->onDelete('set null');
            }

            // Cek dan tambah kolom status_keluarga (jika belum ada dari migrasi sebelumnya)
            if (!Schema::hasColumn('anggota_jemaat', 'status_keluarga')) {
                $table->enum('status_keluarga', ['Kepala Keluarga', 'Istri', 'Anak', 'Famili Lain'])->nullable()->after('ibu_id');
            }

            // Cek dan tambah kolom kode_keluarga_internal
            if (!Schema::hasColumn('anggota_jemaat', 'kode_keluarga_internal')) {
                $table->string('kode_keluarga_internal')->nullable()->unique()->after('nomor_kk');
            }
        });
    }

    /**
     * Batalkan migrasi (Rollback).
     */
    public function down(): void
    {
        Schema::table('anggota_jemaat', function (Blueprint $table) {
            // Hapus foreign key jika ada
            if (Schema::hasColumn('anggota_jemaat', 'ayah_id')) {
                $table->dropForeign(['ayah_id']);
            }
            if (Schema::hasColumn('anggota_jemaat', 'ibu_id')) {
                $table->dropForeign(['ibu_id']);
            }

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