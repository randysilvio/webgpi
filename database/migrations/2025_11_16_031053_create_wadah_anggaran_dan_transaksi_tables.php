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
        // 1. Tabel Anggaran (Rencana Penerimaan & Pengeluaran)
        Schema::create('wadah_kategorial_anggaran', function (Blueprint $table) {
            $table->id();

            // Relasi Wilayah & Wadah
            $table->foreignId('jenis_wadah_id')->constrained('jenis_wadah_kategorial')->onDelete('cascade');
            $table->enum('tingkat', ['sinode', 'klasis', 'jemaat']);
            $table->foreignId('klasis_id')->nullable()->constrained('klasis')->onDelete('cascade');
            $table->foreignId('jemaat_id')->nullable()->constrained('jemaat')->onDelete('cascade');

            // Relasi ke Program Kerja (Opsional, karena ada biaya rutin/operasional non-proker)
            $table->foreignId('program_kerja_id')
                  ->nullable()
                  ->constrained('wadah_kategorial_program_kerja')
                  ->onDelete('cascade');

            $table->year('tahun_anggaran');
            $table->enum('jenis_anggaran', ['penerimaan', 'pengeluaran']); 
            $table->string('nama_pos_anggaran'); // Contoh: "Iuran Anggota" atau "Biaya Konsumsi Rapat"
            $table->text('keterangan')->nullable();

            // Nominal
            $table->decimal('jumlah_target', 15, 2)->default(0); // Rencana
            $table->decimal('jumlah_realisasi', 15, 2)->default(0); // Realisasi (Update otomatis via transaksi)

            $table->timestamps();

            // Index
            $table->index(['jenis_wadah_id', 'tahun_anggaran'], 'idx_wang_jenis_thn');
            $table->index(['tingkat', 'klasis_id', 'jemaat_id'], 'idx_wang_lokasi');
        });

        // 2. Tabel Transaksi (Realisasi Uang Masuk/Keluar)
        Schema::create('wadah_kategorial_transaksi', function (Blueprint $table) {
            $table->id();

            // Relasi ke Pos Anggaran (Wajib, agar terlacak ini transaksi untuk pos apa)
            $table->foreignId('anggaran_id')
                  ->constrained('wadah_kategorial_anggaran')
                  ->onDelete('cascade');

            $table->date('tanggal_transaksi');
            $table->enum('jenis_transaksi', ['masuk', 'keluar']); // Masuk (Penerimaan), Keluar (Belanja)
            
            $table->decimal('jumlah', 15, 2);
            $table->string('uraian'); // Detail transaksi, misal: "Terima Iuran dari Sektor A"
            
            // Bukti (Opsional)
            $table->string('bukti_transaksi')->nullable(); // Path file upload (Nota/Kuitansi)
            
            // Audit Trail
            $table->foreignId('dicatat_oleh_user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Index
            $table->index(['anggaran_id', 'tanggal_transaksi'], 'idx_wtrans_anggaran_tgl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wadah_kategorial_transaksi');
        Schema::dropIfExists('wadah_kategorial_anggaran');
    }
};