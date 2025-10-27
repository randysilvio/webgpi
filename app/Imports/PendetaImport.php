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
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;
use Illuminate\Support\Facades\Auth; // Untuk scoping
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Carbon\Carbon; // Untuk parsing tanggal

class PendetaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use SkipsFailures;

    private $rolePendeta;
    private $klasisMap;
    private $jemaatMap;
    // Tambahkan constraint untuk scoping
    private $allowedKlasisId = null; // null = semua, integer = hanya ID ini
    private $allowedJemaatIds = null; // null = semua, array = hanya ID ini

    public function __construct(?int $klasisIdConstraint = null, ?int $jemaatIdConstraint = null)
    {
        $this->rolePendeta = Role::where('name', 'Pendeta')->first();
        $this->klasisMap = Klasis::pluck('id', 'nama_klasis');
        $this->jemaatMap = Jemaat::pluck('id', 'nama_jemaat');

        // Set constraint berdasarkan input (misal dari controller)
        $this->allowedKlasisId = $klasisIdConstraint;
        if ($jemaatIdConstraint !== null) {
            $this->allowedJemaatIds = [$jemaatIdConstraint];
        } elseif ($klasisIdConstraint !== null) {
            // Jika constraint klasis, ambil ID jemaat di bawahnya
            $this->allowedJemaatIds = Jemaat::where('klasis_id', $klasisIdConstraint)->pluck('id')->toArray();
        }
        // Jika keduanya null, biarkan null (Super Admin)
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $dataBaru = $this->prepareData($row);
        $nipg = $dataBaru['nipg'] ?? null;
        $nik = $dataBaru['nik'] ?? null;

        // --- Validasi Scoping Penempatan ---
        $klasisIdImport = $dataBaru['klasis_penempatan_id'];
        $jemaatIdImport = $dataBaru['jemaat_penempatan_id'];

        // Cek Klasis ID
        if ($this->allowedKlasisId !== null && $klasisIdImport != $this->allowedKlasisId) {
             $message = "Import dilewati: Penempatan Klasis ID {$klasisIdImport} tidak sesuai lingkup Anda (Klasis ID: {$this->allowedKlasisId}).";
             Log::warning($message . " (NIPG: $nipg)");
             $failure = new Failure(0, 'nama_klasis_penempatan', [$message], $row);
             $this->onFailure($failure);
             return null;
        }
        // Cek Jemaat ID
        if ($this->allowedJemaatIds !== null && $jemaatIdImport !== null && !in_array($jemaatIdImport, $this->allowedJemaatIds)) {
             $message = "Import dilewati: Penempatan Jemaat ID {$jemaatIdImport} tidak termasuk dalam lingkup Anda.";
             Log::warning($message . " (NIPG: $nipg)");
             $failure = new Failure(0, 'nama_jemaat_penempatan', [$message], $row);
             $this->onFailure($failure);
             return null;
        }
        // --- Akhir Validasi Scoping Penempatan ---


        DB::beginTransaction();
        try {
            // Cari existing Pendeta by NIPG atau NIK
            $pendeta = null;
            if (!empty($nipg)) {
                $pendeta = Pendeta::where('nipg', $nipg)->first();
            }
            if (!$pendeta && !empty($nik)) {
                 $pendeta = Pendeta::where('nik', $nik)->first();
            }

            $user = null; // Inisialisasi User

            if ($pendeta) {
                // Pendeta Ditemukan -> Update (Logic Upsert Belum ada di Project Brief untuk Pendeta, jadi ini hanya update)
                Log::info('Updating Pendeta by NIPG/NIK: ' . ($nipg ?? $nik));
                // Pastikan user tidak bisa mengubah penempatan di luar scope saat update
                if ($this->allowedKlasisId !== null && $dataBaru['klasis_penempatan_id'] != $this->allowedKlasisId) unset($dataBaru['klasis_penempatan_id']);
                if ($this->allowedJemaatIds !== null && !in_array($dataBaru['jemaat_penempatan_id'], $this->allowedJemaatIds)) unset($dataBaru['jemaat_penempatan_id']);

                $pendeta->update($dataBaru);
                $user = $pendeta->user; // Ambil user terkait

            } else {
                // Pendeta Tidak Ditemukan -> Buat Baru
                Log::info('Creating new Pendeta: ' . ($dataBaru['nama_lengkap'] ?? 'N/A'));
                $pendeta = Pendeta::create($dataBaru);
            }

            // --- Logika Buat/Update User Otomatis ---
            if ($pendeta) {
                $emailToUse = $row['email_unik_opsional_untuk_login'] ?? Str::lower($pendeta->nipg . '@gpipapua.local'); // Sesuaikan header email
                $userIdToIgnore = $user ? $user->id : null; // ID user yang diabaikan saat cek unik email

                // Cek keunikan email
                $existingUserEmail = User::where('email', $emailToUse)->when($userIdToIgnore, fn($q) => $q->where('id', '!=', $userIdToIgnore))->first();

                if ($existingUserEmail) {
                    Log::warning('Email import ' . $emailToUse . ' sudah ada (User ID: '.$existingUserEmail->id.') untuk Pendeta NIPG: ' . $pendeta->nipg . '. Coba NIPG+ID.');
                    $emailToUse = Str::lower($pendeta->nipg . $pendeta->id . '@gpipapua.local');
                    // Cek lagi
                    $existingUserEmailFallback = User::where('email', $emailToUse)->when($userIdToIgnore, fn($q) => $q->where('id', '!=', $userIdToIgnore))->first();
                    if ($existingUserEmailFallback) {
                        throw new \Exception("Gagal membuat/update user: Email fallback {$emailToUse} juga sudah ada.");
                    }
                }

                $userData = [
                    'name' => $pendeta->nama_lengkap,
                    'email' => $emailToUse,
                    'pendeta_id' => $pendeta->id,
                    'klasis_id' => $pendeta->klasis_penempatan_id, // Update penempatan
                    'jemaat_id' => $pendeta->jemaat_penempatan_id, // Update penempatan
                ];

                if (!$user) { // Jika user belum ada (pendeta baru)
                    $userData['password'] = Hash::make($pendeta->nipg); // Password hanya di-set saat create
                    $user = User::create($userData);
                    Log::info('User otomatis dibuat via import untuk Pendeta ID: ' . $pendeta->id . ', User ID: ' . $user->id);
                    // Assign role hanya saat create
                    if ($this->rolePendeta) {
                        $user->assignRole($this->rolePendeta);
                    } else { throw new \Exception("Role Pendeta tidak ditemukan."); }
                } else { // Jika user sudah ada (update pendeta)
                    $user->update($userData);
                    Log::info('User terkait Pendeta ID ' . $pendeta->id . ' diupdate via import. User ID: ' . $user->id);
                    // Pastikan role Pendeta tetap ada
                    if ($this->rolePendeta && !$user->hasRole('Pendeta')) {
                        $user->assignRole($this->rolePendeta);
                    }
                }
            } else {
                 throw new \Exception("Gagal membuat/menemukan record Pendeta dari Excel.");
            }

            DB::commit();
            return null; // Return null karena proses sudah selesai (create/update)

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rollback saat import Pendeta NIPG: ' . ($nipg ?? 'N/A') . '. Error: ' . $e->getMessage());
             $failure = new Failure( 0, 'database_error', [$e->getMessage()], $row );
             $this->onFailure($failure);
            return null;
        }
    }

     /**
     * Menyiapkan data dari baris excel.
     */
    private function prepareData(array $row): array
    {
        // Cari ID Klasis & Jemaat berdasarkan nama dari Excel
        // ** PENTING: Sesuaikan key $row['...'] dengan header di Excel **
        $klasisId = $this->klasisMap->get($row['nama_klasis_penempatan'] ?? null);
        $jemaatId = $this->jemaatMap->get($row['nama_jemaat_penempatan'] ?? null);

         return [
            'nipg' => $row['nipg_wajib_unik'] ?? null, // Sesuaikan header
            'nama_lengkap' => $row['nama_lengkap_wajib'] ?? null, // Sesuaikan header
            'nik' => $row['nik_unik_opsional'] ?? null, // Sesuaikan header
            'tempat_lahir' => $row['tempat_lahir_wajib'] ?? null, // Sesuaikan header
            'tanggal_lahir' => $this->transformDate($row['tanggal_lahir_yyyymmdd_wajib'] ?? null), // Sesuaikan header
            'jenis_kelamin' => $row['jenis_kelamin_lakilakiperempuan_wajib'] ?? null, // Sesuaikan header
            // Email diambil langsung di model()
            'tanggal_tahbisan' => $this->transformDate($row['tanggal_tahbisan_yyyymmdd_wajib'] ?? null), // Sesuaikan header
            'tempat_tahbisan' => $row['tempat_tahbisan_wajib'] ?? null, // Sesuaikan header
            'status_kepegawaian' => $row['status_kepegawaian_wajib'] ?? null, // Sesuaikan header
            'klasis_penempatan_id' => $klasisId,
            'jemaat_penempatan_id' => $jemaatId,
            // Field opsional
            'status_pernikahan' => $row['status_pernikahan'] ?? null,
            'nama_pasangan' => $row['nama_pasangan'] ?? null,
            'golongan_darah' => $row['golongan_darah'] ?? null,
            'alamat_domisili' => $row['alamat_domisili'] ?? null,
            'telepon' => $row['telepon'] ?? null,
            'nomor_sk_kependetaan' => $row['nomor_sk_kependetaan'] ?? null,
            'pendidikan_teologi_terakhir' => $row['pendidikan_teologi_terakhir'] ?? null,
            'institusi_pendidikan_teologi' => $row['institusi_pendidikan_teologi'] ?? null,
            'golongan_pangkat_terakhir' => $row['golongan_pangkat_terakhir'] ?? null,
            'tanggal_mulai_masuk_gpi' => $this->transformDate($row['tanggal_mulai_masuk_gpi_yyyymmdd'] ?? null), // Sesuaikan header
            'jabatan_saat_ini' => $row['jabatan_saat_ini'] ?? null,
            'tanggal_mulai_jabatan_saat_ini' => $this->transformDate($row['tanggal_mulai_jabatan_yyyymmdd'] ?? null), // Sesuaikan header
            'catatan' => $row['catatan'] ?? null,
        ];
    }

    // --- function transformDate tetap sama ---
    private function transformDate($value): ?string { /* ... kode transform date ... */ }

    /**
     * Aturan validasi per baris.
     */
    public function rules(): array
    {
         // ** PENTING: Sesuaikan key array dengan nama header di file Excel Anda **
        return [
            '*.nipg_wajib_unik' => ['required', 'string', 'max:50'], // Unique dicek di model()
            '*.nama_lengkap_wajib' => ['required', 'string', 'max:255'],
            '*.nik_unik_opsional' => ['nullable', 'string', 'max:20'], // Unique dicek di model()
            '*.tempat_lahir_wajib' => ['required', 'string', 'max:100'],
            '*.tanggal_lahir_yyyymmdd_wajib' => ['required', 'numeric'], // Validasi format Excel
            '*.jenis_kelamin_lakilakiperempuan_wajib' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            '*.email_unik_opsional_untuk_login' => ['nullable', 'string', 'email', 'max:255'], // Unique dicek di model()
            '*.tanggal_tahbisan_yyyymmdd_wajib' => ['required', 'numeric'],
            '*.tempat_tahbisan_wajib' => ['required', 'string', 'max:255'],
            '*.status_kepegawaian_wajib' => ['required', 'string', 'max:50'],
            '*.nama_klasis_penempatan' => ['nullable', 'string', Rule::exists('klasis', 'nama_klasis')],
            '*.nama_jemaat_penempatan' => ['nullable', 'string', Rule::exists('jemaat', 'nama_jemaat')],
        ];
    }

    /**
     * Pesan error custom.
     */
     public function customValidationMessages()
     {
         // ** PENTING: Sesuaikan key array dengan nama header di file Excel Anda **
         return [
             '*.nipg_wajib_unik.required' => 'Header [nipg_wajib_unik] wajib diisi.',
             '*.nama_lengkap_wajib.required' => 'Header [nama_lengkap_wajib] wajib diisi.',
             '*.tempat_lahir_wajib.required' => 'Header [tempat_lahir_wajib] wajib diisi.',
             '*.tanggal_lahir_yyyymmdd_wajib.required' => 'Header [tanggal_lahir_yyyymmdd_wajib] wajib diisi.',
             '*.tanggal_lahir_yyyymmdd_wajib.numeric' => 'Format Tanggal Lahir harus angka Excel.',
             '*.jenis_kelamin_lakilakiperempuan_wajib.required' => 'Header [jenis_kelamin_lakilakiperempuan_wajib] wajib diisi.',
             '*.jenis_kelamin_lakilakiperempuan_wajib.in' => 'Jenis Kelamin tidak valid.',
             '*.email_unik_opsional_untuk_login.email' => 'Format Email tidak valid.',
             '*.tanggal_tahbisan_yyyymmdd_wajib.*' => 'Tanggal Tahbisan wajib & format angka Excel.',
             '*.tempat_tahbisan_wajib.required' => 'Header [tempat_tahbisan_wajib] wajib diisi.',
             '*.status_kepegawaian_wajib.required' => 'Header [status_kepegawaian_wajib] wajib diisi.',
             '*.nama_klasis_penempatan.exists' => 'Nama Klasis Penempatan tidak ditemukan.',
             '*.nama_jemaat_penempatan.exists' => 'Nama Jemaat Penempatan tidak ditemukan.',
         ];
     }

    // --- function batchSize & chunkSize tetap sama ---
    public function batchSize(): int { return 50; } // Kurangi batch size jika ada logic user
    public function chunkSize(): int { return 500; }

    // --- function countFilledFields tetap sama ---
    private function countFilledFields(array $data): int { /* ... kode count fields ... */ }

}