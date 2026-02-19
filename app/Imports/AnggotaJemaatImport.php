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
        $jemaatId = $this->jemaatIdDefault;

        // Jika user LUPA pilih di dropdown, baru kita coba ambil dari CSV
        if (!$jemaatId) {
            $jemaatId = $this->findValue($row, 'jemaat_id');
        }

        // --- CEK PENTING ---
        if (!$jemaatId) {
            throw new \Exception("STOP: Anda belum memilih Nama Jemaat di menu Dropdown saat Import. Mohon ulangi dan pilih Jemaatnya.");
        }

        $namaLengkap = $this->findValue($row, 'nama_keluarga') ?? $this->findValue($row, 'nama_lengkap');
        
        // Skip jika baris ini tidak memiliki nama (baris kosong/rusak)
        if (empty(trim($namaLengkap))) {
            return null;
        }

        // 2. Parsing Data Tanggal
        $tglLahir = $this->parseDate($this->findValue($row, 'tanggal_lahir'));
        $tglBaptis = $this->parseDate($this->findValue($row, 'tanggal_baptis'));
        $tglSidi = $this->parseDate($this->findValue($row, 'tanggal_sidi'));
        $tglNikah = $this->parseDate($this->findValue($row, 'tanggal_nikah'));

        // 3. Mapping Data Lama ke Renstra Baru
        // a. Kondisi Rumah
        $kondisiRumah = 'Permanen';
        $konstruksi = strtolower($this->findValue($row, 'konstruksibangunan') ?? '');
        if (str_contains($konstruksi, 'kayu') || str_contains($konstruksi, 'darurat') || str_contains($konstruksi, 'papan')) {
            $kondisiRumah = 'Darurat/Kayu';
        } elseif (str_contains($konstruksi, 'semi')) {
            $kondisiRumah = 'Semi-Permanen';
        }

        // b. Aset Ekonomi
        $aset = [];
        if (!empty($this->findValue($row, 'perkebunan'))) $aset[] = 'Perkebunan';
        if (!empty($this->findValue($row, 'peternakan'))) $aset[] = 'Peternakan';
        if (!empty($this->findValue($row, 'perikanan'))) $aset[] = 'Perikanan';
        if (!empty($this->findValue($row, 'usaha'))) $aset[] = 'Usaha Mikro';
        $asetString = count($aset) > 0 ? implode(', ', $aset) : null;

        // 4. LOGIKA UPSERT (Cari data lama, jika tidak ada buat instansi baru)
        // Kita menggunakan parameter NAMA LENGKAP dan JEMAAT ID sebagai identitas unik pencocokan
        $anggota = AnggotaJemaat::firstOrNew([
            'nama_lengkap' => trim($namaLengkap),
            'jemaat_id'    => $jemaatId,
        ]);

        // 5. ISI / UPDATE DATA
        // Hanya update kolom jika data dari Excel ada isinya. Jika kosong, biarkan data lama utuh.
        
        $nik = $this->findValue($row, 'nik');
        if (!empty($nik)) $anggota->nik = $nik;

        $nbi = $this->findValue($row, 'nomor_buku_induk') ?? $this->findValue($row, 'nij');
        if (!empty($nbi)) $anggota->nomor_buku_induk = $nbi;

        $tempatLahir = $this->findValue($row, 'tempat_lahir');
        if (!empty($tempatLahir)) $anggota->tempat_lahir = $tempatLahir;

        if ($tglLahir) $anggota->tanggal_lahir = $tglLahir;
        
        $jk = $this->mapGender($this->findValue($row, 'jenis_kelamin'));
        if ($jk) $anggota->jenis_kelamin = $jk;

        $golDarah = $this->findValue($row, 'golongan_darah');
        if (!empty($golDarah)) $anggota->golongan_darah = $golDarah;

        $disabilitas = $this->findValue($row, 'disabilitas');
        if (!empty($disabilitas)) $anggota->disabilitas = $disabilitas;

        $alamat = $this->findValue($row, 'alamat');
        if (!empty($alamat)) $anggota->alamat_lengkap = $alamat;
        elseif (!$anggota->exists) $anggota->alamat_lengkap = 'Alamat Jemaat'; // Default jika data baru

        $telepon = $this->findValue($row, 'telepon') ?? $this->findValue($row, 'hp');
        if (!empty($telepon)) $anggota->telepon = $telepon;

        $pendidikan = $this->findValue($row, 'pendidikan');
        if (!empty($pendidikan)) $anggota->pendidikan_terakhir = $pendidikan;

        $pekerjaan = $this->findValue($row, 'pekerjaan');
        if (!empty($pekerjaan)) $anggota->pekerjaan_utama = $pekerjaan;

        $nomorKk = $this->findValue($row, 'nomor_kk');
        if (!empty($nomorKk)) $anggota->nomor_kk = $nomorKk;

        $statusKeluarga = $this->findValue($row, 'status_keluarga');
        if (!empty($statusKeluarga)) $anggota->status_dalam_keluarga = $statusKeluarga;
        elseif (!$anggota->exists) $anggota->status_dalam_keluarga = 'Anggota';

        $statusNikah = ($tglNikah) ? 'Menikah' : $this->findValue($row, 'status_kawin');
        if (!empty($statusNikah)) $anggota->status_pernikahan = $statusNikah;
        elseif (!$anggota->exists) $anggota->status_pernikahan = 'Belum Menikah';

        if ($tglBaptis) $anggota->tanggal_baptis = $tglBaptis;
        if ($tglSidi) $anggota->tanggal_sidi = $tglSidi;

        // Default Data Baru
        if (!$anggota->exists) {
            $anggota->status_keanggotaan = 'Aktif';
            $anggota->kondisi_rumah = $kondisiRumah;
        }

        if ($asetString) $anggota->aset_ekonomi = $asetString;

        $smartphone = $this->findValue($row, 'smartphone');
        if ($smartphone !== null) $anggota->punya_smartphone = (int)$smartphone > 0;

        $internet = $this->findValue($row, 'internet');
        if ($internet !== null) $anggota->akses_internet = (int)$internet == 1;

        $pengeluaran = $this->findValue($row, 'pengeluaran');
        if (!empty($pengeluaran)) $anggota->rentang_pengeluaran = $pengeluaran;

        // Simpan Model (Insert jika baru, Update jika sudah ada)
        return $anggota;
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
        $val = strtolower($val ?? '');
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