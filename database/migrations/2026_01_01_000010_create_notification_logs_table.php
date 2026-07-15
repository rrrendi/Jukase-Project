<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entitas: Notifikasi_Log (Tabel 1.5 No. 10)
     * Menyimpan log pengiriman notifikasi WhatsApp (jenis: pesanan
     * baru/konfirmasi, nomor tujuan, isi pesan, status kirim, waktu kirim)
     * sesuai integrasi WhatsApp Gateway Fonnte (F-05, F-07).
     */
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['pesanan_baru', 'konfirmasi_pesanan']);
            $table->string('recipient');      // nomor WhatsApp tujuan
            $table->text('message');          // isi pesan terkirim
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('response')->nullable(); // response mentah dari Fonnte
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
