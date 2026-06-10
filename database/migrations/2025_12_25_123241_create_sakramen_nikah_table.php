<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sakramen_nikah')) {
            Schema::create('sakramen_nikah', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('mempelai_pria_id');
                $table->unsignedBigInteger('mempelai_wanita_id');
                $table->string('nomor_akta_nikah')->unique();
                $table->date('tanggal_nikah');
                $table->string('tempat_nikah');
                $table->unsignedBigInteger('pendeta_id')->nullable();
                $table->string('pendeta_pelayan_manual')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();

                $table->foreign('mempelai_pria_id')->references('id')->on('anggota_jemaat')->onDelete('cascade');
                $table->foreign('mempelai_wanita_id')->references('id')->on('anggota_jemaat')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sakramen_nikah');
    }
};