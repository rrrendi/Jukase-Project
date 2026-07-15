<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entitas: Konfigurasi (Tabel 1.5 No. 11)
     * Menyimpan konfigurasi sistem (nomor WhatsApp owner, API key Fonnte,
     * informasi QRIS/rekening, template pesan WhatsApp, ongkir, dsb)
     * dalam format key-value agar fleksibel diatur lewat panel Admin (F-15).
     */
    public function up(): void
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
