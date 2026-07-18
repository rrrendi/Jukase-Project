<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/**
 * "Ingatan" browser/HP terhadap pesanan yang pernah dibuat atau berhasil
 * dilacak dari perangkat itu, supaya pelanggan bisa lihat riwayat
 * pesanannya tanpa perlu input kode + WhatsApp setiap kali (selama masih
 * di HP/browser yang sama).
 *
 * Disimpan sebagai cookie terenkripsi & ditandatangani oleh Laravel
 * (EncryptCookies middleware bawaan) — pelanggan/orang lain TIDAK bisa
 * mengedit isi cookie ini untuk "menyusupkan" kode pesanan milik orang
 * lain; kalau cookie dipalsukan/rusak, Laravel otomatis menganggapnya
 * kosong, bukan error.
 */
class OrderDeviceMemory
{
    protected const COOKIE_NAME = 'jk_orders';

    protected const MAX_REMEMBERED = 20;

    protected const MINUTES = 60 * 24 * 365; // ~1 tahun

    /**
     * Daftar kode pesanan yang "dikenali" perangkat ini.
     */
    public static function codes(Request $request): array
    {
        $raw = $request->cookie(self::COOKIE_NAME);
        $codes = $raw ? json_decode($raw, true) : null;

        return is_array($codes) ? array_values(array_unique($codes)) : [];
    }

    public static function remember(Request $request, string $orderCode): void
    {
        self::rememberMany($request, [$orderCode]);
    }

    /**
     * Tambahkan beberapa kode pesanan sekaligus ke "ingatan" perangkat
     * ini. Dipanggil setelah checkout berhasil, ATAU setelah pencarian
     * manual (kode+WA / WA saja) berhasil menemukan pesanan.
     */
    public static function rememberMany(Request $request, array $orderCodes): void
    {
        if (empty($orderCodes)) {
            return;
        }

        $codes = array_diff(self::codes($request), $orderCodes);
        $codes = array_merge($codes, array_values($orderCodes));
        $codes = array_slice(array_values($codes), -self::MAX_REMEMBERED);

        Cookie::queue(self::COOKIE_NAME, json_encode($codes), self::MINUTES);
    }
}