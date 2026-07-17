<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_materis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_registrasi')->unique(); // ID Transaksi (misal: TRK-202607001)
            $table->foreignId('materi_khotbah_id')->constrained('materi_khotbahs')->onDelete('cascade');
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade'); // Pendeta pembeli
            $table->enum('status_pembayaran', ['Menunggu Verifikasi', 'Lunas', 'Ditolak'])->default('Menunggu Verifikasi');
            $table->string('bukti_transfer_path'); // Foto struk bayar
            $table->text('catatan_admin')->nullable(); // Alasan jika ditolak
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_materis');
    }
};