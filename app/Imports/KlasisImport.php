<?php

namespace App\Imports;

use App\Models\Klasis;
use App\Models\Pendeta; // Untuk validasi ketua_mpk_pendeta_id
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Throwable;

class KlasisImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnError // Implementasi SkipsOnError
{
    use SkipsErrors; // Gunakan trait SkipsErrors

    /**
     * Proses satu baris data dari file import.
     * Untuk saat ini hanya menambahkan data baru.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // TODO: Tambahkan logic conditional upsert jika diperlukan (misal berdasarkan kode_klasis)

        Log::info('Importing new Klasis: ' . ($row['nama_klasis_wajib'] ?? 'N/A'));
        return new Klasis([
            'nama_klasis'           => $row['nama_klasis_wajib'] ?? null,
            'kode_klasis'           => $row['kode_klasis_unik_opsional'] ?? null,
            'pusat_klasis'          => $row['pusat_klasis'] ?? null,
            'alamat_kantor'         => $row['alamat_kantor'] ?? null,
            'telepon_kantor'        => $row['telepon_kantor'] ?? null,
            'email_klasis'          => $row['email_klasis_unik_opsional'] ?? null,
            'website_klasis'        => $row['website_klasis_opsional'] ?? null,
            'tanggal_pembentukan'   => $this->transformDate($row['tanggal_pembentukan_yyyymmdd'] ?? null),
            'nomor_sk_pembentukan'  => $row['nomor_sk_pembentukan'] ?? null,
            'klasis_induk'          => $row['klasis_induk_jika_pemekaran'] ?? null,
            'wilayah_pelayanan'     => $row['wilayah_pelayanan_deskripsi'] ?? null,
            'sejarah_singkat'       => $row['sejarah_singkat'] ?? null,
            'ketua_mpk_pendeta_id'  => $row['id_ketua_mpk_pendeta_id_wajib_jika_ada'] ?? null,
        ]);
    }

    /**
     * Aturan validasi per baris.
     * Sesuaikan key array dengan header di file Excel/CSV (lowercase, underscore).
     */
    public function rules(): array
    {
        return [
            '*.nama_klasis_wajib' => ['required', 'string', 'max:255'],
            // Pastikan kode klasis unik jika diisi
            '*.kode_klasis_unik_opsional' => ['nullable', 'string', 'max:50', Rule::unique('klasis', 'kode_klasis')],
            // Pastikan email unik jika diisi
            '*.email_klasis_unik_opsional' => ['nullable', 'email', 'max:255', Rule::unique('klasis', 'email_klasis')],
            // Pastikan ID Pendeta valid jika diisi
            '*.id_ketua_mpk_pendeta_id_wajib_jika_ada' => ['nullable', 'integer', Rule::exists('pendeta', 'id')],
            '*.website_klasis_opsional' => ['nullable', 'url'],
            '*.tanggal_pembentukan_yyyymmdd' => ['nullable'], // Validasi format di transformDate
        ];
    }

    /**
     * Pesan error custom untuk validasi.
     */
    public function customValidationMessages()
    {
        return [
            '*.nama_klasis_wajib.required' => 'Kolom Nama Klasis wajib diisi.',
            '*.kode_klasis_unik_opsional.unique' => 'Kode Klasis sudah digunakan.',
            '*.email_klasis_unik_opsional.unique' => 'Email Klasis sudah digunakan.',
            '*.id_ketua_mpk_pendeta_id_wajib_jika_ada.exists' => 'ID Ketua MPK (Pendeta) tidak ditemukan.',
            '*.website_klasis_opsional.url' => 'Format URL Website Klasis tidak valid.',
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
                 // Angka serial Excel
                 return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
             }
             // Coba parse format umum
             return \Carbon\Carbon::parse($value)->format('Y-m-d');
         } catch (\Exception $e) {
             Log::error("Failed to parse date (Klasis Import): " . $value . " Error: " . $e->getMessage());
             return null; // Gagal parse
         }
    }

    public function batchSize(): int { return 50; } // Sesuaikan
    public function chunkSize(): int { return 500; } // Sesuaikan

    // public function onError(Throwable $e) { Log::error("Error importing row (Klasis): " . $e->getMessage()); }
}