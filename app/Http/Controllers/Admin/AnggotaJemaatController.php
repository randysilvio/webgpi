<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaJemaat;
use App\Models\Jemaat; 
use App\Models\Klasis; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnggotaJemaatExport;
use App\Imports\AnggotaJemaatImport;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Validation\Rule; 

class AnggotaJemaatController extends Controller
{
    public function __construct()
    {
         $this->middleware(['auth']);
         $this->middleware('can:view anggota jemaat')->only(['index', 'show']);
         $this->middleware('can:create anggota jemaat')->only(['create', 'store']);
         $this->middleware('can:edit anggota jemaat')->only(['edit', 'update']);
         $this->middleware('can:delete anggota jemaat')->only(['destroy']);
         $this->middleware('can:import anggota jemaat')->only(['showImportForm', 'import']);
         $this->middleware('can:export anggota jemaat')->only(['export']);
    }

    public function index(Request $request)
    {
        $query = AnggotaJemaat::with(['jemaat', 'jemaat.klasis'])->latest();
        $user = Auth::user();
        $jemaatUser = null; 

        $klasisFilterOptions = collect();
        $jemaatFilterOptions = collect();

        // --- Scoping Data ---
        if ($user->hasRole('Admin Jemaat')) {
            $jemaatId = $user->jemaat_id;
            if ($jemaatId) {
                $query->where('jemaat_id', $jemaatId);
                $jemaatFilterOptions = Jemaat::where('id', $jemaatId)->pluck('nama_jemaat', 'id');
                 $jemaatUser = Jemaat::find($jemaatId); 
                 if ($jemaatUser && $jemaatUser->klasis_id) {
                    $klasisFilterOptions = Klasis::where('id', $jemaatUser->klasis_id)->pluck('nama_klasis', 'id');
                 }
            } else {
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
                     $query->whereRaw('1 = 0');
                }
                $klasisFilterOptions = Klasis::where('id', $klasisId)->pluck('nama_klasis', 'id');
            } else {
                $query->whereRaw('1 = 0');
            }
        } else { 
             $klasisFilterOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
             if ($request->filled('klasis_id') && ($klasisFilterId = filter_var($request->klasis_id, FILTER_VALIDATE_INT))) {
                  if ($klasisFilterOptions->has($klasisFilterId)) {
                        $jemaatFilterOptions = Jemaat::where('klasis_id', $klasisFilterId)->orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
                  }
             }
        }

        // --- Filter ---
        if ($request->filled('klasis_id') && ($klasisFilterId = filter_var($request->klasis_id, FILTER_VALIDATE_INT))) {
            if (($user->hasRole('Admin Klasis') && $user->klasis_id != $klasisFilterId) ||
                ($user->hasRole('Admin Jemaat') && $jemaatUser && $jemaatUser->klasis_id != $klasisFilterId))
            {
                // Abaikan filter illegal
            } else {
                 $query->whereHas('jemaat', fn($q) => $q->where('klasis_id', $klasisFilterId));
            }
        }
        if ($request->filled('jemaat_id') && ($jemaatFilterId = filter_var($request->jemaat_id, FILTER_VALIDATE_INT))) {
             if ($user->hasRole('Admin Jemaat') && $user->jemaat_id != $jemaatFilterId) {
                 // Abaikan
             } else {
                 $query->where('jemaat_id', $jemaatFilterId);
             }
        }
        if ($request->filled('nomor_kk_filter')) {
            $query->where('nomor_kk', 'like', '%' . $request->nomor_kk_filter . '%');
        }

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

    public function create(Request $request)
    {
         $jemaatOptionsQuery = Jemaat::orderBy('nama_jemaat');
         $user = Auth::user();

         if ($user->hasRole('Admin Jemaat')) {
             $jemaatId = $user->jemaat_id;
             if ($jemaatId) $jemaatOptionsQuery->where('id', $jemaatId);
             else return redirect()->route('admin.dashboard')->with('error', 'Akun Anda tidak terhubung ke Jemaat.');
         } elseif ($user->hasRole('Admin Klasis')) {
              $klasisId = $user->klasis_id;
              if ($klasisId) $jemaatOptionsQuery->where('klasis_id', $klasisId);
              else return redirect()->route('admin.dashboard')->with('error', 'Akun Anda tidak terhubung ke Klasis.');
         }
        $jemaatOptions = $jemaatOptionsQuery->pluck('nama_jemaat', 'id');
        
        if ($jemaatOptions->isEmpty() && !$user->hasAnyRole(['Super Admin', 'Admin Bidang 3'])) {
             return redirect()->back()->with('error', 'Tidak ada Jemaat tersedia dalam lingkup Anda.');
        }

        $prefillData = [
             'nomor_kk' => $request->query('nomor_kk'),
             'alamat_lengkap' => $request->query('alamat'),
             'jemaat_id' => $request->query('jemaat_id'),
             'sektor_pelayanan' => $request->query('sektor'),
             'unit_pelayanan' => $request->query('unit'),
        ];

        return view('admin.anggota_jemaat.create', compact('jemaatOptions', 'prefillData'));
    }

    public function store(Request $request)
    {
         $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => ['nullable', 'string', 'max:20', Rule::unique('anggota_jemaat', 'nik')->whereNull('deleted_at')],
            'jemaat_id' => 'required|exists:jemaat,id',
            'nomor_buku_induk' => ['nullable', 'string', 'max:50', Rule::unique('anggota_jemaat', 'nomor_buku_induk')->whereNull('deleted_at')],
            'nomor_kk' => 'nullable|string|max:50',
            'status_dalam_keluarga' => 'nullable|string|max:50',
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
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('anggota_jemaat', 'email')->whereNull('deleted_at')],
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
            'catatan' => 'nullable|string',
            'jabatan_pelayan_khusus' => 'nullable|string|max:100',
            'wadah_kategorial' => 'nullable|string|max:100',
            'keterlibatan_lain' => 'nullable|string',
            'nama_kepala_keluarga' => 'nullable|string|max:255',
            'sektor_pekerjaan_kk' => 'nullable|string|max:100',
            'sumber_penerangan' => 'nullable|string|max:100',
            'sumber_air_minum' => 'nullable|string|max:100',
        ]);

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('Admin Jemaat')) {
                if ($user->jemaat_id != $validatedData['jemaat_id']) {
                    return redirect()->back()->with('error', 'Anda hanya bisa menambah anggota untuk Jemaat Anda.')->withInput();
                }
            } elseif ($user->hasRole('Admin Klasis')) {
                $jemaatDipilih = Jemaat::find($validatedData['jemaat_id']);
                if (!$jemaatDipilih || $jemaatDipilih->klasis_id != $user->klasis_id) {
                    return redirect()->back()->with('error', 'Anda hanya bisa menambah anggota untuk Jemaat dalam Klasis Anda.')->withInput();
                }
            }
        }

        try {
            $anggota = AnggotaJemaat::create($validatedData);

            if ($request->has('save_and_add_another') && $anggota->nomor_kk) {
                return redirect()->route('admin.anggota-jemaat.create', [
                    'nomor_kk' => $anggota->nomor_kk,
                    'alamat' => $anggota->alamat_lengkap,
                    'jemaat_id' => $anggota->jemaat_id,
                    'sektor' => $anggota->sektor_pelayanan,
                    'unit' => $anggota->unit_pelayanan,
                ])->with('success', 'Data berhasil ditambahkan. Lanjut input anggota keluarga berikutnya.');
            } else {
                 return redirect()->route('admin.anggota-jemaat.index')
                                 ->with('success', 'Anggota Jemaat berhasil ditambahkan.');
            }

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data: ' . $e->getMessage());
            return redirect()->route('admin.anggota-jemaat.create')
                             ->with('error', 'Gagal menyimpan data. Error: ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function show(AnggotaJemaat $anggotaJemaat)
    {
         if (Auth::check()) {
             $user = Auth::user();
             if ($user->hasRole('Admin Jemaat') && $anggotaJemaat->jemaat_id != $user->jemaat_id) { abort(403); }
             elseif ($user->hasRole('Admin Klasis')) {
                 $anggotaJemaat->loadMissing('jemaat');
                 if (!$anggotaJemaat->jemaat || $anggotaJemaat->jemaat->klasis_id != $user->klasis_id) { abort(403); }
             }
         } else { abort(401); }

        $anggotaJemaat->load(['jemaat.klasis', 'keluarga', 'kepalaKeluarga']);
        
        return view('admin.anggota_jemaat.show', [
            'anggotaJemaat' => $anggotaJemaat,
            'kepalaKeluarga' => $anggotaJemaat->kepalaKeluarga,
            'anggotaKeluargaLain' => $anggotaJemaat->keluarga
        ]);
    }

    public function edit(AnggotaJemaat $anggotaJemaat)
    {
         if (Auth::check()) {
             $user = Auth::user();
             if ($user->hasRole('Admin Jemaat') && $anggotaJemaat->jemaat_id != $user->jemaat_id) { abort(403); }
             elseif ($user->hasRole('Admin Klasis')) {
                 $anggotaJemaat->loadMissing('jemaat');
                 if (!$anggotaJemaat->jemaat || $anggotaJemaat->jemaat->klasis_id != $user->klasis_id) { abort(403); }
             }
         } else { abort(401); }

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

    public function update(Request $request, AnggotaJemaat $anggotaJemaat)
    {
         if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('Admin Jemaat') && $anggotaJemaat->jemaat_id != $user->jemaat_id) { abort(403); }
            elseif ($user->hasRole('Admin Klasis')) {
                $anggotaJemaat->loadMissing('jemaat');
                if (!$anggotaJemaat->jemaat || $anggotaJemaat->jemaat->klasis_id != $user->klasis_id) { abort(403); }
            }
         } else { abort(401); }

        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => ['nullable', 'string', 'max:20', Rule::unique('anggota_jemaat', 'nik')->ignore($anggotaJemaat->id)->whereNull('deleted_at')],
            'jemaat_id' => 'required|exists:jemaat,id',
            'nomor_buku_induk' => ['nullable', 'string', 'max:50', Rule::unique('anggota_jemaat', 'nomor_buku_induk')->ignore($anggotaJemaat->id)->whereNull('deleted_at')],
            'nomor_kk' => 'nullable|string|max:50',
            'status_dalam_keluarga' => 'nullable|string|max:50',
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
            'status_pekerjaan_kk' => 'nullable|string|max:100',
            'status_kepemilikan_rumah' => 'nullable|string|max:100',
            'perkiraan_pendapatan_keluarga' => 'nullable|string|max:50',
            'catatan' => 'nullable|string',
            'jabatan_pelayan_khusus' => 'nullable|string|max:100',
            'wadah_kategorial' => 'nullable|string|max:100',
            'keterlibatan_lain' => 'nullable|string',
            'nama_kepala_keluarga' => 'nullable|string|max:255',
            'sektor_pekerjaan_kk' => 'nullable|string|max:100',
            'sumber_penerangan' => 'nullable|string|max:100',
            'sumber_air_minum' => 'nullable|string|max:100',
        ]);

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
             return redirect()->route('admin.anggota-jemaat.show', $anggotaJemaat->id)
                              ->with('success', 'Data Anggota Jemaat berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal update data: ' . $e->getMessage());
            return redirect()->route('admin.anggota-jemaat.edit', $anggotaJemaat->id)
                             ->with('error', 'Gagal memperbarui data. Error: ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function destroy(AnggotaJemaat $anggotaJemaat)
    {
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
            $nomorKkRedirect = $anggotaJemaat->nomor_kk;
            $anggotaJemaat->delete();

            $redirectParams = $nomorKkRedirect ? ['nomor_kk_filter' => $nomorKkRedirect] : [];
            return redirect()->route('admin.anggota-jemaat.index', $redirectParams)
                             ->with('success', 'Data Anggota Jemaat (' . $namaAnggota . ') berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Gagal hapus data: ' . $e->getMessage());
            return redirect()->route('admin.anggota-jemaat.index')
                              ->with('error', 'Gagal menghapus data. Error: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
         // 1. Download Template
         if ($request->has('template') && $request->template == 'yes') {
             $headings = [
                'id_jemaat_wajib_diisi_saat_import', 'nik', 'nomor_buku_induk', 'nama_lengkap_wajib',
                'nomor_kk', 'status_dalam_keluarga', 'tempat_lahir', 'tanggal_lahir_yyyymmdd',
                'jenis_kelamin_lakilakiperempuan', 'golongan_darah', 'status_pernikahan',
                'nama_ayah', 'nama_ibu', 'pendidikan_terakhir', 'pekerjaan_utama',
                'alamat_lengkap', 'telepon', 'email', 'sektor_pelayanan', 'unit_pelayanan',
                'tanggal_baptis_yyyymmdd', 'tempat_baptis', 'tanggal_sidi_yyyymmdd', 'tempat_sidi',
                'tanggal_masuk_jemaat_yyyymmdd', 'status_keanggotaan_aktif_tidak_aktif_pindah_meninggal',
                'asal_gereja_sebelumnya', 'nomor_atestasi', 'jabatan_pelayan_khusus', 'wadah_kategorial',
                'keterlibatan_lain', 'nama_kepala_keluarga', 'status_pekerjaan_kk', 'sektor_pekerjaan_kk',
                'status_kepemilikan_rumah', 'sumber_penerangan', 'sumber_air_minum',
                'perkiraan_pendapatan_keluarga', 'catatan'
             ];

             // PERBAIKAN: Gunakan FromArray untuk template kosong
             $fileName = 'template_import_anggota_jemaat.xlsx';
             $templateExport = new class($headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
                 private $headings;
                 public function __construct($headings) { $this->headings = $headings; }
                 public function array(): array { return []; } // Array kosong
                 public function headings(): array { return $this->headings; }
             };
             
             return Excel::download($templateExport, $fileName);
         }

        // 2. Export Data
        try {
            $fileName = 'anggota_jemaat_gpi_papua_' . date('YmdHis') . '.xlsx';
             $user = Auth::user();
             $jemaatId = $user->hasRole('Admin Jemaat') ? $user->jemaat_id : $request->query('jemaat_id');
             $klasisId = $user->hasRole('Admin Klasis') ? $user->klasis_id : $request->query('klasis_id');
             $search = $request->query('search');
             $nomorKkFilter = $request->query('nomor_kk_filter'); 

             $export = new AnggotaJemaatExport($search, $klasisId, $jemaatId, $nomorKkFilter);
             return Excel::download($export, $fileName);
        } catch (\Exception $e) {
             Log::error('Gagal export Anggota Jemaat: ' . $e->getMessage());
             return redirect()->route('admin.anggota-jemaat.index')
                              ->with('error', 'Gagal mengekspor data. Silakan coba lagi.');
        }
    }

    public function showImportForm()
    {
         return view('admin.anggota_jemaat.import');
    }

    public function import(Request $request)
    {
        $request->validate([ 'import_file' => 'required|file|mimes:xlsx,xls,csv', ]);
        $file = $request->file('import_file');

        try {
            $user = Auth::user();
            $jemaatIdConstraint = $user->hasRole('Admin Jemaat') ? $user->jemaat_id : null;
            $klasisIdConstraint = $user->hasRole('Admin Klasis') ? $user->klasis_id : null;

            $import = new AnggotaJemaatImport($jemaatIdConstraint, $klasisIdConstraint);
            Excel::import($import, $file);

            $failures = $import->failures();
            if ($failures->isNotEmpty()) {
                $errorCount = count($failures);
                $errorRows = [];
                foreach ($failures as $failure) {
                    $rowNum = $failure->row();
                    $errors = implode(', ', $failure->errors());
                    $errorRows[] = "Baris {$rowNum}: {$errors}";
                }
                $previewError = implode("\n", array_slice($errorRows, 0, 5));
                $more = $errorCount > 5 ? "\n... dan " . ($errorCount - 5) . " baris lainnya." : "";
                $errorMessage = "Import selesai sebagian. Terdapat {$errorCount} data gagal:\n{$previewError}{$more}";
                
                return redirect()->route('admin.anggota-jemaat.index')->with('warning', $errorMessage);
            }

            return redirect()->route('admin.anggota-jemaat.index')->with('success', 'Data Anggota Jemaat berhasil diimpor.');

        } catch (ValidationException $e) {
             return redirect()->back()->with('error', 'Validasi Gagal: ' . $e->getMessage())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}