<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pastikan timestamp file ini PALING AKHIR di antara semua migrasi ini
// Contoh: 2025_10_24_102911_add_foreign_keys_to_tables.php

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pendeta', function (Blueprint $table) {
            $table->foreign('klasis_penempatan_id')
                  ->references('id')->on('klasis')
                  ->nullOnDelete();
            $table->foreign('jemaat_penempatan_id')
                  ->references('id')->on('jemaat')
                  ->nullOnDelete();
        });

        Schema::table('klasis', function (Blueprint $table) {
            $table->foreign('ketua_mpk_pendeta_id')
                  ->references('id')->on('pendeta')
                  ->nullOnDelete();
        });

        Schema::table('jemaat', function (Blueprint $table) {
            $table->foreign('klasis_id')
                  ->references('id')->on('klasis')
                  ->cascadeOnDelete();
        });

        Schema::table('anggota_jemaat', function (Blueprint $table) {
            $table->foreign('jemaat_id')
                  ->references('id')->on('jemaat')
                  ->cascadeOnDelete();
        });

         Schema::table('users', function (Blueprint $table) {
            $table->foreign('pendeta_id')
                  ->references('id')->on('pendeta')
                  ->nullOnDelete();
             $table->foreign('klasis_id')
                  ->references('id')->on('klasis')
                  ->nullOnDelete();
             $table->foreign('jemaat_id')
                  ->references('id')->on('jemaat')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('users', function (Blueprint $table) {
             $table->dropForeign(['pendeta_id']);
             $table->dropForeign(['klasis_id']);
             $table->dropForeign(['jemaat_id']);
         });

         Schema::table('anggota_jemaat', function (Blueprint $table) {
             $table->dropForeign(['jemaat_id']);
         });

         Schema::table('jemaat', function (Blueprint $table) {
             $table->dropForeign(['klasis_id']);
         });

         Schema::table('klasis', function (Blueprint $table) {
             $table->dropForeign(['ketua_mpk_pendeta_id']);
         });

         Schema::table('pendeta', function (Blueprint $table) {
             $table->dropForeign(['klasis_penempatan_id']);
             $table->dropForeign(['jemaat_penempatan_id']);
         });
    }
};