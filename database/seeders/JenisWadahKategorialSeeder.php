<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisWadahKategorial;

class JenisWadahKategorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Data berdasarkan Peraturan Pokok Nomor 6 Tentang Wadah-Wadah Pelayanan Kategorial
     * Pasal 1 (Nama & Usia) dan Pasal 8 (Motto).
     */
    public function run(): void
    {
        $wadahs = [
            [
                'nama_wadah' => 'PAR', // Persekutuan Anak dan Remaja
                'rentang_usia_min' => 0,
                'rentang_usia_max' => 16,
                'deskripsi' => 'Persekutuan Anak dan Remaja. Motto: Hidup, Bertumbuh dan Berakar di Dalam Kristus (Kolose 2:7a).',
            ],
            [
                'nama_wadah' => 'PP', // Persekutuan Pemuda
                'rentang_usia_min' => 17,
                'rentang_usia_max' => 30,
                'deskripsi' => 'Persekutuan Pemuda. Motto: Pergilah dan Berbuahlah (Yohanes 15:16).',
            ],
            [
                'nama_wadah' => 'PERWATA', // Persekutuan Wanita
                'rentang_usia_min' => 31,
                'rentang_usia_max' => 54,
                'deskripsi' => 'Persekutuan Wanita. Motto: Melayani Bukan Dengan Perkataan, Tetapi Dengan Perbuatan (1 Yohanes 3:18).',
            ],
            [
                'nama_wadah' => 'PERPRI', // Persekutuan Pria
                'rentang_usia_min' => 31,
                'rentang_usia_max' => 54,
                'deskripsi' => 'Persekutuan Pria. Motto: Hendaklah Kamu Cerdik Seperti Ular dan Tulus Seperti Merpati (Matius 10:16b).',
            ],
            [
                'nama_wadah' => 'PERLANSIA', // Persekutuan Lanjut Usia
                'rentang_usia_min' => 55,
                'rentang_usia_max' => 150, // Diset 150 untuk mengakomodasi "55 tahun ke atas"
                'deskripsi' => 'Persekutuan Lanjut Usia. Motto: Takut Akan Tuhan Memperpanjang Umur (Amsal 10:27a).',
            ],
        ];

        foreach ($wadahs as $wadah) {
            JenisWadahKategorial::updateOrCreate(
                ['nama_wadah' => $wadah['nama_wadah']], // Cek berdasarkan nama agar tidak duplikat
                $wadah
            );
        }
    }
}