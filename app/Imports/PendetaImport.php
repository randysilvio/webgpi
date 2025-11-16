<?php

namespace App\Imports;

use App\Models\Pendeta;
use App\Models\User;
use App\Models\Klasis;
use App\Models\Jemaat;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // Interface Wajib
use Maatwebsite\Excel\Concerns\SkipsFailures;  // Trait Wajib
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class PendetaImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    WithBatchInserts, 
    WithChunkReading,
    SkipsOnFailure // Menangani kegagalan per baris
{
    use SkipsFailures; // Menyediakan method failures()

    private $rolePendeta;
    private $klasisMap;
    private $jemaatMap;
    private $allowedKlasisId = null;
    private $allowedJemaatIds = null;

    public function __construct(?int $klasisIdConstraint = null, ?int $jemaatIdConstraint = null)
    {
        $this->rolePendeta = Role::where('name', 'Pendeta')->first();
        
        // Cache map untuk performa (Nama -> ID) agar tidak query berulang
        $this->klasisMap = Klasis::pluck('id', 'nama_klasis');
        $this->jemaatMap = Jemaat::pluck('id', 'nama_jemaat');

        // Set constraint scoping
        $this->allowedKlasisId = $klasisIdConstraint;
        
        if ($jemaatIdConstraint !== null) {
            $this->allowedJemaatIds = [$jemaatIdConstraint];
        } elseif ($klasisIdConstraint !== null) {
            // Jika admin klasis, ambil semua ID jemaat di bawahnya
            $this->allowedJemaatIds = Jemaat::where('klasis_id', $klasisIdConstraint)->pluck('id')->toArray();
        }
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $dataBaru = $this->prepareData($row);
        $nipg = $dataBaru['nipg'] ?? null;
        
        // --- 1. Validasi Scoping (Keamanan) ---
        $klasisIdImport = $dataBaru['klasis_penempatan_id'];
        $jemaatIdImport = $dataBaru['jemaat_penempatan_id'];

        // Cek apakah Admin Klasis mencoba import ke klasis lain
        if ($this->allowedKlasisId !== null && $klasisIdImport != $this->allowedKlasisId) {
             $this->addFailure($row, 'nama_klasis_penempatan', "Klasis penempatan tidak sesuai hak akses Anda.");
             return null;
        }
        // Cek apakah Admin mencoba import ke jemaat di luar wilayahnya
        if ($this->allowedJemaatIds !== null && $jemaatIdImport && !in_array($jemaatIdImport, $this->allowedJemaatIds)) {
             $this->addFailure($row, 'nama_jemaat_penempatan', "Jemaat penempatan tidak sesuai hak akses Anda.");
             return null;
        }

        // --- 2. Proses Database (Pendeta & User) ---
        DB::beginTransaction();
        try {
            // A. Upsert Data Pendeta
            $pendeta = Pendeta::where('nipg', $nipg)->first();

            if ($pendeta) {
                $pendeta->update($dataBaru);
            } else {
                $pendeta = Pendeta::create($dataBaru);
            }

            // B. Upsert Data User Login
            // Generate email default jika kosong: nipg@gpipapua.local
            $email = $row['email_unik_opsional_untuk_login'] ?? Str::lower($nipg . '@gpipapua.local');
            
            // Cek apakah email sudah dipakai user lain (yang bukan pendeta ini)
            $existingUser = User::where('email', $email)->first();
            if ($existingUser && $existingUser->pendeta_id != $pendeta->id) {
                // Jika email bentrok, buat email fallback unik
                $email = Str::lower($nipg . rand(10,99) . '@gpipapua.local');
            }

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $pendeta->nama_lengkap,
                    'password' => Hash::make($nipg), // Default password = NIPG
                    'pendeta_id' => $pendeta->id,
                    'klasis_id' => $pendeta->klasis_penempatan_id,
                    'jemaat_id' => $pendeta->jemaat_penempatan_id,
                ]
            );
            
            // Assign Role Pendeta jika belum punya
            if ($this->rolePendeta && !$user->hasRole('Pendeta')) {
                $user->assignRole($this->rolePendeta);
            }

            DB::commit();
            return null; // Return null karena kita sudah handle save manual

        } catch (\Exception $e) {
            DB::rollBack();
            // Catat error ke list failures
            $this->addFailure($row, 'database_error', $e->getMessage());
            return null;
        }
    }

    /**
     * Helper untuk mencatat kegagalan manual
     */
    private function addFailure(array $row, string $attribute, string $message)
    {
        $rowIndex = $row['nipg_wajib_unik'] ?? 0; // Gunakan NIPG sebagai penanda baris jika row index sulit didapat
        $this->failures[] = new Failure($rowIndex, $attribute, [$message], $row);
    }

    /**
     * Menyiapkan data dari baris excel.
     */
    private function prepareData(array $row): array
    {
        // Mapping ID dari Nama (Cache)
        $klasisId = $this->klasisMap->get($row['nama_klasis_penempatan'] ?? null);
        $jemaatId = $this->jemaatMap->get($row['nama_jemaat_penempatan'] ?? null);

        return [
            'nipg' => $row['nipg_wajib_unik'] ?? null,
            'nama_lengkap' => $row['nama_lengkap_wajib'] ?? null,
            'nik' => $row['nik_unik_opsional'] ?? null,
            'tempat_lahir' => $row['tempat_lahir_wajib'] ?? null,
            'tanggal_lahir' => $this->transformDate($row['tanggal_lahir_yyyymmdd_wajib'] ?? null),
            'jenis_kelamin' => $row['jenis_kelamin_lakilakiperempuan_wajib'] ?? null,
            'tanggal_tahbisan' => $this->transformDate($row['tanggal_tahbisan_yyyymmdd_wajib'] ?? null),
            'tempat_tahbisan' => $row['tempat_tahbisan_wajib'] ?? null,
            'status_kepegawaian' => $row['status_kepegawaian_wajib'] ?? null,
            
            'klasis_penempatan_id' => $klasisId,
            'jemaat_penempatan_id' => $jemaatId,
            
            // Field Opsional
            'status_pernikahan' => $row['status_pernikahan'] ?? null,
            'nama_pasangan' => $row['nama_pasangan'] ?? null,
            'golongan_darah' => $row['golongan_darah'] ?? null,
            'alamat_domisili' => $row['alamat_domisili'] ?? null,
            'telepon' => $row['telepon'] ?? null,
            'nomor_sk_kependetaan' => $row['nomor_sk_kependetaan'] ?? null,
            'pendidikan_teologi_terakhir' => $row['pendidikan_teologi_terakhir'] ?? null,
            'institusi_pendidikan_teologi' => $row['institusi_pendidikan_teologi'] ?? null,
            'golongan_pangkat_terakhir' => $row['golongan_pangkat_terakhir'] ?? null,
            'tanggal_mulai_masuk_gpi' => $this->transformDate($row['tanggal_mulai_masuk_gpi_yyyymmdd'] ?? null),
            'jabatan_saat_ini' => $row['jabatan_saat_ini'] ?? null,
            'tanggal_mulai_jabatan_saat_ini' => $this->transformDate($row['tanggal_mulai_jabatan_yyyymmdd'] ?? null),
            'catatan' => $row['catatan'] ?? null,
        ];
    }

    /**
     * Aturan validasi per baris Excel.
     */
    public function rules(): array
    {
        return [
            '*.nipg_wajib_unik' => ['required', 'string', 'max:50'],
            '*.nama_lengkap_wajib' => ['required', 'string', 'max:255'],
            '*.tempat_lahir_wajib' => ['required'],
            '*.tanggal_lahir_yyyymmdd_wajib' => ['required'],
            '*.status_kepegawaian_wajib' => ['required'],
            '*.nama_klasis_penempatan' => ['nullable', Rule::exists('klasis', 'nama_klasis')], // Validasi nama klasis ada di DB
            '*.nama_jemaat_penempatan' => ['nullable', Rule::exists('jemaat', 'nama_jemaat')], // Validasi nama jemaat ada di DB
        ];
    }
    
    /**
     * Pesan error custom.
     */
    public function customValidationMessages()
    {
        return [
            '*.nipg_wajib_unik.required' => 'NIPG wajib diisi.',
            '*.nama_klasis_penempatan.exists' => 'Nama Klasis tidak ditemukan di database.',
            '*.nama_jemaat_penempatan.exists' => 'Nama Jemaat tidak ditemukan di database.',
        ];
    }

    /**
     * Konversi tanggal Excel ke format Y-m-d.
     */
    private function transformDate($value): ?string
    {
         if (empty($value)) return null;
         try {
             if (is_numeric($value)) {
                 return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
             }
             return Carbon::parse($value)->format('Y-m-d');
         } catch (\Exception $e) { return null; }
    }

    public function batchSize(): int { return 50; }
    public function chunkSize(): int { return 500; }
}