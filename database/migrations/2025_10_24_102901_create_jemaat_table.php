<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pastikan timestamp file ini SETELAH klasis, SEBELUM anggota_jemaat dan pendeta (jika pendeta merujuk ke jemaat)
// Contoh: 2025_10_24_102902_create_jemaat_table.php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jemaat', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jemaat');
            $table->string('kode_jemaat')->nullable()->unique();

            // Kolom untuk foreign key (tanpa constraint)
            $table->unsignedBigInteger('klasis_id'); // Wajib

            $table->text('alamat_gereja')->nullable();
            $table->string('koordinat_gps')->nullable();
            $table->date('tanggal_berdiri')->nullable();
            $table->enum('status_jemaat', ['Mandiri', 'Bakal Jemaat', 'Pos Pelayanan'])->default('Mandiri');
            $table->enum('jenis_jemaat', ['Umum', 'Kategorial'])->default('Umum');
            $table->string('nomor_sk_pendirian')->nullable();
            $table->string('jemaat_induk')->nullable();
            $table->text('sejarah_singkat')->nullable();
            $table->string('nama_ketua_majelis')->nullable();
            $table->string('telepon_ketua_majelis')->nullable();
            $table->string('nama_sekretaris_majelis')->nullable();
            $table->integer('jumlah_pendeta')->default(0);
            $table->integer('jumlah_penatua')->default(0);
            $table->integer('jumlah_diaken')->default(0);
            $table->integer('jumlah_pengajar')->default(0);
            $table->string('periode_majelis')->nullable();
            $table->integer('jumlah_kk')->default(0);
            $table->integer('jumlah_anggota_baptis_anak')->default(0);
            $table->integer('jumlah_anggota_sidi')->default(0);
            $table->integer('jumlah_total_jiwa')->default(0);
            $table->integer('jumlah_sektor')->nullable();
            $table->integer('jumlah_unit')->nullable();
            $table->date('tanggal_update_statistik')->nullable();
            $table->string('status_gedung_gereja')->nullable();
            $table->integer('kapasitas_gedung')->nullable();
            $table->string('status_tanah_gereja')->nullable();
            $table->string('luas_tanah')->nullable();
            $table->string('telepon_kantor')->nullable();
            $table->string('email_jemaat')->nullable()->unique();
            $table->string('website_jemaat')->nullable();
            $table->string('foto_gereja_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jemaat');
    }
};