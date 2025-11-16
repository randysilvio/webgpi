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
        Schema::create('wadah_kategorial_pengurus', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke jenis wadah (PAR, PAM, dll)
            $table->foreignId('jenis_wadah_id')->constrained('jenis_wadah_kategorial')->onDelete('cascade');
            
            // Lingkup Tingkatan (Sinode, Klasis, Jemaat)
            $table->enum('tingkat', ['sinode', 'klasis', 'jemaat']);
            
            // Foreign Keys Opsional (Tergantung Tingkat)
            $table->foreignId('klasis_id')->nullable()->constrained('klasis')->onDelete('cascade');
            $table->foreignId('jemaat_id')->nullable()->constrained('jemaat')->onDelete('cascade');
            
            // Relasi ke Personel (Bisa Anggota Jemaat atau User Login)
            $table->foreignId('anggota_jemaat_id')->nullable()->constrained('anggota_jemaat')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Jika pengurus punya akses login
            
            // Data Jabatan
            $table->string('jabatan'); // Ketua, Sekretaris, Bendahara, Koordinator Seksi X
            $table->string('nomor_sk')->nullable();
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->boolean('is_active')->default(true); // Status aktif
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wadah_kategorial_pengurus');
    }
};