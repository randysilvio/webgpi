<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('module_bidang1')->default(true)->after('footer_description');
            $table->boolean('module_bidang2')->default(true)->after('module_bidang1');
            $table->boolean('module_bidang3')->default(true)->after('module_bidang2');
            $table->boolean('module_bidang4')->default(true)->after('module_bidang3');
            $table->boolean('module_wilayah')->default(true)->after('module_bidang4');
            $table->boolean('module_laporan')->default(true)->after('module_wilayah');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'module_bidang1', 'module_bidang2', 'module_bidang3', 
                'module_bidang4', 'module_wilayah', 'module_laporan'
            ]);
        });
    }
};