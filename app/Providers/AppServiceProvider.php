<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Share store info ke semua view (Blade) untuk navbar & footer
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('store_settings')) {
                $store = \App\Models\StoreSetting::current();
                \Illuminate\Support\Facades\View::share('storeInfo', $store);
            }
        } catch (\Throwable $e) {
            // abaikan saat migrasi belum jalan
        }
    }
}
