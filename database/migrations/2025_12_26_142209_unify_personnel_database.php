<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan Kolom Khas Pendeta ke Tabel Pegawai (Cek dulu biar gak error)
        if (!Schema::hasColumn('pegawai', 'tanggal_tahbisan')) {
            Schema::table('pegawai', function (Blueprint $table) {
                $table->date('tanggal_tahbisan')->nullable()->after('tanggal_pensiun');
                $table->string('tempat_tahbisan')->nullable()->after('tanggal_tahbisan');
                $table->string('nomor_sk_kependetaan')->nullable()->after('tempat_tahbisan');
                $table->string('pendidikan_teologi_terakhir')->nullable()->after('nomor_sk_kependetaan');
                $table->string('institusi_pendidikan_teologi')->nullable()->after('pendidikan_teologi_terakhir');
                $table->text('catatan_khusus')->nullable();
            });
        }

        // 2. Persiapan Kolom Relasi Baru (Cek dulu)
        if (!Schema::hasColumn('mutasi_pendeta', 'pegawai_id')) {
            Schema::table('mutasi_pendeta', function (Blueprint $table) {
                $table->unsignedBigInteger('pegawai_id')->nullable()->after('id');
            });
        }

        if (!Schema::hasColumn('users', 'pegawai_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('pegawai_id')->nullable()->after('id');
            });
        }

        // 3. SKRIP MIGRASI DATA
        // Pindahkan data hanya jika masih ada data di tabel pendeta
        if (Schema::hasTable('pendeta')) {
            Schema::disableForeignKeyConstraints();
            
            $pendetas = DB::table('pendeta')->get();

            foreach ($pendetas as $p) {
                $existingPegawai = DB::table('pegawai')->where('nipg', $p->nipg)->first();

                if ($existingPegawai) {
                    DB::table('pegawai')->where('id', $existingPegawai->id)->update([
                        'jenis_pegawai' => 'Pendeta',
                        'tanggal_tahbisan' => $p->tanggal_tahbisan,
                        'tempat_tahbisan' => $p->tempat_tahbisan,
                        'nomor_sk_kependetaan' => $p->nomor_sk_kependetaan,
                        'pendidikan_teologi_terakhir' => $p->pendidikan_teologi_terakhir,
                        'institusi_pendidikan_teologi' => $p->institusi_pendidikan_teologi,
                        'klasis_id' => $p->klasis_penempatan_id,
                        'jemaat_id' => $p->jemaat_penempatan_id,
                    ]);
                    $newPegawaiId = $existingPegawai->id;
                } else {
                    $newPegawaiId = DB::table('pegawai')->insertGetId([
                        'nipg' => $p->nipg,
                        'nama_lengkap' => $p->nama_lengkap,
                        'tempat_lahir' => $p->tempat_lahir,
                        'tanggal_lahir' => $p->tanggal_lahir,
                        'jenis_kelamin' => $p->jenis_kelamin == 'Laki-laki' ? 'L' : 'P',
                        'status_pernikahan' => $p->status_pernikahan,
                        'golongan_darah' => $p->golongan_darah,
                        'alamat_domisili' => $p->alamat_domisili,
                        'no_hp' => $p->telepon,
                        'jenis_pegawai' => 'Pendeta',
                        'status_kepegawaian' => $p->status_kepegawaian,
                        'status_aktif' => 'Aktif',
                        'klasis_id' => $p->klasis_penempatan_id,
                        'jemaat_id' => $p->jemaat_penempatan_id,
                        'tanggal_tahbisan' => $p->tanggal_tahbisan,
                        'tempat_tahbisan' => $p->tempat_tahbisan,
                        'nomor_sk_kependetaan' => $p->nomor_sk_kependetaan,
                        'pendidikan_teologi_terakhir' => $p->pendidikan_teologi_terakhir,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Update Relasi
                DB::table('mutasi_pendeta')
                    ->where('pendeta_id', $p->id)
                    ->update(['pegawai_id' => $newPegawaiId]);

                DB::table('users')
                    ->where('pendeta_id', $p->id)
                    ->update(['pegawai_id' => $newPegawaiId]);
            }
            
            Schema::enableForeignKeyConstraints();
        }

        // 4. CLEANUP & FINALISASI (Dengan Penanganan Error Foreign Key)
        
        // --- Tabel MUTASI PENDETA ---
        Schema::table('mutasi_pendeta', function (Blueprint $table) {
            // Cek apakah kolom lama masih ada
            if (Schema::hasColumn('mutasi_pendeta', 'pendeta_id')) {
                // DROP FOREIGN KEY DULU! (Ini yang bikin error sebelumnya)
                // Menggunakan array sintaks agar Laravel mendeteksi nama constraint otomatis
                $table->dropForeign(['pendeta_id']); 
                $table->dropColumn('pendeta_id');
            }
            
            // Tambah constraint ke kolom baru (jika belum ada)
            // Kita cek pakai try-catch manual atau asumsikan aman karena baru dibuat
            try {
                $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
            } catch (\Exception $e) {
                // Ignore jika key sudah ada
            }
        });

        // --- Tabel USERS ---
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'pendeta_id')) {
                // DROP FOREIGN KEY DULU!
                $table->dropForeign(['pendeta_id']);
                $table->dropColumn('pendeta_id');
            }

            try {
                $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');
            } catch (\Exception $e) {
                // Ignore
            }
        });
    }

    public function down(): void
    {
        // Tidak perlu rollback untuk migrasi unifikasi one-way
    }
};