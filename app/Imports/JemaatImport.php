<?php

namespace App\Imports;

use App\Models\Jemaat;
use App\Models\Klasis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // Interface Wajib
use Maatwebsite\Excel\Concerns\SkipsFailures;  // Trait Wajib
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class JemaatImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnFailure
{
    use SkipsFailures; // Mengaktifkan method failures()

    private $allowedKlasisId = null;

    public function __construct()
    {
        // Cek role user untuk scoping (Admin Klasis hanya boleh import ke klasisnya)
        if (Auth::check() && Auth::user()->hasRole('Admin Klasis')) {
             $this->allowedKlasisId = Auth::user()->klasis_id;
        }
    }

    /**
    * @param array $row
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $data = $this->prepareData($row);
        $klasisIdInput = $data['klasis_id'];
        $namaJemaat = $data['nama_jemaat'];

        // --- 1. Validasi Scoping (Keamanan) ---
        // Jika user adalah Admin Klasis, pastikan ID Klasis di Excel sesuai dengan ID Klasis User
        if ($this->allowedKlasisId && $klasisIdInput != $this->allowedKlasisId) {
             $errorMsg = "Import dilewati: ID Klasis {$klasisIdInput} tidak sesuai dengan wilayah Anda (ID: {$this->allowedKlasisId}).";
             Log::warning($errorMsg);
             
             // Tambahkan ke daftar failure agar muncul di laporan error
             $this->failures[] = new Failure(
                 0, // Row index (estimasi)
                 'id_klasis_wajib', 
                 [$errorMsg], 
                 $row
             );
             return null; // Skip baris ini
        }

        // --- 2. Logika Upsert (Update or Create) ---
        $kodeJemaat = $data['kode_jemaat'] ?? null;
        
        // A. Cek berdasarkan Kode Unik (Prioritas Utama)
        if (!empty($kodeJemaat)) {
            $jemaatByKode = Jemaat::where('kode_jemaat', $kodeJemaat)->first();
            if ($jemaatByKode) {
                // Data ditemukan, lakukan Update
                $jemaatByKode->update($data);
                return null; // Return null artinya tidak insert baris baru
            }
        }

        // B. Cek berdasarkan Nama Jemaat di Klasis yang sama (Fallback jika kode kosong/beda)
        $jemaatByName = Jemaat::where('nama_jemaat', $namaJemaat)
                              ->where('klasis_id', $klasisIdInput)
                              ->first();
        
        if ($jemaatByName) {
            // Update data lama (misal melengkapi kode yang sebelumnya kosong)
            if (!empty($kodeJemaat)) {
                $jemaatByName->kode_jemaat = $kodeJemaat;
            }
            $jemaatByName->update($data);
            return null;
        }

        // C. Jika benar-benar belum ada, Create Baru
        return new Jemaat($data);
    }

    /**
     * Mapping data dari baris Excel ke kolom Database.
     * Pastikan key array sesuai dengan nama kolom di DB.
     */
    private function prepareData(array $row): array
    {
        return [
            'klasis_id'         => $row['id_klasis_wajib'] ?? null,
            'nama_jemaat'       => $row['nama_jemaat_wajib'] ?? null,
            'kode_jemaat'       => $row['kode_jemaat_unik_opsional'] ?? null,
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
        ];
    }

    /**
     * Aturan validasi per baris.
     * Key array harus sesuai dengan header di file Excel.
     */
    public function rules(): array
    {
        return [
             'id_klasis_wajib' => ['required', 'integer', Rule::exists('klasis', 'id')],
             'nama_jemaat_wajib' => ['required', 'string', 'max:255'],
             'kode_jemaat_unik_opsional' => ['nullable', 'string', 'max:50'],
             'status_jemaat_mandiribakal_jemaatpos_pelayanan' => ['required', Rule::in(['Mandiri', 'Bakal Jemaat', 'Pos Pelayanan'])],
             'jenis_jemaat_umumkategorial' => ['required', Rule::in(['Umum', 'Kategorial'])],
             'website_jemaat_opsional' => ['nullable', 'url'],
             'jumlah_kk' => ['nullable', 'integer', 'min:0'],
             'jumlah_jiwa' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Pesan error kustom.
     */
    public function customValidationMessages()
    {
         return [
             'id_klasis_wajib.required' => 'ID Klasis wajib diisi.',
             'id_klasis_wajib.exists' => 'ID Klasis tidak ditemukan di database.',
             'nama_jemaat_wajib.required' => 'Nama Jemaat wajib diisi.',
             'website_jemaat_opsional.url' => 'Format URL tidak valid.',
             'status_jemaat_mandiribakal_jemaatpos_pelayanan.in' => 'Status Jemaat harus: Mandiri, Bakal Jemaat, atau Pos Pelayanan.',
         ];
    }

    /**
     * Helper konversi tanggal Excel ke format Y-m-d.
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