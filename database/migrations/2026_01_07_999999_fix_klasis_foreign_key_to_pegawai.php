<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pastikan kolom 'ketua_mpk_pendeta_id' bersifat nullable terlebih dahulu
        // Ini penting agar kita bisa melakukan nullOnDelete()
        Schema::table('klasis', function (Blueprint $table) {
            $table->unsignedBigInteger('ketua_mpk_pendeta_id')->nullable()->change();
        });

        // 2. Bersihkan data yang tidak valid
        // Kita set NULL semua ID yang tidak ada di tabel 'pegawai'
        DB::table('klasis')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('pegawai')
                      ->whereRaw('pegawai.id = klasis.ketua_mpk_pendeta_id');
            })
            ->update(['ketua_mpk_pendeta_id' => null]);

        // 3. Modifikasi Foreign Key
        Schema::table('klasis', function (Blueprint $table) {
            // Hapus constraint lama jika ada (bungkus dalam try-catch agar tidak error jika sudah tidak ada)
            try {
                $table->dropForeign(['ketua_mpk_pendeta_id']);
            } catch (\Exception $e) {
                // Abaikan jika constraint tidak ditemukan
            }

            // Buat Constraint Baru mengarah ke 'pegawai'
            $table->foreign('ketua_mpk_pendeta_id')
                  ->references('id')
                  ->on('pegawai')
                  ->nullOnDelete(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('klasis', function (Blueprint $table) {
            $table->dropForeign(['ketua_mpk_pendeta_id']);
            
            if (Schema::hasTable('pendeta')) {
                $table->foreign('ketua_mpk_pendeta_id')
                      ->references('id')
                      ->on('pendeta')
                      ->nullOnDelete();
            }
        });
    }
};