<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entitas: Produk (Tabel 1.5 No. 3)
     * Menyimpan data produk sepatu: kategori, merek, model, ukuran, warna,
     * harga jual, HPP rata-rata (Moving Average - F-12), jumlah stok,
     * batas stok minimum (F-15), foto, dan status aktif/non-aktif (F-08).
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()
                ->constrained('categories')->nullOnDelete();
            $table->string('brand');           // Merek, mis. "Nike"
            $table->string('name');            // Nama model, mis. "Air Force 1 '07"
            $table->string('size_range')->nullable();   // Ukuran, mis. "39-44"
            $table->string('color')->nullable();        // Warna
            $table->decimal('price', 12, 2)->default(0);     // Harga jual
            $table->decimal('avg_cost', 12, 2)->default(0);  // HPP rata-rata (Moving Average)
            $table->integer('stock')->default(0);           // Jumlah stok saat ini
            $table->integer('min_stock')->default(5);       // Batas stok minimum (F-15)
            $table->string('image')->nullable();            // Path foto produk
            $table->boolean('is_active')->default(true);    // Status aktif di katalog (F-08)
            $table->timestamps();

            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
