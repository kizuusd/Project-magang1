<?php

/**
 * Helper functions untuk Modul Keuangan.
 *
 * Fungsi-fungsi prosedural global yang dapat digunakan
 * di seluruh aplikasi untuk keperluan modul keuangan.
 */

if (!function_exists('keuangan_meta')) {
    /**
     * Mendapatkan metadata modul keuangan.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function keuangan_meta(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return config('keuangan');
        }

        return config("keuangan.{$key}", $default);
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format angka ke format Rupiah.
     *
     * @param float|int $amount
     * @param bool $withSymbol
     * @return string
     */
    function format_rupiah(float|int $amount, bool $withSymbol = true): string
    {
        $config = config('keuangan.currency');

        $formatted = number_format(
            abs($amount),
            $config['decimal_places'],
            $config['decimal_separator'],
            $config['thousands_separator']
        );

        $prefix = $amount < 0 ? '-' : '';

        if ($withSymbol) {
            return $prefix . $config['symbol'] . ' ' . $formatted;
        }

        return $prefix . $formatted;
    }
}

if (!function_exists('keuangan_view')) {
    /**
     * Mendapatkan nama view dengan namespace modul.
     *
     * @param string $view
     * @return string
     */
    function keuangan_view(string $view): string
    {
        return "keuangan::{$view}";
    }
}
