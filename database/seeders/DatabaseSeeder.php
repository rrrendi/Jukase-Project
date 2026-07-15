<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan eksekusi penting karena ada relasi foreign key:
     * Kategori & Supplier -> Produk -> Stok_Masuk -> Pesanan/Penjualan_Manual.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,
            ConfigurationSeeder::class,
            ProductSeeder::class,
            DemoTransactionSeeder::class,
        ]);
    }
}
