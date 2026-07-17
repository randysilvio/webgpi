<?php

namespace App\Exports;

use App\Models\AnggotaJemaat;
use Maatwebsite\Excel\Concerns\FromCollection; // Ubah ke FromCollection jika data sedikit, atau FromQuery jika banyak
// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Optional
use Illuminate\Support\Facades\Auth;
use App\Models\Jemaat; // Digunakan di query
use App\Models\Klasis; // Digunakan di query

// Gunakan FromCollection jika data < ribuan, FromQuery jika > ribuan
class AnggotaJemaatExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $search;
    protected $klasisId;
    protected $jemaatId;
    protected $nomorKkFilter;

    // Terima parameter filter
    public function __construct($search = null, $klasisId = null, $jemaatId = null, $nomorKkFilter = null)
    {
        $this->search = $search;
        $this->klasisId = $klasisId;
        $this->jemaatId = $jemaatId;
        $this->nomorKkFilter = $nomorKkFilter;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() // Jika pakai FromCollection
    // public function query() // Jika pakai FromQuery
    {
        $query = AnggotaJemaat::query()->with(['jemaat', 'jemaat.klasis'])->orderBy('jemaat_id')->orderBy('nomor_kk')->orderBy('tanggal_lahir'); // Urutkan
        $user = Auth::user();

        // --- Apply Scoping based on User Role ---
        if ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            // Admin Jemaat hanya export anggota dari Jemaatnya
            $query->where('jemaat_id', $user->jemaat_id);
        } elseif ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            // Admin Klasis hanya export anggota dari Jemaat dalam Klasisnya
            $jemaatIds = Jemaat::where('klasis_id', $user->klasis_id)->pluck('id');
            $query->whereIn('jemaat_id', $jemaatIds);
        }
        // Super Admin & relevant roles can export all based on filters

        // --- Apply Filters from Request ---
        // Filter Klasis (dari relasi jemaat)
        if ($this->klasisId) {
            // Pastikan filter klasis ID sesuai scope
            if (!$user->hasRole('Admin Klasis') || $user->klasis_id == $this->klasisId) {
                $query->whereHas('jemaat', function ($q) {
                    $q->where('klasis_id', $this->klasisId);
                });
            }
        }
        // Filter Jemaat
        if ($this->jemaatId) {
            // Pastikan filter jemaat ID sesuai scope
             if (!$user->hasRole('Admin Jemaat') || $user->jemaat_id == $this->jemaatId) {
                $query->where('jemaat_id', $this->jemaatId);
             }
        }
        // Filter Nomor KK
        if ($this->nomorKkFilter) {
            $query->where('nomor_kk', 'like', '%' . $this->nomorKkFilter . '%');
        }
        // Filter Search Term
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_lengkap', 'like', $searchTerm)
                  ->orWhere('nik', 'like', $searchTerm)
                  ->orWhere('nomor_buku_induk', 'like', $searchTerm)
                  ->orWhere('nomor_kk', 'like', $searchTerm);
            });
        }

        return $query->get(); // Jika pakai FromCollection
        // return $query; // Jika pakai FromQuery
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Sesuaikan dengan template import dan tambahkan kolom KK
        return [
            'ID Jemaat (Wajib diisi saat import!)',
            'Nama Jemaat (Readonly)',
            'NIK',
            'Nomor Buku Induk',
            'Nama Lengkap (Wajib)',
            'Nomor KK', // <-- Tambahkan
            'Status Dalam Keluarga', // <-- Tambahkan
            'Tempat Lahir',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Jenis Kelamin (Laki-laki/Perempuan)',
            'Golongan Darah',
            'Status Pernikahan',
            'Nama Ayah',
            'Nama Ibu',
            'Pendidikan Terakhir',
            'Pekerjaan Utama',
            'Alamat Lengkap',
            'Telepon',
            'Email',
            'Sektor Pelayanan',
            'Unit Pelayanan',
            'Tanggal Baptis (YYYY-MM-DD)',
            'Tempat Baptis',
            'Tanggal Sidi (YYYY-MM-DD)',
            'Tempat Sidi',
            'Tanggal Masuk Jemaat (YYYY-MM-DD)',
            'Status Keanggotaan (Aktif/Tidak Aktif/Pindah/Meninggal)', // Wajib
            'Asal Gereja Sebelumnya',
            'Nomor Atestasi',
            'Jabatan Pelayan Khusus',
            'Wadah Kategorial',
            'Keterlibatan Lain',
            'Nama Kepala Keluarga', // Mungkin tidak perlu diexport jika pakai nomor_kk
            'Status Pekerjaan KK',
            'Sektor Pekerjaan KK',
            'Status Kepemilikan Rumah',
            'Sumber Penerangan',
            'Sumber Air Minum',
            'Perkiraan Pendapatan Keluarga',
            'Catatan',
        ];
    }

    /**
     * @param AnggotaJemaat $anggota
     * @return array
     */
    public function map($anggota): array
    {
        return [
            $anggota->jemaat_id,
            $anggota->jemaat->nama_jemaat ?? '',
            $anggota->nik,
            $anggota->nomor_buku_induk,
            $anggota->nama_lengkap,
            $anggota->nomor_kk, // <-- Tambahkan
            $anggota->status_dalam_keluarga, // <-- Tambahkan
            $anggota->tempat_lahir,
            optional($anggota->tanggal_lahir)->format('Y-m-d'),
            $anggota->jenis_kelamin,
            $anggota->golongan_darah,
            $anggota->status_pernikahan,
            $anggota->nama_ayah,
            $anggota->nama_ibu,
            $anggota->pendidikan_terakhir,
            $anggota->pekerjaan_utama,
            $anggota->alamat_lengkap,
            $anggota->telepon,
            $anggota->email,
            $anggota->sektor_pelayanan,
            $anggota->unit_pelayanan,
            optional($anggota->tanggal_baptis)->format('Y-m-d'),
            $anggota->tempat_baptis,
            optional($anggota->tanggal_sidi)->format('Y-m-d'),
            $anggota->tempat_sidi,
            optional($anggota->tanggal_masuk_jemaat)->format('Y-m-d'),
            $anggota->status_keanggotaan,
            $anggota->asal_gereja_sebelumnya,
            $anggota->nomor_atestasi,
            $anggota->jabatan_pelayan_khusus,
            $anggota->wadah_kategorial,
            $anggota->keterlibatan_lain,
            $anggota->nama_kepala_keluarga,
            $anggota->status_pekerjaan_kk,
            $anggota->sektor_pekerjaan_kk,
            $anggota->status_kepemilikan_rumah,
            $anggota->sumber_penerangan,
            $anggota->sumber_air_minum,
            $anggota->perkiraan_pendapatan_keluarga,
            $anggota->catatan,
        ];
    }
}