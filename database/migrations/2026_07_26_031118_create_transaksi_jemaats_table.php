<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('transaksi_jemaat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jemaat_id')->constrained('jemaat')->cascadeOnDelete();
            $table->string('kategori_kas')->default('Kas Induk Jemaat'); // Bisa diset "PHBG", dll.
            $table->date('tanggal');
            $table->string('uraian');
            $table->string('kode_ayat')->nullable(); // Ketik bebas sesuai contoh Excel
            $table->enum('jenis_transaksi', ['Pemasukan', 'Pengeluaran']);
            $table->decimal('nominal', 15, 2);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('transaksi_jemaat'); }
};