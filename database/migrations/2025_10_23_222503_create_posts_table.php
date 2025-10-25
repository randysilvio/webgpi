// database/migrations/YYYY_MM_DD_HHMMSS_create_posts_table.php
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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul berita/kegiatan
            $table->string('slug')->unique(); // Untuk URL yang ramah SEO
            $table->longText('content'); // Isi berita/kegiatan
            $table->string('image_path')->nullable(); // Path gambar unggulan
            $table->timestamp('published_at')->nullable(); // Kapan dipublikasikan (null jika draft)
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};