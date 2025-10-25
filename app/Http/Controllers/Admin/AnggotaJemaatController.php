<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaJemaat;
use App\Models\Jemaat; // Import Jemaat model
use App\Models\Klasis; // <-- Import Klasis model
use Illuminate\Http\Request; // <-- Pastikan Request di-use
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnggotaJemaatExport;
use App\Imports\AnggotaJemaatImport;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\FromCollection;

class AnggotaJemaatController extends Controller
{
    // Middleware
    public function __construct()
    {
         $this->middleware(['auth']); // Semua harus login

         // Sesuaikan permission berdasarkan RolesAndPermissionsSeeder
         $this->middleware('can:view anggota jemaat')->only(['index', 'show']);
         $this->middleware('can:create anggota jemaat')->only(['create', 'store']);
         $this->middleware('can:edit anggota jemaat')->only(['edit', 'update']);
         $this->middleware('can:delete anggota jemaat')->only(['destroy']);
         $this->middleware('can:import anggota jemaat')->only(['showImportForm', 'import']);
         $this->middleware('can:export anggota jemaat')->only(['export']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // <-- Tambahkan Request $request
    {
        $query = AnggotaJemaat::with(['jemaat', 'jemaat.klasis'])->latest(); // Eager load jemaat & klasisnya
        $user = Auth::user(); // Ambil user yang login

        // Variabel untuk menyimpan opsi filter
        $klasisFilterOptions = collect();
        $jemaatFilterOptions = collect();

        // --- Scoping berdasarkan Role ---
        if ($user->hasRole('Admin Jemaat')) {
            $jemaatId = $user->jemaat_id;
            if ($jemaatId) {
                $query->where('jemaat_id', $jemaatId);
            } else {
                Log::warning('Admin Jemaat ' . $user->id . ' tidak terhubung ke Jemaat manapun.');
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->hasRole('Admin Klasis')) {
            $klasisId = $user->klasis_id;
            if ($klasisId) {
                // Ambil semua jemaat_id dalam klasis ini
                $jemaatIds = Jemaat::where('klasis_id', $klasisId)->pluck('id');
                if ($jemaatIds->isNotEmpty()) {
                     $query->whereIn('jemaat_id', $jemaatIds);
                     // Ambil opsi Jemaat HANYA dari klasis ini untuk filter
                     $jemaatFilterOptions = Jemaat::where('klasis_id', $klasisId)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
                } else {
                     Log::warning('Admin Klasis ' . $user->id . ' scope index, tapi Klasis ' . $klasisId . ' tidak punya Jemaat.');
                     $query->whereRaw('1 = 0');
                }
                // Ambil opsi Klasis HANYA klasis ini untuk filter (meskipun hanya 1)
                $klasisFilterOptions = Klasis::where('id', $klasisId)->pluck('nama_klasis', 'id');

            } else {
                 Log::warning('Admin Klasis ' . $user->id . ' tidak terhubung ke Klasis manapun.');
                $query->whereRaw('1 = 0');
            }
        } else { // Super Admin, Admin Bidang 3, dll.
             // Ambil semua Klasis untuk filter
             $klasisFilterOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
             // Ambil semua Jemaat untuk filter (atau filter berdasarkan klasis yg dipilih)
             // 👇👇👇 Logika Opsi Jemaat berdasarkan Filter Klasis 👇👇👇
             if ($request->filled('klasis_id')) {
                 $klasisFilterId = filter_var($request->klasis_id, FILTER_VALIDATE_INT);
                 if ($klasisFilterId) {
                      $jemaatFilterOptions = Jemaat::where('klasis_id', $klasisFilterId)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
                 } else {
                      // Jika filter klasis_id tidak valid, ambil semua jemaat
                      $jemaatFilterOptions = Jemaat::orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
                 }
             } else {
                  // Jika tidak ada filter klasis, ambil semua jemaat
                  $jemaatFilterOptions = Jemaat::orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
             }
        }
        // --- Akhir Scoping & Opsi Filter ---


        // --- Filter berdasarkan Request ---
        // Filter by Klasis
        if ($request->filled('klasis_id')) {
            $klasisFilterId = filter_var($request->klasis_id, FILTER_VALIDATE_INT);
            if ($klasisFilterId) {
                // Scoping sudah ditangani di atas, di sini hanya apply filter
                // Perlu filter via relasi jemaat
                $query->whereHas('jemaat', function ($q) use ($klasisFilterId) {
                    $q->where('klasis_id', $klasisFilterId);
                });
            }
        }
        // Filter by Jemaat
        if ($request->filled('jemaat_id')) {
             $jemaatFilterId = filter_var($request->jemaat_id, FILTER_VALIDATE_INT);
             if ($jemaatFilterId) {
                  // Scoping sudah ditangani di atas, di sini hanya apply filter
                  $query->where('jemaat_id', $jemaatFilterId);
             }
        }
        // --- Akhir Filter ---

         // Fitur Search Sederhana
         if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                 $q->where('nama_lengkap', 'like', $searchTerm)
                   ->orWhere('nik', 'like', $searchTerm)
                   ->orWhere('nomor_buku_induk', 'like', $searchTerm);
             });
         }

        // Ambil data Anggota dengan pagination
        $anggotaJemaatData = $query->paginate(20)->appends($request->query());

        // Kirim data ke view
        // 👇👇👇 Kirim Opsi Filter & Request ke view 👇👇👇
        return view('admin.anggota_jemaat.index', compact('anggotaJemaatData', 'klasisFilterOptions', 'jemaatFilterOptions', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $jemaatOptionsQuery = Jemaat::orderBy('nama_jemaat');
         $user = Auth::user();

         if ($user->hasRole('Admin Jemaat')) {
             $jemaatId = $user->jemaat_id;
             if ($jemaatId) $jemaatOptionsQuery->where('id', $jemaatId);
             else { Log::error('Admin Jemaat ' . $user->id . ' create anggota tanpa jemaat_id.'); return redirect()->route('admin.dashboard')->with('error', 'Akun Anda tidak terhubung ke Jemaat.'); }
         } elseif ($user->hasRole('Admin Klasis')) {
              $klasisId = $user->klasis_id;
              if ($klasisId) $jemaatOptionsQuery->where('klasis_id', $klasisId);
              else { Log::error('Admin Klasis ' . $user->id . ' create anggota tanpa klasis_id.'); return redirect()->route('admin.dashboard')->with('error', 'Akun Anda tidak terhubung ke Klasis.'); }
         }

        $jemaatOptions = $jemaatOptionsQuery->pluck('nama_jemaat', 'id');

        if ($jemaatOptions->isEmpty()) {
             if (Auth::check() && !$user->hasAnyRole(['Super Admin', 'Admin Bidang 3'])) {
                 return redirect()->back()->with('error', 'Tidak ada Jemaat tersedia dalam lingkup Anda.');
             }
             Log::warning('Tidak ada data Jemaat untuk pilihan form tambah anggota.');
        }
        return view('admin.anggota_jemaat.create', compact('jemaatOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // Validasi semua field yang ada di form create Anda
         $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:anggota_jemaat,nik',
            'jemaat_id' => 'required|exists:jemaat,id',
            'nomor_buku_induk' => 'nullable|string|max:50|unique:anggota_jemaat,nomor_buku_induk',
            // ... (validasi field lainnya) ...
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'golongan_darah' => 'nullable|string|max:10',
            'status_pernikahan' => 'nullable|string|max:50',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'pekerjaan_utama' => 'nullable|string|max:100',
            'alamat_lengkap' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:255',
            'sektor_pelayanan' => 'nullable|string|max:50',
            'unit_pelayanan' => 'nullable|string|max:50',
            'tanggal_baptis' => 'nullable|date',
            'tempat_baptis' => 'nullable|string|max:150',
            'tanggal_sidi' => 'nullable|date',
            'tempat_sidi' => 'nullable|string|max:150',
            'tanggal_masuk_jemaat' => 'nullable|date',
            'status_keanggotaan' => 'required|in:Aktif,Tidak Aktif,Pindah,Meninggal',
            'asal_gereja_sebelumnya' => 'nullable|string|max:150',
            'nomor_atestasi' => 'nullable|string|max:50',
            'status_pekerjaan_kk' => 'nullable|string|max:100',
            'status_kepemilikan_rumah' => 'nullable|string|max:100',
            'perkiraan_pendapatan_keluarga' => 'nullable|string|max:50',
        ]);

        // --- Security Check (Diaktifkan) ---
        // 👇👇👇 Blok ini diaktifkan 👇👇👇
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('Admin Jemaat')) {
                $adminJemaatId = $user->jemaat_id;
                // Pastikan admin jemaat hanya bisa input ke jemaatnya sendiri
                if (!$adminJemaatId || $adminJemaatId != $validatedData['jemaat_id']) {
                    return redirect()->back()->with('error', 'Anda hanya bisa menambah anggota untuk Jemaat Anda.')->withInput();
                }
            } elseif ($user->hasRole('Admin Klasis')) {
                $adminKlasisId = $user->klasis_id;
                // Pastikan jemaat yang dipilih ada dalam klasis admin
                $jemaatDipilih = Jemaat::find($validatedData['jemaat_id']);
                if (!$adminKlasisId || !$jemaatDipilih || $jemaatDipilih->klasis_id != $adminKlasisId) {
                    return redirect()->back()->with('error', 'Anda hanya bisa menambah anggota untuk Jemaat dalam Klasis Anda.')->withInput();
                }
            }
            // Super Admin & Admin Bidang 3 bisa menambah ke Jemaat mana saja
        }
        // --- Akhir Security Check ---


        try {
            AnggotaJemaat::create($validatedData);

            // Redirect kembali ke form create untuk input cepat berikutnya
             return redirect()->route('admin.anggota-jemaat.create')
                             ->with('success', 'Anggota Jemaat (' . $validatedData['nama_lengkap'] . ') berhasil ditambahkan. Silakan tambah lagi jika ada.');

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data Anggota Jemaat: ' . $e->getMessage());
            return redirect()->route('admin.anggota-jemaat.create')
                             ->with('error', 'Gagal menyimpan data Anggota Jemaat. Error DB: ' . $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AnggotaJemaat $anggotaJemaat)
    {
         // --- Scoping (Diaktifkan) ---
         // 👇👇👇 Blok ini diaktifkan 👇👇👇
         if (Auth::check()) {
             $user = Auth::user();
             // Jika Admin Jemaat, cek ID jemaat
             if ($user->hasRole('Admin Jemaat') && $anggotaJemaat->jemaat_id != $user->jemaat_id) {
                 abort(403, 'Anda tidak diizinkan melihat data anggota ini.');
             }
             // Jika Admin Klasis, cek ID klasis dari jemaat anggota
             elseif ($user->hasRole('Admin Klasis')) {
                 // Pastikan relasi jemaat sudah di-load atau load di sini
                 $anggotaJemaat->loadMissing('jemaat'); // Load jika belum ada
                 if (!$anggotaJemaat->jemaat || $anggotaJemaat->jemaat->klasis_id != $user->klasis_id) {
                    abort(403, 'Anda tidak diizinkan melihat data anggota dari Klasis lain.');
                 }
             }
             // Role lain yang punya 'view anggota jemaat' bisa lihat
         } else {
              abort(401);
         }
        // --- Akhir Scoping ---

        $anggotaJemaat->load('jemaat.klasis'); // Eager load relasi Jemaat dan Klasisnya
        return view('admin.anggota_jemaat.show', compact('anggotaJemaat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AnggotaJemaat $anggotaJemaat)
    {
         // --- Scoping (Diaktifkan) ---
         // 👇👇👇 Blok ini diaktifkan 👇👇👇
         if (Auth::check()) {
             $user = Auth::user();
             // Check permission sudah di __construct

             // Batasi scope Admin Jemaat & Klasis
             if ($user->hasRole('Admin Jemaat') && $anggotaJemaat->jemaat_id != $user->jemaat_id) {
                  abort(403, 'Anda tidak diizinkan mengedit anggota Jemaat lain.');
             } elseif ($user->hasRole('Admin Klasis')) {
                 $anggotaJemaat->loadMissing('jemaat');
                 if (!$anggotaJemaat->jemaat || $anggotaJemaat->jemaat->klasis_id != $user->klasis_id) {
                    abort(403, 'Anda tidak diizinkan mengedit anggota dari Klasis lain.');
                 }
             }
         } else {
              abort(401);
         }
        // --- Akhir Scoping ---

         $jemaatOptionsQuery = Jemaat::orderBy('nama_jemaat');

         // --- Scoping Pilihan Jemaat (Diaktifkan) ---
         // 👇👇👇 Blok ini diaktifkan 👇👇👇
         if (Auth::check()) {
             $user = Auth::user();
             if ($user->hasRole('Admin Jemaat')) {
                  // Hanya bisa edit anggota di jemaatnya, pilihan jemaat hanya jemaatnya
                  $jemaatId = $user->jemaat_id;
                  if ($jemaatId) $jemaatOptionsQuery->where('id', $jemaatId);
                  else $jemaatOptionsQuery->whereRaw('1 = 0'); // Kosongkan jika tidak terhubung

             } elseif ($user->hasRole('Admin Klasis')) {
                  // Hanya bisa edit anggota di klasisnya, pilihan jemaat terbatas di klasisnya
                  $klasisId = $user->klasis_id;
                  if ($klasisId) $jemaatOptionsQuery->where('klasis_id', $klasisId);
                  else $jemaatOptionsQuery->whereRaw('1 = 0');
             }
             // Super Admin & Admin Bidang 3 bisa edit semua & pindah jemaat
         }
         // --- Akhir Scoping Pilihan ---

        $jemaatOptions = $jemaatOptionsQuery->pluck('nama_jemaat', 'id');

        return view('admin.anggota_jemaat.edit', compact('anggotaJemaat', 'jemaatOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AnggotaJemaat $anggotaJemaat)
    {
         // --- Scoping (Diaktifkan) ---
         // 👇👇👇 Blok ini diaktifkan 👇👇👇
         if (Auth::check()) {
             $user = Auth::user();
             // Check permission sudah di __construct

             // Batasi scope Admin Jemaat & Klasis
             if ($user->hasRole('Admin Jemaat') && $anggotaJemaat->jemaat_id != $user->jemaat_id) {
                  abort(403, 'Anda tidak diizinkan mengupdate anggota Jemaat lain.');
             } elseif ($user->hasRole('Admin Klasis')) {
                  $anggotaJemaat->loadMissing('jemaat');
                  if (!$anggotaJemaat->jemaat || $anggotaJemaat->jemaat->klasis_id != $user->klasis_id) {
                     abort(403, 'Anda tidak diizinkan mengupdate anggota dari Klasis lain.');
                  }
             }
         } else {
              abort(401);
         }
        // --- Akhir Scoping ---

        // Validasi semua field yang ada di form edit Anda
        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:anggota_jemaat,nik,' . $anggotaJemaat->id, // Abaikan ID saat ini
            'jemaat_id' => 'required|exists:jemaat,id',
            'nomor_buku_induk' => 'nullable|string|max:50|unique:anggota_jemaat,nomor_buku_induk,' . $anggotaJemaat->id, // Abaikan ID saat ini
            // ... (Validasi field lainnya sama seperti store, tapi unique rule diubah) ...
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'golongan_darah' => 'nullable|string|max:10',
            'status_pernikahan' => 'nullable|string|max:50',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'pekerjaan_utama' => 'nullable|string|max:100',
            'alamat_lengkap' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:255',
            'sektor_pelayanan' => 'nullable|string|max:50',
            'unit_pelayanan' => 'nullable|string|max:50',
            'tanggal_baptis' => 'nullable|date',
            'tempat_baptis' => 'nullable|string|max:150',
            'tanggal_sidi' => 'nullable|date',
            'tempat_sidi' => 'nullable|string|max:150',
            'tanggal_masuk_jemaat' => 'nullable|date',
            'status_keanggotaan' => 'required|in:Aktif,Tidak Aktif,Pindah,Meninggal',
            'asal_gereja_sebelumnya' => 'nullable|string|max:150',
            'nomor_atestasi' => 'nullable|string|max:50',
            'status_pekerjaan_kk' => 'nullable|string|max:100',
            'status_kepemilikan_rumah' => 'nullable|string|max:100',
            'perkiraan_pendapatan_keluarga' => 'nullable|string|max:50',
        ]);

        // --- Security Check Pindah Jemaat (Diaktifkan) ---
         // 👇👇👇 Blok ini diaktifkan 👇👇👇
         if (Auth::check()) {
             $user = Auth::user();
             // Jika jemaat_id yang diinput berbeda dengan jemaat_id anggota saat ini
             // DAN user yang melakukan BUKAN Super Admin atau Admin Bidang 3
             if ($validatedData['jemaat_id'] != $anggotaJemaat->jemaat_id && !$user->hasAnyRole(['Super Admin', 'Admin Bidang 3'])) {
                  // Admin Jemaat tidak boleh pindah
                  if ($user->hasRole('Admin Jemaat')) {
                      return redirect()->back()->with('error', 'Anda tidak bisa memindahkan anggota ke Jemaat lain.')->withInput();
                  }
                  // Admin Klasis hanya boleh pindah dalam klasisnya
                  elseif ($user->hasRole('Admin Klasis')) {
                      $adminKlasisId = $user->klasis_id;
                      $jemaatTujuan = Jemaat::find($validatedData['jemaat_id']);
                      if (!$adminKlasisId || !$jemaatTujuan || $jemaatTujuan->klasis_id != $adminKlasisId) {
                           return redirect()->back()->with('error', 'Anda hanya bisa memindahkan anggota antar Jemaat dalam Klasis Anda.')->withInput();
                      }
                  }
             }
         }
         // --- Akhir Security Check ---

        try {
            $anggotaJemaat->update($validatedData);
            // Redirect ke index anggota jemaat (atau ke show view anggota tersebut)
             return redirect()->route('admin.anggota-jemaat.index', ['search' => $anggotaJemaat->nik ?? $anggotaJemaat->nomor_buku_induk]) // Redirect ke index & langsung cari yg diupdate
                              ->with('success', 'Data Anggota Jemaat berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Gagal update data Anggota Jemaat ID: ' . $anggotaJemaat->id . '. Error: ' . $e->getMessage());
            return redirect()->route('admin.anggota-jemaat.edit', $anggotaJemaat->id)
                             ->with('error', 'Gagal memperbarui data Anggota Jemaat. Error DB: ' . $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AnggotaJemaat $anggotaJemaat)
    {
         // --- Scoping & Hak Akses (Diaktifkan) ---
         // 👇👇👇 Blok ini diaktifkan 👇👇👇
         if (Auth::check()) {
             $user = Auth::user();
             // Check permission sudah di __construct

             // Batasi scope Admin Jemaat & Klasis
             if ($user->hasRole('Admin Jemaat') && $anggotaJemaat->jemaat_id != $user->jemaat_id) {
                  abort(403, 'Anda tidak diizinkan menghapus anggota Jemaat lain.');
             } elseif ($user->hasRole('Admin Klasis')) {
                 $anggotaJemaat->loadMissing('jemaat');
                 if (!$anggotaJemaat->jemaat || $anggotaJemaat->jemaat->klasis_id != $user->klasis_id) {
                     abort(403, 'Anda tidak diizinkan menghapus anggota dari Klasis lain.');
                 }
             }
         } else {
              abort(401);
         }
        // --- Akhir Scoping ---

        try {
            $namaAnggota = $anggotaJemaat->nama_lengkap; // Simpan nama untuk pesan sukses
            $jemaatIdRedirect = $anggotaJemaat->jemaat_id; // Simpan ID jemaat untuk redirect
            $anggotaJemaat->delete();

            return redirect()->route('admin.anggota-jemaat.index', ['jemaat_id' => $jemaatIdRedirect]) // Redirect ke index jemaatnya (atau hapus filter)
                             ->with('success', 'Data Anggota Jemaat (' . $namaAnggota . ') berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Gagal hapus data Anggota Jemaat ID: ' . $anggotaJemaat->id . '. Error: ' . $e->getMessage());
            return redirect()->route('admin.anggota-jemaat.index')
                              ->with('error', 'Gagal menghapus data Anggota Jemaat. Error DB: ' . $e->getMessage());
        }
    }

    /**
     * Handle request export data.
     */
    public function export(Request $request) // Tambahkan Request
    {
        // Permission check sudah di __construct

         // Cek apakah request meminta template
         if ($request->has('template') && $request->template == 'yes') {
             $export = new AnggotaJemaatExport();
             $headings = $export->headings();
             $templateCollection = collect([$headings]);
             $fileName = 'template_import_anggota_jemaat.xlsx';
             $templateExport = new class($templateCollection) implements FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                 protected $collection; protected $headingsData;
                 public function __construct($collection) { $this->collection = $collection; $this->headingsData = $collection->first(); }
                 public function collection() { return collect([]); }
                 public function headings(): array { return $this->headingsData ?? []; }
             };
             return Excel::download($templateExport, $fileName);
         }

        // Jika bukan template, export data normal
        try {
            $fileName = 'anggota_jemaat_gpi_papua_' . date('YmdHis') . '.xlsx';
            // TODO: Tambahkan scoping export untuk Admin Klasis/Jemaat
            // 👇👇👇 Contoh Scoping Export (bisa diaktifkan nanti) 👇👇👇
             $user = Auth::user();
             $jemaatId = $user->hasRole('Admin Jemaat') ? $user->jemaat_id : $request->query('jemaat_id'); // Ambil dari user atau filter
             $klasisId = $user->hasRole('Admin Klasis') ? $user->klasis_id : $request->query('klasis_id'); // Ambil dari user atau filter
             $search = $request->query('search'); // Ambil search term

             $export = new AnggotaJemaatExport($search, $klasisId, $jemaatId); // Pass filter ke Export class
             return Excel::download($export, $fileName);

            // return Excel::download(new AnggotaJemaatExport, $fileName); // Kode lama (export semua)
        } catch (\Exception $e) {
             Log::error('Gagal export Anggota Jemaat: ' . $e->getMessage());
             return redirect()->route('admin.anggota-jemaat.index')
                              ->with('error', 'Gagal mengekspor data. Silakan coba lagi.');
        }
    }

    /**
     * Menampilkan halaman form import.
     */
    public function showImportForm()
    {
        // Permission check sudah di __construct
         return view('admin.anggota_jemaat.import'); // View import.blade.php
    }


    /**
     * Handle request import data.
     */
    public function import(Request $request)
    {
        // Permission check sudah di __construct

        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv', // Hanya izinkan file spreadsheet
        ]);

        $file = $request->file('import_file');

        try {
            // TODO: Tambahkan scoping import untuk Admin Klasis/Jemaat
            // 👇👇👇 Contoh Scoping Import (bisa diaktifkan nanti) 👇👇👇
            $user = Auth::user();
            $jemaatIdConstraint = $user->hasRole('Admin Jemaat') ? $user->jemaat_id : null;
            $klasisIdConstraint = $user->hasRole('Admin Klasis') ? $user->klasis_id : null;

            $import = new AnggotaJemaatImport($jemaatIdConstraint, $klasisIdConstraint); // Pass constraint ke Import class
            Excel::import($import, $file);

            // $import = new AnggotaJemaatImport(); // Kode lama (tanpa scope)
            // Excel::import($import, $file);

            // Cek jika ada error validasi selama import (jika pakai SkipsOnError)
             $failures = $import->failures();
            if ($failures->isNotEmpty()) {
                $errorRows = []; $errorCount = count($failures);
                foreach ($failures as $failure) { $errorRows[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Nilai: ' . implode(', ', array_slice($failure->values(), 0, 5)) . '...)';}
                $errorMessage = "Import selesai, namun terdapat {$errorCount} kesalahan validasi:\n" . implode("\n", $errorRows);
                Log::warning($errorMessage);
                if (count($errorRows) > 10) { $errorMessage = "Import selesai, namun terdapat {$errorCount} kesalahan validasi (ditampilkan 10 error pertama):\n" . implode("\n", array_slice($errorRows, 0, 10)) . "\n... (silakan cek log aplikasi untuk detail)";}
                return redirect()->route('admin.anggota-jemaat.index')->with('warning', $errorMessage);
            }

            return redirect()->route('admin.anggota-jemaat.index')->with('success', 'Data Anggota Jemaat berhasil diimpor.');

        } catch (ValidationException $e) {
            $failures = $e->failures(); $errorRows = []; $errorCount = count($failures);
            foreach ($failures as $failure) { $errorRows[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Nilai: ' . implode(', ', array_slice($failure->values(), 0, 5)) . '...)'; }
            $errorMessage = "Gagal import karena {$errorCount} kesalahan validasi:\n" . implode("\n", $errorRows);
            Log::error($errorMessage);
            if (count($errorRows) > 10) { $errorMessage = "Gagal import karena {$errorCount} kesalahan validasi (ditampilkan 10 error pertama):\n" . implode("\n", array_slice($errorRows, 0, 10)) . "\n... (silakan cek log aplikasi untuk detail)"; }
            return redirect()->back()->with('error', $errorMessage);

        } catch (\InvalidArgumentException $e) { // <-- Tangkap error jika import dibatasi scope
             Log::error('Gagal import Anggota Jemaat karena pembatasan scope: ' . $e->getMessage());
             return redirect()->back()->with('error', $e->getMessage()); // Tampilkan pesan error dari Import Class
        } catch (\Exception $e) {
            Log::error('Gagal import Anggota Jemaat: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data. Silakan periksa format file atau hubungi administrator. Error: ' . $e->getMessage());
        }
    }

} // Akhir Class