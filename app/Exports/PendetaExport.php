<?php

namespace App\Exports;

use App\Models\Pendeta;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Optional: Adjust column width
use Illuminate\Support\Facades\Auth; // For scoping

class PendetaExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $search;
    protected $klasisId;
    protected $jemaatId;
    protected $status;

    public function __construct($search = null, $klasisId = null, $jemaatId = null, $status = null)
    {
        $this->search = $search;
        $this->klasisId = $klasisId;
        $this->jemaatId = $jemaatId;
        $this->status = $status;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = Pendeta::query()->with(['klasisPenempatan', 'jemaatPenempatan'])->orderBy('nama_lengkap', 'asc');
        $user = Auth::user();

        // --- Apply Scoping based on User Role ---
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            // Admin Klasis hanya export Pendeta yang ditempatkan di Klasisnya
            $query->where('klasis_penempatan_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            // Admin Jemaat hanya export Pendeta yang ditempatkan di Jemaatnya
             $query->where('jemaat_penempatan_id', $user->jemaat_id);
        }
        // Super Admin & relevant roles can export all based on filters

        // --- Apply Filters from Request ---
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_lengkap', 'like', $searchTerm)
                  ->orWhere('nipg', 'like', $searchTerm);
            });
        }
        if ($this->klasisId) {
             // Pastikan filter klasis ID sesuai scope jika user bukan Super Admin
            if (!$user->hasRole('Admin Klasis') || $user->klasis_id == $this->klasisId) {
                $query->where('klasis_penempatan_id', $this->klasisId);
            }
        }
        if ($this->jemaatId) {
             // Pastikan filter jemaat ID sesuai scope
             if ((!$user->hasRole('Admin Jemaat') || $user->jemaat_id == $this->jemaatId))
             {
                 $query->where('jemaat_penempatan_id', $this->jemaatId);
             }
        }
        if ($this->status) {
            $query->where('status_kepegawaian', $this->status);
        }

        return $query;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Sesuaikan dengan kolom yang ada di model Pendeta dan template import
        return [
            'NIPG (Wajib Unik)',
            'Nama Lengkap (Wajib)',
            'NIK (Unik Opsional)',
            'Tempat Lahir (Wajib)',
            'Tanggal Lahir (YYYY-MM-DD) (Wajib)',
            'Jenis Kelamin (Laki-laki/Perempuan) (Wajib)',
            'Email (Unik Opsional, untuk Login)',
            'Tanggal Tahbisan (YYYY-MM-DD) (Wajib)',
            'Tempat Tahbisan (Wajib)',
            'Status Kepegawaian (Wajib)',
            'Klasis Penempatan ID',
            'Nama Klasis Penempatan', // Untuk referensi import nama
            'Jemaat Penempatan ID',
            'Nama Jemaat Penempatan', // Untuk referensi import nama
            // Tambahkan kolom lain sesuai kebutuhan
            'Status Pernikahan',
            'Nama Pasangan',
            'Golongan Darah',
            'Alamat Domisili',
            'Telepon',
            'Nomor SK Kependetaan',
            'Pendidikan Teologi Terakhir',
            'Institusi Pendidikan Teologi',
            'Golongan Pangkat Terakhir',
            'Tanggal Mulai Masuk GPI (YYYY-MM-DD)',
            'Jabatan Saat Ini',
            'Tanggal Mulai Jabatan (YYYY-MM-DD)',
            'Catatan',
        ];
    }

    /**
     * @param Pendeta $pendeta
     * @return array
     */
    public function map($pendeta): array
    {
        return [
            $pendeta->nipg,
            $pendeta->nama_lengkap,
            $pendeta->nik,
            $pendeta->tempat_lahir,
            optional($pendeta->tanggal_lahir)->format('Y-m-d'),
            $pendeta->jenis_kelamin,
            // Ambil email dari user terkait jika ada
            optional($pendeta->user)->email, // Penting untuk template import user
            optional($pendeta->tanggal_tahbisan)->format('Y-m-d'),
            $pendeta->tempat_tahbisan,
            $pendeta->status_kepegawaian,
            $pendeta->klasis_penempatan_id,
            optional($pendeta->klasisPenempatan)->nama_klasis, // Untuk referensi import
            $pendeta->jemaat_penempatan_id,
            optional($pendeta->jemaatPenempatan)->nama_jemaat, // Untuk referensi import
            // Map kolom lain
            $pendeta->status_pernikahan,
            $pendeta->nama_pasangan,
            $pendeta->golongan_darah,
            $pendeta->alamat_domisili,
            $pendeta->telepon,
            $pendeta->nomor_sk_kependetaan,
            $pendeta->pendidikan_teologi_terakhir,
            $pendeta->institusi_pendidikan_teologi,
            $pendeta->golongan_pangkat_terakhir,
            optional($pendeta->tanggal_mulai_masuk_gpi)->format('Y-m-d'),
            $pendeta->jabatan_saat_ini,
            optional($pendeta->tanggal_mulai_jabatan_saat_ini)->format('Y-m-d'),
            $pendeta->catatan,
        ];
    }
}