<?php

namespace App\Imports;

use App\Models\Jemaat;
use App\Models\Klasis; // Untuk validasi klasis_id
use Maatwebsite\Excel\Concerns\ToModel; // Ganti ke ToCollection
use Maatwebsite\Excel\Concerns\ToCollection; // Gunakan ToCollection
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // <-- Import Validator
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Collection; // <-- Import Collection
use Throwable;
use Illuminate\Support\Facades\Auth; // Jika perlu scoping

class JemaatImport implements
    // ToModel, // <-- Ganti ToModel menjadi ToCollection
    ToCollection, // <-- Gunakan ToCollection
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnError
{
    use SkipsErrors;

    // Simpan ID Klasis yang diizinkan (untuk scoping Admin Klasis)
    private $allowedKlasisId;
    // Cache data klasis untuk validasi scope
    private $klasisCache = [];

    public function __construct()
    {
        // Isi $allowedKlasisId berdasarkan role user yg import (jika perlu)
        if (Auth::check() && Auth::user()->hasRole('Admin Klasis')) {
             $this->allowedKlasisId = Auth::user()->klasis_id;
             if ($this->allowedKlasisId) {
                // Preload data klasis yang diizinkan ke cache
                 $klasis = Klasis::find($this->allowedKlasisId);
                 if ($klasis) $this->klasisCache[$this->allowedKlasisId] = true;
             }
        }
        // Super Admin/Bidang 3 tidak dibatasi $allowedKlasisId (null)
    }

    /**
     * Proses collection data dari file import dengan logic upsert dan scope.
     */
    public function collection(Collection $rows) // <-- Ubah method 'model' menjadi 'collection'
    {
        // Validator::make($rows->toArray(), $this->rules(), $this->customValidationMessages())->validate(); // Validasi awal (opsional)

        foreach ($rows as $row)
        {
             try {
                $kodeJemaat = $row['kode_jemaat_unik_opsional'] ?? null;
                $dataToProcess = $this->prepareData($row);
                $klasisIdInput = $dataToProcess['klasis_id'] ?? null;

                // --- Validasi Scope Admin Klasis ---
                if ($this->allowedKlasisId && $klasisIdInput != $this->allowedKlasisId) {
                     Log::warning('Import Jemaat dilewati: Klasis ID ' . $klasisIdInput . ' tidak diizinkan untuk user.');
                     $this->errors[] = new \Maatwebsite\Excel\Validators\Failure(count($this->errors)+1, 'id_klasis_wajib', ['Anda hanya boleh mengimpor Jemaat untuk Klasis Anda.'], $row->toArray());
                     continue; // Lanjut ke baris berikutnya
                }
                // --- Akhir Validasi Scope ---

                if (!empty($kodeJemaat)) {
                    // Ada kode_jemaat, coba updateOrCreate
                    Log::info('Processing Jemaat with Kode: ' . $kodeJemaat);
                    Jemaat::updateOrCreate(
                        ['kode_jemaat' => $kodeJemaat], // Kunci unik
                        $dataToProcess // Data update/insert
                    );
                } else {
                    // Tidak ada kode_jemaat, coba create baru (jika nama belum ada di klasis yg sama)
                    $existingByName = Jemaat::where('nama_jemaat', $dataToProcess['nama_jemaat'])
                                            ->where('klasis_id', $klasisIdInput) // Cek di klasis yg sama
                                            ->first();
                    if (!$existingByName) {
                        Log::info('Creating new Jemaat (no code): ' . $dataToProcess['nama_jemaat']);
                        Jemaat::create($dataToProcess);
                    } else {
                         Log::warning('Skipping Jemaat creation (no code and name exists in Klasis): ' . $dataToProcess['nama_jemaat']);
                         $this->errors[] = new \Maatwebsite\Excel\Validators\Failure(count($this->errors)+1, 'kode_jemaat_unik_opsional', ['Kode Jemaat kosong dan Nama Jemaat "' . $dataToProcess['nama_jemaat'] . '" sudah ada di Klasis ID ' . $klasisIdInput . '.'], $row->toArray());
                    }
                }
            } catch (\Illuminate\Validation\ValidationException $e) {
                 $this->onError($e);
            } catch (Throwable $e) {
                 $this->onError($e);
            }
        }
    }

    /**
     * Menyiapkan data dari baris excel.
     */
    private function prepareData(array $row): array
    {
        return [
            'klasis_id'         => $row['id_klasis_wajib'] ?? null,
            'nama_jemaat'       => $row['nama_jemaat_wajib'] ?? null,
            'kode_jemaat'       => $row['kode_jemaat_unik_opsional'] ?? null, // Kode tetap disertakan
            'alamat_gereja'     => $row['alamat_gereja'] ?? null,
            'status_jemaat'     => $row['status_jemaat_mandiribakal_jemaatpos_pelayanan'] ?? 'Mandiri',
            'jenis_jemaat'      => $row['jenis_jemaat_umumkategorial'] ?? 'Umum',
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
            // ... tambahkan field lain ...
        ];
    }

    /**
     * Aturan validasi per baris.
     */
    public function rules(): array
    {
        return [
             'id_klasis_wajib' => ['required', 'integer', Rule::exists('klasis', 'id')],
             'nama_jemaat_wajib' => ['required', 'string', 'max:255'],
             // Validasi unique kode & email dihapus, dicek manual/updateOrCreate
             'kode_jemaat_unik_opsional' => ['nullable', 'string', 'max:50'],
             'email_jemaat_unik_opsional' => ['nullable', 'email', 'max:255'],
             'status_jemaat_mandiribakal_jemaatpos_pelayanan' => ['required', Rule::in(['Mandiri', 'Bakal Jemaat', 'Pos Pelayanan'])],
             'jenis_jemaat_umumkategorial' => ['required', Rule::in(['Umum', 'Kategorial'])],
             'tanggal_berdiri_yyyymmdd' => ['nullable'],
             'jumlah_kk' => ['nullable', 'integer', 'min:0'],
             'jumlah_jiwa' => ['nullable', 'integer', 'min:0'],
             'tanggal_update_statistik_yyyymmdd' => ['nullable'],
             'website_jemaat_opsional' => ['nullable', 'url'],
             // ... tambahkan validasi lain ...
        ];
    }

     /**
      * Pesan error custom untuk validasi.
      */
     public function customValidationMessages()
     {
         return [
             'id_klasis_wajib.required' => 'Kolom ID Klasis wajib diisi.',
             'id_klasis_wajib.exists' => 'ID Klasis tidak ditemukan di database.',
             'nama_jemaat_wajib.required' => 'Kolom Nama Jemaat wajib diisi.',
             'status_jemaat_mandiribakal_jemaatpos_pelayanan.*' => 'Status Jemaat tidak valid.', // Pesan lebih umum
             'jenis_jemaat_umumkategorial.*' => 'Jenis Jemaat tidak valid.', // Pesan lebih umum
             'jumlah_kk.integer' => 'Jumlah KK harus angka.',
             'jumlah_jiwa.integer' => 'Jumlah Jiwa harus angka.',
             'website_jemaat_opsional.url' => 'Format URL Website Jemaat tidak valid.',
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

    public function batchSize(): int { return 100; }
    public function chunkSize(): int { return 1000; }

    // onError otomatis ada dari trait
}