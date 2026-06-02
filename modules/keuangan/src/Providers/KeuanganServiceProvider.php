<?php

namespace Modules\Keuangan\Providers;

use Illuminate\Support\ServiceProvider;

class KeuanganServiceProvider extends ServiceProvider
{
    /**
     * Register any module services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/keuangan.php',
            'keuangan'
        );
    }

    /**
     * Bootstrap any module services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');

        $this->loadViewsFrom(
            __DIR__ . '/../../resources/views',
            'keuangan'
        );

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
