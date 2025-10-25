// database/migrations/YYYY_MM_DD_HHMMSS_create_settings_table.php
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
        Schema::create('settings', function (Blueprint $table) {
            $table->id(); // Kunci utama

            // Identitas & Tampilan
            $table->string('site_name')->nullable();
            $table->string('site_tagline')->nullable();
            $table->string('logo_path')->nullable(); // Simpan path file logo

            // Konten Halaman Depan
            $table->text('hero_text')->nullable();
            $table->text('about_us')->nullable();
            $table->text('vision')->nullable();
            $table->string('about_image_path')->nullable(); // Simpan path file ilustrasi

            // Kontak
            $table->text('contact_address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_website')->nullable();
            $table->string('work_hours')->nullable();

            // Media Sosial
            $table->string('social_facebook')->nullable();
            $table->string('social_youtube')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_twitter')->nullable();

            // Footer
            $table->text('footer_description')->nullable();

            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};