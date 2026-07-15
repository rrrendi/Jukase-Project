<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entitas: Pesanan_Detail (Tabel 1.5 No. 7)
     * Menyimpan detail item pada setiap pesanan (relasi ke pesanan dan
     * produk, jumlah, harga satuan). 'product_name' & 'price' adalah
     * snapshot saat pesanan dibuat. 'cost_price' (HPP) diisi saat admin
     * menyetujui pesanan (F-11, F-12) agar laporan keuangan akurat
     * sesuai HPP pada saat stok benar-benar berkurang.
     */
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()
                ->constrained('products')->nullOnDelete();
            $table->string('product_name');           // snapshot nama produk
            $table->integer('quantity');
            $table->decimal('price', 12, 2);           // snapshot harga jual saat pesan
            $table->decimal('cost_price', 12, 2)->nullable(); // snapshot HPP saat disetujui
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
