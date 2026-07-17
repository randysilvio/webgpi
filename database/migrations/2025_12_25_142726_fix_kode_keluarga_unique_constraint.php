<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('anggota_jemaat', function (Blueprint $table) {
            // 1. Hapus aturan UNIQUE yang menyebabkan Error Duplicate Entry
            // Kita gunakan array syntax agar Laravel mencari nama index otomatis, 
            // atau gunakan string nama index persis dari pesan error Anda:
            // 'anggota_jemaat_kode_keluarga_internal_unique'
            $table->dropUnique(['kode_keluarga_internal']);
            
            // 2. Ganti menjadi INDEX biasa (agar pencarian cepat, tapi boleh kembar)
            $table->index('kode_keluarga_internal');
        });
    }

    public function down()
    {
        Schema::table('anggota_jemaat', function (Blueprint $table) {
            $table->dropIndex(['kode_keluarga_internal']);
            $table->unique('kode_keluarga_internal');
        });
    }
};