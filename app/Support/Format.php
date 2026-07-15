<?php

namespace App\Support;

class Format
{
    /**
     * Nama bulan singkat berbahasa Indonesia, dipakai oleh tanggal()
     * dan tanggalSingkat() agar konsisten di seluruh aplikasi.
     */
    private static array $bulanSingkat = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    /**
     * Format angka menjadi mata uang Rupiah, mis. 1499000 -> "Rp1.499.000".
     */
    public static function rupiah(int|float|string|null $amount): string
    {
        $amount = (float) ($amount ?? 0);

        return 'Rp'.number_format($amount, 0, ',', '.');
    }

    /**
     * Format tanggal Indonesia singkat, mis. "31 Mei 2026".
     */
    public static function tanggal(\DateTimeInterface|string|null $date): string
    {
        $date = self::toDateTime($date);

        if (! $date) {
            return '-';
        }

        return $date->format('d').' '.self::$bulanSingkat[(int) $date->format('n')].' '.$date->format('Y');
    }

    /**
     * Format tanggal pendek tanpa tahun, mis. "31 Mei". Digunakan untuk
     * label pada grafik penjualan Dashboard (F-14).
     */
    public static function tanggalSingkat(\DateTimeInterface|string|null $date): string
    {
        $date = self::toDateTime($date);

        if (! $date) {
            return '-';
        }

        return ((int) $date->format('d')).' '.self::$bulanSingkat[(int) $date->format('n')];
    }

    private static function toDateTime(\DateTimeInterface|string|null $date): ?\DateTimeInterface
    {
        if (! $date) {
            return null;
        }

        return is_string($date) ? new \DateTime($date) : $date;
    }
}
