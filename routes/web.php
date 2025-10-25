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
use App\Http\Controllers\Admin\DashboardController; // <-- Controller dashboard diimpor

// Models
use App\Models\Setting;
use App\Models\Post;
use App\Models\Service;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/* Rute Publik */
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

/* Rute Admin */
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    // Dashboard menggunakan Controller
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pengaturan Situs
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings')->middleware('role:Super Admin|Admin Bidang 4');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update')->middleware('role:Super Admin|Admin Bidang 4');

    // Pesan Masuk
    Route::get('/messages', [MessageController::class, 'index'])->name('messages')->middleware('role:Super Admin|Admin Bidang 4');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show')->middleware('role:Super Admin|Admin Bidang 4');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy')->middleware('role:Super Admin|Admin Bidang 4');

    // Berita & Kegiatan
    Route::resource('posts', AdminPostController::class)->middleware('role:Super Admin|Admin Bidang 4');

    // Layanan
    Route::resource('services', ServiceController::class)->middleware('role:Super Admin|Admin Bidang 4');

    // CRUD Pendeta
    Route::resource('pendeta', PendetaController::class);
    Route::get('pendeta-export', [PendetaController::class, 'export'])->name('pendeta.export')->middleware('role:Super Admin|Admin Bidang 3');
    Route::get('pendeta-import', [PendetaController::class, 'showImportForm'])->name('pendeta.import-form')->middleware('role:Super Admin|Admin Bidang 3');
    Route::post('pendeta-import', [PendetaController::class, 'import'])->name('pendeta.import')->middleware('role:Super Admin|Admin Bidang 3');

    // CRUD Klasis
    Route::resource('klasis', KlasisController::class);
    Route::get('klasis-export', [KlasisController::class, 'export'])->name('klasis.export')->middleware('role:Super Admin|Admin Bidang 3');
    Route::get('klasis-import', [KlasisController::class, 'showImportForm'])->name('klasis.import-form')->middleware('role:Super Admin|Admin Bidang 3');
    Route::post('klasis-import', [KlasisController::class, 'import'])->name('klasis.import')->middleware('role:Super Admin|Admin Bidang 3');

    // CRUD Jemaat
    Route::resource('jemaat', JemaatController::class);
    Route::get('jemaat-export', [JemaatController::class, 'export'])->name('jemaat.export')->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis');
    Route::get('jemaat-import', [JemaatController::class, 'showImportForm'])->name('jemaat.import-form')->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis');
    Route::post('jemaat-import', [JemaatController::class, 'import'])->name('jemaat.import')->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis');

    // CRUD Anggota Jemaat
    Route::resource('anggota-jemaat', AnggotaJemaatController::class);
    Route::get('anggota-jemaat-export', [AnggotaJemaatController::class, 'export'])->name('anggota-jemaat.export')->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis|Admin Jemaat');
    Route::get('anggota-jemaat-import', [AnggotaJemaatController::class, 'showImportForm'])->name('anggota-jemaat.import-form')->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis|Admin Jemaat');
    Route::post('anggota-jemaat-import', [AnggotaJemaatController::class, 'import'])->name('anggota-jemaat.import')->middleware('role:Super Admin|Admin Bidang 3|Admin Klasis|Admin Jemaat');

    // CRUD User Management
    Route::resource('users', UserController::class)->middleware('role:Super Admin');

    // Contoh Rute Modul Bidang
    // ...

}); // Akhir Grup Admin

/* Rute Autentikasi */
require __DIR__.'/auth.php';

/* Rute Fallback */
// ...