<?php

namespace App\Imports;

use App\Models\AnggotaJemaat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date; 

// HAPUS SkipsOnFailure dan SkipsOnError agar error langsung muncul merah
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

        // --- CEK PENTING ---
        // Jika ID Jemaat masih kosong juga, kita hentikan paksa (Error Merah)
        if (!$jemaatId) {
            throw new \Exception("STOP: Anda belum memilih Nama Jemaat di menu Dropdown saat Import. Mohon ulangi dan pilih Jemaatnya.");
        }

        // 2. Mapping Data (Tanpa Try-Catch)
        $namaLengkap = $this->findValue($row, 'nama') ?? 'Tanpa Nama';
        $kodeKel     = $this->findValue($row, 'kode_keluarga') ?? Str::random(10);
        
        // Jenis Kelamin
        $rawJK = $this->findValue($row, 'jenis_kelamin');
        $jk = 'Laki-laki'; 
        if ($rawJK) {
            $val = strtolower(trim($rawJK));
            if ($val == '2' || str_contains($val, 'p') || str_contains($val, 'wanita')) { 
                $jk = 'Perempuan'; 
            }
        }

        // Status Keluarga
        $rawStatus = $this->findValue($row, 'status_keluarga');
        $statusKeluarga = 'Anggota Keluarga';
        if ($rawStatus == 1) $statusKeluarga = 'Kepala Keluarga';
        elseif ($rawStatus == 2) $statusKeluarga = 'Istri';
        elseif ($rawStatus == 3) $statusKeluarga = 'Anak';

        // Tanggal
        $tglLahir  = $this->parseDate($this->findValue($row, 'tanggal_lahir'));
        $tglBaptis = $this->parseDate($this->findValue($row, 'tanggal_baptis'));
        $tglSidi   = $this->parseDate($this->findValue($row, 'tanggal_sidi'));
        $tglNikah  = $this->parseDate($this->findValue($row, 'tanggal_nikah'));

        // 3. Simpan (Akan error merah jika ID Jemaat salah)
        return new AnggotaJemaat([
            'jemaat_id'             => $jemaatId,
            'nama_lengkap'          => $namaLengkap,
            'kode_keluarga_internal'=> $kodeKel,
            
            'tempat_lahir'          => $this->findValue($row, 'tempat_lahir'),
            'tanggal_lahir'         => $tglLahir,
            'jenis_kelamin'         => $jk,
            'golongan_darah'        => '-',
            
            'status_pernikahan'     => ($tglNikah) ? 'Menikah' : 'Belum Menikah',
            'status_dalam_keluarga' => $statusKeluarga,
            
            'tanggal_baptis'        => $tglBaptis,
            'tanggal_sidi'          => $tglSidi,
            'tanggal_nikah'         => $tglNikah,
            
            'status_keanggotaan'    => 'Aktif',
            'alamat_lengkap'        => 'Alamat Jemaat',
        ]);
    }

    // Bypass validasi
    public function rules(): array
    {
        return [];
    }

    private function findValue($row, $keyword)
    {
        foreach ($row as $key => $value) {
            if (str_contains(strtolower($key), $keyword)) {
                return $value;
            }
        }
        return null;
    }

    private function parseDate($val)
    {
        if (empty($val) || $val == '0000-00-00' || $val == '0' || $val == '-') return null;
        try {
            if (is_numeric($val)) {
                return Date::excelToDateTimeObject($val)->format('Y-m-d');
            }
            return Carbon::parse($val)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}