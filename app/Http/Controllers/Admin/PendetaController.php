<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendeta;
use App\Models\User; // Tambahkan User model
use App\Models\Klasis; // Tambahkan Klasis model untuk dropdown
use App\Models\Jemaat; // Tambahkan Jemaat model untuk dropdown
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Tambahkan Hash
use Illuminate\Support\Facades\Log; // Tambahkan Log untuk debugging
use Illuminate\Database\QueryException; // Tambahkan QueryException
use Illuminate\Support\Facades\Storage; // Untuk menghapus file foto jika ada
use Illuminate\Support\Facades\Auth; // Untuk scoping (jika login aktif)
use Maatwebsite\Excel\Facades\Excel; // <-- Tambahkan
use App\Exports\PendetaExport; // <-- Tambahkan
use App\Imports\PendetaImport; // <-- Tambahkan
use Maatwebsite\Excel\Validators\ValidationException; // <-- Tambahkan
use Maatwebsite\Excel\Concerns\FromCollection; // <-- Tambahkan

class PendetaController extends Controller
{
    // Middleware untuk hak akses (Contoh)
    public function __construct()
    {
        // KOMENTARI SEMUA MIDDLEWARE SEMENTARA
        // $this->middleware(['auth']);

        // Izinkan Super Admin dan Admin Bidang 3 mengelola data Pendeta
        // Sesuaikan permission atau role sesuai implementasi Spatie Anda
        // $this->middleware('role:Super Admin|Admin Bidang 3')->except(['show', 'index']); // <-- KOMENTARI INI
        // Mungkin role 'Pendeta' bisa melihat show? Atau role lain? Sesuaikan.
        // $this->middleware('permission:view pendeta')->only('show');
        // $this->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis|Admin Jemaat|Pendeta')->only(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // Tambahkan Request
    {
        $query = Pendeta::with(['klasisPenempatan', 'jemaatPenempatan'])->latest(); // Eager load

        // Fitur Search Sederhana
         if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                 $q->where('nama_lengkap', 'like', $searchTerm)
                   ->orWhere('nipg', 'like', $searchTerm);
             });
         }

        // Ambil data pendeta dengan pagination
        $pendetaData = $query->paginate(15)->appends($request->query()); // Ambil 15 data per halaman
        return view('admin.pendeta.index', compact('pendetaData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil data Klasis dan Jemaat untuk dropdown
        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        $jemaatOptions = Jemaat::orderBy('nama_jemaat')->pluck('nama_jemaat', 'id'); // Nanti bisa difilter
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
            'email' => 'nullable|string|email|max:255|unique:users,email', // Validasi email unik di tabel users juga
            'tanggal_tahbisan' => 'required|date',
            'tempat_tahbisan' => 'required|string|max:255',
            'nomor_sk_kependetaan' => 'nullable|string|max:100',
            'status_kepegawaian' => 'required|string|max:50', // Sesuaikan dengan opsi Anda
            'pendidikan_teologi_terakhir' => 'nullable|string|max:100',
            'institusi_pendidikan_teologi' => 'nullable|string|max:150',
            'golongan_pangkat_terakhir' => 'nullable|string|max:50',
            'tanggal_mulai_masuk_gpi' => 'nullable|date',
            'klasis_penempatan_id' => 'nullable|exists:klasis,id',
            'jemaat_penempatan_id' => 'nullable|exists:jemaat,id',
            'jabatan_saat_ini' => 'nullable|string|max:100',
            'tanggal_mulai_jabatan_saat_ini' => 'nullable|date',
            'foto_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file foto
            'catatan' => 'nullable|string',
        ]);

        // --- Handle File Upload Foto ---
        if ($request->hasFile('foto_path')) {
            $validatedData['foto_path'] = $request->file('foto_path')->store('pendeta_photos', 'public');
        }

        // --- Simpan Data Pendeta ---
        $pendeta = null; // Inisialisasi variabel pendeta
        try {
            $pendeta = Pendeta::create($validatedData);

            // --- Logika Buat User Otomatis ---
            if ($pendeta) {
                try {
                    // Pastikan ada email atau buat logic jika email nullable
                    // Jika email kosong & unik, coba pakai NIPG + domain dummy
                    $emailToUse = $validatedData['email'] ?? $pendeta->nipg . '@gpipapua.local';
                    // Cek lagi keunikan email dummy jika email asli tidak ada
                    if (!isset($validatedData['email']) && User::where('email', $emailToUse)->exists()) {
                         // Jika email dummy sudah ada, tambahkan suffix unik atau minta email asli
                         Log::warning('Email dummy ' . $emailToUse . ' sudah ada untuk Pendeta ID: ' . $pendeta->id . '. Coba NIPG+ID.');
                         $emailToUse = $pendeta->nipg . $pendeta->id . '@gpipapua.local'; // Tambah ID agar unik
                    }

                    $user = User::create([
                        'name' => $pendeta->nama_lengkap,
                        'email' => $emailToUse,
                        // 'username' => $pendeta->nipg, // Alternatif jika login pakai username
                        'password' => Hash::make($pendeta->nipg), // NIPG sebagai password awal
                        'pendeta_id' => $pendeta->id,
                    ]);

                    // Assign role (Pastikan Role 'Pendeta' ada di DB)
                    $rolePendeta = Role::where('name', 'Pendeta')->first();
                    if ($rolePendeta) {
                        $user->assignRole($rolePendeta);
                    } else {
                         Log::error('Role "Pendeta" tidak ditemukan saat membuat user otomatis untuk Pendeta ID: ' . $pendeta->id);
                         // Pertimbangkan apa yg harus dilakukan? Hapus user & pendeta? Atau biarkan tanpa role?
                         // Untuk sementara, biarkan user tanpa role 'Pendeta' tapi beri warning
                         return redirect()->route('admin.pendeta.index')->with('warning', 'Data Pendeta berhasil dibuat, TAPI role "Pendeta" tidak ditemukan. Akun user dibuat tanpa role.');
                    }


                    Log::info('User otomatis dibuat untuk Pendeta ID: ' . $pendeta->id . ', User ID: ' . $user->id);

                } catch (QueryException $e) {
                    Log::error('Gagal membuat user otomatis untuk Pendeta ID: ' . ($pendeta->id ?? 'unknown') . '. Error: ' . $e->getMessage());
                    // Hapus data pendeta yang baru dibuat karena user gagal
                    if ($pendeta) {
                        // Hapus foto jika sudah terupload
                        if (isset($validatedData['foto_path']) && $validatedData['foto_path'] && Storage::disk('public')->exists($validatedData['foto_path'])) {
                            Storage::disk('public')->delete($validatedData['foto_path']);
                        }
                        $pendeta->delete();
                    }
                    return redirect()->route('admin.pendeta.create')
                                 ->with('error', 'Gagal menyimpan data Pendeta: Gagal membuat akun user terkait (Email mungkin sudah terdaftar atau tidak valid).')
                                 ->withInput();
                } catch (\Exception $e) {
                     Log::error('Error lain saat membuat user otomatis: ' . $e->getMessage());
                     if ($pendeta) {
                         if (isset($validatedData['foto_path']) && $validatedData['foto_path'] && Storage::disk('public')->exists($validatedData['foto_path'])) {
                             Storage::disk('public')->delete($validatedData['foto_path']);
                         }
                         $pendeta->delete(); // Rollback data pendeta
                     }
                     return redirect()->route('admin.pendeta.create')
                                 ->with('error', 'Terjadi kesalahan saat membuat akun user.')
                                 ->withInput();
                }
            } else {
                 // Ini seharusnya tidak terjadi jika create berhasil, tapi sebagai fallback
                 throw new \Exception("Gagal membuat record Pendeta.");
            }

            return redirect()->route('admin.pendeta.index')->with('success', 'Data Pendeta dan akun user berhasil ditambahkan.');

        } catch (\Exception $e) {
             Log::error('Gagal menyimpan data Pendeta: ' . $e->getMessage());
             // Hapus file foto jika sudah terupload tapi penyimpanan DB gagal
             if (isset($validatedData['foto_path']) && $validatedData['foto_path'] && Storage::disk('public')->exists($validatedData['foto_path'])) {
                 Storage::disk('public')->delete($validatedData['foto_path']);
             }
             return redirect()->route('admin.pendeta.create')
                                 ->with('error', 'Gagal menyimpan data Pendeta. Error: ' . $e->getMessage())
                                 ->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Pendeta $pendeta)
    {
         // Eager load relasi jika diperlukan
         $pendeta->load(['klasisPenempatan', 'jemaatPenempatan', 'user']);
         return view('admin.pendeta.show', compact('pendeta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pendeta $pendeta)
    {
        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        $jemaatOptions = Jemaat::orderBy('nama_jemaat')->pluck('nama_jemaat', 'id'); // Nanti bisa difilter
        return view('admin.pendeta.edit', compact('pendeta', 'klasisOptions', 'jemaatOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pendeta $pendeta)
    {
        // --- Validasi Data Pendeta (mirip store, tapi unique rule diubah) ---
         $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:pendeta,nik,' . $pendeta->id, // Abaikan ID saat ini
            'nipg' => 'required|string|max:50|unique:pendeta,nipg,' . $pendeta->id, // Abaikan ID saat ini
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'status_pernikahan' => 'nullable|string|max:50',
            'nama_pasangan' => 'nullable|string|max:255',
            'golongan_darah' => 'nullable|string|max:5',
            'alamat_domisili' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            // Validasi email unik di users, abaikan user yang terhubung ke pendeta ini
            'email' => 'nullable|string|email|max:255|unique:users,email,' . optional($pendeta->user)->id,
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

        // --- Handle File Upload Foto (Hapus yg lama jika ada baru) ---
        if ($request->hasFile('foto_path')) {
            // Hapus foto lama jika ada
            if ($pendeta->foto_path && Storage::disk('public')->exists($pendeta->foto_path)) {
                Storage::disk('public')->delete($pendeta->foto_path);
            }
            $validatedData['foto_path'] = $request->file('foto_path')->store('pendeta_photos', 'public');
        }

        // --- Update Data Pendeta ---
        try {
            $pendeta->update($validatedData);

            // --- Update Data User Terkait (jika ada perubahan relevan) ---
            if ($pendeta->user) {
                $userDataToUpdate = [];
                if (isset($validatedData['nama_lengkap']) && $pendeta->user->name !== $validatedData['nama_lengkap']) {
                    $userDataToUpdate['name'] = $validatedData['nama_lengkap'];
                }
                 if (isset($validatedData['email']) && $validatedData['email'] && $pendeta->user->email !== $validatedData['email']) {
                    $userDataToUpdate['email'] = $validatedData['email'];
                }
                // Update password jika NIPG berubah? (Hati-hati, mungkin tidak diinginkan)
                // if (isset($validatedData['nipg']) && !Hash::check($validatedData['nipg'], $pendeta->user->password)) {
                //     $userDataToUpdate['password'] = Hash::make($validatedData['nipg']);
                // }

                if (!empty($userDataToUpdate)) {
                    $pendeta->user()->update($userDataToUpdate);
                }
            }

            return redirect()->route('admin.pendeta.index')->with('success', 'Data Pendeta berhasil diperbarui.');

        } catch (\Exception $e) {
             Log::error('Gagal update data Pendeta ID: ' . $pendeta->id . '. Error: ' . $e->getMessage());
             // Jangan hapus file baru jika update gagal, biarkan saja
             return redirect()->route('admin.pendeta.edit', $pendeta->id)
                                 ->with('error', 'Gagal memperbarui data Pendeta. Error: ' . $e->getMessage())
                                 ->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pendeta $pendeta)
    {
         try {
             $fotoPath = $pendeta->foto_path;
             $user = $pendeta->user; // Ambil user terkait

             // Hapus data Pendeta
             // Relasi di DB (users.pendeta_id) di-set 'nullOnDelete'
             // jadi user TIDAK ikut terhapus, tapi pendeta_id-nya jadi null
             $pendeta->delete();

              // Opsional: Hapus user terkait jika diinginkan
              // if ($user) {
              //     $user->delete();
              // }

             // Hapus file foto dari storage
             if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                 Storage::disk('public')->delete($fotoPath);
             }

             return redirect()->route('admin.pendeta.index')->with('success', 'Data Pendeta berhasil dihapus.');

         } catch (\Exception $e) {
              Log::error('Gagal hapus data Pendeta ID: ' . $pendeta->id . '. Error: ' . $e->getMessage());
              return redirect()->route('admin.pendeta.index')
                                  ->with('error', 'Gagal menghapus data Pendeta. Error: ' . $e->getMessage());
         }
    }

     /**
      * Handle request export data Pendeta.
      */
     public function export(Request $request)
     {
         // Nanti tambahkan check permission/role
         // abort_if(!Auth::user()->can('export pendeta'), 403);

         // Cek jika request meminta template
          if ($request->has('template') && $request->template == 'yes') {
              // Buat instance Export hanya untuk header (template)
              $export = new PendetaExport(); // Ganti dengan PendetaExport
              $headings = $export->headings();
              // Buat collection kosong hanya berisi header
              $templateCollection = collect([$headings]);
              $fileName = 'template_import_pendeta.xlsx'; // Ganti nama file

              // Buat Class Export Anonim hanya untuk header
              $templateExport = new class($templateCollection) implements FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                  protected $collection;
                  protected $headingsData;
                  public function __construct($collection) { $this->collection = $collection; $this->headingsData = $collection->first();}
                  public function collection() { return collect([]); } // Kosongkan data
                  public function headings(): array { return $this->headingsData ?? []; }
              };
              return Excel::download($templateExport, $fileName);
          }

         // Export data normal
         try {
             $fileName = 'pendeta_gpi_papua_' . date('YmdHis') . '.xlsx';
             return Excel::download(new PendetaExport, $fileName); // Ganti dengan PendetaExport
         } catch (\Exception $e) {
              Log::error('Gagal export Pendeta: ' . $e->getMessage());
              return redirect()->route('admin.pendeta.index') // Ganti ke route index pendeta
                               ->with('error', 'Gagal mengekspor data Pendeta.');
         }
     }

     /**
      * Menampilkan halaman form import Pendeta.
      */
     public function showImportForm()
     {
         // Nanti tambahkan check permission/role
         // abort_if(!Auth::user()->can('import pendeta'), 403);
          return view('admin.pendeta.import'); // Arahkan ke view pendeta.import
     }


     /**
      * Handle request import data Pendeta.
      */
     public function import(Request $request)
     {
         // Nanti tambahkan check permission/role
         // abort_if(!Auth::user()->can('import pendeta'), 403);

         $request->validate([
             'import_file' => 'required|file|mimes:xlsx,xls,csv',
         ]);

         $file = $request->file('import_file');

         try {
             $import = new PendetaImport(); // Ganti dengan PendetaImport
             Excel::import($import, $file);

             $failures = $import->failures();
             if ($failures->isNotEmpty()) {
                 // Handle partial failure (tampilkan warning)
                 $errorRows = []; $errorCount = count($failures);
                 foreach ($failures as $failure) { $errorRows[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Nilai: ' . implode(', ', array_slice($failure->values(), 0, 3)) . '...)';}
                 $errorMessage = "Import selesai, namun terdapat {$errorCount} kesalahan validasi:\n" . implode("\n", $errorRows);
                 Log::warning($errorMessage);
                 if ($errorCount > 10) { $errorMessage = "Import selesai dengan {$errorCount} kesalahan validasi (10 error pertama):\n" . implode("\n", array_slice($errorRows, 0, 10)) . "\n... (cek log)";}
                 return redirect()->route('admin.pendeta.index')->with('warning', $errorMessage);
             }

             return redirect()->route('admin.pendeta.index')->with('success', 'Data Pendeta berhasil diimpor.');

         } catch (ValidationException $e) {
              // Handle validation exception
             $failures = $e->failures(); $errorRows = []; $errorCount = count($failures);
             foreach ($failures as $failure) {$errorRows[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Nilai: ' . implode(', ', array_slice($failure->values(), 0, 3)) . '...)';}
             $errorMessage = "Gagal import karena {$errorCount} kesalahan validasi:\n" . implode("\n", $errorRows);
             Log::error($errorMessage);
             if ($errorCount > 10) { $errorMessage = "Gagal import karena {$errorCount} kesalahan validasi (10 error pertama):\n" . implode("\n", array_slice($errorRows, 0, 10)) . "\n... (cek log)";}
             return redirect()->back()->with('error', $errorMessage);
         } catch (\Exception $e) {
              // Handle general exception
              Log::error('Gagal import Pendeta: ' . $e->getMessage());
              return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data Pendeta. Error: ' . $e->getMessage());
         }
     }
}