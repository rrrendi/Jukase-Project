<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migrasi DATA (bukan skema): memindahkan foto utama lama di kolom
     * products.image ke tabel product_images, supaya sistem hanya punya
     * SATU sumber foto produk (F-02, F-08) - lihat juga perubahan pada
     * Product::getImageUrlAttribute() yang sekarang selalu mengambil dari
     * foto pertama (sort_order terkecil) di relasi images().
     *
     * Foto lama dimasukkan dengan sort_order lebih kecil dari foto galeri
     * yang sudah ada, supaya tetap tampil sebagai sampul di katalog.
     * Kolom products.image sendiri TIDAK dihapus di sini (aman untuk
     * rollback / audit), tinggal berhenti dipakai oleh aplikasi.
     */
    public function up(): void
    {
        $products = DB::table('products')
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->get();

        foreach ($products as $product) {
            $alreadyMigrated = DB::table('product_images')
                ->where('product_id', $product->id)
                ->where('path', $product->image)
                ->exists();

            if ($alreadyMigrated) {
                continue;
            }

            $minSort = DB::table('product_images')
                ->where('product_id', $product->id)
                ->min('sort_order');

            DB::table('product_images')->insert([
                'product_id' => $product->id,
                'path' => $product->image,
                'sort_order' => is_null($minSort) ? 0 : $minSort - 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Migrasi data satu arah - sengaja tidak dibalik otomatis supaya
        // foto yang sudah dipindah ke product_images tidak hilang saat rollback.
    }
};