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
        Schema::table('klasis', function (Blueprint $table) {
            // Menambahkan kolom baru untuk fitur Peta
            // nullable() digunakan karena data lama mungkin belum memiliki koordinat
            
            $table->string('kabupaten_kota')->nullable()->after('alamat_kantor')
                  ->comment('Nama Kabupaten/Kota sesuai peta GeoJSON untuk pewarnaan wilayah');
            
            $table->string('latitude')->nullable()->after('kabupaten_kota')
                  ->comment('Koordinat Garis Lintang');
            
            $table->string('longitude')->nullable()->after('latitude')
                  ->comment('Koordinat Garis Bujur');
            
            $table->string('warna_peta')->default('#3B82F6')->after('longitude')
                  ->comment('Kode warna HEX untuk wilayah klasis di peta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('klasis', function (Blueprint $table) {
            $table->dropColumn(['kabupaten_kota', 'latitude', 'longitude', 'warna_peta']);
        });
    }
};