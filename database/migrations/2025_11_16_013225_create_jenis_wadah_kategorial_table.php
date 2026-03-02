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
        Schema::create('jenis_wadah_kategorial', function (Blueprint $table) {
            $table->id();
            $table->string('nama_wadah')->unique(); // Contoh: PAR, PAM, PW, PKB
            $table->integer('rentang_usia_min')->nullable(); // Untuk filter statistik otomatis
            $table->integer('rentang_usia_max')->nullable(); // Untuk filter statistik otomatis
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_wadah_kategorial');
    }
};