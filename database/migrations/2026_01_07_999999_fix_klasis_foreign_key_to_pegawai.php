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
        Schema::table('klasis', function (Blueprint $table) {
            // 1. Hapus Constraint Lama (yang mengarah ke tabel 'pendeta')
            // Kita gunakan array syntax agar Laravel mencari nama constraint otomatis
            // Biasanya namanya: klasis_ketua_mpk_pendeta_id_foreign
            $table->dropForeign(['ketua_mpk_pendeta_id']);

            // 2. Buat Constraint Baru (mengarah ke tabel 'pegawai')
            $table->foreign('ketua_mpk_pendeta_id')
                  ->references('id')
                  ->on('pegawai')
                  ->nullOnDelete(); // Jika pegawai dihapus, kolom ini jadi NULL (aman)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('klasis', function (Blueprint $table) {
            // Kembalikan ke settingan lama (jika perlu rollback)
            $table->dropForeign(['ketua_mpk_pendeta_id']);
            
            // Asumsi tabel pendeta masih ada
            if (Schema::hasTable('pendeta')) {
                $table->foreign('ketua_mpk_pendeta_id')
                      ->references('id')
                      ->on('pendeta')
                      ->nullOnDelete();
            }
        });
    }
};