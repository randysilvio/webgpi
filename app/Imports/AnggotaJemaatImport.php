<?php

namespace App\Imports;

use App\Models\AnggotaJemaat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date; 

class AnggotaJemaatImport implements ToModel, WithHeadingRow, WithValidation
{
    private $jemaatIdDefault;

    public function __construct($jemaatId = null)
    {
        $this->jemaatIdDefault = $jemaatId;
    }

    public function model(array $row)
    {
        // 1. Tentukan ID Jemaat
        // Prioritas: Pilihan dari Form Website (Dropdown)
        $jemaatId = $this->jemaatIdDefault;

        // Jika user LUPA pilih di dropdown, baru kita coba ambil dari CSV
        if (!$jemaatId) {
            $jemaatId = $this->findValue($row, 'jemaat_id');
        }

        // --- CEK PENTING --—
        if (!$jemaatId) {
            throw new \Exception("STOP: Anda belum memilih Nama Jemaat di menu Dropdown saat Import. Mohon ulangi dan pilih Jemaatnya.");
        }

        // 2. Parsing Data Tanggal
        $tglLahir = $this->parseDate($this->findValue($row, 'tanggal_lahir'));
        $tglBaptis = $this->parseDate($this->findValue($row, 'tanggal_baptis'));
        $tglSidi = $this->parseDate($this->findValue($row, 'tanggal_sidi'));
        $tglNikah = $this->parseDate($this->findValue($row, 'tanggal_nikah'));

        // 3. Mapping Data Lama ke Renstra Baru
        
        // a. Kondisi Rumah (Konversi dari deskripsi lama ke kategori baru)
        $kondisiRumah = 'Permanen';
        $konstruksi = strtolower($this->findValue($row, 'konstruksibangunan') ?? '');
        if (str_contains($konstruksi, 'kayu') || str_contains($konstruksi, 'darurat') || str_contains($konstruksi, 'papan')) {
            $kondisiRumah = 'Darurat/Kayu';
        } elseif (str_contains($konstruksi, 'semi')) {
            $kondisiRumah = 'Semi-Permanen';
        }

        // b. Aset Ekonomi (Gabungkan kolom boolean lama menjadi satu string)
        $aset = [];
        if (!empty($this->findValue($row, 'perkebunan'))) $aset[] = 'Perkebunan';
        if (!empty($this->findValue($row, 'peternakan'))) $aset[] = 'Peternakan';
        if (!empty($this->findValue($row, 'perikanan'))) $aset[] = 'Perikanan';
        if (!empty($this->findValue($row, 'usaha'))) $aset[] = 'Usaha Mikro';

        // 4. Return Model
        return new AnggotaJemaat([
            'jemaat_id'         => $jemaatId,
            'nama_lengkap'      => $this->findValue($row, 'nama_keluarga') ?? $this->findValue($row, 'nama_lengkap'),
            'nik'               => $this->findValue($row, 'nik'),
            'nomor_buku_induk'  => $this->findValue($row, 'nomor_buku_induk') ?? $this->findValue($row, 'nij'),
            
            // Data Pribadi
            'tempat_lahir'      => $this->findValue($row, 'tempat_lahir'),
            'tanggal_lahir'     => $tglLahir,
            'jenis_kelamin'     => $this->mapGender($this->findValue($row, 'jenis_kelamin')),
            'golongan_darah'    => $this->findValue($row, 'golongan_darah') ?? '-',
            'disabilitas'       => $this->findValue($row, 'disabilitas') ?? 'Tidak Ada',
            
            // Kontak & Pendidikan
            'alamat_lengkap'    => $this->findValue($row, 'alamat') ?? 'Alamat Jemaat',
            'telepon'           => $this->findValue($row, 'telepon') ?? $this->findValue($row, 'hp'),
            'pendidikan_terakhir' => $this->findValue($row, 'pendidikan'),
            'pekerjaan_utama'   => $this->findValue($row, 'pekerjaan'),
            
            // Keluarga
            'nomor_kk'          => $this->findValue($row, 'nomor_kk'),
            'status_dalam_keluarga' => $this->findValue($row, 'status_keluarga') ?? 'Anggota',
            'status_pernikahan'     => ($tglNikah) ? 'Menikah' : ($this->findValue($row, 'status_kawin') ?? 'Belum Menikah'),
            
            // Gerejawi
            'tanggal_baptis'    => $tglBaptis,
            'tanggal_sidi'      => $tglSidi,
            'status_keanggotaan' => 'Aktif',
            
            // Renstra Fields (Baru)
            'kondisi_rumah'     => $kondisiRumah,
            'aset_ekonomi'      => implode(', ', $aset),
            'punya_smartphone'  => ($this->findValue($row, 'smartphone') ?? 0) > 0, // Asumsi 1=Ya
            'akses_internet'    => ($this->findValue($row, 'internet') ?? 0) == 1,
            'rentang_pengeluaran' => $this->findValue($row, 'pengeluaran') // Jika ada di excel lama
        ]);
    }

    public function rules(): array
    {
        return []; // Bypass validasi excel agar fleksibel
    }

    private function findValue($row, $keyword)
    {
        // Cari kolom yang mengandung kata kunci (case-insensitive)
        foreach ($row as $key => $value) {
            if (str_contains(strtolower($key), $keyword)) {
                return $value;
            }
        }
        return null;
    }

    private function mapGender($val)
    {
        $val = strtolower($val);
        if ($val == 'l' || str_contains($val, 'laki')) return 'Laki-laki';
        if ($val == 'p' || str_contains($val, 'perempuan')) return 'Perempuan';
        return null;
    }

    private function parseDate($val)
    {
        if (empty($val) || $val == '0000-00-00' || $val == '0' || $val == '-') return null;
        try {
            if (is_numeric($val)) return Date::excelToDateTimeObject($val)->format('Y-m-d');
            return Carbon::parse($val)->format('Y-m-d');
        } catch (\Exception $e) { return null; }
    }
}