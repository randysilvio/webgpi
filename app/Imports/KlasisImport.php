<?php

namespace App\Imports;

use App\Models\Klasis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class KlasisImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // 1. Cari apakah Klasis sudah ada (berdasarkan Nama atau Kode)
        $klasis = Klasis::where('nama_klasis', $row['nama_klasis'])
                        ->orWhere('kode_klasis', $row['kode_klasis'])
                        ->first();

        // 2. Jika sudah ada, Update. Jika belum, Create.
        if ($klasis) {
            $klasis->update([
                'nama_klasis' => $row['nama_klasis'],
                'kode_klasis' => $row['kode_klasis'],
                // Kita tidak update ID, biarkan ID baru auto-increment
            ]);
            return $klasis;
        } else {
            return new Klasis([
                'nama_klasis' => $row['nama_klasis'],
                'kode_klasis' => $row['kode_klasis'],
                // Kolom lain dibiarkan null (default)
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nama_klasis' => ['required', 'string'],
            'kode_klasis' => ['required', 'string'],
        ];
    }
}