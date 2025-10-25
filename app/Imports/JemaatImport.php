<?php

namespace App\Imports;

use App\Models\Jemaat;
use App\Models\Klasis; // Untuk validasi klasis_id
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
use Illuminate\Support\Facades\Auth; // Jika perlu scoping

class JemaatImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnError
{
    use SkipsErrors;

    // Simpan Klasis ID yang valid untuk scope (jika diperlukan)
    private $allowedKlasisId;

    public function __construct()
    {
        // Nanti bisa tambahkan logic untuk mengisi $allowedKlasisId berdasarkan role user yg import
        // if (Auth::check() && Auth::user()->hasRole('Admin Klasis')) {
        //      $this->allowedKlasisId = Auth::user()->klasis_id;
        // }
    }

    /**
     * Proses satu baris data dari file import.
     * Untuk saat ini hanya menambahkan data baru.
     */
    public function model(array $row)
    {
        // TODO: Tambahkan logic conditional upsert jika diperlukan (misal berdasarkan kode_jemaat)

        $klasisId = $row['id_klasis_wajib'] ?? null;

        // Validasi Scope Sederhana (Jika user adalah Admin Klasis)
        // if ($this->allowedKlasisId && $klasisId != $this->allowedKlasisId) {
        //      Log::warning('Import Jemaat dilewati: Klasis ID ' . $klasisId . ' tidak diizinkan untuk user.');
        //      // Lewati baris ini atau lempar error validasi
        //       $this->skipOnError(new \Exception('Anda hanya boleh mengimpor Jemaat untuk Klasis Anda.'));
        //      return null;
        // }


        Log::info('Importing new Jemaat: ' . ($row['nama_jemaat_wajib'] ?? 'N/A'));
        return new Jemaat([
            'klasis_id'         => $klasisId,
            'nama_jemaat'       => $row['nama_jemaat_wajib'] ?? null,
            'kode_jemaat'       => $row['kode_jemaat_unik_opsional'] ?? null,
            'alamat_gereja'     => $row['alamat_gereja'] ?? null,
            'status_jemaat'     => $row['status_jemaat_mandiribakal_jemaatpos_pelayanan'] ?? 'Mandiri', // Default
            'jenis_jemaat'      => $row['jenis_jemaat_umumkategorial'] ?? 'Umum', // Default
            'tanggal_berdiri'   => $this->transformDate($row['tanggal_berdiri_yyyymmdd'] ?? null),
            'nomor_sk_pendirian'=> $row['nomor_sk_pendirian'] ?? null,
            'jemaat_induk'      => $row['jemaat_induk_jika_pemekaran'] ?? null,
            'jumlah_kk'         => $row['jumlah_kk'] ?? 0,
            'jumlah_total_jiwa' => $row['jumlah_jiwa'] ?? 0,
            'tanggal_update_statistik' => $this->transformDate($row['tanggal_update_statistik_yyyymmdd'] ?? null),
            'telepon_kantor'    => $row['telepon_kantorkontak'] ?? null,
            'email_jemaat'      => $row['email_jemaat_unik_opsional'] ?? null,
            'website_jemaat'    => $row['website_jemaat_opsional'] ?? null,
            'sejarah_singkat'   => $row['sejarah_singkat'] ?? null,
            // ... tambahkan field lain dari headings ...
        ]);
    }

    /**
     * Aturan validasi per baris.
     */
    public function rules(): array
    {
        return [
             '*.id_klasis_wajib' => ['required', 'integer', Rule::exists('klasis', 'id')],
             '*.nama_jemaat_wajib' => ['required', 'string', 'max:255'],
             '*.kode_jemaat_unik_opsional' => ['nullable', 'string', 'max:50', Rule::unique('jemaat', 'kode_jemaat')],
             '*.email_jemaat_unik_opsional' => ['nullable', 'email', 'max:255', Rule::unique('jemaat', 'email_jemaat')],
             '*.status_jemaat_mandiribakal_jemaatpos_pelayanan' => ['required', Rule::in(['Mandiri', 'Bakal Jemaat', 'Pos Pelayanan'])],
             '*.jenis_jemaat_umumkategorial' => ['required', Rule::in(['Umum', 'Kategorial'])],
             '*.tanggal_berdiri_yyyymmdd' => ['nullable'],
             '*.jumlah_kk' => ['nullable', 'integer', 'min:0'],
             '*.jumlah_jiwa' => ['nullable', 'integer', 'min:0'],
             '*.tanggal_update_statistik_yyyymmdd' => ['nullable'],
             '*.website_jemaat_opsional' => ['nullable', 'url'],
        ];
    }

     /**
      * Pesan error custom untuk validasi.
      */
     public function customValidationMessages()
     {
         return [
             '*.id_klasis_wajib.required' => 'Kolom ID Klasis wajib diisi.',
             '*.id_klasis_wajib.exists' => 'ID Klasis tidak ditemukan di database.',
             '*.nama_jemaat_wajib.required' => 'Kolom Nama Jemaat wajib diisi.',
             '*.kode_jemaat_unik_opsional.unique' => 'Kode Jemaat sudah digunakan.',
             '*.email_jemaat_unik_opsional.unique' => 'Email Jemaat sudah digunakan.',
             '*.status_jemaat_mandiribakal_jemaatpos_pelayanan.required' => 'Kolom Status Jemaat wajib diisi.',
             '*.status_jemaat_mandiribakal_jemaatpos_pelayanan.in' => 'Status Jemaat tidak valid.',
             '*.jenis_jemaat_umumkategorial.required' => 'Kolom Jenis Jemaat wajib diisi.',
             '*.jenis_jemaat_umumkategorial.in' => 'Jenis Jemaat tidak valid.',
             '*.jumlah_kk.integer' => 'Jumlah KK harus angka.',
             '*.jumlah_jiwa.integer' => 'Jumlah Jiwa harus angka.',
             '*.website_jemaat_opsional.url' => 'Format URL Website Jemaat tidak valid.',
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
                 return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
             }
             return \Carbon\Carbon::parse($value)->format('Y-m-d');
         } catch (\Exception $e) {
             Log::error("Failed to parse date (Jemaat Import): " . $value . " Error: " . $e->getMessage());
             return null;
         }
    }

    public function batchSize(): int { return 100; } // Sesuaikan
    public function chunkSize(): int { return 1000; } // Sesuaikan

    // public function onError(Throwable $e) { Log::error("Error importing row (Jemaat): " . $e->getMessage()); }
}