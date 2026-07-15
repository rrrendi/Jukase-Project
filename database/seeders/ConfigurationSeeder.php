<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Mengisi nilai default Konfigurasi sistem (F-15). Seluruh nilai ini
     * dapat diubah Admin melalui menu Konfigurasi tanpa mengedit kode.
     */
    public function run(): void
    {
        $defaults = [
            // WhatsApp Gateway (Fonnte) - lihat 1.6.5
            'fonnte_token' => '',
            'owner_whatsapp' => '6281234567890',
            'notif_active' => '1',

            // Template pesan WhatsApp (F-05, F-07)
            'msg_template_new_order' => "🛍️ Pesanan baru {id} dari {nama}.\nItem: {items}\nTotal: {total}\nSilakan cek panel admin untuk verifikasi pembayaran.",
            'msg_template_confirmation' => "Halo {nama}! 👋 Pesanan {id} kamu *{status}*.\nTotal: {total}\nTerima kasih telah berbelanja di Jukase Project 🙌",

            // Informasi pembayaran (F-04)
            'payment_bank_info' => 'BCA 7220-1234-567 a.n. Jukase Project',
            'payment_qris_image' => '',

            // Stok & peringatan (F-15)
            'default_min_stock' => '5',
            'low_stock_alert_active' => '1',

            // Ongkos kirim flat (opsional, di luar cakupan ERD inti -
            // dapat diset 0 jika ongkir dihitung manual oleh Admin)
            'shipping_cost' => '0',
        ];

        foreach ($defaults as $key => $value) {
            Configuration::query()->firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
