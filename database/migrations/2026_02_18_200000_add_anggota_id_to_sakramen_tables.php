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
        // Perbaiki Tabel Sakramen Baptis
        if (Schema::hasTable('sakramen_baptis')) {
            Schema::table('sakramen_baptis', function (Blueprint $table) {
                if (!Schema::hasColumn('sakramen_baptis', 'anggota_id')) {
                    $table->unsignedBigInteger('anggota_id')->nullable()->after('id')->index();
                    // Tambahkan Foreign Key (Opsional, agar data konsisten)
                    $table->foreign('anggota_id')->references('id')->on('anggota_jemaat')->onDelete('cascade');
                }
            });
        }

        // Perbaiki Tabel Sakramen Sidi
        if (Schema::hasTable('sakramen_sidi')) {
            Schema::table('sakramen_sidi', function (Blueprint $table) {
                if (!Schema::hasColumn('sakramen_sidi', 'anggota_id')) {
                    $table->unsignedBigInteger('anggota_id')->nullable()->after('id')->index();
                    // Tambahkan Foreign Key
                    $table->foreign('anggota_id')->references('id')->on('anggota_jemaat')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sakramen_baptis')) {
            Schema::table('sakramen_baptis', function (Blueprint $table) {
                if (Schema::hasColumn('sakramen_baptis', 'anggota_id')) {
                    $table->dropForeign(['anggota_id']); // Hapus FK dulu
                    $table->dropColumn('anggota_id');
                }
            });
        }

        if (Schema::hasTable('sakramen_sidi')) {
            Schema::table('sakramen_sidi', function (Blueprint $table) {
                if (Schema::hasColumn('sakramen_sidi', 'anggota_id')) {
                    $table->dropForeign(['anggota_id']);
                    $table->dropColumn('anggota_id');
                }
            });
        }
    }
};