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
use Illuminate\Validation\Rule; // <-- Import Rule

class AnggotaJemaatController extends Controller
{
    // Middleware
    public function __construct()
    {
         $this->middleware(['auth']); // Semua harus login
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
    public function index(Request $request)
    {
        $query = AnggotaJemaat::with(['jemaat', 'jemaat.klasis'])->latest();
        $user = Auth::user();
        $jemaatUser = null; // Inisialisasi

        $klasisFilterOptions = collect();
        $jemaatFilterOptions = collect();

        // --- Scoping berdasarkan Role ---
        if ($user->hasRole('Admin Jemaat')) {
            $jemaatId = $user->jemaat_id;
            if ($jemaatId) {
                $query->where('jemaat_id', $jemaatId);
                $jemaatFilterOptions = Jemaat::where('id', $jemaatId)->pluck('nama_jemaat', 'id');
                 $jemaatUser = Jemaat::find($jemaatId); // Ambil model Jemaat
                 if ($jemaatUser && $jemaatUser->klasis_id) {
                    $klasisFilterOptions = Klasis::where('id', $jemaatUser->klasis_id)->pluck('nama_klasis', 'id');
                 }
            } else {
                Log::warning('Admin Jemaat ' . $user->id . ' tidak terhubung ke Jemaat manapun.');
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->hasRole('Admin Klasis')) {
            $klasisId = $user->klasis_id;
            if ($klasisId) {
                $jemaatIds = Jemaat::where('klasis_id', $klasisId)->pluck('id');
                if ($jemaatIds->isNotEmpty()) {
                     $query->whereIn('jemaat_id', $jemaatIds);
                     $jemaatFilterOptions = Jemaat::where('klasis_id', $klasisId)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
                } else {
                     Log::warning('Admin Klasis ' . $user->id . ' scope index, tapi Klasis ' . $klasisId . ' tidak punya Jemaat.');
                     $query->whereRaw('1 = 0');
                }
                $klasisFilterOptions = Klasis::where('id', $klasisId)->pluck('nama_klasis', 'id');
            } else {
                 Log::warning('Admin Klasis ' . $user->id . ' tidak terhubung ke Klasis manapun.');
                $query->whereRaw('1 = 0');
            }
        } else { // Super Admin, Admin Bidang, dll.
             $klasisFilterOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
             if ($request->filled('klasis_id') && ($klasisFilterId = filter_var($request->klasis_id, FILTER_VALIDATE_INT))) {
                  // Hanya load jemaat jika ada filter klasis (untuk performa)
                  if ($klasisFilterOptions->has($klasisFilterId)) {
                        $jemaatFilterOptions = Jemaat::where('klasis_id', $klasisFilterId)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
                  }
             } else {
                 // Jika tidak ada filter klasis, jangan load semua jemaat
                 // $jemaatFilterOptions = Jemaat::orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
             }
        }

        // --- Filter berdasarkan Request ---
        if ($request->filled('klasis_id') && ($klasisFilterId = filter_var($request->klasis_id, FILTER_VALIDATE_INT))) {
            // Cek scope sebelum apply filter
            if (($user->hasRole('Admin Klasis') && $user->klasis_id != $klasisFilterId) ||
                ($user->hasRole('Admin Jemaat') && $jemaatUser && $jemaatUser->klasis_id != $klasisFilterId))
            {
                // Abaikan filter jika tidak sesuai scope
                Log::warning('User '.$user->id.' mencoba filter klasis '.$klasisFilterId.' di luar scope.');
            } else {
                 $query->whereHas('jemaat', fn($q) => $q->where('klasis_id', $klasisFilterId));
            }
        }
        if ($request->filled('jemaat_id') && ($jemaatFilterId = filter_var($request->jemaat_id, FILTER_VALIDATE_INT))) {
             // Cek scope sebelum apply filter
             if ($user->hasRole('Admin Jemaat') && $user->jemaat_id != $jemaatFilterId) {
                  Log::warning('User '.$user->id.' mencoba filter jemaat '.$jemaatFilterId.' di luar scope.');
             } else {
                 $query->where('jemaat_id', $jemaatFilterId);
             }
        }
        // Filter Nomor KK (Sudah benar)
        if ($request->filled('nomor_kk_filter')) {
            $query->where('nomor_kk', 'like', '%' . $request->nomor_kk_filter . '%');
        }

         // Fitur Search (Sudah benar)
         if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                 $q->where('nama_lengkap', 'like', $searchTerm)
                   ->orWhere('nik', 'like', $searchTerm)
                   ->orWhere('nomor_buku_induk', 'like', $searchTerm)
                   ->orWhere('nomor_kk', 'like', $searchTerm);
             });
         }

        $anggotaJemaatData = $query->paginate(20)->appends($request->query());
        return view('admin.anggota_jemaat.index', compact('anggotaJemaatData', 'klasisFilterOptions', 'jemaatFilterOptions', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) // <-- Request sudah ada
    {
         $jemaatOptionsQuery = Jemaat::orderBy('nama_jemaat');
         $user = Auth::user();

         // Scoping Pilihan Jemaat (Sudah benar)
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
        if ($jemaatOptions->isEmpty() && !$user->hasAnyRole(['Super Admin', 'Admin Bidang 3'])) {
             return redirect()->back()->with('error', 'Tidak ada Jemaat tersedia dalam lingkup Anda.');
        }

        // Ambil data pre-fill dari request (Sudah benar)
        $prefillData = [
             'nomor_kk' => $request->query('nomor_kk'),
             'alamat_lengkap' => $request->query('alamat'),
             'jemaat_id' => $request->query('jemaat_id'),
             'sektor_pelayanan' => $request->query('sektor'), // Tambahkan jika perlu
             'unit_pelayanan' => $request->query('unit'), // Tambahkan jika perlu
        ];

        return view('admin.anggota_jemaat.create', compact('jemaatOptions', 'prefillData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // Validasi semua field, tambahkan nomor_kk dan status_dalam_keluarga
         $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => ['nullable', 'string', 'max:20', Rule::unique('anggota_jemaat', 'nik')->whereNull('deleted_at')],
            'jemaat_id' => 'required|exists:jemaat,id',
            'nomor_buku_induk' => ['nullable', 'string', 'max:50', Rule::unique('anggota_jemaat', 'nomor_buku_induk')->whereNull('deleted_at')],
            'nomor_kk' => 'nullable|string|max:50', // <-- Tambah validasi
            'status_dalam_keluarga' => 'nullable|string|max:50', // <-- Tambah validasi
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
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('anggota_jemaat', 'email')->whereNull('deleted_at')], // Cek unik email
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
            'status_pekerjaan_kk' => 'nullable|string|max:100', // Sensus
            'status_kepemilikan_rumah' => 'nullable|string|max:100', // Sensus
            'perkiraan_pendapatan_keluarga' => 'nullable|string|max:50', // Sensus
            'catatan' => 'nullable|string',
            // Tambahkan validasi field lain dari model jika ada
            'jabatan_pelayan_khusus' => 'nullable|string|max:100',
            'wadah_kategorial' => 'nullable|string|max:100',
            'keterlibatan_lain' => 'nullable|string',
            'nama_kepala_keluarga' => 'nullable|string|max:255', // Mungkin tidak perlu jika pakai relasi
            'sektor_pekerjaan_kk' => 'nullable|string|max:100',
            'sumber_penerangan' => 'nullable|string|max:100',
            'sumber_air_minum' => 'nullable|string|max:100',
        ]);

        // --- Security Check (Sudah benar) ---
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('Admin Jemaat')) {
                $adminJemaatId = $user->jemaat_id;
                if (!$adminJemaatId || $adminJemaatId != $validatedData['jemaat_id']) {
                    return redirect()->back()->with('error', 'Anda hanya bisa menambah anggota untuk Jemaat Anda.')->withInput();
                }
            } elseif ($user->hasRole('Admin Klasis')) {
                $adminKlasisId = $user->klasis_id;
                $jemaatDipilih = Jemaat::find($validatedData['jemaat_id']);
                if (!$adminKlasisId || !$jemaatDipilih || $jemaatDipilih->klasis_id != $adminKlasisId) {
                    return redirect()->back()->with('error', 'Anda hanya bisa menambah anggota untuk Jemaat dalam Klasis Anda.')->withInput();
                }
            }
        }

        try {
            $anggota = AnggotaJemaat::create($validatedData);

            // --- Logika Redirect (Sudah benar) ---
            if ($request->has('save_and_add_another') && $anggota->nomor_kk) {
                return redirect()->route('admin.anggota-jemaat.create', [
                    'nomor_kk' => $anggota->nomor_kk,
                    'alamat' => $anggota->alamat_lengkap,
                    'jemaat_id' => $anggota->jemaat_id,
                    'sektor' => $anggota->sektor_pelayanan, // Bawa field lain jika perlu
                    'unit' => $anggota->unit_pelayanan,
                ])->with('success', 'Anggota Jemaat (' . $validatedData['nama_lengkap'] . ') berhasil ditambahkan. Silakan tambah anggota keluarga berikutnya.');
            } else {
                 return redirect()->route('admin.anggota-jemaat.index')
                                 ->with('success', 'Anggota Jemaat (' . $validatedData['nama_lengkap'] . ') berhasil ditambahkan.');
            }

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
    public function show(AnggotaJemaat $anggotaJemaat) // Parameter $anggotaJemaat sudah benar
    {
         // --- Scoping (Sudah benar) ---
         if (Auth::check()) {
             $user = Auth::user();
             if ($user->hasRole('Admin Jemaat') && $anggotaJemaat->jemaat_id != $user->jemaat_id) { abort(403); }
             elseif ($user->hasRole('Admin Klasis')) {
                 $anggotaJemaat->loadMissing('jemaat');
                 if (!$anggotaJemaat->jemaat || $anggotaJemaat->jemaat->klasis_id != $user->klasis_id) { abort(403); }
             }
         } else { abort(401); }

        // Load relasi Jemaat, Klasis, anggota keluarga lain, dan kepala keluarga (Sudah benar)
        $anggotaJemaat->load(['jemaat.klasis', 'keluarga', 'kepalaKeluarga']);

        $kepalaKeluarga = $anggotaJemaat->kepalaKeluarga; // Ambil dari relasi
        $anggotaKeluargaLain = $anggotaJemaat->keluarga; // Ambil dari relasi

        return view('admin.anggota_jemaat.show', compact('anggotaJemaat', 'kepalaKeluarga', 'anggotaKeluargaLain'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AnggotaJemaat $anggotaJemaat) // Parameter $anggotaJemaat sudah benar
    {
         // --- Scoping (Sudah benar) ---
         if (Auth::check()) {
             $user = Auth::user();
             if ($user->hasRole('Admin Jemaat') && $anggotaJemaat->jemaat_id != $user->jemaat_id) { abort(403); }
             elseif ($user->hasRole('Admin Klasis')) {
                 $anggotaJemaat->loadMissing('jemaat');
                 if (!$anggotaJemaat->jemaat || $anggotaJemaat->jemaat->klasis_id != $user->klasis_id) { abort(403); }
             }
         } else { abort(401); }

         // --- Scoping Pilihan Jemaat (Sudah benar) ---
         $jemaatOptionsQuery = Jemaat::orderBy('nama_jemaat');
         if (Auth::check()) {
             $user = Auth::user();
             if ($user->hasRole(['Admin Jemaat'])) {
                  $jemaatId = $user->jemaat_id;
                  if ($jemaatId) $jemaatOptionsQuery->where('id', $jemaatId); else $jemaatOptionsQuery->whereRaw('1 = 0');
             } elseif ($user->hasRole('Admin Klasis')) {
                  $klasisId = $user->klasis_id;
                  if ($klasisId) $jemaatOptionsQuery->where('klasis_id', $klasisId); else $jemaatOptionsQuery->whereRaw('1 = 0');
             }
         }
        $jemaatOptions = $jemaatOptionsQuery->pluck('nama_jemaat', 'id');

        return view('admin.anggota_jemaat.edit', compact('anggotaJemaat', 'jemaatOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AnggotaJemaat $anggotaJemaat) // Parameter $anggotaJemaat sudah benar
    {
         // --- Scoping (Sudah benar) ---
         if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('Admin Jemaat') && $anggotaJemaat->jemaat_id != $user->jemaat_id) { abort(403); }
            elseif ($user->hasRole('Admin Klasis')) {
                $anggotaJemaat->loadMissing('jemaat');
                if (!$anggotaJemaat->jemaat || $anggotaJemaat->jemaat->klasis_id != $user->klasis_id) { abort(403); }
            }
         } else { abort(401); }

        // Validasi, tambahkan nomor_kk, status_dalam_keluarga, sesuaikan unique rule
        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => ['nullable', 'string', 'max:20', Rule::unique('anggota_jemaat', 'nik')->ignore($anggotaJemaat->id)->whereNull('deleted_at')],
            'jemaat_id' => 'required|exists:jemaat,id',
            'nomor_buku_induk' => ['nullable', 'string', 'max:50', Rule::unique('anggota_jemaat', 'nomor_buku_induk')->ignore($anggotaJemaat->id)->whereNull('deleted_at')],
            'nomor_kk' => 'nullable|string|max:50', // <-- Validasi KK
            'status_dalam_keluarga' => 'nullable|string|max:50', // <-- Validasi Status Keluarga
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
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('anggota_jemaat', 'email')->ignore($anggotaJemaat->id)->whereNull('deleted_at')],
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
            'status_pekerjaan_kk' => 'nullable|string|max:100', // Sensus
            'status_kepemilikan_rumah' => 'nullable|string|max:100', // Sensus
            'perkiraan_pendapatan_keluarga' => 'nullable|string|max:50', // Sensus
            'catatan' => 'nullable|string',
            // Tambahkan validasi field lain dari model jika ada
            'jabatan_pelayan_khusus' => 'nullable|string|max:100',
            'wadah_kategorial' => 'nullable|string|max:100',
            'keterlibatan_lain' => 'nullable|string',
            'nama_kepala_keluarga' => 'nullable|string|max:255',
            'sektor_pekerjaan_kk' => 'nullable|string|max:100',
            'sumber_penerangan' => 'nullable|string|max:100',
            'sumber_air_minum' => 'nullable|string|max:100',
        ]);

        // --- Security Check Pindah Jemaat (Sudah benar) ---
         if (Auth::check()) {
             $user = Auth::user();
             if ($validatedData['jemaat_id'] != $anggotaJemaat->jemaat_id && !$user->hasAnyRole(['Super Admin', 'Admin Bidang 3'])) {
                  if ($user->hasRole('Admin Jemaat')) {
                      return redirect()->back()->with('error', 'Anda tidak bisa memindahkan anggota ke Jemaat lain.')->withInput();
                  } elseif ($user->hasRole('Admin Klasis')) {
                      $adminKlasisId = $user->klasis_id;
                      $jemaatTujuan = Jemaat::find($validatedData['jemaat_id']);
                      if (!$adminKlasisId || !$jemaatTujuan || $jemaatTujuan->klasis_id != $adminKlasisId) {
                           return redirect()->back()->with('error', 'Anda hanya bisa memindahkan anggota antar Jemaat dalam Klasis Anda.')->withInput();
                      }
                  }
             }
         }

        try {
            $anggotaJemaat->update($validatedData);
            // Redirect ke show page (Sudah benar)
             return redirect()->route('admin.anggota-jemaat.show', $anggotaJemaat->id)
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
    public function destroy(AnggotaJemaat $anggotaJemaat) // Parameter $anggotaJemaat sudah benar
    {
         // --- Scoping (Sudah benar) ---
         if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('Admin Jemaat') && $anggotaJemaat->jemaat_id != $user->jemaat_id) { abort(403); }
            elseif ($user->hasRole('Admin Klasis')) {
                $anggotaJemaat->loadMissing('jemaat');
                if (!$anggotaJemaat->jemaat || $anggotaJemaat->jemaat->klasis_id != $user->klasis_id) { abort(403); }
            }
         } else { abort(401); }

        try {
            $namaAnggota = $anggotaJemaat->nama_lengkap;
            $nomorKkRedirect = $anggotaJemaat->nomor_kk; // Simpan nomor KK
            $anggotaJemaat->delete();

            // Redirect ke index dengan filter nomor KK (Sudah benar)
            $redirectParams = $nomorKkRedirect ? ['nomor_kk_filter' => $nomorKkRedirect] : [];
            return redirect()->route('admin.anggota-jemaat.index', $redirectParams)
                             ->with('success', 'Data Anggota Jemaat (' . $namaAnggota . ') berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Gagal hapus data Anggota Jemaat ID: ' . $anggotaJemaat->id . '. Error: ' . $e->getMessage());
             if (str_contains($e->getMessage(), 'constraint violation')) {
                 return redirect()->route('admin.anggota-jemaat.index')
                                 ->with('error', 'Gagal menghapus Anggota: Data mungkin masih terkait dengan data lain.');
             }
            return redirect()->route('admin.anggota-jemaat.index')
                              ->with('error', 'Gagal menghapus data Anggota Jemaat. Error DB: ' . $e->getMessage());
        }
    }

    /**
     * Handle request export data.
     */
    public function export(Request $request)
    {
         // Cek template
         if ($request->has('template') && $request->template == 'yes') {
             $export = new AnggotaJemaatExport();
             $headings = $export->headings();
             // Tambahkan kolom nomor_kk dan status_dalam_keluarga ke template (Sudah benar)
             $headings[] = 'nomor_kk';
             $headings[] = 'status_dalam_keluarga';
             $templateCollection = collect([$headings]);
             $fileName = 'template_import_anggota_jemaat.xlsx';
             $templateExport = new class($templateCollection) implements FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                 // ... (kode class template export tetap sama) ...
             };
             return Excel::download($templateExport, $fileName);
         }

        // Export data normal
        try {
            $fileName = 'anggota_jemaat_gpi_papua_' . date('YmdHis') . '.xlsx';
             $user = Auth::user();
             $jemaatId = $user->hasRole('Admin Jemaat') ? $user->jemaat_id : $request->query('jemaat_id');
             $klasisId = $user->hasRole('Admin Klasis') ? $user->klasis_id : $request->query('klasis_id');
             $search = $request->query('search');
             $nomorKkFilter = $request->query('nomor_kk_filter'); // Tambahkan filter KK

             // Pastikan Export Class diupdate untuk menerima $nomorKkFilter
             // TODO: Anda perlu memodifikasi AnggotaJemaatExport.php untuk menerima parameter ke-4
             $export = new AnggotaJemaatExport($search, $klasisId, $jemaatId, $nomorKkFilter);
             return Excel::download($export, $fileName);
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
         return view('admin.anggota_jemaat.import'); // Sudah benar
    }


    /**
     * Handle request import data.
     */
    public function import(Request $request)
    {
        $request->validate([ 'import_file' => 'required|file|mimes:xlsx,xls,csv', ]);
        $file = $request->file('import_file');

        try {
            // Scoping Import (Sudah benar)
            $user = Auth::user();
            $jemaatIdConstraint = $user->hasRole('Admin Jemaat') ? $user->jemaat_id : null;
            $klasisIdConstraint = $user->hasRole('Admin Klasis') ? $user->klasis_id : null;

            // Pastikan Import Class diupdate untuk menerima constraint (Sudah benar)
            $import = new AnggotaJemaatImport($jemaatIdConstraint, $klasisIdConstraint);
            Excel::import($import, $file);

            // Handle failures (Sudah benar)
             $failures = $import->failures();
            if ($failures->isNotEmpty()) {
                $errorRows = []; $errorCount = count($failures);
                foreach ($failures as $failure) {
                    $rowNum = $failure->row() ?: '?';
                    $errors = implode(', ', $failure->errors());
                    $values = implode(', ', array_slice($failure->values(), 0, 5));
                    $errorRows[] = "Baris {$rowNum}: {$errors} (Nilai: {$values}...)";
                }
                $errorMessage = "Import selesai, namun terdapat {$errorCount} kesalahan:\n" . implode("\n", $errorRows);
                Log::warning($errorMessage);
                if ($errorCount > 10) { $errorMessage = "Import selesai dengan {$errorCount} kesalahan (10 error pertama):\n" . implode("\n", array_slice($errorRows, 0, 10)) . "\n... (cek log)";}

                return redirect()->route('admin.anggota-jemaat.index')->with('warning', $errorMessage);
            }

            return redirect()->route('admin.anggota-jemaat.index')->with('success', 'Data Anggota Jemaat berhasil diimpor.');

        } catch (ValidationException $e) {
             // ... (Error handling ValidationException sudah benar) ...
            return redirect()->back()->with('error', $errorMessage)->withInput();
        } catch (\InvalidArgumentException $e) { // Tangkap error scope
             // ... (Error handling InvalidArgumentException sudah benar) ...
             return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
             // ... (Error handling Exception umum sudah benar) ...
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data. Error: ' . $e->getMessage())->withInput();
        }
    }

} // Akhir Class