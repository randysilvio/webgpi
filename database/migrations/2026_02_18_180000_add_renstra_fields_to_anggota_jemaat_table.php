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
        Schema::table('anggota_jemaat', function (Blueprint $table) {
            // Sektor Kesejahteraan & Hunian
            // Disimpan sebagai String (Permanen, Semi-Permanen, Darurat)
            $table->string('kondisi_rumah')->nullable()->after('status_kepemilikan_rumah'); 
            
            // Disimpan sebagai String (< 1jt, 1jt-3jt, > 3jt)
            $table->string('rentang_pengeluaran')->nullable()->after('perkiraan_pendapatan_keluarga');
            
            // Sektor Ekonomi & Aset
            // Disimpan sebagai Text karena berisi array/checkbox (contoh: "Perkebunan, Peternakan")
            $table->text('aset_ekonomi')->nullable()->after('rentang_pengeluaran'); 
            
            // Sektor Kesehatan & Disabilitas
            $table->string('disabilitas')->default('Tidak Ada')->after('golongan_darah');
            
            // Sektor Digital
            $table->boolean('punya_smartphone')->default(false)->after('telepon');
            $table->boolean('akses_internet')->default(false)->after('punya_smartphone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota_jemaat', function (Blueprint $table) {
            $table->dropColumn([
                'kondisi_rumah', 
                'rentang_pengeluaran', 
                'aset_ekonomi', 
                'disabilitas', 
                'punya_smartphone', 
                'akses_internet'
            ]);
        });
    }
};