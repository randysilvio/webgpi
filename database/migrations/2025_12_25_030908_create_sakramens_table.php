<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('sakramen_baptis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_jemaat_id')->constrained('anggota_jemaat')->onDelete('cascade');
            $table->string('no_akta_baptis')->unique();
            $table->date('tanggal_baptis');
            $table->string('tempat_baptis');
            $table->string('pendeta_pelayan');
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->timestamps();
        });

        Schema::create('sakramen_sidi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_jemaat_id')->constrained('anggota_jemaat')->onDelete('cascade');
            $table->string('no_akta_sidi')->unique();
            $table->date('tanggal_sidi');
            $table->string('tempat_sidi');
            $table->string('pendeta_pelayan');
            $table->timestamps();
        });

        Schema::create('sakramen_nikah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suami_id')->constrained('anggota_jemaat')->onDelete('cascade');
            $table->foreignId('istri_id')->constrained('anggota_jemaat')->onDelete('cascade');
            $table->string('no_akta_nikah')->unique();
            $table->date('tanggal_nikah');
            $table->string('tempat_nikah');
            $table->string('pendeta_pelayan');
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('sakramen_nikah');
        Schema::dropIfExists('sakramen_sidi');
        Schema::dropIfExists('sakramen_baptis');
    }
};