<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom 'role' pada tabel users.
     * Sesuai Tabel 1.4 (Definisi Aktor), aktor yang memiliki akun
     * login hanyalah Admin (Owner Jukase Project). Kolom ini disiapkan
     * agar middleware AdminMiddleware dapat memverifikasi hak akses.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
