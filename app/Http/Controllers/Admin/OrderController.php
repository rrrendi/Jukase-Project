<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected WhatsAppService $whatsapp)
    {
    }

    /**
     * F-06: Daftar pesanan website dengan tab status
     * pending / approved / rejected.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'pending');

        if (! in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = 'pending';
        }

        $orders = Order::with('details')
            ->where('status', $status)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'counts' => [
                'pending' => Order::where('status', 'pending')->count(),
                'approved' => Order::where('status', 'approved')->count(),
                'rejected' => Order::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load('details.product'),
        ]);
    }

    /**
     * F-06/F-07/F-11/F-12: Menyetujui pesanan - HPP saat ini disimpan
     * sebagai snapshot pada setiap detail item, stok produk berkurang
     * sesuai jumlah pesanan, lalu notifikasi WhatsApp konfirmasi
     * dikirim ke pelanggan.
     */
    public function approve(Order $order): RedirectResponse
    {
        $order->load('details.product');

        if (! $order->approve()) {
            return back()->with('error', 'Pesanan ini sudah diproses sebelumnya.');
        }

        $this->whatsapp->notifyOrderConfirmation($order);

        return redirect()->route('admin.orders.index', ['status' => 'pending'])
            ->with('success', 'Pesanan '.$order->order_code.' disetujui. Stok diperbarui & notifikasi WhatsApp terkirim ke pelanggan.');
    }

    /**
     * F-06/F-07: Menolak pesanan (stok TIDAK berkurang) & mengirim
     * notifikasi WhatsApp ke pelanggan.
     */
    public function reject(Order $order): RedirectResponse
    {
        if (! $order->reject()) {
            return back()->with('error', 'Pesanan ini sudah diproses sebelumnya.');
        }

        $this->whatsapp->notifyOrderConfirmation($order);

        return redirect()->route('admin.orders.index', ['status' => 'pending'])
            ->with('success', 'Pesanan '.$order->order_code.' ditolak & notifikasi WhatsApp terkirim ke pelanggan.');
    }
}
