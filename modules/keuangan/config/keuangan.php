<?php

/**
 * Konfigurasi Modul Keuangan.
 *
 * File ini berisi konfigurasi spesifik modul yang dapat
 * diakses melalui config('keuangan.key').
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Nama Modul
    |--------------------------------------------------------------------------
    */
    'name' => 'Keuangan Pribadi',

    /*
    |--------------------------------------------------------------------------
    | Versi Modul
    |--------------------------------------------------------------------------
    */
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Mata Uang
    |--------------------------------------------------------------------------
    | Konfigurasi format mata uang yang digunakan dalam modul.
    */
    'currency' => [
        'code' => 'IDR',
        'symbol' => 'Rp',
        'decimal_separator' => ',',
        'thousands_separator' => '.',
        'decimal_places' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipe Kategori
    |--------------------------------------------------------------------------
    | Daftar tipe kategori yang diizinkan.
    */
    'category_types' => [
        'income' => 'Pemasukan',
        'expense' => 'Pengeluaran',
    ],
];
