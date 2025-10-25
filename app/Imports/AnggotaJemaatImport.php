<?php

namespace App\Imports;

use App\Models\AnggotaJemaat;
use App\Models\Jemaat; // Untuk validasi jemaat_id
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Membaca baris header
use Maatwebsite\Excel\Concerns\WithValidation; // Untuk validasi
use Maatwebsite\Excel\Concerns\WithBatchInserts; // Optimasi insert batch
use Maatwebsite\Excel\Concerns\WithChunkReading; // Optimasi baca file besar
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule; // Untuk rule validasi

// Gunakan SkipsOnError agar baris valid tetap diproses jika ada error
// Gunakan SkipsErrors untuk menangkap error
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Throwable;

// WithUpserts jika ingin pakai fitur upsert bawaan (tapi kita custom)
// use Maatwebsite\Excel\Concerns\WithUpserts;

class AnggotaJemaatImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnError // Implementasi SkipsOnError
    // WithUpserts // Kita tidak pakai ini karena logic custom
{
    use SkipsErrors; // Gunakan trait SkipsErrors

    // Simpan Jemaat ID yang valid untuk scope (jika diperlukan)
    private $allowedJemaatIds;

    public function __construct()
    {
        // Nanti bisa tambahkan logic untuk mengisi $allowedJemaatIds berdasarkan role user yg import
        // $this->allowedJemaatIds = Jemaat::where('klasis_id', Auth::user()->klasis_id)->pluck('id')->toArray();
    }


    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Logika untuk conditional upsert berdasarkan NIK dan kelengkapan data
        $nik = $row['nik'] ?? null;
        $dataBaru = $this->prepareData($row);

        // Validasi Jemaat ID (contoh scoping sederhana)
        // if ($this->allowedJemaatIds && !in_array($dataBaru['jemaat_id'], $this->allowedJemaatIds)) {
        //     Log::warning('Import dilewati: Jemaat ID ' . $dataBaru['jemaat_id'] . ' tidak diizinkan untuk user.');
        //     return null; // Lewati baris ini
        // }


        if (!empty($nik)) {
            $anggotaLama = AnggotaJemaat::where('nik', $nik)->first();

            if ($anggotaLama) {
                // NIK DITEMUKAN -> Cek Kelengkapan
                $jumlahBaru = $this->countFilledFields($dataBaru);
                $jumlahLama = $this->countFilledFields($anggotaLama->toArray());

                if ($jumlahBaru > $jumlahLama) {
                    // Data baru lebih lengkap -> Update
                    Log::info('Updating AnggotaJemaat by NIK: ' . $nik);
                    $anggotaLama->update($dataBaru);
                    return null; // Jangan buat model baru, sudah diupdate
                } else {
                    // Data baru tidak lebih lengkap -> Abaikan
                    Log::info('Skipping AnggotaJemaat by NIK (data not newer): ' . $nik);
                    return null; // Abaikan baris ini
                }
            }
        }
        // Jika NIK kosong ATAU NIK tidak ditemukan -> Buat Baru
        Log::info('Creating new AnggotaJemaat: ' . ($dataBaru['nama_lengkap'] ?? 'N/A'));
        return new AnggotaJemaat($dataBaru);
    }

    /**
     * Menyiapkan data dari baris excel, konversi tanggal, dll.
     */
    private function prepareData(array $row): array
    {
        // Hati-hati dengan nama header di Excel/CSV, gunakan WithHeadingRow
        // Sesuaikan key array ($row['...']) dengan header di headings() export Anda (lowercase, underscore)
         return [
            'jemaat_id'         => $row['id_jemaat_wajib_diisi_saat_import'] ?? null,
            'nik'               => $row['nik'] ?? null,
            'nomor_buku_induk'  => $row['nomor_buku_induk'] ?? null,
            'nama_lengkap'      => $row['nama_lengkap_wajib'] ?? null,
            'tempat_lahir'      => $row['tempat_lahir'] ?? null,
            'tanggal_lahir'     => $this->transformDate($row['tanggal_lahir_yyyymmdd'] ?? null),
            'jenis_kelamin'     => $row['jenis_kelamin_lakilakiperempuan'] ?? null,
            'golongan_darah'    => $row['golongan_darah'] ?? null,
            'status_pernikahan' => $row['status_pernikahan'] ?? null,
            'alamat_lengkap'    => $row['alamat_lengkap'] ?? null,
            'telepon'           => $row['telepon'] ?? null,
            'email'             => $row['email'] ?? null,
            'sektor_pelayanan'  => $row['sektor_pelayanan'] ?? null,
            'unit_pelayanan'    => $row['unit_pelayanan'] ?? null,
            'tanggal_baptis'    => $this->transformDate($row['tanggal_baptis_yyyymmdd'] ?? null),
            'tempat_baptis'     => $row['tempat_baptis'] ?? null,
            'tanggal_sidi'      => $this->transformDate($row['tanggal_sidi_yyyymmdd'] ?? null),
            'tempat_sidi'       => $row['tempat_sidi'] ?? null,
            'status_keanggotaan'=> $row['status_keanggotaan_aktif_tidak_aktif_pindah_meninggal'] ?? 'Aktif', // Default
            'tanggal_masuk_jemaat' => $this->transformDate($row['tanggal_masuk_jemaat_yyyymmdd'] ?? null),
            'asal_gereja_sebelumnya' => $row['asal_gereja_sebelumnya'] ?? null,
            'nomor_atestasi'    => $row['nomor_atestasi'] ?? null,
            'status_pekerjaan_kk' => $row['status_pekerjaan_kk'] ?? null,
            'status_kepemilikan_rumah' => $row['status_kepemilikan_rumah'] ?? null,
            'perkiraan_pendapatan_keluarga' => $row['perkiraan_pendapatan_keluarga'] ?? null,
            'catatan'           => $row['catatan'] ?? null,
            // ... tambahkan field lain ...
        ];
    }

    /**
     * Helper untuk mengubah format tanggal Excel/String ke YYYY-MM-DD.
     */
    private function transformDate($value): ?string
    {
        if (empty($value)) return null;
        try {
            // Handle jika formatnya angka serial Excel
            if (is_numeric($value)) {
                return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
            }
            // Coba parse format umum
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error("Failed to parse date: " . $value . " Error: " . $e->getMessage());
            return null; // Gagal parse, kembalikan null
        }
    }


    /**
     * Aturan validasi per baris.
     * Sesuaikan key array dengan header di file Excel/CSV (lowercase, underscore).
     */
    public function rules(): array
    {
        return [
            // 'id_jemaat_wajib_diisi_saat_import' => ['required', 'integer', Rule::exists('jemaat', 'id')], // Validasi Jemaat ID
             '*.id_jemaat_wajib_diisi_saat_import' => ['required', 'integer', Rule::exists('jemaat', 'id')], // Validasi Jemaat ID per baris
            // 'nik' => ['nullable', 'string', 'max:20', Rule::unique('anggota_jemaat', 'nik')->ignore($this->existingAnggotaId)], // Unique rule kompleks jika update
             '*.nik' => ['nullable', 'string', 'max:20'], // Validasi NIK unik ditangani di logic model()
             '*.nama_lengkap_wajib' => ['required', 'string', 'max:255'],
             '*.tanggal_lahir_yyyymmdd' => ['nullable'], // Validasi format tanggal di transformDate
             '*.jenis_kelamin_lakilakiperempuan' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
             '*.status_keanggotaan_aktif_tidak_aktif_pindah_meninggal' => ['required', Rule::in(['Aktif', 'Tidak Aktif', 'Pindah', 'Meninggal'])],
             '*.email' => ['nullable', 'email', 'max:255'],
            // ... tambahkan aturan validasi untuk field lain ...
             '*.tanggal_baptis_yyyymmdd' => ['nullable'],
             '*.tanggal_sidi_yyyymmdd' => ['nullable'],
             '*.tanggal_masuk_jemaat_yyyymmdd' => ['nullable'],
        ];
    }

    /**
     * Pesan error custom untuk validasi.
     */
    public function customValidationMessages()
    {
        return [
            '*.id_jemaat_wajib_diisi_saat_import.required' => 'Kolom ID Jemaat wajib diisi.',
            '*.id_jemaat_wajib_diisi_saat_import.exists' => 'ID Jemaat tidak ditemukan di database.',
            '*.nama_lengkap_wajib.required' => 'Kolom Nama Lengkap wajib diisi.',
            '*.status_keanggotaan_aktif_tidak_aktif_pindah_meninggal.required' => 'Kolom Status Keanggotaan wajib diisi.',
            '*.status_keanggotaan_aktif_tidak_aktif_pindah_meninggal.in' => 'Status Keanggotaan tidak valid.',
            '*.jenis_kelamin_lakilakiperempuan.in' => 'Jenis Kelamin tidak valid.',
            '*.email.email' => 'Format Email tidak valid.',
        ];
    }


    /**
     * Ukuran batch insert.
     */
    public function batchSize(): int
    {
        return 100; // Proses 100 baris per query insert
    }

    /**
     * Ukuran chunk reading.
     */
    public function chunkSize(): int
    {
        return 1000; // Baca 1000 baris dari file per batch
    }

    /**
     * Hitung jumlah field yang terisi (tidak null & tidak string kosong).
     */
    private function countFilledFields(array $data): int
    {
        $filledCount = 0;
        // Kolom yg diabaikan dalam perhitungan (misal ID internal, timestamp)
        $ignoreKeys = ['id', 'created_at', 'updated_at'];

        foreach ($data as $key => $value) {
            if (!in_array($key, $ignoreKeys) && $value !== null && $value !== '') {
                $filledCount++;
            }
        }
        return $filledCount;
    }

    // Jika ingin menangani error spesifik per baris
    // public function onError(Throwable $e)
    // {
    //     // Tangani error, bisa log atau simpan pesan error
    //     Log::error("Error importing row: " . $e->getMessage());
    // }
}