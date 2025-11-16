<?php

namespace App\Imports;

use App\Models\AnggotaJemaat;
use App\Models\Jemaat; // Untuk validasi jemaat_id
use App\Models\Klasis; // Untuk scoping
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Membaca baris header
use Maatwebsite\Excel\Concerns\WithValidation; // Untuk validasi
use Maatwebsite\Excel\Concerns\WithBatchInserts; // Optimasi insert batch
use Maatwebsite\Excel\Concerns\WithChunkReading; // Optimasi baca file besar
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule; // Untuk rule validasi
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures; // Menggunakan SkipsFailures
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // Menggunakan SkipsOnFailure
use Maatwebsite\Excel\Validators\Failure; // Menggunakan Failure
use Throwable;
use Illuminate\Support\Facades\Auth; // Untuk scoping
use Carbon\Carbon; // Untuk parsing tanggal

class AnggotaJemaatImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnFailure // Implementasi SkipsOnFailure
{
    use SkipsFailures; // Gunakan trait

    private $allowedJemaatIds = null;
    private $jemaatKlasisMap;

    public function __construct(?int $jemaatIdConstraint = null, ?int $klasisIdConstraint = null)
    {
        $this->jemaatKlasisMap = Jemaat::pluck('klasis_id', 'id');

        if ($jemaatIdConstraint !== null) {
            if ($this->jemaatKlasisMap->has($jemaatIdConstraint)) {
                 $this->allowedJemaatIds = [$jemaatIdConstraint];
            } else {
                 Log::error("Constraint Jemaat ID $jemaatIdConstraint tidak valid saat import.");
                 throw new \InvalidArgumentException("ID Jemaat yang terkait dengan akun Anda ($jemaatIdConstraint) tidak valid.");
            }
        } elseif ($klasisIdConstraint !== null) {
            $this->allowedJemaatIds = $this->jemaatKlasisMap->filter(fn ($klasis_id) => $klasis_id == $klasisIdConstraint)->keys()->toArray();
            if (empty($this->allowedJemaatIds)) {
                  Log::warning("Constraint Klasis ID $klasisIdConstraint tidak memiliki jemaat saat import.");
                  throw new \InvalidArgumentException("Klasis yang terkait dengan akun Anda (ID: $klasisIdConstraint) tidak memiliki Jemaat terdaftar.");
            }
        }
    }

    /**
    * @param array $row
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Panggil prepareData
        $dataBaru = $this->prepareData($row);

        // Ambil NIK dan No Buku Induk
        $nik = $dataBaru['nik'] ?? null;
        $nomorBukuInduk = $dataBaru['nomor_buku_induk'] ?? null;

        // --- Validasi Scoping ---
        $jemaatIdImport = $dataBaru['jemaat_id'];
        if ($this->allowedJemaatIds !== null && !in_array($jemaatIdImport, $this->allowedJemaatIds)) {
             $message = "Import dilewati: Jemaat ID {$jemaatIdImport} tidak termasuk dalam lingkup Anda.";
             Log::warning($message . " (User: " . Auth::id() . ")");
             // Sesuaikan 'key' failure dengan nama header ID Jemaat di Excel
             $failure = new Failure(0, 'id_jemaat_wajib_diisi_saat_import', [$message], $row);
             $this->onFailure($failure);
             return null;
        }

        // --- Logika Conditional Upsert ---
        $anggotaLama = null;
        if (!empty($nik)) {
            $anggotaLama = AnggotaJemaat::where('nik', $nik)->first();
        }
        if (!$anggotaLama && !empty($nomorBukuInduk)) {
             $anggotaLama = AnggotaJemaat::where('nomor_buku_induk', $nomorBukuInduk)->first();
        }

        if ($anggotaLama) {
            $jumlahBaru = $this->countFilledFields($dataBaru);
            $jumlahLama = $this->countFilledFields($anggotaLama->toArray());

            // Update jika data baru lebih lengkap atau sama
            if ($jumlahBaru >= $jumlahLama) {
                Log::info('Updating AnggotaJemaat by NIK/NoInduk: ' . ($nik ?? $nomorBukuInduk));
                 if ($this->allowedJemaatIds !== null && $anggotaLama->jemaat_id != $dataBaru['jemaat_id']) {
                     Log::warning('Skipping jemaat_id update for NIK/NoInduk: ' . ($nik ?? $nomorBukuInduk) . ' due to scope.');
                     unset($dataBaru['jemaat_id']);
                 }
                $anggotaLama->update($dataBaru);
                return null;
            } else {
                Log::info('Skipping AnggotaJemaat by NIK/NoInduk (data not newer): ' . ($nik ?? $nomorBukuInduk));
                return null;
            }
        } else {
            Log::info('Creating new AnggotaJemaat: ' . ($dataBaru['nama_lengkap'] ?? 'N/A'));
             if ($this->allowedJemaatIds !== null && !in_array($dataBaru['jemaat_id'], $this->allowedJemaatIds)) {
                  Log::error('Attempted to create AnggotaJemaat outside scope: Jemaat ID ' . $dataBaru['jemaat_id']);
                  return null;
             }
            return new AnggotaJemaat($dataBaru);
        }
    }

    /**
     * Menyiapkan data dari baris excel.
     */
    private function prepareData(array $row): array
    {
        // ** PENTING: Sesuaikan key array $row['...'] dengan nama header di file Excel Anda **
         return [
            'jemaat_id'         => $row['id_jemaat_wajib_diisi_saat_import'] ?? null, // Sesuaikan header
            'nik'               => $row['nik'] ?? null,
            'nomor_buku_induk'  => $row['nomor_buku_induk'] ?? null,
            'nama_lengkap'      => $row['nama_lengkap_wajib'] ?? null, // Sesuaikan header
            
            // --- PERBAIKAN: Pastikan mapping header KK ini benar ---
            'nomor_kk'          => $row['nomor_kk'] ?? null,
            'status_dalam_keluarga' => $row['status_dalam_keluarga'] ?? null,
            
            'tempat_lahir'      => $row['tempat_lahir'] ?? null,
            'tanggal_lahir'     => $this->transformDate($row['tanggal_lahir_yyyymmdd'] ?? null), // Sesuaikan header
            'jenis_kelamin'     => $row['jenis_kelamin_lakilakiperempuan'] ?? null, // Sesuaikan header
            'golongan_darah'    => $row['golongan_darah'] ?? null,
            'status_pernikahan' => $row['status_pernikahan'] ?? null,
            'nama_ayah'         => $row['nama_ayah'] ?? null,
            'nama_ibu'          => $row['nama_ibu'] ?? null,
            'pendidikan_terakhir' => $row['pendidikan_terakhir'] ?? null,
            'pekerjaan_utama'   => $row['pekerjaan_utama'] ?? null,
            'alamat_lengkap'    => $row['alamat_lengkap'] ?? null,
            'telepon'           => $row['telepon'] ?? null,
            'email'             => $row['email'] ?? null,
            'sektor_pelayanan'  => $row['sektor_pelayanan'] ?? null,
            'unit_pelayanan'    => $row['unit_pelayanan'] ?? null,
            'tanggal_baptis'    => $this->transformDate($row['tanggal_baptis_yyyymmdd'] ?? null), // Sesuaikan header
            'tempat_baptis'     => $row['tempat_baptis'] ?? null,
            'tanggal_sidi'      => $this->transformDate($row['tanggal_sidi_yyyymmdd'] ?? null), // Sesuaikan header
            'tempat_sidi'       => $row['tempat_sidi'] ?? null,
            'tanggal_masuk_jemaat' => $this->transformDate($row['tanggal_masuk_jemaat_yyyymmdd'] ?? null), // Sesuaikan header
            'status_keanggotaan'=> $row['status_keanggotaan_aktif_tidak_aktif_pindah_meninggal'] ?? 'Aktif', // Sesuaikan header
            'asal_gereja_sebelumnya' => $row['asal_gereja_sebelumnya'] ?? null,
            'nomor_atestasi'    => $row['nomor_atestasi'] ?? null,
            'jabatan_pelayan_khusus' => $row['jabatan_pelayan_khusus'] ?? null,
            'wadah_kategorial' => $row['wadah_kategorial'] ?? null,
            'keterlibatan_lain' => $row['keterlibatan_lain'] ?? null,
            'nama_kepala_keluarga' => $row['nama_kepala_keluarga'] ?? null,
            'status_pekerjaan_kk' => $row['status_pekerjaan_kk'] ?? null,
            'sektor_pekerjaan_kk' => $row['sektor_pekerjaan_kk'] ?? null,
            'status_kepemilikan_rumah' => $row['status_kepemilikan_rumah'] ?? null,
            'sumber_penerangan' => $row['sumber_penerangan'] ?? null,
            'sumber_air_minum' => $row['sumber_air_minum'] ?? null,
            'perkiraan_pendapatan_keluarga' => $row['perkiraan_pendapatan_keluarga'] ?? null,
            'catatan'           => $row['catatan'] ?? null,
        ];
    }

    /**
     * Helper untuk mengubah format tanggal Excel/String ke YYYY-MM-DD.
     */
    private function transformDate($value): ?string
    {
        if (empty($value)) return null;
        try {
            if (is_numeric($value)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("Gagal parse tanggal saat import: " . $value . " Error: " . $e->getMessage());
            return null;
        }
    }


    /**
     * Aturan validasi per baris.
     */
    public function rules(): array
    {
        // ** PENTING: Sesuaikan key array dengan nama header di file Excel Anda **
        return [
            '*.id_jemaat_wajib_diisi_saat_import' => ['required', 'integer', Rule::exists('jemaat', 'id')],
            '*.nik' => ['nullable', 'string', 'max:20'], 
            '*.nomor_buku_induk' => ['nullable', 'string', 'max:50'], 
            '*.nama_lengkap_wajib' => ['required', 'string', 'max:255'],
            
            // --- PERBAIKAN: Validasi untuk Nomor KK & Status Keluarga ---
            '*.nomor_kk' => ['nullable', 'string', 'max:50'], 
            '*.status_dalam_keluarga' => ['nullable', 'string', 'max:50'], 
            
            '*.tanggal_lahir_yyyymmdd' => ['nullable'], 
            '*.jenis_kelamin_lakilakiperempuan' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            '*.status_keanggotaan_aktif_tidak_aktif_pindah_meninggal' => ['required', Rule::in(['Aktif', 'Tidak Aktif', 'Pindah', 'Meninggal'])],
            '*.email' => ['nullable', 'email', 'max:255'], 
            '*.tanggal_baptis_yyyymmdd' => ['nullable'],
            '*.tanggal_sidi_yyyymmdd' => ['nullable'],
            '*.tanggal_masuk_jemaat_yyyymmdd' => ['nullable'],
        ];
    }

    /**
     * Pesan error custom.
     */
    public function customValidationMessages()
    {
         // ** PENTING: Sesuaikan key array dengan nama header di file Excel Anda **
        return [
            '*.id_jemaat_wajib_diisi_saat_import.required' => 'Header [id_jemaat_wajib_diisi_saat_import] wajib diisi.',
            '*.id_jemaat_wajib_diisi_saat_import.exists' => 'ID Jemaat di [id_jemaat_wajib_diisi_saat_import] tidak ditemukan.',
            '*.nama_lengkap_wajib.required' => 'Header [nama_lengkap_wajib] wajib diisi.',
            '*.status_keanggotaan_aktif_tidak_aktif_pindah_meninggal.*' => 'Status Keanggotaan tidak valid.',
            '*.jenis_kelamin_lakilakiperempuan.in' => 'Jenis Kelamin tidak valid.',
            '*.email.email' => 'Format Email tidak valid.',
        ];
    }

    // --- function batchSize & chunkSize tetap sama ---
    public function batchSize(): int { return 100; }
    public function chunkSize(): int { return 1000; }

    // --- function countFilledFields tetap sama ---
    private function countFilledFields(array $data): int
    {
        $filledCount = 0;
        $ignoreKeys = ['id', 'created_at', 'updated_at', 'deleted_at'];

        foreach ($data as $key => $value) {
            if (!in_array($key, $ignoreKeys) && $value !== null && $value !== '') {
                $filledCount++;
            }
        }
        return $filledCount;
    }

}