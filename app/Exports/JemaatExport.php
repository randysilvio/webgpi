<?php

namespace App\Exports;

use App\Models\Jemaat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth; // Jika perlu scoping
use App\Models\Klasis; // Jika perlu scoping

class JemaatExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Jemaat::with('klasis'); // Eager load klasis

        // --- Scoping (AKTIFKAN SETELAH LOGIN & ROLE USER TERHUBUNG) ---
        /*
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('Admin Klasis')) {
                $query->where('klasis_id', $user->klasis_id);
            } elseif ($user->hasRole('Admin Jemaat')) {
                $query->where('id', $user->jemaat_id);
            }
            // Super Admin / Bidang 3 bisa export semua
        }
        */

        return $query->get();
    }

    /**
     * Definisikan header kolom. HARUS SESUAI DENGAN TEMPLATE IMPORT!
     */
    public function headings(): array
    {
        return [
            'ID Klasis (Wajib)', // Kolom bantuan import
            'Nama Klasis (Readonly)',
            'Nama Jemaat (Wajib)',
            'Kode Jemaat (Unik, Opsional)',
            'Alamat Gereja',
            'Status Jemaat (Mandiri/Bakal Jemaat/Pos Pelayanan)', // Wajib
            'Jenis Jemaat (Umum/Kategorial)', // Wajib
            'Tanggal Berdiri (YYYY-MM-DD)',
            'Nomor SK Pendirian',
            'Jemaat Induk (Jika Pemekaran)',
            'Jumlah KK',
            'Jumlah Jiwa',
            'Tanggal Update Statistik (YYYY-MM-DD)',
            'Telepon Kantor/Kontak',
            'Email Jemaat (Unik, Opsional)',
            'Website Jemaat (Opsional)',
            'Sejarah Singkat',
            // 'Koordinat GPS', // Opsional
            // 'Foto Gereja Path', // Tidak diimport/export
            // 'Status Gedung', 'Kapasitas', 'Status Tanah', 'Luas Tanah' // Bisa ditambahkan
        ];
    }

    /**
     * Petakan data ke baris Excel/CSV. URUTAN HARUS SAMA DENGAN HEADINGS!
     */
    public function map($jemaat): array
    {
        return [
            $jemaat->klasis_id, // Untuk referensi import
            $jemaat->klasis->nama_klasis ?? '', // Tampilkan nama klasis
            $jemaat->nama_jemaat,
            $jemaat->kode_jemaat,
            $jemaat->alamat_gereja,
            $jemaat->status_jemaat,
            $jemaat->jenis_jemaat,
            optional($jemaat->tanggal_berdiri)->format('Y-m-d'),
            $jemaat->nomor_sk_pendirian,
            $jemaat->jemaat_induk,
            $jemaat->jumlah_kk,
            $jemaat->jumlah_total_jiwa,
            optional($jemaat->tanggal_update_statistik)->format('Y-m-d'),
            $jemaat->telepon_kantor,
            $jemaat->email_jemaat,
            $jemaat->website_jemaat,
            $jemaat->sejarah_singkat,
        ];
    }
}