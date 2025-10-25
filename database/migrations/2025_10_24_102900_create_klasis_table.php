<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pastikan timestamp file ini SETELAH pendeta, SEBELUM jemaat
// Contoh: 2025_10_24_102901_create_klasis_table.php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_klasis');
            $table->string('kode_klasis')->nullable()->unique();
            $table->string('pusat_klasis')->nullable();
            $table->text('alamat_kantor')->nullable();
            $table->string('koordinat_gps')->nullable();
            $table->text('wilayah_pelayanan')->nullable();
            $table->date('tanggal_pembentukan')->nullable();
            $table->string('nomor_sk_pembentukan')->nullable();
            $table->string('klasis_induk')->nullable();
            $table->text('sejarah_singkat')->nullable();

            // Kolom untuk foreign key (tanpa constraint)
            $table->unsignedBigInteger('ketua_mpk_pendeta_id')->nullable();

            $table->string('telepon_kantor')->nullable();
            $table->string('email_klasis')->nullable()->unique();
            $table->string('website_klasis')->nullable();
            $table->string('foto_kantor_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klasis');
    }
};