<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entitas: Stok_Masuk (Tabel 1.5 No. 5)
     * Menyimpan riwayat stok masuk dari supplier (relasi ke produk dan supplier,
     * jumlah, harga modal/satuan, tanggal). Menjadi dasar perhitungan
     * HPP Moving Average (F-09, F-12).
     */
    public function up(): void
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()
                ->constrained('suppliers')->nullOnDelete();
            $table->integer('quantity');             // Jumlah masuk
            $table->decimal('unit_cost', 12, 2);     // Harga modal per satuan saat stok masuk ini
            $table->date('date');                    // Tanggal stok masuk
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};
