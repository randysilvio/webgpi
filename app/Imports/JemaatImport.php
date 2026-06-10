<?php

namespace App\Imports;

use App\Models\Jemaat;
use App\Models\Klasis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Str;

class JemaatImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    private $allowedKlasisId = null;

    public function __construct($klasisId = null)
    {
        $this->allowedKlasisId = $klasisId;
    }

    public function model(array $row)
    {
        // 1. TENTUKAN KLASIS ID
        $klasisIdToUse = null;

        if ($this->allowedKlasisId) {
            $klasisIdToUse = $this->allowedKlasisId;
        } else {
            if (!empty($row['klasis_lama'])) {
                $klasisName = trim($row['klasis_lama']);
                $klasis = Klasis::where('nama_klasis', 'LIKE', '%' . $klasisName . '%')->first();
                $klasisIdToUse = $klasis ? $klasis->id : null;
            }
        }

        // Jika Klasis tidak ketemu, skip agar tidak error
        if (!$klasisIdToUse) {
            return null; 
        }

        // 2. CEK DUPLIKASI (Anti-Duplikat)
        // Kita cari apakah Nama Jemaat ini sudah ada di Klasis tersebut?
        $existingJemaat = Jemaat::where('nama_jemaat', trim($row['nama_jemaat']))
                                ->where('klasis_id', $klasisIdToUse)
                                ->first();

        // 3. LOGIKA SIMPAN
        if ($existingJemaat) {
            // A. JIKA SUDAH ADA: Update data yang kosong saja (Opsional)
            // Sistem TIDAK akan membuat data baru.
            // Contoh: Kita update alamat jika di database kosong tapi di CSV ada.
            if (empty($existingJemaat->alamat_gereja) && !empty($row['alamat_gereja'])) {
                $existingJemaat->update(['alamat_gereja' => $row['alamat_gereja']]);
            }
            
            return $existingJemaat; // Kembalikan objek yang ada (tidak error)

        } else {
            // B. JIKA BELUM ADA: Buat Baru
            
            // Generate Kode Jemaat Otomatis (Format: J-TAHUN-4HURUFACAK)
            // Contoh: J-2025-XK9L
            $autoKode = 'J-' . date('Y') . '-' . strtoupper(Str::random(4));

            return new Jemaat([
                'nama_jemaat'       => trim($row['nama_jemaat']),
                'kode_jemaat'       => $autoKode,
                'klasis_id'         => $klasisIdToUse,
                
                // Default Value
                'jumlah_total_jiwa' => 0, 
                'jumlah_kk'         => 0,
                'alamat_gereja'     => $row['alamat_gereja'] ?? null,
                'status_jemaat'     => 'Mandiri',
                'jenis_jemaat'      => 'Umum',
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nama_jemaat' => ['required', 'string'],
            'klasis_lama' => $this->allowedKlasisId ? ['nullable'] : ['required', 'string'],
        ];
    }
}