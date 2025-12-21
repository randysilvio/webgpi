<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// Public Controllers
use App\Http\Controllers\PostPublicController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;

// Admin Controllers
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PendetaController;
use App\Http\Controllers\Admin\KlasisController;
use App\Http\Controllers\Admin\JemaatController;
use App\Http\Controllers\Admin\AnggotaJemaatController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MutasiPendetaController;

// Wadah Kategorial Controllers
use App\Http\Controllers\Admin\WadahKategorialPengurusController;
use App\Http\Controllers\Admin\WadahStatistikController;
use App\Http\Controllers\Admin\WadahProgramKerjaController;
use App\Http\Controllers\Admin\WadahAnggaranController;
use App\Http\Controllers\Admin\WadahTransaksiController;

// HRIS / Kepegawaian Controllers
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\KeluargaPegawaiController;
use App\Http\Controllers\Admin\RiwayatPendidikanController;
use App\Http\Controllers\Admin\RiwayatSkController;

// Perbendaharaan & Aset Controllers (Fase 7)
use App\Http\Controllers\Admin\AsetController;
use App\Http\Controllers\Admin\MataAnggaranController;
use App\Http\Controllers\Admin\AnggaranIndukController; 
use App\Http\Controllers\Admin\TransaksiIndukController;
use App\Http\Controllers\Admin\LaporanController; // Tambahan: Controller Laporan

// Models
use App\Models\Setting;
use App\Models\Post;
use App\Models\Service;
use App\Models\Jemaat;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/* --- RUTE PUBLIK --- */
Route::get('/', function () {
    $setting = Setting::firstOrCreate(['id' => 1]);
    $posts = Post::whereNotNull('published_at')
                 ->where('published_at', '<=', now())
                 ->latest('published_at')
                 ->take(3)
                 ->get();
    $services = Service::orderBy('order')->orderBy('created_at')->get();
    return view('welcome', compact('setting', 'posts', 'services'));
})->name('home');

Route::get('/berita', [PostPublicController::class, 'index'])->name('posts.public.index');
Route::get('/berita/{slug}', [PostPublicController::class, 'show'])->name('posts.public.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

/* --- RUTE ADMIN (MEMERLUKAN LOGIN) --- */
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    // 1. Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Pengaturan & Konten Website (Role: Super Admin | Admin Bidang 4)
    Route::middleware('role:Super Admin|Admin Bidang 4')->group(function () {
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        
        Route::get('/messages', [MessageController::class, 'index'])->name('messages');
        Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
        
        Route::resource('posts', AdminPostController::class);
        Route::resource('services', ServiceController::class);
    });

    // 3. Data Master Wilayah & Jemaat
    Route::resource('klasis', KlasisController::class)->parameter('klasis', 'klasis');
    Route::get('klasis-export', [KlasisController::class, 'export'])->name('klasis.export')->middleware('can:export klasis');
    Route::get('klasis-import', [KlasisController::class, 'showImportForm'])->name('klasis.import-form')->middleware('can:import klasis');
    Route::post('klasis-import', [KlasisController::class, 'import'])->name('klasis.import')->middleware('can:import klasis');

    Route::resource('jemaat', JemaatController::class);
    Route::get('jemaat-export', [JemaatController::class, 'export'])->name('jemaat.export')->middleware('can:export jemaat');
    Route::get('jemaat-import', [JemaatController::class, 'showImportForm'])->name('jemaat.import-form')->middleware('can:import jemaat');
    Route::post('jemaat-import', [JemaatController::class, 'import'])->name('jemaat.import')->middleware('can:import jemaat');

    Route::resource('anggota-jemaat', AnggotaJemaatController::class);
    Route::get('anggota-jemaat-export', [AnggotaJemaatController::class, 'export'])->name('anggota-jemaat.export')->middleware('can:export anggota jemaat');
    Route::get('anggota-jemaat-import', [AnggotaJemaatController::class, 'showImportForm'])->name('anggota-jemaat.import-form')->middleware('can:import anggota jemaat');
    Route::post('anggota-jemaat-import', [AnggotaJemaatController::class, 'import'])->name('anggota-jemaat.import')->middleware('can:import anggota jemaat');

    // 4. Manajemen Pendeta (Legacy & Mutasi)
    Route::resource('pendeta', PendetaController::class)->parameter('pendeta', 'pendeta');
    Route::get('pendeta-export', [PendetaController::class, 'export'])->name('pendeta.export')->middleware('can:export pendeta');
    Route::get('pendeta-import', [PendetaController::class, 'showImportForm'])->name('pendeta.import-form')->middleware('can:import pendeta');
    Route::post('pendeta-import', [PendetaController::class, 'import'])->name('pendeta.import')->middleware('can:import pendeta');

    Route::middleware('role:Super Admin|Admin Bidang 3')->group(function () {
        Route::get('pendeta/{pendeta}/mutasi/create', [MutasiPendetaController::class, 'create'])->name('pendeta.mutasi.create');
        Route::post('pendeta/{pendeta}/mutasi', [MutasiPendetaController::class, 'store'])->name('pendeta.mutasi.store');
        Route::resource('mutasi', MutasiPendetaController::class)->except(['create', 'store'])->parameters(['mutasi' => 'mutasiPendeta']);
    });

    // 5. Wadah Kategorial
    Route::prefix('wadah')->name('wadah.')->group(function () {
        Route::get('statistik', [WadahStatistikController::class, 'index'])->name('statistik.index');
        Route::resource('pengurus', WadahKategorialPengurusController::class);
        
        Route::get('program/get-parents', [WadahProgramKerjaController::class, 'getParentPrograms'])->name('program.get-parents'); 
        Route::resource('program', WadahProgramKerjaController::class);
        
        Route::get('anggaran/get-programs', [WadahAnggaranController::class, 'getPrograms'])->name('anggaran.get-programs');
        Route::resource('anggaran', WadahAnggaranController::class);
        Route::post('transaksi', [WadahTransaksiController::class, 'store'])->name('transaksi.store');
        Route::delete('transaksi/{transaksi}', [WadahTransaksiController::class, 'destroy'])->name('transaksi.destroy');
    });

    // 6. Kepegawaian / HRIS (Fase 6)
    Route::prefix('kepegawaian')->name('kepegawaian.')->group(function () {
        Route::get('pegawai/{pegawai}/print', [PegawaiController::class, 'print'])->name('pegawai.print');
        Route::resource('pegawai', PegawaiController::class);
        
        // Data Pendukung Pegawai
        Route::post('keluarga', [KeluargaPegawaiController::class, 'store'])->name('keluarga.store');
        Route::put('keluarga/{keluarga}', [KeluargaPegawaiController::class, 'update'])->name('keluarga.update');
        Route::delete('keluarga/{keluarga}', [KeluargaPegawaiController::class, 'destroy'])->name('keluarga.destroy');
        
        Route::post('pendidikan', [RiwayatPendidikanController::class, 'store'])->name('pendidikan.store');
        Route::put('pendidikan/{pendidikan}', [RiwayatPendidikanController::class, 'update'])->name('pendidikan.update');
        Route::delete('pendidikan/{pendidikan}', [RiwayatPendidikanController::class, 'destroy'])->name('pendidikan.destroy');
        
        Route::post('sk', [RiwayatSkController::class, 'store'])->name('sk.store');
        Route::put('sk/{sk}', [RiwayatSkController::class, 'update'])->name('sk.update');
        Route::delete('sk/{sk}', [RiwayatSkController::class, 'destroy'])->name('sk.destroy');
    });

    // 7. Perbendaharaan & Aset (Fase 7)
    Route::prefix('perbendaharaan')->name('perbendaharaan.')->group(function () {
        Route::resource('aset', AsetController::class);
        Route::resource('mata-anggaran', MataAnggaranController::class);
        Route::resource('anggaran', AnggaranIndukController::class); 
        Route::resource('transaksi', TransaksiIndukController::class);
        
        // Rute Laporan (Penyesuaian Terbaru)
        Route::get('laporan/realisasi', [LaporanController::class, 'realisasi'])->name('laporan.realisasi');
        Route::get('laporan/aset', [LaporanController::class, 'aset'])->name('laporan.aset');
    });

    // 8. Manajemen User (Hanya Super Admin)
    Route::resource('users', UserController::class)->middleware('role:Super Admin');

});

/* --- RUTE PROFIL PENGGUNA --- */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* --- API DROPDOWN DINAMIS --- */
Route::get('/api/jemaat-by-klasis/{klasisId}', function ($klasisId) {
    if (!ctype_digit((string)$klasisId)) { return response()->json([], 400); }
    $jemaat = Jemaat::where('klasis_id', $klasisId)->orderBy('nama_jemaat')->select('id', 'nama_jemaat')->get();
    return response()->json($jemaat);
})->name('api.jemaat.by.klasis');

/* --- AUTENTIKASI --- */
require __DIR__.'/auth.php';