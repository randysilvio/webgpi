<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\WadahKategorialTransaksi; // <-- Import Model Transaksi
use App\Observers\WadahTransaksiObserver; // <-- Import Observer

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mendaftarkan Observer agar realisasi anggaran terupdate otomatis saat ada transaksi
        WadahKategorialTransaksi::observe(WadahTransaksiObserver::class);
    }
}