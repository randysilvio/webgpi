<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_pelayanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jemaat_id')->constrained('jemaat')->onDelete('cascade');
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade'); // ID Pendeta
            $table->string('kategori'); // Misal: Diakonia, Pembangunan, Resolusi Konflik, Pengajaran
            $table->date('tanggal_kegiatan');
            $table->text('konteks_situasi'); // Analisis Pendeta
            $table->text('tindak_lanjut')->nullable(); // PR/Rekomendasi untuk penerus
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_pelayanans');
    }
};