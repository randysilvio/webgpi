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
        Schema::table('users', function (Blueprint $table) {
            // Cek apakah kolom 'pendeta_id' BELUM ADA sebelum menambahkannya
            if (!Schema::hasColumn('users', 'pendeta_id')) {
                $table->foreignId('pendeta_id')->nullable()->after('password')->constrained('pendeta')->nullOnDelete();
            }

            // Cek apakah kolom 'klasis_id' BELUM ADA sebelum menambahkannya
            if (!Schema::hasColumn('users', 'klasis_id')) {
                $table->foreignId('klasis_id')->nullable()->after('pendeta_id')->constrained('klasis')->nullOnDelete();
            }

            // Cek apakah kolom 'jemaat_id' BELUM ADA sebelum menambahkannya
            if (!Schema::hasColumn('users', 'jemaat_id')) {
                $table->foreignId('jemaat_id')->nullable()->after('klasis_id')->constrained('jemaat')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek apakah kolom ADA sebelum mencoba menghapusnya
            // Dihapus dalam urutan terbalik dari method up()

            if (Schema::hasColumn('users', 'jemaat_id')) {
                // Method ini akan otomatis menghapus foreign key constraint dan kolomnya
                $table->dropConstrainedForeignId('jemaat_id');
            }

            if (Schema::hasColumn('users', 'klasis_id')) {
                $table->dropConstrainedForeignId('klasis_id');
            }

            if (Schema::hasColumn('users', 'pendeta_id')) {
                $table->dropConstrainedForeignId('pendeta_id');
            }
        });
    }
};