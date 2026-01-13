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
        // 1. Tabel Pegawai (Master Data SDM)
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            
            // Identitas Utama
            $table->string('nipg', 20)->unique()->comment('Nomor Induk Pegawai Gereja');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Data Pribadi
            $table->string('nama_lengkap');
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('status_pernikahan')->nullable(); // Menikah, Belum, Janda/Duda
            $table->string('golongan_darah', 5)->nullable();
            
            // Kontak & Alamat
            $table->string('nik_ktp', 20)->nullable();
            $table->text('alamat_domisili')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('email')->nullable();
            
            // Data Kepegawaian
            // Jenis: Pendeta, Pengajar, Pegawai Kantor (TU), Koster/Tuagama, Lainnya
            $table->string('jenis_pegawai', 50); 
            // Status: Organik (Tetap), Kontrak, Honorer
            $table->string('status_kepegawaian', 50);
            // Status Aktif: Aktif, Cuti, Tugas Belajar, MPP, Pensiun, Meninggal, Diberhentikan
            $table->string('status_aktif', 50)->default('Aktif');
            
            $table->string('golongan_terakhir')->nullable(); // Misal: III/a, IV/b
            $table->string('jabatan_terakhir')->nullable();  // Misal: Ketua Klasis, Staff Keuangan
            $table->date('tmt_pegawai')->nullable(); // Tanggal Mulai Tugas Pertama
            $table->date('tanggal_pensiun')->nullable(); // Estimasi (Tgl Lahir + 60 Thn)
            
            // Data Administrasi Negara (Opsional)
            $table->string('npwp', 30)->nullable();
            $table->string('no_bpjs_kesehatan', 30)->nullable();
            $table->string('no_bpjs_ketenagakerjaan', 30)->nullable();
            
            // Lokasi Tugas Saat Ini (Snapshot)
            $table->foreignId('klasis_id')->nullable()->constrained('klasis')->onDelete('set null');
            $table->foreignId('jemaat_id')->nullable()->constrained('jemaat')->onDelete('set null');
            
            $table->string('foto_diri')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Agar data pegawai tidak hilang permanen jika dihapus
        });

        // 2. Tabel Keluarga Pegawai (Untuk Tunjangan)
        Schema::create('keluarga_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            
            $table->string('nama_lengkap');
            $table->enum('hubungan', ['Suami', 'Istri', 'Anak']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 10)->nullable();
            
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('pekerjaan')->nullable();
            
            // Status Tunjangan: Apakah masuk dalam perhitungan gaji/tunjangan?
            // (Misal: Anak > 25 tahun atau sudah menikah = false)
            $table->boolean('status_tunjangan')->default(false);
            $table->string('keterangan')->nullable();
            
            $table->timestamps();
        });

        // 3. Tabel Riwayat Pendidikan
        Schema::create('riwayat_pendidikan_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            
            $table->string('jenjang', 20); // SD, SMP, SMA, D3, S1, S2, S3
            $table->string('nama_institusi');
            $table->string('jurusan')->nullable();
            $table->year('tahun_lulus');
            $table->string('gelar_akademis')->nullable();
            $table->string('nomor_ijazah')->nullable();
            $table->string('file_ijazah')->nullable(); // Upload PDF/JPG
            
            $table->timestamps();
        });

        // 4. Tabel Riwayat SK / Kepangkatan (Track Record)
        Schema::create('riwayat_sk_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            
            $table->string('nomor_sk');
            $table->date('tanggal_sk');
            $table->date('tmt_sk'); // Terhitung Mulai Tanggal
            
            // Jenis SK: Pengangkatan, Kenaikan Pangkat, Kenaikan Gaji Berkala, Mutasi, Pensiun
            $table->string('jenis_sk'); 
            
            $table->string('golongan_baru')->nullable();
            $table->string('jabatan_baru')->nullable();
            $table->decimal('gaji_pokok_baru', 15, 2)->nullable(); // Opsional (Rahasia)
            
            $table->string('pejabat_penanda_tangan')->nullable();
            $table->string('file_sk')->nullable(); // Upload PDF
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_sk_pegawai');
        Schema::dropIfExists('riwayat_pendidikan_pegawai');
        Schema::dropIfExists('keluarga_pegawai');
        Schema::dropIfExists('pegawai');
    }
};