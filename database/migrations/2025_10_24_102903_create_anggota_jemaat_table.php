<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pastikan timestamp file ini SETELAH jemaat
// Contoh: 2025_10_24_102903_create_anggota_jemaat_table.php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_jemaat', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nik')->nullable()->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('status_pernikahan')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('pekerjaan_utama')->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('nomor_buku_induk')->nullable()->unique();

            // Kolom untuk foreign key (tanpa constraint)
            $table->unsignedBigInteger('jemaat_id'); // Wajib

            $table->string('sektor_pelayanan')->nullable();
            $table->string('unit_pelayanan')->nullable();
            $table->date('tanggal_baptis')->nullable();
            $table->string('tempat_baptis')->nullable();
            $table->date('tanggal_sidi')->nullable();
            $table->string('tempat_sidi')->nullable();
            $table->date('tanggal_masuk_jemaat')->nullable();
            $table->enum('status_keanggotaan', ['Aktif', 'Tidak Aktif', 'Pindah', 'Meninggal'])->default('Aktif');
            $table->string('asal_gereja_sebelumnya')->nullable();
            $table->string('nomor_atestasi')->nullable();
            $table->string('jabatan_pelayan_khusus')->nullable();
            $table->string('wadah_kategorial')->nullable();
            $table->text('keterlibatan_lain')->nullable();
            $table->string('status_dalam_keluarga')->nullable();
            $table->string('nama_kepala_keluarga')->nullable();
            $table->string('status_pekerjaan_kk')->nullable();
            $table->string('sektor_pekerjaan_kk')->nullable();
            $table->string('status_kepemilikan_rumah')->nullable();
            $table->string('sumber_penerangan')->nullable();
            $table->string('sumber_air_minum')->nullable();
            $table->string('perkiraan_pendapatan_keluarga')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_jemaat');
    }
};