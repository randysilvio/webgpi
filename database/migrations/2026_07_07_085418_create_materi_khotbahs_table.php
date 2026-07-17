<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi_khotbahs', function (Blueprint $table) {
            $table->id();
            $table->string('judul_dokumen');
            $table->string('kategori'); // Misal: Khotbah Minggu, Liturgi Hari Raya, Surat Gembala
            $table->text('deskripsi_singkat')->nullable();
            $table->decimal('harga_dokumen', 15, 2)->default(0); // 0 jika gratis
            $table->string('file_path'); // Path dokumen PDF/Word yang diunggah Bidang 1
            $table->string('cover_path')->nullable(); // Gambar sampul (opsional)
            $table->boolean('is_active')->default(true); // Bisa dinonaktifkan jika materi sudah usang
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete(); // Admin yang mengunggah
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_khotbahs');
    }
};