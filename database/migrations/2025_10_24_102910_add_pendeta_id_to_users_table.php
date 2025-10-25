<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pastikan timestamp file ini SETELAH semua tabel utama (pendeta, klasis, jemaat)
// Contoh: 2025_10_24_102910_add_pendeta_id_to_users_table.php

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hanya tambahkan kolomnya
            $table->unsignedBigInteger('pendeta_id')->nullable()->after('remember_token');
            $table->unsignedBigInteger('klasis_id')->nullable()->after('pendeta_id');
            $table->unsignedBigInteger('jemaat_id')->nullable()->after('klasis_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
             // Cukup drop kolomnya saja
            $table->dropColumn(['pendeta_id', 'klasis_id', 'jemaat_id']);
        });
    }
};