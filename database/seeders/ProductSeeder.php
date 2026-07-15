<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Data contoh produk untuk keperluan demo, mengikuti contoh data
     * pada prototipe UI. Produk dengan stok awal > 0 dicatat melalui
     * Stok_Masuk lalu dihitung HPP-nya menggunakan Moving Average
     * (F-09, F-12), sehingga riwayat stok masuk juga ikut terisi.
     *
     * Dua produk (New Balance 550 & Converse Chuck 70) sengaja
     * dibuat dengan stok 0 untuk mendemonstrasikan badge "Habis" (F-02)
     * pada katalog dan peringatan stok kritis pada dashboard (F-14).
     * Nilai HPP pada kedua produk ini diisi langsung sebagai referensi
     * harga modal terakhir (belum ada Stok_Masuk tercatat).
     */
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');
        $suppliers = Supplier::pluck('id')->values();

        $products = [
            ['brand' => 'Nike', 'name' => "Air Force 1 '07", 'category' => 'Sneakers', 'size_range' => '39-44', 'color' => 'Putih', 'price' => 1499000, 'cost' => 1180000, 'stock' => 8, 'days_ago' => 22],
            ['brand' => 'Adidas', 'name' => 'Samba OG', 'category' => 'Casual', 'size_range' => '40-45', 'color' => 'Putih/Hijau', 'price' => 1899000, 'cost' => 1520000, 'stock' => 3, 'days_ago' => 9],
            ['brand' => 'New Balance', 'name' => '550 White Green', 'category' => 'Sneakers', 'size_range' => '40-44', 'color' => 'Putih/Hijau', 'price' => 2150000, 'cost' => 1740000, 'stock' => 0, 'days_ago' => null],
            ['brand' => 'Nike', 'name' => 'Pegasus 41', 'category' => 'Sport', 'size_range' => '39-45', 'color' => 'Hitam', 'price' => 2099000, 'cost' => 1690000, 'stock' => 12, 'days_ago' => 26],
            ['brand' => 'Vans', 'name' => 'Old Skool Black', 'category' => 'Casual', 'size_range' => '38-44', 'color' => 'Hitam/Putih', 'price' => 899000, 'cost' => 680000, 'stock' => 15, 'days_ago' => 19],
            ['brand' => 'Adidas', 'name' => 'Ultraboost Light', 'category' => 'Sport', 'size_range' => '40-45', 'color' => 'Hitam', 'price' => 2799000, 'cost' => 2300000, 'stock' => 2, 'days_ago' => 14],
            ['brand' => 'Dr. Martens', 'name' => '1460 Boots', 'category' => 'Boots', 'size_range' => '39-44', 'color' => 'Hitam', 'price' => 2450000, 'cost' => 1980000, 'stock' => 5, 'days_ago' => 17],
            ['brand' => 'Converse', 'name' => 'Chuck 70 High', 'category' => 'Casual', 'size_range' => '38-43', 'color' => 'Hitam', 'price' => 1199000, 'cost' => 920000, 'stock' => 0, 'days_ago' => null],
            ['brand' => 'Nike', 'name' => 'Dunk Low Panda', 'category' => 'Sneakers', 'size_range' => '40-44', 'color' => 'Hitam/Putih', 'price' => 1799000, 'cost' => 1430000, 'stock' => 6, 'days_ago' => 12],
            ['brand' => 'Salomon', 'name' => 'XT-6 Trail', 'category' => 'Sport', 'size_range' => '40-45', 'color' => 'Abu-abu', 'price' => 3199000, 'cost' => 2600000, 'stock' => 4, 'days_ago' => 11],
            ['brand' => 'Timberland', 'name' => '6-Inch Premium', 'category' => 'Boots', 'size_range' => '40-45', 'color' => 'Cokelat', 'price' => 2890000, 'cost' => 2350000, 'stock' => 7, 'days_ago' => 24],
            ['brand' => 'Adidas', 'name' => 'Gazelle Indoor', 'category' => 'Casual', 'size_range' => '39-44', 'color' => 'Hijau Tua', 'price' => 1699000, 'cost' => 1340000, 'stock' => 9, 'days_ago' => 28],
        ];

        foreach ($products as $i => $data) {
            $product = Product::updateOrCreate(
                ['brand' => $data['brand'], 'name' => $data['name']],
                [
                    'category_id' => $categories[$data['category']] ?? null,
                    'size_range' => $data['size_range'],
                    'color' => $data['color'],
                    'price' => $data['price'],
                    'avg_cost' => $data['stock'] > 0 ? 0 : $data['cost'],
                    'stock' => 0,
                    'min_stock' => 5,
                    'is_active' => true,
                ]
            );

            if ($data['stock'] > 0) {
                $supplierId = $suppliers->count() ? $suppliers[$i % $suppliers->count()] : null;

                StockIn::create([
                    'product_id' => $product->id,
                    'supplier_id' => $supplierId,
                    'quantity' => $data['stock'],
                    'unit_cost' => $data['cost'],
                    'date' => now()->subDays($data['days_ago'])->toDateString(),
                ]);

                // Stok awal 0 -> setelah applyStockIn(), avg_cost = harga modal & stock = qty masuk.
                $product->applyStockIn($data['stock'], $data['cost']);
            }
        }
    }
}
