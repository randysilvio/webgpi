<?php

namespace App\Imports;

use App\Models\Klasis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // Interface Wajib
use Maatwebsite\Excel\Concerns\SkipsFailures;  // Trait Wajib
use Carbon\Carbon;

class KlasisImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnFailure // Menandakan class ini bisa menangani kegagalan per baris
{
    use SkipsFailures; // Menyediakan method failures()

    /**
    * @param array $row
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $data = $this->prepareData($row);
        $kodeKlasis = $data['kode_klasis'] ?? null;
        $namaKlasis = $data['nama_klasis'];

        // --- Logika Upsert (Update or Create) ---

        // 1. Cek berdasarkan Kode Klasis (Prioritas Utama)
        if (!empty($kodeKlasis)) {
            $klasisByKode = Klasis::where('kode_klasis', $kodeKlasis)->first();
            if ($klasisByKode) {
                $klasisByKode->update($data);
                return null; // Return null agar tidak insert baris baru
            }
        }

        // 2. Cek berdasarkan Nama Klasis (Fallback jika kode kosong/beda)
        $klasisByName = Klasis::where('nama_klasis', $namaKlasis)->first();
        if ($klasisByName) {
            // Update data jika ditemukan nama yang sama
            if (!empty($kodeKlasis)) {
                $klasisByName->kode_klasis = $kodeKlasis;
            }
            $klasisByName->update($data);
            return null;
        }

        // 3. Jika belum ada, Buat Baru
        return new Klasis($data);
    }

    /**
     * Menyiapkan data dari baris excel.
     * Pastikan key array sesuai dengan nama kolom di database.
     * Key $row['...'] harus sesuai dengan header di Excel (huruf kecil, spasi jadi underscore).
     */
    private function prepareData(array $row): array
    {
        return [
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
            // 'ketua_mpk_pendeta_id' => $row['id_ketua_mpk_pendeta_id_wajib_jika_ada'] ?? null, // Opsional jika ingin import relasi ketua
        ];
    }

    /**
     * Aturan validasi.
     */
    public function rules(): array
    {
        return [
            'nama_klasis_wajib' => ['required', 'string', 'max:255'],
            'kode_klasis_unik_opsional' => ['nullable', 'string', 'max:50'],
            'email_klasis_unik_opsional' => ['nullable', 'email', 'max:255'],
            'website_klasis_opsional' => ['nullable', 'url'],
        ];
    }

    /**
     * Pesan error custom.
     */
    public function customValidationMessages()
    {
        return [
            'nama_klasis_wajib.required' => 'Kolom Nama Klasis wajib diisi.',
            'email_klasis_unik_opsional.email' => 'Format Email tidak valid.',
            'website_klasis_opsional.url' => 'Format URL Website tidak valid.',
        ];
    }

    /**
     * Helper konversi tanggal Excel.
     */
    private function transformDate($value): ?string
    {
         if (empty($value)) return null;
         try {
             if (is_numeric($value)) {
                 return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
             }
             return Carbon::parse($value)->format('Y-m-d');
         } catch (\Exception $e) {
             return null;
         }
    }

    public function batchSize(): int { return 100; }
    public function chunkSize(): int { return 1000; }
}