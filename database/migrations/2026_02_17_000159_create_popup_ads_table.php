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
        Schema::create('popup_ads', function (Blueprint $table) {
            $table->id();
            $table->string('judul'); // Judul iklan/pengumuman
            $table->string('gambar_path'); // Path penyimpanan gambar di storage
            $table->date('mulai_tanggal'); // Tanggal mulai tayang
            $table->date('selesai_tanggal'); // Tanggal selesai tayang
            $table->boolean('is_active')->default(true); // Status aktif manual (On/Off)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popup_ads');
    }
};