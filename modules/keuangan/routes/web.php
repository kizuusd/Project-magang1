<?php

use Illuminate\Support\Facades\Route;
use Modules\Keuangan\Http\Controllers\Web\CategoryController;
use Modules\Keuangan\Http\Controllers\Web\DashboardController;

/*
|--------------------------------------------------------------------------
| Routes Modul Keuangan
|--------------------------------------------------------------------------
|
| Semua route untuk fitur keuangan didefinisikan di sini.
| Route ini diload otomatis oleh KeuanganServiceProvider.
|
*/

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
});
