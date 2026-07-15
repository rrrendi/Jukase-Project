<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ConfigurationController extends Controller
{
    /**
     * Daftar key konfigurasi yang dikelola pada F-15.
     */
    protected array $keys = [
        'fonnte_token',
        'owner_whatsapp',
        'notif_active',
        'msg_template_new_order',
        'msg_template_confirmation',
        'payment_bank_info',
        'payment_qris_image',
        'default_min_stock',
        'low_stock_alert_active',
        'shipping_cost',
    ];

    /**
     * F-15: Form konfigurasi sistem - nomor WhatsApp Owner, API key
     * WhatsApp Gateway (Fonnte), template notifikasi, informasi
     * pembayaran (QRIS/rekening), batas stok minimum default, dan
     * ongkos kirim.
     */
    public function edit(): View
    {
        return view('admin.configuration.edit', [
            'config' => Configuration::getMany($this->keys),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fonnte_token' => ['nullable', 'string', 'max:255'],
            'owner_whatsapp' => ['required', 'string', 'max:20'],
            'msg_template_new_order' => ['required', 'string'],
            'msg_template_confirmation' => ['required', 'string'],
            'payment_bank_info' => ['required', 'string'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'default_min_stock' => ['required', 'integer', 'min:0'],
            'shipping_cost' => ['required', 'numeric', 'min:0'],
        ], [], [
            'fonnte_token' => 'API Key Fonnte',
            'owner_whatsapp' => 'Nomor WhatsApp Owner',
            'msg_template_new_order' => 'Template Notifikasi Pesanan Baru',
            'msg_template_confirmation' => 'Template Konfirmasi Pesanan',
            'payment_bank_info' => 'Informasi Rekening',
            'qris_image' => 'Gambar QRIS',
            'default_min_stock' => 'Batas Stok Minimum Default',
            'shipping_cost' => 'Ongkos Kirim',
        ]);

        Configuration::set('fonnte_token', $validated['fonnte_token'] ?? '');
        Configuration::set('owner_whatsapp', $validated['owner_whatsapp']);
        Configuration::set('notif_active', $request->boolean('notif_active') ? '1' : '0');
        Configuration::set('msg_template_new_order', $validated['msg_template_new_order']);
        Configuration::set('msg_template_confirmation', $validated['msg_template_confirmation']);
        Configuration::set('payment_bank_info', $validated['payment_bank_info']);
        Configuration::set('default_min_stock', (string) $validated['default_min_stock']);
        Configuration::set('low_stock_alert_active', $request->boolean('low_stock_alert_active') ? '1' : '0');
        Configuration::set('shipping_cost', (string) $validated['shipping_cost']);

        if ($request->hasFile('qris_image')) {
            $old = Configuration::get('payment_qris_image');

            if ($old) {
                Storage::disk('public')->delete($old);
            }

            $path = $request->file('qris_image')->store('qris', 'public');
            Configuration::set('payment_qris_image', $path);
        }

        return redirect()->route('admin.configuration.edit')->with('success', 'Konfigurasi berhasil disimpan.');
    }
}
