<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migrasi DATA (bukan skema): memperbaiki notification_logs.status
     * yang salah tercatat 'sent' pada baris lama — sebelum
     * WhatsAppService::send() diperbaiki agar membaca field "status"
     * pada body JSON Fonnte, bukan cuma kode HTTP (Fonnte tetap balas
     * HTTP 200 walau gagal secara logis, mis. device disconnected/banned).
     */
    public function up(): void
    {
        DB::table('notification_logs')
            ->where('status', 'sent')
            ->orderBy('id')
            ->chunkById(100, function ($logs) {
                foreach ($logs as $log) {
                    $decoded = json_decode((string) $log->response, true);

                    $trulyFailed = is_array($decoded)
                        && array_key_exists('status', $decoded)
                        && $decoded['status'] === false;

                    if ($trulyFailed) {
                        DB::table('notification_logs')
                            ->where('id', $log->id)
                            ->update(['status' => 'failed']);
                    }
                }
            });
    }

    public function down(): void
    {
        // Migrasi data satu arah - sengaja tidak dibalik otomatis.
    }
};