<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Mengambil satu nilai konfigurasi berdasarkan key.
     * Mengembalikan $default jika key belum diatur (F-15).
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $row = static::query()->where('key', $key)->first();

        return $row?->value ?? $default;
    }

    /**
     * Menyimpan / memperbarui satu nilai konfigurasi (F-15).
     */
    public static function set(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Mengambil beberapa konfigurasi sekaligus sebagai array
     * asosiatif [key => value], mempertahankan urutan $keys
     * dan mengisi null untuk key yang belum ada di database.
     */
    public static function getMany(array $keys): array
    {
        $rows = static::query()->whereIn('key', $keys)->pluck('value', 'key');

        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $rows[$key] ?? null;
        }

        return $result;
    }
}
