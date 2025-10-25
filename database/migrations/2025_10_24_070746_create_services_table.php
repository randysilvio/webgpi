// database/migrations/YYYY_MM_DD_HHMMSS_create_services_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul layanan (cth: Ibadah & Sakramen)
            $table->text('description')->nullable(); // Deskripsi singkat
            $table->text('list_items')->nullable(); // Daftar poin (simpan sbg teks, pisahkan dgn baris baru)
            $table->string('icon')->nullable(); // Nama ikon (cth: 'cross', 'book', 'heart')
            $table->string('color_theme')->default('blue'); // Tema warna (cth: 'blue', 'green', 'orange')
            $table->integer('order')->default(0); // Urutan tampil
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};