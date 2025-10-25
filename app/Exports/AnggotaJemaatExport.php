<?php

namespace App\Exports;

use App\Models\AnggotaJemaat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping; // Untuk memformat data
use Illuminate\Support\Facades\Auth; // Jika perlu scoping
use App\Models\Jemaat; // Jika perlu scoping

class AnggotaJemaatExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Ambil data sesuai scope (Nanti aktifkan scoping jika perlu)
        $query = AnggotaJemaat::with('jemaat'); // Eager load jemaat

        /*
        // --- Scoping (AKTIFKAN SETELAH LOGIN & ROLE USER TERHUBUNG) ---
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('Admin Jemaat')) {
                $query->where('jemaat_id', $user->jemaat_id);
            } elseif ($user->hasRole('Admin Klasis')) {
                $jemaatIds = Jemaat::where('klasis_id', $user->klasis_id)->pluck('id');
                $query->whereIn('jemaat_id', $jemaatIds);
            }
            // Super Admin / Bidang 3 bisa export semua
        }
        */

        return $query->get();
    }

    /**
     * Mendefinisikan heading kolom di file Excel/CSV.
     * HARUS SAMA URUTANNYA DENGAN TEMPLATE IMPORT NANTI!
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Jemaat (Wajib diisi saat import!)', // Kolom BANTUAN untuk import
            'Nama Jemaat (Readonly)',
            'NIK',
            'Nomor Buku Induk',
            'Nama Lengkap (Wajib)',
            'Tempat Lahir',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Jenis Kelamin (Laki-laki/Perempuan)',
            'Golongan Darah',
            'Status Pernikahan',
            'Alamat Lengkap',
            'Telepon',
            'Email',
            'Sektor Pelayanan',
            'Unit Pelayanan',
            'Tanggal Baptis (YYYY-MM-DD)',
            'Tempat Baptis',
            'Tanggal Sidi (YYYY-MM-DD)',
            'Tempat Sidi',
            'Status Keanggotaan (Aktif/Tidak Aktif/Pindah/Meninggal)', // Wajib
            'Tanggal Masuk Jemaat (YYYY-MM-DD)',
            'Asal Gereja Sebelumnya',
            'Nomor Atestasi',
            // Tambahkan header untuk field lain (Pendidikan, Pekerjaan, Keluarga, Ekonomi)
            'Status Pekerjaan KK',
            'Status Kepemilikan Rumah',
            'Perkiraan Pendapatan Keluarga',
            'Catatan',
        ];
    }

    /**
     * Memetakan data dari collection ke baris Excel/CSV.
     * Urutan data HARUS SAMA dengan headings().
     *
     * @param AnggotaJemaat $anggota
     * @return array
     */
    public function map($anggota): array
    {
        return [
            $anggota->jemaat_id, // Untuk referensi import
            $anggota->jemaat->nama_jemaat ?? '', // Tampilkan nama jemaat
            $anggota->nik,
            $anggota->nomor_buku_induk,
            $anggota->nama_lengkap,
            $anggota->tempat_lahir,
            optional($anggota->tanggal_lahir)->format('Y-m-d'), // Format tanggal
            $anggota->jenis_kelamin,
            $anggota->golongan_darah,
            $anggota->status_pernikahan,
            $anggota->alamat_lengkap,
            $anggota->telepon,
            $anggota->email,
            $anggota->sektor_pelayanan,
            $anggota->unit_pelayanan,
            optional($anggota->tanggal_baptis)->format('Y-m-d'),
            $anggota->tempat_baptis,
            optional($anggota->tanggal_sidi)->format('Y-m-d'),
            $anggota->tempat_sidi,
            $anggota->status_keanggotaan,
            optional($anggota->tanggal_masuk_jemaat)->format('Y-m-d'),
            $anggota->asal_gereja_sebelumnya,
            $anggota->nomor_atestasi,
            // Tambahkan mapping untuk field lain
            $anggota->status_pekerjaan_kk,
            $anggota->status_kepemilikan_rumah,
            $anggota->perkiraan_pendapatan_keluarga,
            $anggota->catatan,
        ];
    }
}