<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jemaat;
use App\Models\Klasis; // Import Klasis model
use Illuminate\Http\Request; // <-- Pastikan Request di-use
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // <-- Pastikan Auth di-use
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JemaatExport;
use App\Imports\JemaatImport;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\FromCollection;

class JemaatController extends Controller
{
    // Middleware (akan aktif setelah login diimplementasikan)
    public function __construct()
    {
        // 👇👇👇 Middleware auth & role/permission bisa ditaruh di sini atau di routes/web.php 👇👇👇
        $this->middleware(['auth']); // Pastikan user login

        // Contoh: Middleware permission (perlu didefinisikan di Seeder)
        $this->middleware('can:view jemaat')->only(['index', 'show']);
        $this->middleware('can:create jemaat')->only(['create', 'store']);
        $this->middleware('can:edit jemaat')->only(['edit', 'update']);
        $this->middleware('can:delete jemaat')->only(['destroy']);
        $this->middleware('can:import jemaat')->only(['showImportForm', 'import']);
        $this->middleware('can:export jemaat')->only(['export']);

        // Anda bisa juga menggunakan middleware role jika lebih sederhana
        // $this->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis')->only(['create', 'store', 'destroy']);
        // $this->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis|Admin Jemaat')->only(['edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // <-- Parameter Request ditambahkan
    {
        $query = Jemaat::with('klasis')->latest(); // Eager load klasis
        $user = Auth::user(); // Ambil user yang login

        // --- Scoping berdasarkan Role (Diaktifkan) ---
        // 👇👇👇 Blok ini diaktifkan 👇👇👇
        if (Auth::check()) { // Cek jika user login
            // $user = Auth::user(); // Sudah diambil di atas
            if ($user->hasRole('Admin Klasis')) {
                $klasisId = $user->klasis_id; // Ambil ID Klasis dari user
                if ($klasisId) {
                    $query->where('klasis_id', $klasisId); // Filter berdasarkan klasis_id user
                } else {
                    Log::warning('Admin Klasis ' . $user->id . ' tidak terhubung ke Klasis manapun.');
                    $query->whereRaw('1 = 0'); // Jangan tampilkan apa pun jika ID Klasis tidak ada
                }
            } elseif ($user->hasRole('Admin Jemaat')) {
                $jemaatId = $user->jemaat_id; // Ambil ID Jemaat dari user
                 if ($jemaatId) {
                     $query->where('id', $jemaatId); // Hanya tampilkan jemaatnya sendiri
                 } else {
                     Log::warning('Admin Jemaat ' . $user->id . ' tidak terhubung ke Jemaat manapun.');
                     $query->whereRaw('1 = 0'); // Jangan tampilkan apa pun
                 }
            }
            // Super Admin & Admin Bidang 3 (atau role lain yg relevan) bisa lihat semua (tidak perlu filter)
        } else {
             // Seharusnya tidak bisa sampai sini karena ada middleware('auth')
             abort(401); // Unauthorized
        }
        // --- Akhir Scoping ---

        // --- Filter berdasarkan Request ---
        // Filter by Klasis (hanya relevan jika user bisa lihat > 1 klasis)
        // 👇👇👇 Logika Filter Klasis ditambahkan 👇👇👇
        if ($request->filled('klasis_id')) {
            // Validasi sederhana, pastikan hanya angka
            $klasisFilterId = filter_var($request->klasis_id, FILTER_VALIDATE_INT);
            if ($klasisFilterId) {
                 // Jika user adalah Admin Klasis, pastikan filter ID = ID klasisnya
                 if ($user->hasRole('Admin Klasis') && $user->klasis_id != $klasisFilterId) {
                     // Abaikan filter jika tidak sesuai scope
                     Log::warning('Admin Klasis '.$user->id.' mencoba filter klasis lain: '.$klasisFilterId);
                 } else {
                     $query->where('klasis_id', $klasisFilterId);
                 }
            }
        }
        // --- Akhir Filter Klasis ---

         // Fitur Search Sederhana
         if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                 $q->where('nama_jemaat', 'like', $searchTerm)
                   ->orWhere('kode_jemaat', 'like', $searchTerm);
                 // Bisa tambahkan search by nama klasis jika perlu (pakai whereHas)
                 // ->orWhereHas('klasis', function($klasisQuery) use ($searchTerm) {
                 //     $klasisQuery->where('nama_klasis', 'like', $searchTerm);
                 // });
             });
         }

        // --- Ambil Opsi untuk Filter Dropdown ---
        // 👇👇👇 Ambil daftar Klasis untuk dropdown filter ditambahkan 👇👇👇
        $klasisOptionsQuery = Klasis::orderBy('nama_klasis');
        // Jika user adalah Admin Klasis, hanya tampilkan klasisnya di filter
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $klasisOptionsQuery->where('id', $user->klasis_id);
        }
        $klasisFilterOptions = $klasisOptionsQuery->pluck('nama_klasis', 'id');
        // --- Akhir Ambil Opsi Filter ---


        // Ambil data Jemaat dengan pagination, sertakan query string (filter & search)
        $jemaatData = $query->paginate(15)->appends($request->query()); // <-- appends ditambahkan

        // Kirim data ke view
        // 👇👇👇 Kirim $klasisFilterOptions dan nilai filter aktif ke view 👇👇👇
        return view('admin.jemaat.index', compact('jemaatData', 'klasisFilterOptions', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $klasisOptionsQuery = Klasis::orderBy('nama_klasis');

        // --- Scoping Pilihan Klasis (Diaktifkan) ---
        // 👇👇👇 Blok ini diaktifkan 👇👇👇
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('Admin Klasis')) {
                 $klasisId = $user->klasis_id;
                 if ($klasisId) {
                    // Hanya tampilkan klasisnya sendiri di dropdown
                    $klasisOptionsQuery->where('id', $klasisId);
                 } else {
                      // Jika admin klasis tidak terhubung, arahkan & beri error
                      return redirect()->route('admin.dashboard')->with('error', 'Akun Anda tidak terhubung ke Klasis manapun.');
                 }
            }
             // Admin Jemaat tidak boleh create Jemaat, sudah dicegah oleh middleware/permission
             // if ($user->hasRole('Admin Jemaat')) {
             //      abort(403, 'Admin Jemaat tidak dapat membuat Jemaat baru.');
             // }
        }
        // --- Akhir Scoping Pilihan ---

        $klasisOptions = $klasisOptionsQuery->pluck('nama_klasis', 'id');

        // Handle jika tidak ada Klasis yang bisa dipilih (misal Admin Klasis baru)
        if ($klasisOptions->isEmpty()) {
            // Jika auth aktif dan user bukan Super Admin/Bidang 3
             if (Auth::check() && !$user->hasAnyRole(['Super Admin', 'Admin Bidang 3'])) {
                 return redirect()->back()->with('error', 'Tidak ada Klasis tersedia dalam lingkup Anda untuk ditambahkan Jemaat.');
             }
             // Jika Super Admin/Bidang 3, beri peringatan saja
             Log::warning('Tidak ada data Klasis ditemukan untuk pilihan form tambah Jemaat.');
             // return redirect()->back()->with('warning', 'Data Klasis belum ada. Silakan tambahkan Klasis terlebih dahulu.');
        }

        return view('admin.jemaat.create', compact('klasisOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_jemaat' => 'required|string|max:255',
            'kode_jemaat' => 'nullable|string|max:50|unique:jemaat,kode_jemaat',
            'klasis_id' => 'required|exists:klasis,id',
            'alamat_gereja' => 'nullable|string',
            'koordinat_gps' => 'nullable|string|max:100',
            'tanggal_berdiri' => 'nullable|date',
            'status_jemaat' => 'required|in:Mandiri,Bakal Jemaat,Pos Pelayanan',
            'jenis_jemaat' => 'required|in:Umum,Kategorial',
            'jumlah_kk' => 'nullable|integer|min:0',
            'jumlah_total_jiwa' => 'nullable|integer|min:0',
            'tanggal_update_statistik' => 'nullable|date',
            'telepon_kantor' => 'nullable|string|max:50',
            'email_jemaat' => 'nullable|string|email|max:255|unique:jemaat,email_jemaat',
            'foto_gereja_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // ... (tambahkan validasi untuk field lain jika perlu) ...
        ]);

        // --- Security Check (Diaktifkan) ---
        // 👇👇👇 Blok ini diaktifkan 👇👇👇
        if (Auth::check()) {
            $user = Auth::user();
            // Jika user adalah Admin Klasis
            if ($user->hasRole('Admin Klasis')) {
                $adminKlasisId = $user->klasis_id;
                // Pastikan klasis_id yang diinput sama dengan klasis_id admin
                if (!$adminKlasisId || $adminKlasisId != $validatedData['klasis_id']) {
                    return redirect()->back()
                                     ->with('error', 'Anda tidak diizinkan menambah Jemaat untuk Klasis lain.')
                                     ->withInput();
                }
            }
             // Admin Jemaat sudah diblokir oleh middleware/permission 'create jemaat'
             // if ($user->hasRole('Admin Jemaat')) { abort(403); }
        }
        // --- Akhir Security Check ---

        // Handle File Upload Foto
        if ($request->hasFile('foto_gereja_path')) {
            $validatedData['foto_gereja_path'] = $request->file('foto_gereja_path')->store('jemaat_photos', 'public');
        }

        try {
            Jemaat::create($validatedData);
            return redirect()->route('admin.jemaat.index')->with('success', 'Data Jemaat berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data Jemaat: ' . $e->getMessage());
            if (isset($validatedData['foto_gereja_path']) && $validatedData['foto_gereja_path'] && Storage::disk('public')->exists($validatedData['foto_gereja_path'])) {
                Storage::disk('public')->delete($validatedData['foto_gereja_path']);
            }
            return redirect()->route('admin.jemaat.create')
                             ->with('error', 'Gagal menyimpan data Jemaat. Error DB: ' . $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Jemaat $jemaat)
    {
         // --- Scoping (Diaktifkan) ---
         // 👇👇👇 Blok ini diaktifkan 👇👇👇
         if (Auth::check()) {
            $user = Auth::user();
            // Jika Admin Klasis, cek apakah Jemaat ini ada di Klasisnya
            if ($user->hasRole('Admin Klasis') && $jemaat->klasis_id != $user->klasis_id) {
                abort(403, 'Anda tidak diizinkan melihat detail Jemaat ini.');
            }
             // Jika Admin Jemaat, cek apakah ini Jemaatnya
             if ($user->hasRole('Admin Jemaat') && $jemaat->id != $user->jemaat_id) {
                 abort(403, 'Anda hanya bisa melihat detail Jemaat Anda.');
            }
            // Role lain yang punya permission 'view jemaat' bisa lihat
        } else {
             abort(401);
        }
        // --- Akhir Scoping ---

        $jemaat->load(['klasis', 'anggotaJemaat', 'pendetaDitempatkan']); // Eager load relasi
        return view('admin.jemaat.show', compact('jemaat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jemaat $jemaat)
    {
        // --- Scoping (Diaktifkan) ---
        // 👇👇👇 Blok ini diaktifkan 👇👇👇
        if (Auth::check()) {
            $user = Auth::user();
            // Cek role/permission sudah dilakukan di __construct

            // Cek scope untuk Admin Klasis dan Admin Jemaat
            if ($user->hasRole('Admin Klasis') && $jemaat->klasis_id != $user->klasis_id) {
                 abort(403, 'Anda tidak diizinkan mengedit Jemaat di Klasis lain.');
            }
             if ($user->hasRole('Admin Jemaat') && $jemaat->id != $user->jemaat_id) {
                  abort(403, 'Anda hanya bisa mengedit Jemaat Anda.');
            }
        } else {
             abort(401);
        }
        // --- Akhir Scoping ---

        $klasisOptionsQuery = Klasis::orderBy('nama_klasis');
        // --- Scoping Pilihan Klasis (Diaktifkan) ---
        // 👇👇👇 Blok ini diaktifkan 👇👇👇
        if (Auth::check()) {
             $user = Auth::user();
             // Jika Admin Klasis atau Jemaat, batasi pilihan Klasis hanya ke Klasis mereka
             if ($user->hasRole(['Admin Klasis', 'Admin Jemaat'])) {
                 $klasisId = $user->klasis_id ?? $jemaat->klasis_id; // Ambil ID Klasis dari user atau Jemaat yg diedit
                 if ($klasisId) {
                     $klasisOptionsQuery->where('id', $klasisId);
                 } else {
                     // Jika tidak terhubung ke klasis, mungkin error? Kosongkan pilihan.
                     $klasisOptionsQuery->whereRaw('1 = 0');
                 }
             }
             // Super Admin & Admin Bidang 3 bisa memilih semua Klasis
        }
        // --- Akhir Scoping Pilihan ---
        $klasisOptions = $klasisOptionsQuery->pluck('nama_klasis', 'id');

        return view('admin.jemaat.edit', compact('jemaat', 'klasisOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jemaat $jemaat)
    {
        // --- Scoping (Diaktifkan) ---
        // 👇👇👇 Blok ini diaktifkan 👇👇👇
        if (Auth::check()) {
            $user = Auth::user();
             // Cek role/permission sudah di __construct

             // Cek scope untuk Admin Klasis dan Admin Jemaat
            if ($user->hasRole('Admin Klasis') && $jemaat->klasis_id != $user->klasis_id) {
                  abort(403, 'Anda tidak diizinkan mengupdate Jemaat di Klasis lain.');
            }
            if ($user->hasRole('Admin Jemaat') && $jemaat->id != $user->jemaat_id) {
                   abort(403, 'Anda hanya bisa mengupdate Jemaat Anda.');
            }
        } else {
             abort(401);
        }
        // --- Akhir Scoping ---

        $validatedData = $request->validate([
            'nama_jemaat' => 'required|string|max:255',
            'kode_jemaat' => 'nullable|string|max:50|unique:jemaat,kode_jemaat,' . $jemaat->id,
            'klasis_id' => 'required|exists:klasis,id',
            'alamat_gereja' => 'nullable|string',
            'koordinat_gps' => 'nullable|string|max:100',
            'tanggal_berdiri' => 'nullable|date',
            'status_jemaat' => 'required|in:Mandiri,Bakal Jemaat,Pos Pelayanan',
            'jenis_jemaat' => 'required|in:Umum,Kategorial',
            'jumlah_kk' => 'nullable|integer|min:0',
            'jumlah_total_jiwa' => 'nullable|integer|min:0',
            'tanggal_update_statistik' => 'nullable|date',
            'telepon_kantor' => 'nullable|string|max:50',
            'email_jemaat' => 'nullable|string|email|max:255|unique:jemaat,email_jemaat,' . $jemaat->id,
            'foto_gereja_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
             // ... (tambahkan validasi untuk field lain jika perlu) ...
        ]);

        // --- Security Check Pindah Klasis (Diaktifkan) ---
         // 👇👇👇 Blok ini diaktifkan 👇👇👇
         if (Auth::check()) {
             $user = Auth::user();
             // Jika klasis_id yang diinput berbeda dengan klasis_id jemaat saat ini
             // DAN user yang melakukan BUKAN Super Admin atau Admin Bidang 3
             if ($validatedData['klasis_id'] != $jemaat->klasis_id && !$user->hasAnyRole(['Super Admin', 'Admin Bidang 3'])) {
                  return redirect()->back()
                                      ->with('error', 'Anda tidak diizinkan memindahkan Jemaat ke Klasis lain.')
                                      ->withInput();
             }
         }
         // --- Akhir Security Check ---

        // Handle File Upload Foto (Hapus yg lama jika ada baru)
        if ($request->hasFile('foto_gereja_path')) {
            if ($jemaat->foto_gereja_path && Storage::disk('public')->exists($jemaat->foto_gereja_path)) {
                Storage::disk('public')->delete($jemaat->foto_gereja_path);
            }
            $validatedData['foto_gereja_path'] = $request->file('foto_gereja_path')->store('jemaat_photos', 'public');
        }

        try {
            $jemaat->update($validatedData);
            return redirect()->route('admin.jemaat.index')->with('success', 'Data Jemaat berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal update data Jemaat ID: ' . $jemaat->id . '. Error: ' . $e->getMessage());
            return redirect()->route('admin.jemaat.edit', $jemaat->id)
                             ->with('error', 'Gagal memperbarui data Jemaat. Error DB: '. $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jemaat $jemaat)
    {
         // --- Scoping & Hak Akses (Diaktifkan) ---
         // 👇👇👇 Blok ini diaktifkan 👇👇👇
         if (Auth::check()) {
             $user = Auth::user();
             // Cek role/permission sudah di __construct

             // Cek scope untuk Admin Klasis
             if ($user->hasRole('Admin Klasis') && $jemaat->klasis_id != $user->klasis_id) {
                   abort(403, 'Anda tidak diizinkan menghapus Jemaat di Klasis lain.');
             }
              // Admin Jemaat tidak boleh hapus Jemaat, sudah dicegah oleh permission 'delete jemaat'
              // if ($user->hasRole('Admin Jemaat')) { abort(403); }
         } else {
              abort(401);
         }
        // --- Akhir Scoping ---

         try {
             $fotoPath = $jemaat->foto_gereja_path;
             $namaJemaat = $jemaat->nama_jemaat;

             // Hapus data Jemaat (Relasi Anggota akan terhapus jika cascadeOnDelete di migrasi)
             $jemaat->delete();

             // Hapus file foto dari storage
             if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                 Storage::disk('public')->delete($fotoPath);
             }

             return redirect()->route('admin.jemaat.index')->with('success', 'Data Jemaat (' . $namaJemaat . ') berhasil dihapus.');

         } catch (\Exception $e) {
             Log::error('Gagal hapus data Jemaat ID: ' . $jemaat->id . '. Error: ' . $e->getMessage());
             if (str_contains($e->getMessage(), 'Integrity constraint violation')) {
                 return redirect()->route('admin.jemaat.index')
                                  ->with('error', 'Gagal menghapus Jemaat: Masih ada data Anggota Jemaat atau data lain yang terkait. Hapus data terkait terlebih dahulu.');
             }
             return redirect()->route('admin.jemaat.index')
                                  ->with('error', 'Gagal menghapus data Jemaat. Error DB: ' . $e->getMessage());
         }
    }

    /**
     * Handle request export data Jemaat.
     */
    public function export(Request $request)
    {
        // Permission check sudah di __construct

        // Cek jika request meminta template
         if ($request->has('template') && $request->template == 'yes') {
             $export = new JemaatExport();
             $headings = $export->headings();
             $templateCollection = collect([$headings]);
             $fileName = 'template_import_jemaat.xlsx';

             $templateExport = new class($templateCollection) implements FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                 protected $collection; protected $headingsData;
                 public function __construct($collection) { $this->collection = $collection; $this->headingsData = $collection->first();}
                 public function collection() { return collect([]); }
                 public function headings(): array { return $this->headingsData ?? []; }
             };
             return Excel::download($templateExport, $fileName);
         }

        // Export data normal
        try {
            $fileName = 'jemaat_gpi_papua_' . date('YmdHis') . '.xlsx';
            // TODO: Tambahkan scoping data export untuk Admin Klasis jika perlu
            // $klasisId = Auth::user()->hasRole('Admin Klasis') ? Auth::user()->klasis_id : null;
            // return Excel::download(new JemaatExport($klasisId), $fileName);
            return Excel::download(new JemaatExport, $fileName); // Sementara export semua
        } catch (\Exception $e) {
             Log::error('Gagal export Jemaat: ' . $e->getMessage());
             return redirect()->route('admin.jemaat.index')
                              ->with('error', 'Gagal mengekspor data Jemaat.');
        }
    }

    /**
     * Menampilkan halaman form import Jemaat.
     */
    public function showImportForm()
    {
         // Permission check sudah di __construct
         return view('admin.jemaat.import'); // Buat view import.blade.php
    }

    /**
     * Handle request import data Jemaat.
     */
    public function import(Request $request)
    {
        // Permission check sudah di __construct

        $request->validate(['import_file' => 'required|file|mimes:xlsx,xls,csv']);
        $file = $request->file('import_file');

        try {
            // TODO: Tambahkan scoping untuk Admin Klasis jika perlu saat import
            // $klasisId = Auth::user()->hasRole('Admin Klasis') ? Auth::user()->klasis_id : null;
            // $import = new JemaatImport($klasisId);
            $import = new JemaatImport(); // Sementara izinkan import ke semua klasis
            Excel::import($import, $file);

            $failures = $import->failures();
            if ($failures->isNotEmpty()) {
                // Handle partial failure (tampilkan warning)
                $errorRows = []; $errorCount = count($failures);
                foreach ($failures as $failure) { $errorRows[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Nilai: ' . implode(', ', array_slice($failure->values(), 0, 3)) . '...)';}
                $errorMessage = "Import selesai, namun terdapat {$errorCount} kesalahan validasi:\n" . implode("\n", $errorRows);
                Log::warning($errorMessage);
                if ($errorCount > 10) { $errorMessage = "Import selesai dengan {$errorCount} kesalahan validasi (10 error pertama):\n" . implode("\n", array_slice($errorRows, 0, 10)) . "\n... (cek log)";}
                return redirect()->route('admin.jemaat.index')->with('warning', $errorMessage);
            }

            return redirect()->route('admin.jemaat.index')->with('success', 'Data Jemaat berhasil diimpor.');

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
             Log::error('Gagal import Jemaat: ' . $e->getMessage());
             return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data Jemaat. Error: ' . $e->getMessage());
        }
    }

} // Akhir Class