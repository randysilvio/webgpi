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
        Schema::table('anggota_jemaat', function (Blueprint $table) {
            // Tambahkan kolom nomor_kk setelah jemaat_id
            $table->string('nomor_kk', 50)->nullable()->after('jemaat_id')->index();
            // Anda mungkin sudah punya kolom status_dalam_keluarga, jika belum, tambahkan:
            // $table->string('status_dalam_keluarga', 50)->nullable()->after('nomor_kk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota_jemaat', function (Blueprint $table) {
            $table->dropIndex(['nomor_kk']); // Hapus index jika ada
            $table->dropColumn('nomor_kk');
            // $table->dropColumn('status_dalam_keluarga'); // Hapus jika ditambahkan di atas
        });
    }
};