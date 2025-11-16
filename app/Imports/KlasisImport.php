<?php

namespace App\Imports;

use App\Models\Klasis;
use App\Models\Pendeta; // Untuk validasi ketua_mpk_pendeta_id
use Maatwebsite\Excel\Concerns\ToModel; // Akan kita ganti sebagian dengan ToCollection
use Maatwebsite\Excel\Concerns\ToCollection; // Gunakan ToCollection untuk custom logic
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts; // Tetap berguna untuk insert baru
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // <-- Import Validator
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Collection; // <-- Import Collection
use Throwable;

class KlasisImport implements
    // ToModel, // <-- Ganti ToModel menjadi ToCollection
    ToCollection, // <-- Gunakan ToCollection
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnError // Implementasi SkipsOnError
{
    use SkipsErrors; // Gunakan trait SkipsErrors

    /**
     * Proses collection data dari file import dengan logic upsert.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows) // <-- Ubah method 'model' menjadi 'collection'
    {
        // Validasi seluruh collection sebelum diproses (opsional tapi bagus)
        // Validator::make($rows->toArray(), $this->rules(), $this->customValidationMessages())->validate(); // Jalankan validasi awal

        foreach ($rows as $row)
        {
            try {
                // Validasi per baris (akan otomatis dijalankan oleh WithValidation)
                // Jika validasi gagal, onError akan dipanggil

                $kodeKlasis = $row['kode_klasis_unik_opsional'] ?? null;
                $dataToProcess = $this->prepareData($row);

                if (!empty($kodeKlasis)) {
                    // Ada kode_klasis, coba updateOrCreate
                    Log::info('Processing Klasis with Kode: ' . $kodeKlasis);
                    Klasis::updateOrCreate(
                        ['kode_klasis' => $kodeKlasis], // Kunci unik untuk mencari
                        $dataToProcess // Data untuk insert atau update
                    );
                } else {
                    // Tidak ada kode_klasis, coba create baru (jika nama belum ada)
                    // Perlu cek manual agar tidak duplikat nama jika kode kosong
                    $existingByName = Klasis::where('nama_klasis', $dataToProcess['nama_klasis'])->first();
                    if (!$existingByName) {
                        Log::info('Creating new Klasis (no code provided): ' . $dataToProcess['nama_klasis']);
                        Klasis::create($dataToProcess);
                    } else {
                        Log::warning('Skipping Klasis creation (no code provided and name exists): ' . $dataToProcess['nama_klasis']);
                        // Bisa juga di-skip atau di-log sebagai error
                         $this->errors[] = new \Maatwebsite\Excel\Validators\Failure(
                             count($this->errors) + 1, // Baris (mungkin tidak akurat jika pakai chunk)
                             'kode_klasis_unik_opsional', // Atribut error
                             ['Kode Klasis kosong dan Nama Klasis "' . $dataToProcess['nama_klasis'] . '" sudah ada.'], // Pesan error
                             $row->toArray() // Nilai baris
                         );
                    }
                }
            } catch (\Illuminate\Validation\ValidationException $e) {
                 // Tangkap error validasi dari Validator::make jika dipakai, atau dari WithValidation
                 $this->onError($e); // Teruskan ke handler error
            } catch (Throwable $e) {
                 // Tangkap error lainnya (misal DB error)
                 $this->onError($e); // Teruskan ke handler error
            }
        }
    }

     /**
     * Menyiapkan data dari baris excel, konversi tanggal, dll.
     */
    private function prepareData(array $row): array
    {
        return [
            'nama_klasis'           => $row['nama_klasis_wajib'] ?? null,
            'kode_klasis'           => $row['kode_klasis_unik_opsional'] ?? null, // Kode tetap disertakan
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
            // Hapus field kunci dari data update jika tidak ingin diubah saat update
            // unset($data['kode_klasis']);
        ];
    }

    /**
     * Aturan validasi per baris.
     * WithValidation akan otomatis menerapkan ini ke setiap $row.
     */
    public function rules(): array
    {
        return [
            // Ganti key menjadi nama header asli dari Excel
            'nama_klasis_wajib' => ['required', 'string', 'max:255'],
            // Validasi unique kode klasis dihapus dari sini, dicek manual di collection()
            'kode_klasis_unik_opsional' => ['nullable', 'string', 'max:50'],
            // Validasi unique email dihapus dari sini, ditangani oleh updateOrCreate
            'email_klasis_unik_opsional' => ['nullable', 'email', 'max:255'],
            // Pastikan ID Pendeta valid jika diisi
            'id_ketua_mpk_pendeta_id_wajib_jika_ada' => ['nullable', 'integer', Rule::exists('pendeta', 'id')],
            'website_klasis_opsional' => ['nullable', 'url'],
            'tanggal_pembentukan_yyyymmdd' => ['nullable'], // Validasi format di transformDate
             // Tambahkan validasi lain jika ada field baru
        ];
    }

    /**
     * Pesan error custom untuk validasi.
     */
    public function customValidationMessages()
    {
        return [
            'nama_klasis_wajib.required' => 'Kolom Nama Klasis wajib diisi.',
            // Pesan unique dihapus karena validasi di logic collection
            'id_ketua_mpk_pendeta_id_wajib_jika_ada.exists' => 'ID Ketua MPK (Pendeta) tidak ditemukan.',
            'website_klasis_opsional.url' => 'Format URL Website Klasis tidak valid.',
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

    // // Method onError sudah otomatis ada dari SkipsOnError & SkipsErrors traits
    // public function onError(Throwable $e)
    // {
    //     // Log atau simpan error ke DB
    //     Log::error("Error importing row (Klasis): " . $e->getMessage());
    //     // Trait SkipsErrors akan otomatis mengumpulkan error ini
    // }
}