<?php

namespace App\Exports;

use App\Models\Klasis;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth; // Jika perlu scoping

class KlasisExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Nanti tambahkan scoping jika diperlukan (misal Super Admin export semua)
        return Klasis::with('ketuaMp')->get();
    }

    /**
     * Definisikan header kolom. HARUS SESUAI DENGAN TEMPLATE IMPORT!
     */
    public function headings(): array
    {
        return [
            'ID Ketua MPK (Pendeta ID, Wajib jika ada)', // Kolom bantuan import
            'Nama Ketua MPK (Readonly)',
            'Nama Klasis (Wajib)',
            'Kode Klasis (Unik, Opsional)',
            'Pusat Klasis',
            'Alamat Kantor',
            'Telepon Kantor',
            'Email Klasis (Unik, Opsional)',
            'Website Klasis (Opsional)',
            'Tanggal Pembentukan (YYYY-MM-DD)',
            'Nomor SK Pembentukan',
            'Klasis Induk (Jika Pemekaran)',
            'Wilayah Pelayanan (Deskripsi)',
            'Sejarah Singkat',
            // 'Koordinat GPS', // Opsional ditambahkan jika perlu
            // 'Foto Kantor Path', // Biasanya tidak diimport/export
        ];
    }

    /**
     * Petakan data ke baris Excel/CSV. URUTAN HARUS SAMA DENGAN HEADINGS!
     */
    public function map($klasis): array
    {
        return [
            $klasis->ketua_mpk_pendeta_id, // Untuk referensi import
            $klasis->ketuaMp->nama_lengkap ?? '', // Tampilkan nama ketua
            $klasis->nama_klasis,
            $klasis->kode_klasis,
            $klasis->pusat_klasis,
            $klasis->alamat_kantor,
            $klasis->telepon_kantor,
            $klasis->email_klasis,
            $klasis->website_klasis,
            optional($klasis->tanggal_pembentukan)->format('Y-m-d'),
            $klasis->nomor_sk_pembentukan,
            $klasis->klasis_induk,
            $klasis->wilayah_pelayanan,
            $klasis->sejarah_singkat,
        ];
    }
}