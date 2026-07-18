<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Order;
use App\Support\OrderDeviceMemory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    /**
     * F-07: Halaman publik untuk pelanggan mengecek status pesanannya
     * sendiri kapan saja tanpa akun (guest checkout).
     *
     * Tiga lapis pencarian, dari paling nyaman ke paling manual:
     *  1) Otomatis lewat cookie perangkat (jk_orders) — pesanan yang
     *     pernah dibuat/dicek dari browser ini langsung tampil.
     *  2) Kode pesanan + nomor WhatsApp yang cocok (persis seperti
     *     sebelumnya) — untuk perangkat lain / cookie sudah hilang.
     *  3) Nomor WhatsApp saja (tanpa kode) — kode pesanan sequential
     *     gampang ditebak, tapi nomor WA jauh lebih sulit ditebak orang
     *     lain, jadi ini tetap aman dipakai sendirian.
     *  4) Kalau semuanya lupa — tombol Hubungi Admin via WhatsApp.
     */
    public function index(Request $request): View
    {
        $order = null;
        $notFound = false;
        $waSearched = false;
        $waResults = collect();

        $exactSearch = $request->filled('code') && $request->filled('whatsapp');
        $waOnlySearch = ! $exactSearch && $request->filled('whatsapp_only');

        if ($exactSearch) {
            $candidate = Order::where('order_code', trim($request->input('code')))
                ->with('details')
                ->first();

            if ($candidate && $this->normalizePhone($candidate->whatsapp) === $this->normalizePhone($request->input('whatsapp'))) {
                $order = $candidate;
                OrderDeviceMemory::remember($request, $order->order_code);
            }

            $notFound = ! $order;
        } elseif ($waOnlySearch) {
            $waSearched = true;
            $waResults = $this->findByWhatsappOnly($request->input('whatsapp_only'));
            OrderDeviceMemory::rememberMany($request, $waResults->pluck('order_code')->all());
        }

        $deviceOrders = collect();

        if (! $exactSearch && ! $waOnlySearch) {
            $codes = OrderDeviceMemory::codes($request);

            if (! empty($codes)) {
                $deviceOrders = Order::whereIn('order_code', $codes)
                    ->latest()
                    ->get(['id', 'order_code', 'status', 'created_at']);
            }
        }

        return view('checkout.track', [
            'order' => $order,
            'notFound' => $notFound,
            'code' => (string) $request->input('code', ''),
            'whatsapp' => (string) $request->input('whatsapp', ''),
            'waOnly' => (string) $request->input('whatsapp_only', ''),
            'waSearched' => $waSearched,
            'waResults' => $waResults,
            'deviceOrders' => $deviceOrders,
            'ownerWhatsappUrl' => $this->ownerWhatsappUrl(),
        ]);
    }

    /**
     * Cocokkan nomor WhatsApp tanpa peduli format penulisan
     * (0812.../+62812.../62812...). Untuk skala toko kecil ini cukup
     * di-filter di PHP; kalau volume pesanan sudah besar, sebaiknya
     * tambah kolom whatsapp_normalized + index di database.
     */
    private function findByWhatsappOnly(string $whatsapp): Collection
    {
        $target = $this->normalizePhone($whatsapp);

        return Order::query()
            ->get(['id', 'order_code', 'whatsapp', 'status', 'created_at'])
            ->filter(fn (Order $o) => $this->normalizePhone($o->whatsapp) === $target)
            ->sortByDesc('created_at')
            ->values();
    }

    private function ownerWhatsappUrl(): ?string
    {
        $owner = Configuration::get('owner_whatsapp');

        if (! $owner) {
            return null;
        }

        $message = 'Halo, saya lupa kode pesanan & nomor WhatsApp yang saya pakai saat checkout di Jukase Project. Mohon bantuannya untuk mengecek status pesanan saya ya. Terima kasih!';

        return 'https://wa.me/'.$this->normalizePhone($owner).'?text='.rawurlencode($message);
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        return $phone;
    }
}