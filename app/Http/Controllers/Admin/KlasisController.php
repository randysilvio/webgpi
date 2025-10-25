<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klasis;
use App\Models\Pendeta; // Import model Pendeta
use Illuminate\Http\Request; // <-- Pastikan Request di-use
use Illuminate\Support\Facades\Storage; // Untuk menghapus file foto
use Illuminate\Support\Facades\Log; // Untuk logging
use Illuminate\Support\Facades\Auth; // Untuk scoping (jika login aktif)
use Maatwebsite\Excel\Facades\Excel; // <-- Tambahkan Facade Excel
use App\Exports\KlasisExport; // <-- Tambahkan Export Class
use App\Imports\KlasisImport; // <-- Tambahkan Import Class
use Maatwebsite\Excel\Validators\ValidationException; // <-- Tambahkan untuk error import
use Maatwebsite\Excel\Concerns\FromCollection; // <-- Tambahkan untuk template export

class KlasisController extends Controller
{
    // Middleware (akan aktif setelah login diimplementasikan)
    public function __construct()
    {
        // AKTIFKAN KEMBALI SETELAH LOGIN STABIL
        $this->middleware(['auth']);

        // Sesuaikan role/permission
        // Contoh: Hanya Super Admin & Bidang 3 yang bisa create/edit/delete/import/export
        $this->middleware('role:Super Admin|Admin Bidang 3')->except(['show', 'index']);
        // Contoh: Banyak role bisa melihat index & show
        $this->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis|Admin Jemaat|Pendeta')->only(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // <-- Parameter Request sudah ada
    {
        $query = Klasis::with(['ketuaMp', 'jemaat']) // Eager load ketua MPK & relasi jemaat (untuk count)
                    ->withCount('jemaat') // Menghitung jumlah jemaat terkait efisien
                    ->latest();

        // --- Scoping (Jika Diperlukan Nanti) ---
        // Biasanya Klasis tidak perlu di-scope, tapi jika misal ada Region/Wilayah:
        /*
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('Admin Wilayah')) {
                 $wilayahId = $user->wilayah_id; // Asumsi ada relasi wilayah
                 if ($wilayahId) {
                    $query->where('wilayah_id', $wilayahId);
                 } else { $query->whereRaw('1 = 0'); }
            }
        }
        */
        // --- Akhir Scoping ---

        // Fitur Search Sederhana (Sudah ada)
         if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                 $q->where('nama_klasis', 'like', $searchTerm)
                   ->orWhere('kode_klasis', 'like', $searchTerm)
                   ->orWhere('pusat_klasis', 'like', $searchTerm)
                   // Tambahkan pencarian berdasarkan nama ketua jika perlu
                   ->orWhereHas('ketuaMp', function($pendetaQuery) use ($searchTerm) {
                        $pendetaQuery->where('nama_lengkap', 'like', $searchTerm);
                   });
             });
         }

        $klasisData = $query->paginate(15)->appends($request->query()); // <-- appends sudah ada

        return view('admin.klasis.index', compact('klasisData')); // Kirim data ke view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil data Pendeta aktif untuk dropdown Ketua MPK
        $pendetaOptions = Pendeta::where('status_kepegawaian', 'Aktif') // Filter hanya yg aktif
                                ->orderBy('nama_lengkap')
                                ->pluck('nama_lengkap', 'id');
        return view('admin.klasis.create', compact('pendetaOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_klasis' => 'required|string|max:255',
            'kode_klasis' => 'nullable|string|max:50|unique:klasis,kode_klasis',
            'pusat_klasis' => 'nullable|string|max:100',
            'alamat_kantor' => 'nullable|string',
            'koordinat_gps' => 'nullable|string|max:100',
            'wilayah_pelayanan' => 'nullable|string',
            'tanggal_pembentukan' => 'nullable|date',
            'nomor_sk_pembentukan' => 'nullable|string|max:100',
            'klasis_induk' => 'nullable|string|max:100',
            'sejarah_singkat' => 'nullable|string',
            'ketua_mpk_pendeta_id' => 'nullable|exists:pendeta,id', // Validasi foreign key
            'telepon_kantor' => 'nullable|string|max:50',
            'email_klasis' => 'nullable|string|email|max:255|unique:klasis,email_klasis',
            'website_klasis' => 'nullable|url|max:255',
            'foto_kantor_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle File Upload Foto
        if ($request->hasFile('foto_kantor_path')) {
            $validatedData['foto_kantor_path'] = $request->file('foto_kantor_path')->store('klasis_photos', 'public');
        }

        try {
            Klasis::create($validatedData);
            return redirect()->route('admin.klasis.index')->with('success', 'Data Klasis berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data Klasis: ' . $e->getMessage());
            // Hapus file foto jika terupload tapi DB gagal
            if (isset($validatedData['foto_kantor_path']) && $validatedData['foto_kantor_path'] && Storage::disk('public')->exists($validatedData['foto_kantor_path'])) {
                Storage::disk('public')->delete($validatedData['foto_kantor_path']);
            }
            return redirect()->route('admin.klasis.create')
                             ->with('error', 'Gagal menyimpan data Klasis. Error DB: '. $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Klasis $klasis)
    {
         // --- Scoping (Jika diperlukan) ---
         // Contoh: Jika Admin Klasis hanya boleh lihat detail klasisnya sendiri
         /*
         if (Auth::check()) {
             $user = Auth::user();
             if ($user->hasRole('Admin Klasis') && $klasis->id != $user->klasis_id) {
                 abort(403, 'Anda tidak diizinkan melihat detail Klasis ini.');
             }
         }
         */
        // --- Akhir Scoping ---

        $klasis->load(['ketuaMp', 'jemaat']); // Eager load relasi Ketua dan Jemaat
        return view('admin.klasis.show', compact('klasis')); // Pastikan ada view show.blade.php
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Klasis $klasis)
    {
         // --- Scoping (Jika diperlukan) ---
         // Contoh: Jika Admin Klasis hanya boleh edit klasisnya sendiri
         /*
         if (Auth::check()) {
             $user = Auth::user();
             if ($user->hasRole('Admin Klasis') && $klasis->id != $user->klasis_id && !$user->hasRole('Super Admin')) { // Super Admin boleh edit semua
                 abort(403, 'Anda tidak diizinkan mengedit Klasis ini.');
             }
         }
         */
        // --- Akhir Scoping ---

        // Ambil data Pendeta aktif untuk dropdown
        $pendetaOptions = Pendeta::where('status_kepegawaian', 'Aktif')
                                ->orderBy('nama_lengkap')
                                ->pluck('nama_lengkap', 'id');
        return view('admin.klasis.edit', compact('klasis', 'pendetaOptions')); // Pastikan ada view edit.blade.php
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Klasis $klasis)
    {
        // --- Scoping (Jika diperlukan) ---
        /*
        if (Auth::check()) {
             $user = Auth::user();
             if ($user->hasRole('Admin Klasis') && $klasis->id != $user->klasis_id && !$user->hasRole('Super Admin')) {
                 abort(403, 'Anda tidak diizinkan mengupdate Klasis ini.');
             }
        }
        */
       // --- Akhir Scoping ---

        $validatedData = $request->validate([
            'nama_klasis' => 'required|string|max:255',
            'kode_klasis' => 'nullable|string|max:50|unique:klasis,kode_klasis,' . $klasis->id, // Abaikan ID saat ini
            'pusat_klasis' => 'nullable|string|max:100',
            'alamat_kantor' => 'nullable|string',
            'koordinat_gps' => 'nullable|string|max:100',
            'wilayah_pelayanan' => 'nullable|string',
            'tanggal_pembentukan' => 'nullable|date',
            'nomor_sk_pembentukan' => 'nullable|string|max:100',
            'klasis_induk' => 'nullable|string|max:100',
            'sejarah_singkat' => 'nullable|string',
            'ketua_mpk_pendeta_id' => 'nullable|exists:pendeta,id',
            'telepon_kantor' => 'nullable|string|max:50',
            'email_klasis' => 'nullable|string|email|max:255|unique:klasis,email_klasis,' . $klasis->id, // Abaikan ID saat ini
            'website_klasis' => 'nullable|url|max:255',
            'foto_kantor_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle File Upload Foto (Hapus yg lama jika ada baru)
        if ($request->hasFile('foto_kantor_path')) {
            if ($klasis->foto_kantor_path && Storage::disk('public')->exists($klasis->foto_kantor_path)) {
                Storage::disk('public')->delete($klasis->foto_kantor_path);
            }
            $validatedData['foto_kantor_path'] = $request->file('foto_kantor_path')->store('klasis_photos', 'public');
        }

        try {
            $klasis->update($validatedData);
            return redirect()->route('admin.klasis.index')->with('success', 'Data Klasis berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal update data Klasis ID: ' . $klasis->id . '. Error: ' . $e->getMessage());
            return redirect()->route('admin.klasis.edit', $klasis->id)
                             ->with('error', 'Gagal memperbarui data Klasis. Error DB: '. $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Klasis $klasis)
    {
        // --- Scoping & Hak Akses (Sudah diatur di __construct) ---
        try {
            $fotoPath = $klasis->foto_kantor_path;
            $namaKlasis = $klasis->nama_klasis;

            // Hapus data Klasis (Relasi Jemaat akan terhapus jika cascadeOnDelete)
            $klasis->delete();

            // Hapus file foto dari storage
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }

            return redirect()->route('admin.klasis.index')->with('success', 'Data Klasis (' . $namaKlasis . ') berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Gagal hapus data Klasis ID: ' . $klasis->id . '. Error: ' . $e->getMessage());
            // Tangani error jika ada foreign key constraint (misal dari Jemaat jika tidak cascade)
            if (str_contains($e->getMessage(), 'Integrity constraint violation')) {
                 return redirect()->route('admin.klasis.index')
                                 ->with('error', 'Gagal menghapus Klasis: Masih ada data Jemaat atau referensi lain yang terkait. Hapus data terkait terlebih dahulu.');
            }
             return redirect()->route('admin.klasis.index')
                                 ->with('error', 'Gagal menghapus data Klasis. Error DB: ' . $e->getMessage());
        }
    }

    /**
     * Handle request export data Klasis.
     */
    public function export(Request $request)
    {
        // Cek jika request meminta template
         if ($request->has('template') && $request->template == 'yes') {
             $export = new KlasisExport();
             $headings = $export->headings();
             $templateCollection = collect([$headings]);
             $fileName = 'template_import_klasis.xlsx';

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
            $fileName = 'klasis_gpi_papua_' . date('YmdHis') . '.xlsx';
            // 👇👇👇 Pass search term ke Export Class 👇👇👇
            $export = new KlasisExport($request->query('search'));
            return Excel::download($export, $fileName);
        } catch (\Exception $e) {
             Log::error('Gagal export Klasis: ' . $e->getMessage());
             return redirect()->route('admin.klasis.index')
                              ->with('error', 'Gagal mengekspor data Klasis.');
        }
    }

    /**
     * Menampilkan halaman form import Klasis.
     */
    public function showImportForm()
    {
         return view('admin.klasis.import'); // Buat view import.blade.php
    }

    /**
     * Handle request import data Klasis.
     */
    public function import(Request $request)
    {
        $request->validate(['import_file' => 'required|file|mimes:xlsx,xls,csv']);
        $file = $request->file('import_file');

        try {
            $import = new KlasisImport();
            Excel::import($import, $file);

            $failures = $import->failures();
            if ($failures->isNotEmpty()) {
                // Handle partial failure (tampilkan warning)
                $errorRows = []; $errorCount = count($failures);
                foreach ($failures as $failure) { $errorRows[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Nilai: ' . implode(', ', array_slice($failure->values(), 0, 3)) . '...)';}
                $errorMessage = "Import selesai, namun terdapat {$errorCount} kesalahan validasi:\n" . implode("\n", $errorRows);
                Log::warning($errorMessage);
                if ($errorCount > 10) { $errorMessage = "Import selesai dengan {$errorCount} kesalahan validasi (10 error pertama):\n" . implode("\n", array_slice($errorRows, 0, 10)) . "\n... (cek log)";}
                return redirect()->route('admin.klasis.index')->with('warning', $errorMessage);
            }

            return redirect()->route('admin.klasis.index')->with('success', 'Data Klasis berhasil diimpor.');

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
             Log::error('Gagal import Klasis: ' . $e->getMessage());
             return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data Klasis. Error: ' . $e->getMessage());
        }
    }

} // Akhir Class