<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataAnggaran;

class MataAnggaranSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     */
    public function run(): void
    {
        $data = [
            // --- KELOMPOK PENDAPATAN (1.x) ---
            [
                'kode' => '1.1',
                'nama_mata_anggaran' => 'Penerimaan Persembahan Syukur Hari Minggu',
                'jenis' => 'Pendapatan',
                'kelompok' => 'Rutin',
                'deskripsi' => 'Penerimaan dari kotak persembahan ibadah hari minggu.'
            ],
            [
                'kode' => '1.2',
                'nama_mata_anggaran' => 'Penerimaan Iuran Wajib Anggota (Keluarga)',
                'jenis' => 'Pendapatan',
                'kelompok' => 'Rutin',
                'deskripsi' => 'Iuran bulanan/tahunan wajib dari setiap KK.'
            ],
            [
                'kode' => '1.3',
                'nama_mata_anggaran' => 'Penerimaan Persembahan Persepuluhan',
                'jenis' => 'Pendapatan',
                'kelompok' => 'Rutin',
                'deskripsi' => 'Penerimaan dari kewajiban persepuluhan anggota.'
            ],
            [
                'kode' => '1.4',
                'nama_mata_anggaran' => 'Penerimaan Khusus Pembangunan',
                'jenis' => 'Pendapatan',
                'kelompok' => 'Pembangunan',
                'deskripsi' => 'Dana yang dikumpulkan khusus untuk rehabilitasi atau pembangunan gedung.'
            ],

            // --- KELOMPOK BELANJA (2.x) ---
            [
                'kode' => '2.1',
                'nama_mata_anggaran' => 'Belanja Gaji & Tunjangan Pegawai',
                'jenis' => 'Belanja',
                'kelompok' => 'Personel',
                'deskripsi' => 'Pembayaran gaji pokok dan tunjangan keluarga pegawai organik.'
            ],
            [
                'kode' => '2.2',
                'nama_mata_anggaran' => 'Belanja Operasional Kantor',
                'jenis' => 'Belanja',
                'kelompok' => 'Operasional',
                'deskripsi' => 'Pembelian ATK, listrik, air, dan biaya pemeliharaan kantor.'
            ],
            [
                'kode' => '2.3',
                'nama_mata_anggaran' => 'Belanja Pelayanan Mimbar & Sakramen',
                'jenis' => 'Belanja',
                'kelompok' => 'Pelayanan',
                'deskripsi' => 'Biaya pelaksanaan sakramen baptisan, perjamuan kudus, dan honor pelayan mimbar.'
            ],
            [
                'kode' => '2.4',
                'nama_mata_anggaran' => 'Setoran Sentralisasi ke Sinode (Wajib)',
                'jenis' => 'Belanja',
                'kelompok' => 'Sentralisasi',
                'deskripsi' => 'Kewajiban penyetoran persentase pendapatan jemaat ke kas Sinode.'
            ],
            [
                'kode' => '2.5',
                'nama_mata_anggaran' => 'Belanja Bantuan Sosial & Diakonia',
                'jenis' => 'Belanja',
                'kelompok' => 'Pelayanan',
                'deskripsi' => 'Bantuan untuk anggota jemaat yang sakit atau berduka.'
            ],
        ];

        foreach ($data as $item) {
            MataAnggaran::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }
    }
}