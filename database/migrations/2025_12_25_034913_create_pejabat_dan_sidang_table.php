<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // Tabel Pejabat Gerejawi (Penatua & Diaken)
        Schema::create('pejabat_gerejawi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_jemaat_id')->constrained('anggota_jemaat')->onDelete('cascade');
            $table->enum('jabatan', ['Penatua', 'Diaken']);
            $table->string('periode_mulai'); // Contoh: 2022
            $table->string('periode_selesai'); // Contoh: 2027
            $table->string('no_sk_pelantikan')->nullable();
            $table->enum('status_aktif', ['Aktif', 'Demisioner', 'Emeritus'])->default('Aktif');
            $table->timestamps();
        });

        // Tabel Risalah Sidang
        Schema::create('risalah_sidang', function (Blueprint $table) {
            $table->id();
            $table->string('judul_sidang'); // Contoh: Sidang Jemaat XXX
            $table->date('tanggal_sidang');
            $table->enum('tingkat_sidang', ['Jemaat', 'Klasis', 'Sinode']);
            $table->text('ringkasan_keputusan');
            $table->string('file_risalah')->nullable(); // Upload PDF Hasil Sidang
            $table->foreignId('klasis_id')->nullable()->constrained('klasis');
            $table->foreignId('jemaat_id')->nullable()->constrained('jemaat');
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('risalah_sidang');
        Schema::dropIfExists('pejabat_gerejawi');
    }
};