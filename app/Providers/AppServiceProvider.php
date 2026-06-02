<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Keuangan\Providers\KeuanganServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(KeuanganServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
