<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom relasi ke jenis wadah setelah kolom jemaat_id
            $table->foreignId('jenis_wadah_id')
                  ->nullable()
                  ->after('jemaat_id')
                  ->constrained('jenis_wadah_kategorial')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['jenis_wadah_id']);
            $table->dropColumn('jenis_wadah_id');
        });
    }
};