<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pastikan timestamp file ini SEBELUM klasis, jemaat, anggota_jemaat
// Contoh: 2025_10_24_102900_create_pendeta_table.php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendeta', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nik')->nullable()->unique();
            $table->string('nipg')->unique();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('status_pernikahan')->nullable();
            $table->string('nama_pasangan')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->text('alamat_domisili')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable()->unique();
            $table->date('tanggal_tahbisan');
            $table->string('tempat_tahbisan');
            $table->string('nomor_sk_kependetaan')->nullable();
            $table->string('status_kepegawaian')->default('Aktif');
            $table->string('pendidikan_teologi_terakhir')->nullable();
            $table->string('institusi_pendidikan_teologi')->nullable();
            $table->string('golongan_pangkat_terakhir')->nullable();
            $table->date('tanggal_mulai_masuk_gpi')->nullable();

            // Kolom untuk foreign keys (tanpa constraint)
            $table->unsignedBigInteger('klasis_penempatan_id')->nullable();
            $table->unsignedBigInteger('jemaat_penempatan_id')->nullable();

            $table->string('jabatan_saat_ini')->nullable();
            $table->date('tanggal_mulai_jabatan_saat_ini')->nullable();
            $table->string('foto_path')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendeta');
    }
};