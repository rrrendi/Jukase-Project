<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entitas: Penjualan_Manual (Tabel 1.5 No. 8)
     * Menyimpan data penjualan dari kanal non-website
     * (WhatsApp/Instagram/Facebook/Walk-in) yang dicatat Admin (F-10).
     * Stok berkurang otomatis dan omzet tercatat di laporan (F-11).
     */
    public function up(): void
    {
        Schema::create('manual_sales', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable(); // opsional sesuai F-10
            $table->enum('channel', ['WhatsApp', 'Instagram', 'Facebook', 'Walk-in']);
            $table->decimal('total', 12, 2)->default(0);
            $table->date('sale_date');
            $table->timestamps();

            $table->index(['sale_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_sales');
    }
};
