<?php

/**
 * Bootstrap file untuk Modul Keuangan.
 *
 * File ini bertanggung jawab untuk menginisialisasi modul
 * dan mendaftarkan Service Provider ke aplikasi Laravel.
 */

use Modules\Keuangan\Providers\KeuanganServiceProvider;

return function ($app) {
    $app->register(KeuanganServiceProvider::class);
};
