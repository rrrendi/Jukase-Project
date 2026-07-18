<?php

namespace App\Services;

use App\Models\Configuration;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Support\Format;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integrasi WhatsApp Gateway pihak ketiga (Fonnte) sesuai Batasan Masalah
 * no. 2 dan Landasan Teori 1.6.5. Bertanggung jawab atas:
 *  - F-05: Notifikasi pesanan baru ke Admin (Owner)
 *  - F-07: Konfirmasi status pesanan ke Pelanggan
 * Seluruh pengiriman dicatat pada tabel notification_logs (Entitas Notifikasi_Log).
 */
class WhatsAppService
{
    /** Endpoint resmi REST API Fonnte. */
    protected string $endpoint = 'https://api.fonnte.com/send';

    /**
     * F-05: Kirim notifikasi pesanan baru ke nomor WhatsApp Owner.
     * Dipanggil sesaat setelah pelanggan berhasil checkout (status: pending).
     */
    public function notifyNewOrder(Order $order): bool
    {
        $ownerWa = Configuration::get('owner_whatsapp');

        if (!$ownerWa) {
            return false;
        }

        $template = Configuration::get('msg_template_new_order', $this->defaultNewOrderTemplate());

        return $this->send($ownerWa, $this->renderTemplate($template, $order), 'pesanan_baru');
    }

    /**
     * F-07: Kirim konfirmasi status pesanan ke nomor WhatsApp Pelanggan.
     * Dipanggil setelah Admin menyetujui (approve) atau menolak (reject) pesanan.
     */
    public function notifyOrderConfirmation(Order $order): bool
    {
        $template = Configuration::get('msg_template_confirmation', $this->defaultConfirmationTemplate());

        return $this->send($order->whatsapp, $this->renderTemplate($template, $order), 'konfirmasi_pesanan');
    }

    /**
     * Mengganti variabel pada template pesan dengan data pesanan.
     * Variabel yang didukung: {nama}, {id}, {total}, {status}, {items}.
     */
    protected function renderTemplate(string $template, Order $order): string
    {
        $statusLabel = match ($order->status) {
            'approved' => 'DISETUJUI ✅',
            'rejected' => 'DITOLAK ❌',
            default => 'MENUNGGU VERIFIKASI ⏳',
        };

        return str_replace(
            ['{nama}', '{id}', '{total}', '{status}', '{items}'],
            [
                $order->customer_name,
                $order->order_code,
                Format::rupiah($order->total),
                $statusLabel,
                $order->items_summary,
            ],
            $template
        );
    }

    protected function defaultNewOrderTemplate(): string
    {
        return "🛍️ Pesanan baru {id} dari {nama}.\nItem: {items}\nTotal: {total}\nSilakan cek panel admin untuk verifikasi pembayaran.";
    }

    protected function defaultConfirmationTemplate(): string
    {
        return "Halo {nama}! 👋 Pesanan {id} kamu *{status}*.\nTotal: {total}\nTerima kasih telah berbelanja di Jukase Project 🙌";
    }

    /**
     * Mengirim pesan WhatsApp melalui REST API Fonnte dan mencatat hasilnya
     * pada tabel notification_logs. Sengaja TIDAK melempar exception agar
     * kegagalan notifikasi tidak menggagalkan transaksi utama (pesanan/penjualan).
     */
    public function send(string $recipient, string $message, string $type): bool
    {
        $token = Configuration::get('fonnte_token');
        $active = Configuration::get('notif_active', '1');

        if (!$token || $active !== '1') {
            NotificationLog::create([
                'type' => $type,
                'recipient' => $recipient,
                'message' => $message,
                'status' => 'failed',
                'response' => 'Notifikasi tidak aktif atau API Token Fonnte belum diatur pada menu Konfigurasi.',
                'sent_at' => now(),
            ]);

            return false;
        }

        try {
            $response = Http::asForm()
                ->withHeaders(['Authorization' => $token])
                ->timeout(15)
                ->post($this->endpoint, [
                    'target' => $this->normalizePhone($recipient),
                    'message' => $message,
                ]);

            // Fonnte tetap balas HTTP 200 walau gagal secara logis (mis. device
            // terputus/banned) — status sukses/gagal yang sebenarnya ada di field
            // "status" pada body JSON, bukan cuma dari kode HTTP.
            $success = $response->successful() && (bool) ($response->json('status') ?? false);

            NotificationLog::create([
                'type' => $type,
                'recipient' => $recipient,
                'message' => $message,
                'status' => $success ? 'sent' : 'failed',
                'response' => $response->body(),
                'sent_at' => now(),
            ]);

            return $success;
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim notifikasi WhatsApp Fonnte: ' . $e->getMessage());

            NotificationLog::create([
                'type' => $type,
                'recipient' => $recipient,
                'message' => $message,
                'status' => 'failed',
                'response' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            return false;
        }
    }

    /**
     * Menormalisasi nomor WhatsApp ke format 62xxxxxxxxxx yang dibutuhkan
     * Fonnte, mis. "0812-3456-7890" -> "6281234567890".
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }
}
