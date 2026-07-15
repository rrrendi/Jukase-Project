<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entitas: Pesanan (Tabel 1.5 No. 6)
     * Menyimpan data pesanan dari website (nama pelanggan, alamat, nomor
     * WhatsApp, total bayar, bukti pembayaran, status pending/approved/
     * rejected, tanggal). Guest checkout - tanpa akun pelanggan (F-03, F-04, F-06).
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->nullable()->unique(); // mis. JKS-2041
            $table->string('customer_name');
            $table->text('address');
            $table->string('whatsapp');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('payment_proof')->nullable(); // path bukti transfer (F-04)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // F-06
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
