<?php

namespace Modules\Keuangan\Helpers;

/**
 * Class CurrencyFormatter
 *
 * Helper class untuk memformat angka ke format mata uang.
 */
class CurrencyFormatter
{
    /**
     * Format angka ke format Rupiah.
     */
    public static function rupiah(float|int $amount, bool $withSymbol = true): string
    {
        return format_rupiah($amount, $withSymbol);
    }

    /**
     * Format angka ke format Rupiah dengan warna (untuk Blade).
     * Mengembalikan class CSS berdasarkan tipe transaksi.
     */
    public static function colorClass(string $type): string
    {
        return match ($type) {
            'income' => 'text-green-600',
            'expense' => 'text-red-600',
            default => 'text-gray-900',
        };
    }

    /**
     * Format angka dengan tanda +/- berdasarkan tipe.
     */
    public static function signed(float|int $amount, string $type): string
    {
        $formatted = format_rupiah($amount);

        return match ($type) {
            'income' => '+' . $formatted,
            'expense' => '-' . $formatted,
            default => $formatted,
        };
    }
}
