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
use App\Http\Controllers\Admin\KlasisController;
use App\Http\Controllers\Admin\JemaatController;
use App\Http\Controllers\Admin\AnggotaJemaatController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MutasiPendetaController;
use App\Http\Controllers\Admin\PopupAdController; 
use App\Http\Controllers\Admin\LaporanRenstraController;

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

// Perbendaharaan & Aset Controllers
use App\Http\Controllers\Admin\AsetController;
use App\Http\Controllers\Admin\MataAnggaranController;
use App\Http\Controllers\Admin\AnggaranIndukController; 
use App\Http\Controllers\Admin\TransaksiIndukController;
use App\Http\Controllers\Admin\LaporanController;

// E-Office Controllers
use App\Http\Controllers\Admin\SuratMasukController;
use App\Http\Controllers\Admin\SuratKeluarController;

// Sakramen Controllers
use App\Http\Controllers\Admin\SakramenBaptisController;
use App\Http\Controllers\Admin\SakramenNikahController;
use App\Http\Controllers\Admin\SakramenSidiController; 
use App\Http\Controllers\Admin\SakramenCetakController;

// Pejabat & Sidang Controllers
use App\Http\Controllers\Admin\PejabatGerejawiController; 
use App\Http\Controllers\Admin\PersidanganController;

// Models
use App\Models\Setting;
use App\Models\Post;
use App\Models\Service;
use App\Models\Jemaat;
use App\Models\Klasis;

/*
|--------------------------------------------------------------------------
| Web Routes - GPI PAPUA ECOSYSTEM
|--------------------------------------------------------------------------
*/

/* --- RUTE PUBLIK --- */
Route::get('/', function () {
    try {
        $setting = Setting::firstOrCreate(['id' => 1]);
        $posts = Post::whereNotNull('published_at')
                     ->where('published_at', '<=', now())
                     ->latest('published_at')
                     ->take(3)
                     ->get();
        $services = Service::orderBy('order')->orderBy('created_at')->get();
    } catch (\Exception $e) {
        $setting = new Setting();
        $posts = collect();
        $services = collect();
    }
    return view('welcome', compact('setting', 'posts', 'services'));
})->name('home');

Route::get('/berita', [PostPublicController::class, 'index'])->name('posts.public.index');
Route::get('/berita/{slug}', [PostPublicController::class, 'show'])->name('posts.public.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

/* --- RUTE ADMIN (MEMERLUKAN LOGIN) --- */
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    // Redirect Root Admin ke Dashboard
    Route::get('/', function () { return redirect()->route('admin.dashboard'); });

    // 1. Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/peta-widget', [DashboardController::class, 'petaWidget'])->name('dashboard.peta_widget');

    // 2. Pengaturan, Konten Website & POPUP ADS
    Route::middleware('role:Super Admin|Admin Bidang 4')->group(function () {
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        
        Route::get('/messages', [MessageController::class, 'index'])->name('messages');
        Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
        
        Route::resource('posts', AdminPostController::class);
        Route::resource('services', ServiceController::class);

        Route::get('/popup-ads', [PopupAdController::class, 'index'])->name('popup.index');
        Route::post('/popup-ads', [PopupAdController::class, 'store'])->name('popup.store');
        Route::delete('/popup-ads/{popup}', [PopupAdController::class, 'destroy'])->name('popup.destroy');
        Route::patch('/popup-ads/{popup}/toggle', [PopupAdController::class, 'toggle'])->name('popup.toggle');
    });

    // 3. E-Office / Persuratan Digital
    Route::prefix('e-office')->name('e-office.')->group(function () {
        Route::resource('surat-masuk', SuratMasukController::class);
        Route::resource('surat-keluar', SuratKeluarController::class);
        Route::post('surat-masuk/{surat}/disposisi', [SuratMasukController::class, 'disposisi'])->name('surat-masuk.disposisi');
    });

    // 4. Administrasi Sakramen (Bidang 1)
    Route::prefix('sakramen')->name('sakramen.')->group(function () {
        // Baptis
        Route::get('baptis', [SakramenBaptisController::class, 'index'])->name('baptis.index');
        Route::get('baptis/create', [SakramenBaptisController::class, 'create'])->name('baptis.create');
        Route::post('baptis', [SakramenBaptisController::class, 'store'])->name('baptis.store');
        Route::get('baptis/{id}/edit', [SakramenBaptisController::class, 'edit'])->name('baptis.edit');
        Route::put('baptis/{id}', [SakramenBaptisController::class, 'update'])->name('baptis.update');
        Route::delete('baptis/{id}', [SakramenBaptisController::class, 'destroy'])->name('baptis.destroy');
        Route::get('baptis/{id}/cetak', [SakramenBaptisController::class, 'cetakSurat'])->name('baptis.cetak'); 

        // Sidi
        Route::get('sidi', [SakramenSidiController::class, 'index'])->name('sidi.index');
        Route::get('sidi/create', [SakramenSidiController::class, 'create'])->name('sidi.create');
        Route::post('sidi', [SakramenSidiController::class, 'store'])->name('sidi.store');
        Route::get('sidi/{id}/edit', [SakramenSidiController::class, 'edit'])->name('sidi.edit');
        Route::put('sidi/{id}', [SakramenSidiController::class, 'update'])->name('sidi.update');
        Route::delete('sidi/{id}', [SakramenSidiController::class, 'destroy'])->name('sidi.destroy');
        Route::get('sidi/{id}/cetak', [SakramenSidiController::class, 'cetakSurat'])->name('sidi.cetak'); 
        
        // Nikah
        Route::get('nikah', [SakramenNikahController::class, 'index'])->name('nikah.index');
        Route::get('nikah/create', [SakramenNikahController::class, 'create'])->name('nikah.create');
        Route::post('nikah', [SakramenNikahController::class, 'store'])->name('nikah.store');
        Route::get('nikah/{id}/edit', [SakramenNikahController::class, 'edit'])->name('nikah.edit');
        Route::put('nikah/{id}', [SakramenNikahController::class, 'update'])->name('nikah.update');
        Route::delete('nikah/{id}', [SakramenNikahController::class, 'destroy'])->name('nikah.destroy');
        Route::get('nikah/{id}/cetak', [SakramenNikahController::class, 'cetakSurat'])->name('nikah.cetak');
    });

    // 5. Pejabat & Persidangan (Bidang 1)
    Route::prefix('tata-gereja')->name('tata-gereja.')->group(function () {
        Route::resource('pejabat', PejabatGerejawiController::class);
        Route::resource('sidang', PersidanganController::class);
    });

    // 6. Data Master Wilayah & Jemaat
    Route::resource('klasis', KlasisController::class)->parameter('klasis', 'klasis');
    Route::get('klasis-export', [KlasisController::class, 'export'])->name('klasis.export');
    Route::get('klasis-import', [KlasisController::class, 'showImportForm'])->name('klasis.import-form');
    Route::post('klasis-import', [KlasisController::class, 'import'])->name('klasis.import');

    Route::resource('jemaat', JemaatController::class);
    Route::get('jemaat-export', [JemaatController::class, 'export'])->name('jemaat.export');
    Route::get('jemaat-import', [JemaatController::class, 'showImportForm'])->name('jemaat.import-form');
    Route::post('jemaat-import', [JemaatController::class, 'import'])->name('jemaat.import');

    // --- ANGGOTA JEMAAT ---
    Route::get('/anggota-jemaat/search', [AnggotaJemaatController::class, 'search'])->name('anggota-jemaat.search');
    Route::get('anggota-jemaat/{id}/cetak-kk', [AnggotaJemaatController::class, 'cetakKartuKeluarga'])->name('anggota-jemaat.cetak-kk');
    Route::resource('anggota-jemaat', AnggotaJemaatController::class);
    Route::get('anggota-jemaat-export', [AnggotaJemaatController::class, 'export'])->name('anggota-jemaat.export');
    Route::get('anggota-jemaat-import', [AnggotaJemaatController::class, 'showImportForm'])->name('anggota-jemaat.import-form');
    Route::post('anggota-jemaat-import', [AnggotaJemaatController::class, 'import'])->name('anggota-jemaat.import');

    // 7. Wadah Kategorial
    Route::prefix('wadah')->name('wadah.')->group(function () {
        Route::get('statistik/cetak', [WadahStatistikController::class, 'print'])->name('statistik.print');
        Route::get('statistik', [WadahStatistikController::class, 'index'])->name('statistik.index');
        Route::resource('pengurus', WadahKategorialPengurusController::class);
        Route::get('program/get-parents', [WadahProgramKerjaController::class, 'getParentPrograms'])->name('program.get-parents'); 
        Route::resource('program', WadahProgramKerjaController::class);
        Route::get('anggaran/get-programs', [WadahAnggaranController::class, 'getPrograms'])->name('anggaran.get-programs');
        Route::resource('anggaran', WadahAnggaranController::class);
        Route::post('transaksi', [WadahTransaksiController::class, 'store'])->name('transaksi.store');
        Route::delete('transaksi/{transaksi}', [WadahTransaksiController::class, 'destroy'])->name('transaksi.destroy');
    });

    // 8. KEPEGAWAIAN / HRIS
    Route::prefix('kepegawaian')->name('kepegawaian.')
        ->middleware(['auth'])
        ->group(function () {
            
            Route::resource('pegawai', PegawaiController::class);
            Route::get('pegawai/{pegawai}/print', [PegawaiController::class, 'print'])->name('pegawai.print');
            
            Route::middleware('role:Super Admin|Admin Bidang 3')->group(function(){
                Route::get('pegawai-export', [PegawaiController::class, 'export'])->name('pegawai.export');
                Route::get('pegawai-import', [PegawaiController::class, 'showImportForm'])->name('pegawai.import-form');
                Route::post('pegawai-import', [PegawaiController::class, 'import'])->name('pegawai.import');
            });

            Route::post('keluarga', [KeluargaPegawaiController::class, 'store'])->name('keluarga.store');
            Route::put('keluarga/{keluarga}', [KeluargaPegawaiController::class, 'update'])->name('keluarga.update');
            Route::delete('keluarga/{keluarga}', [KeluargaPegawaiController::class, 'destroy'])->name('keluarga.destroy');
            
            Route::post('pendidikan', [RiwayatPendidikanController::class, 'store'])->name('pendidikan.store');
            Route::put('pendidikan/{pendidikan}', [RiwayatPendidikanController::class, 'update'])->name('pendidikan.update');
            Route::delete('pendidikan/{pendidikan}', [RiwayatPendidikanController::class, 'destroy'])->name('pendidikan.destroy');
            
            Route::post('sk', [RiwayatSkController::class, 'store'])->name('sk.store');
            Route::put('sk/{sk}', [RiwayatSkController::class, 'update'])->name('sk.update');
            Route::delete('sk/{sk}', [RiwayatSkController::class, 'destroy'])->name('sk.destroy');

            Route::get('pegawai/{pegawai}/mutasi/create', [MutasiPendetaController::class, 'create'])->name('pegawai.mutasi.create');
            Route::post('pegawai/{pegawai}/mutasi', [MutasiPendetaController::class, 'store'])->name('pegawai.mutasi.store');
        });

    Route::middleware('role:Super Admin|Admin Bidang 3')->group(function () {
        Route::resource('mutasi', MutasiPendetaController::class)
             ->except(['create', 'store'])
             ->parameters(['mutasi' => 'mutasiPendeta']);
    });

    // 9. Perbendaharaan, Aset & Keuangan
    Route::prefix('perbendaharaan')->name('perbendaharaan.')->group(function () {
        Route::resource('aset', AsetController::class);
        Route::resource('mata-anggaran', MataAnggaranController::class);
        Route::resource('anggaran', AnggaranIndukController::class); 
        Route::resource('transaksi', TransaksiIndukController::class);
        
        Route::get('laporan/realisasi', [LaporanController::class, 'realisasi'])->name('laporan.realisasi');
        Route::get('laporan/aset', [LaporanController::class, 'aset'])->name('laporan.aset');
        Route::get('laporan/gabungan', [LaporanController::class, 'gabungan'])->name('laporan.gabungan');
    });

    // 10. Manajemen User
    Route::resource('users', UserController::class)->middleware('role:Super Admin');

    // 11. Laporan Renstra (BARU)
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('renstra', [LaporanRenstraController::class, 'index'])->name('renstra.index');
        Route::get('renstra/cetak', [LaporanRenstraController::class, 'cetakPdf'])->name('renstra.print');
    });

});

/* --- RUTE PROFIL & API --- */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/api/jemaat-by-klasis/{klasisId}', function ($klasisId) {
    $jemaat = Jemaat::where('klasis_id', $klasisId)->orderBy('nama_jemaat')->select('id', 'nama_jemaat')->get();
    return response()->json($jemaat);
})->name('api.jemaat.by.klasis');

require __DIR__.'/auth.php';