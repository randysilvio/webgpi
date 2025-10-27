<?php

namespace App\Http\Controllers\Admin;
// app/Http/Controllers/Admin/PendetaController.php

use App\Http\Controllers\Controller;
use App\Models\Pendeta;
use App\Models\User;
use App\Models\Klasis; // <-- Diperlukan untuk filter
use App\Models\Jemaat; // <-- Diperlukan untuk filter
use Illuminate\Http\Request; // <-- Pastikan Request di-use
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // <-- Pastikan Auth di-use
use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\PendetaExport; // Diganti di bawah
// use App\Imports\PendetaImport; // Diganti di bawah
use App\Exports\PendetaExport; // <-- Pastikan namespace benar
use App\Imports\PendetaImport; // <-- Pastikan namespace benar
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\FromCollection;
use Spatie\Permission\Models\Role; // <-- Import Role
use Illuminate\Support\Facades\DB; // <-- Import DB Facade for transaction
use Illuminate\Validation\Rule; // <-- Import Rule for unique validation update
use Illuminate\Support\Str; // <-- Import Str Facade

class PendetaController extends Controller
{
    // Middleware untuk hak akses
    public function __construct()
    {
        $this->middleware(['auth']);
        // Hak akses untuk CRUD (kecuali index/show) hanya Super Admin & Admin Bidang 3
        $this->middleware('role:Super Admin|Admin Bidang 3')->except(['show', 'index']);
        // Hak akses untuk melihat index/show lebih luas
        $this->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis|Admin Jemaat|Pendeta')->only(['index', 'show']);
        // Tambahkan hak akses spesifik untuk import/export jika perlu (misal, tidak untuk Pendeta biasa)
        // $this->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis|Admin Jemaat')->only(['showImportForm', 'import', 'export']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // --- Query dasar ---
        $query = Pendeta::with(['klasisPenempatan', 'jemaatPenempatan', 'user'])->latest(); // Tambahkan 'user'
        $user = Auth::user();

        // --- Scoping Tampilan Data ---
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $query->where('klasis_penempatan_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
             $query->where('jemaat_penempatan_id', $user->jemaat_id);
        }
        // Super Admin & Admin Bidang 3 bisa lihat semua (sesuai filter)
        // Pendeta biasa bisa lihat semua (sesuai filter), atau bisa dibatasi hanya lihat diri sendiri jika perlu

        // --- Opsi Filter ---
        // Load opsi Klasis berdasarkan scope user
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $klasisFilterOptions = Klasis::where('id', $user->klasis_id)->pluck('nama_klasis', 'id');
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            // Admin Jemaat hanya bisa filter berdasarkan Klasis dari Jemaatnya
            $jemaatUser = Jemaat::find($user->jemaat_id);
            if ($jemaatUser && $jemaatUser->klasis_id) {
                 $klasisFilterOptions = Klasis::where('id', $jemaatUser->klasis_id)->pluck('nama_klasis', 'id');
            } else {
                 $klasisFilterOptions = collect(); // Kosongkan jika tidak ada klasis
            }
        } else { // Super Admin, Admin Bidang, Pendeta
            $klasisFilterOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        }

        $jemaatFilterOptions = collect();
        $statusOptions = Pendeta::select('status_kepegawaian')->distinct()->pluck('status_kepegawaian', 'status_kepegawaian');

        // --- Terapkan Filter Request (dengan memperhatikan scope) ---
        $selectedKlasisId = null; // Untuk load opsi jemaat
        if ($request->filled('klasis_penempatan_id')) {
            $klasisFilterId = filter_var($request->klasis_penempatan_id, FILTER_VALIDATE_INT);
            // Hanya terapkan filter jika ID valid DAN sesuai scope user
            if ($klasisFilterId && $klasisFilterOptions->has($klasisFilterId)) {
                $query->where('klasis_penempatan_id', $klasisFilterId);
                $selectedKlasisId = $klasisFilterId; // Simpan untuk load opsi Jemaat
            }
        } elseif ($user->hasRole('Admin Klasis') && $user->klasis_id) {
             // Jika Admin Klasis tidak filter, defaultnya adalah klasisnya sendiri
             $selectedKlasisId = $user->klasis_id;
        } elseif ($user->hasRole('Admin Jemaat') && $jemaatUser && $jemaatUser->klasis_id) {
             // Jika Admin Jemaat tidak filter, defaultnya adalah klasisnya
             $selectedKlasisId = $jemaatUser->klasis_id;
        }


        // Load Opsi Jemaat hanya jika ada Klasis terpilih (baik dari filter atau scope)
        if ($selectedKlasisId) {
            $jemaatQuery = Jemaat::where('klasis_id', $selectedKlasisId);
            // Jika user Admin Jemaat, hanya tampilkan Jemaatnya
            if ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
                $jemaatQuery->where('id', $user->jemaat_id);
            }
            $jemaatFilterOptions = $jemaatQuery->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
        }


        // Terapkan Filter Jemaat
        if ($request->filled('jemaat_penempatan_id')) {
            $jemaatFilterId = filter_var($request->jemaat_penempatan_id, FILTER_VALIDATE_INT);
            // Hanya terapkan jika ID valid DAN ada di opsi yang di-load (sesuai scope Klasis/Jemaat)
            if ($jemaatFilterId && $jemaatFilterOptions->has($jemaatFilterId)) {
                $query->where('jemaat_penempatan_id', $jemaatFilterId);
            }
        }

        // Terapkan Filter Status
        if ($request->filled('status_kepegawaian')) {
            $statusFilter = $request->status_kepegawaian;
            if ($statusOptions->has($statusFilter)) {
                $query->where('status_kepegawaian', $statusFilter);
            }
        }

        // Terapkan Filter Search
        if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                 $q->where('nama_lengkap', 'like', $searchTerm)
                   ->orWhere('nipg', 'like', $searchTerm);
             });
         }

        $pendetaData = $query->paginate(15)->appends($request->query());
        return view('admin.pendeta.index', compact('pendetaData', 'klasisFilterOptions', 'jemaatFilterOptions', 'statusOptions', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        // Opsi Jemaat bisa di-load via JS/AJAX saat Klasis dipilih di form
        $jemaatOptions = collect(); // Awalnya kosong
        return view('admin.pendeta.create', compact('klasisOptions', 'jemaatOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // --- Validasi Data Pendeta ---
        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:pendeta,nik',
            'nipg' => 'required|string|max:50|unique:pendeta,nipg',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'status_pernikahan' => 'nullable|string|max:50',
            'nama_pasangan' => 'nullable|string|max:255',
            'golongan_darah' => 'nullable|string|max:5',
            'alamat_domisili' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'tanggal_tahbisan' => 'required|date',
            'tempat_tahbisan' => 'required|string|max:255',
            'nomor_sk_kependetaan' => 'nullable|string|max:100',
            'status_kepegawaian' => 'required|string|max:50',
            'pendidikan_teologi_terakhir' => 'nullable|string|max:100',
            'institusi_pendidikan_teologi' => 'nullable|string|max:150',
            'golongan_pangkat_terakhir' => 'nullable|string|max:50',
            'tanggal_mulai_masuk_gpi' => 'nullable|date',
            'klasis_penempatan_id' => 'nullable|exists:klasis,id',
            'jemaat_penempatan_id' => 'nullable|exists:jemaat,id',
            'jabatan_saat_ini' => 'nullable|string|max:100',
            'tanggal_mulai_jabatan_saat_ini' => 'nullable|date',
            'foto_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'catatan' => 'nullable|string',
        ]);

        // --- Handle File Upload Foto ---
        $fotoPath = null;
        if ($request->hasFile('foto_path')) {
            $fotoPath = $request->file('foto_path')->store('pendeta_photos', 'public');
        }
        $validatedData['foto_path'] = $fotoPath;

        // --- Gunakan DB Transaction ---
        DB::beginTransaction();
        try {
            // --- Simpan Data Pendeta ---
            $pendeta = Pendeta::create($validatedData);

            // --- Logika Buat User Otomatis ---
            $emailToUse = $validatedData['email'] ?? Str::lower($pendeta->nipg . '@gpipapua.local');
            if (!isset($validatedData['email'])) {
                 $counter = 1;
                 while (User::where('email', $emailToUse)->exists()) {
                     $emailToUse = Str::lower($pendeta->nipg . '_' . $counter . '@gpipapua.local');
                     $counter++;
                     if ($counter > 5) { // Batasi percobaan
                        throw new \Exception("Gagal membuat user: Terlalu banyak email fallback yang sama untuk NIPG {$pendeta->nipg}.");
                     }
                 }
            } elseif (User::where('email', $emailToUse)->exists()) {
                 // Ini seharusnya sudah dicegah validasi, tapi double check
                 throw new \Exception("Gagal membuat user: Email {$emailToUse} sudah digunakan.");
            }


            $user = User::create([
                'name' => $pendeta->nama_lengkap,
                'email' => $emailToUse,
                'password' => Hash::make($pendeta->nipg),
                'pendeta_id' => $pendeta->id,
                'klasis_id' => $pendeta->klasis_penempatan_id,
                'jemaat_id' => $pendeta->jemaat_penempatan_id,
            ]);

            $rolePendeta = Role::where('name', 'Pendeta')->first();
            if ($rolePendeta) {
                $user->assignRole($rolePendeta);
            } else {
                 Log::error('Role "Pendeta" tidak ditemukan.');
                 DB::rollBack();
                 if ($fotoPath && Storage::disk('public')->exists($fotoPath)) { Storage::disk('public')->delete($fotoPath); }
                 return redirect()->route('admin.pendeta.create')->with('error', 'Gagal: Role "Pendeta" tidak ada.')->withInput();
            }

            Log::info('User otomatis dibuat (Pendeta ID: ' . $pendeta->id . ', User ID: ' . $user->id.')');
            DB::commit();
            return redirect()->route('admin.pendeta.index')->with('success', 'Data Pendeta & akun user berhasil dibuat.');

        } catch (\Exception $e) {
             DB::rollBack();
             Log::error('Gagal simpan Pendeta (Rollback): ' . $e->getMessage());
             if ($fotoPath && Storage::disk('public')->exists($fotoPath)) { Storage::disk('public')->delete($fotoPath); }
             return redirect()->route('admin.pendeta.create')->with('error', 'Gagal menyimpan. Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pendeta $pendeta)
    {
         // Scoping untuk show (jika perlu):
         $user = Auth::user();
         if (($user->hasRole('Admin Klasis') && $user->klasis_id != $pendeta->klasis_penempatan_id) ||
             ($user->hasRole('Admin Jemaat') && $user->jemaat_id != $pendeta->jemaat_penempatan_id)) {
            // Uncomment jika Admin Klasis/Jemaat hanya boleh lihat pendeta di scope nya
            // abort(403, 'Anda tidak diizinkan melihat data pendeta ini.');
         }
         // Pendeta biasa mungkin hanya boleh lihat profil sendiri?
         // if ($user->hasRole('Pendeta') && $user->pendeta_id != $pendeta->id) {
         //     abort(403, 'Anda hanya dapat melihat profil Anda sendiri.');
         // }


         $pendeta->load(['klasisPenempatan', 'jemaatPenempatan', 'user']);
         return view('admin.pendeta.show', compact('pendeta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pendeta $pendeta)
    {
        // Scoping edit (middleware sudah handle role Super Admin/Bidang 3)
        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        $jemaatOptions = collect();
        if ($pendeta->klasis_penempatan_id) {
            $jemaatOptions = Jemaat::where('klasis_id', $pendeta->klasis_penempatan_id)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
        }
        return view('admin.pendeta.edit', compact('pendeta', 'klasisOptions', 'jemaatOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pendeta $pendeta)
    {
        // Scoping update (middleware sudah handle role)

         $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:pendeta,nik,' . $pendeta->id,
            'nipg' => 'required|string|max:50|unique:pendeta,nipg,' . $pendeta->id,
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'status_pernikahan' => 'nullable|string|max:50',
            'nama_pasangan' => 'nullable|string|max:255',
            'golongan_darah' => 'nullable|string|max:5',
            'alamat_domisili' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore(optional($pendeta->user)->id)],
            'tanggal_tahbisan' => 'required|date',
            'tempat_tahbisan' => 'required|string|max:255',
            'nomor_sk_kependetaan' => 'nullable|string|max:100',
            'status_kepegawaian' => 'required|string|max:50',
            'pendidikan_teologi_terakhir' => 'nullable|string|max:100',
            'institusi_pendidikan_teologi' => 'nullable|string|max:150',
            'golongan_pangkat_terakhir' => 'nullable|string|max:50',
            'tanggal_mulai_masuk_gpi' => 'nullable|date',
            'klasis_penempatan_id' => 'nullable|exists:klasis,id',
            'jemaat_penempatan_id' => 'nullable|exists:jemaat,id',
            'jabatan_saat_ini' => 'nullable|string|max:100',
            'tanggal_mulai_jabatan_saat_ini' => 'nullable|date',
            'foto_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'catatan' => 'nullable|string',
        ]);

        $fotoPathLama = $pendeta->foto_path;
        $fotoPathBaru = null;

        if ($request->hasFile('foto_path')) {
            $fotoPathBaru = $request->file('foto_path')->store('pendeta_photos', 'public');
            $validatedData['foto_path'] = $fotoPathBaru;
        } else {
             // Jika tidak ada file baru, jangan update path foto di DB
             unset($validatedData['foto_path']);
        }


        DB::beginTransaction();
        try {
            // Update Pendeta
            $pendeta->update($validatedData);

            // Update User Terkait
            if ($pendeta->user) {
                $userDataToUpdate = [];
                if (isset($validatedData['nama_lengkap']) && $pendeta->user->name !== $validatedData['nama_lengkap']) {
                    $userDataToUpdate['name'] = $validatedData['nama_lengkap'];
                }
                 if (isset($validatedData['email']) && $validatedData['email'] && $pendeta->user->email !== $validatedData['email']) {
                    $userDataToUpdate['email'] = $validatedData['email'];
                 }
                 // Update penempatan
                 if (array_key_exists('klasis_penempatan_id', $validatedData) && $pendeta->user->klasis_id !== $validatedData['klasis_penempatan_id']) {
                     $userDataToUpdate['klasis_id'] = $validatedData['klasis_penempatan_id'];
                 }
                 if (array_key_exists('jemaat_penempatan_id', $validatedData) && $pendeta->user->jemaat_id !== $validatedData['jemaat_penempatan_id']) {
                     $userDataToUpdate['jemaat_id'] = $validatedData['jemaat_penempatan_id'];
                 }

                if (!empty($userDataToUpdate)) {
                    $pendeta->user()->update($userDataToUpdate);
                }
            }

             if ($fotoPathBaru && $fotoPathLama && Storage::disk('public')->exists($fotoPathLama)) {
                 Storage::disk('public')->delete($fotoPathLama);
             }

            DB::commit();
            return redirect()->route('admin.pendeta.index')->with('success', 'Data Pendeta berhasil diperbarui.');

        } catch (\Exception $e) {
             DB::rollBack();
             Log::error('Gagal update Pendeta ID: ' . $pendeta->id . '. Error: ' . $e->getMessage());
             if ($fotoPathBaru && Storage::disk('public')->exists($fotoPathBaru)) { Storage::disk('public')->delete($fotoPathBaru); }
             return redirect()->route('admin.pendeta.edit', $pendeta->id)->with('error', 'Gagal update. Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pendeta $pendeta)
    {
         // Scoping destroy (middleware sudah handle role)
         DB::beginTransaction();
         try {
             $fotoPath = $pendeta->foto_path;
             $user = $pendeta->user;
             $namaPendeta = $pendeta->nama_lengkap;

             // Hapus Pendeta
             $pendeta->delete();

             // Hapus user jika hanya punya role Pendeta
             if ($user && $user->roles()->count() === 1 && $user->hasRole('Pendeta')) {
                 Log::info("Menghapus user {$user->id} terkait Pendeta {$namaPendeta}");
                 $user->delete();
             } elseif ($user) {
                 // Jika punya role lain, hanya null kan pendeta_id
                 $user->update(['pendeta_id' => null]);
             }

             // Hapus foto
             if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                 Storage::disk('public')->delete($fotoPath);
             }

             DB::commit();
             return redirect()->route('admin.pendeta.index')->with('success', 'Data Pendeta (' . $namaPendeta . ') berhasil dihapus.');

         } catch (\Exception $e) {
              DB::rollBack();
              Log::error('Gagal hapus Pendeta ID: ' . $pendeta->id . '. Error: ' . $e->getMessage());
               if (str_contains($e->getMessage(), 'constraint violation')) {
                    return redirect()->route('admin.pendeta.index')->with('error', 'Gagal hapus: Pendeta ini mungkin masih terkait dengan data lain (misal: Ketua Klasis).');
               }
              return redirect()->route('admin.pendeta.index')->with('error', 'Gagal menghapus. Error: ' . $e->getMessage());
         }
    }

     /**
      * Handle request export data Pendeta.
      * (DIUPDATE untuk scoping)
      */
     public function export(Request $request)
     {
          $user = Auth::user(); // Ambil user untuk scoping

          // --- Handle Template Export ---
          if ($request->has('template') && $request->template == 'yes') {
                // Gunakan PendetaExport (tanpa filter) untuk ambil headings
                $export = new PendetaExport();
                $headings = $export->headings();
                $templateCollection = collect([$headings]);
                $fileName = 'template_import_pendeta.xlsx';

                $templateExport = new class($templateCollection) implements FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
                    // ... (kode class template export) ...
                     use \Maatwebsite\Excel\Concerns\Exportable;
                     protected $collection; protected $headingsData;
                     public function __construct($collection) { $this->collection = $collection; $this->headingsData = $collection->first();}
                     public function collection() { return collect([]); }
                     public function headings(): array { return $this->headingsData ?? []; }
                };
                return Excel::download($templateExport, $fileName);
          }

         // --- Export Data Normal (dengan Scoping) ---
         try {
             $fileName = 'pendeta_gpi_papua_' . date('YmdHis') . '.xlsx';

             // Ambil filter dari request
             $search = $request->query('search');
             $klasisIdFilter = $request->query('klasis_penempatan_id');
             $jemaatIdFilter = $request->query('jemaat_penempatan_id');
             $statusFilter = $request->query('status_kepegawaian');

             // Buat instance PendetaExport dengan filter
             // Class Export akan menangani scoping di dalam method query()
             $export = new PendetaExport( $search, $klasisIdFilter, $jemaatIdFilter, $statusFilter );

             return Excel::download($export, $fileName);

         } catch (\Exception $e) {
              Log::error('Gagal export Pendeta: ' . $e->getMessage());
              return redirect()->route('admin.pendeta.index')->with('error', 'Gagal mengekspor data Pendeta. Error: ' . $e->getMessage());
         }
     }

     /**
      * Menampilkan halaman form import Pendeta.
      */
     public function showImportForm()
     {
          return view('admin.pendeta.import');
     }


     /**
      * Handle request import data Pendeta.
      * (DIUPDATE untuk scoping)
      */
     public function import(Request $request)
     {
         $request->validate([ 'import_file' => 'required|file|mimes:xlsx,xls,csv', ]);
         $file = $request->file('import_file');

         try {
             // --- Tentukan Constraint Scoping ---
             $user = Auth::user();
             $klasisIdConstraint = $user->hasRole('Admin Klasis') ? $user->klasis_id : null;
             $jemaatIdConstraint = $user->hasRole('Admin Jemaat') ? $user->jemaat_id : null;
             // Jika Admin Jemaat, kita juga perlu constraint klasisnya
             if ($jemaatIdConstraint && !$klasisIdConstraint) {
                 $jemaatUser = Jemaat::find($jemaatIdConstraint);
                 $klasisIdConstraint = $jemaatUser->klasis_id ?? null;
             }

             // Buat instance Import dengan constraint
             $import = new PendetaImport($klasisIdConstraint, $jemaatIdConstraint);
             Excel::import($import, $file);

             // Handle failures (Sudah benar)
             $failures = $import->failures();
             if ($failures->isNotEmpty()) {
                $errorRows = []; $errorCount = count($failures);
                foreach ($failures as $failure) { $errorRows[] = 'Baris ' . ($failure->row() ?: '?') . ': ' . implode(', ', $failure->errors()) . ' (Nilai: ' . implode(', ', array_slice($failure->values(), 0, 3)) . '...)';}
                $errorMessage = "Import selesai, namun terdapat {$errorCount} kesalahan:\n" . implode("\n", $errorRows);
                Log::warning($errorMessage);
                if ($errorCount > 10) { $errorMessage = "Import selesai dengan {$errorCount} kesalahan (10 error pertama):\n" . implode("\n", array_slice($errorRows, 0, 10)) . "\n... (cek log)";}
                return redirect()->route('admin.pendeta.index')->with('warning', $errorMessage);
             }

             return redirect()->route('admin.pendeta.index')->with('success', 'Data Pendeta berhasil diimpor.');

         } catch (ValidationException $e) {
            // ... (Error handling ValidationException sudah benar) ...
             $failures = $e->failures(); $errorRows = []; $errorCount = count($failures);
             foreach ($failures as $failure) { $errorRows[] = 'Baris ' . ($failure->row() ?: '?') . ': ' . implode(', ', $failure->errors()) . ' (Nilai: ' . implode(', ', array_slice($failure->values(), 0, 3)) . '...)'; }
             $errorMessage = "Gagal import karena {$errorCount} kesalahan validasi:\n" . implode("\n", $errorRows); Log::error($errorMessage);
             if ($errorCount > 10) { $errorMessage = "Gagal import karena {$errorCount} kesalahan validasi (10 error pertama):\n" . implode("\n", array_slice($errorRows, 0, 10)) . "\n... (cek log)"; }
            return redirect()->back()->with('error', $errorMessage)->withInput();
         } catch (\InvalidArgumentException $e) { // Tangkap error scope dari Import class
             Log::error('Gagal import Pendeta: ' . $e->getMessage());
             return redirect()->back()->with('error', $e->getMessage())->withInput();
         } catch (\Exception $e) {
              Log::error('Gagal import Pendeta: ' . $e->getMessage());
              return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data Pendeta. Error: ' . $e->getMessage())->withInput();
         }
     }
}