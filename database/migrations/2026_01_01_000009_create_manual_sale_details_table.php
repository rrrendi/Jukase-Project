<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entitas: Penjualan_Manual_Detail (Tabel 1.5 No. 9)
     * Menyimpan detail item pada setiap penjualan manual. 'cost_price'
     * diisi dari HPP (avg_cost) produk pada saat transaksi dicatat (F-12).
     */
    public function up(): void
    {
        Schema::create('manual_sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manual_sale_id')->constrained('manual_sales')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()
                ->constrained('products')->nullOnDelete();
            $table->string('product_name'); // snapshot nama produk
            $table->integer('quantity');
            $table->decimal('price', 12, 2);       // harga jual saat transaksi
            $table->decimal('cost_price', 12, 2);  // HPP saat transaksi (Moving Average)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_sale_details');
    }
};
