<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Order;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Support\OrderDeviceMemory;

class CheckoutController extends Controller
{
    public function __construct(protected WhatsAppService $whatsapp)
    {
    }

    /**
     * F-03/F-04: Menampilkan form pemesanan guest checkout (nama,
     * alamat, nomor WhatsApp) beserta informasi pembayaran (QRIS/
     * rekening) dari menu Konfigurasi dan ringkasan keranjang.
     */
    public function index(): View|RedirectResponse
    {
        [$items, $subtotal] = CartController::resolveCart(session('cart', []));

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $config = Configuration::getMany(['payment_bank_info', 'payment_qris_image', 'shipping_cost']);
        $shipping = (float) ($config['shipping_cost'] ?? 0);

        return view('checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
            'paymentBankInfo' => $config['payment_bank_info'],
            'paymentQrisImage' => $config['payment_qris_image'],
        ]);
    }

    /**
     * F-03/F-04: Menyimpan pesanan baru berstatus 'pending' beserta
     * detail item & bukti pembayaran, mengosongkan keranjang, lalu
     * mengirim notifikasi WhatsApp pesanan baru ke Admin (F-05).
     */
    public function store(Request $request): RedirectResponse
    {
        [$items, $subtotal] = CartController::resolveCart(session('cart', []));

        if (empty($items)) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang masih kosong atau stok produk tidak lagi tersedia.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [], [
            'customer_name' => 'Nama Lengkap',
            'whatsapp' => 'Nomor WhatsApp',
            'address' => 'Alamat Lengkap',
            'payment_proof' => 'Bukti Pembayaran',
        ]);

        $shipping = (float) Configuration::get('shipping_cost', '0');
        $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order = Order::create([
            'customer_name' => $validated['customer_name'],
            'address' => $validated['address'],
            'whatsapp' => $validated['whatsapp'],
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'total' => $subtotal + $shipping,
            'payment_proof' => $proofPath,
            'status' => 'pending',
        ]);

        foreach ($items as $item) {
            $order->details()->create([
                'product_id' => $item['product']->id,
                'product_name' => $item['product']->full_name,
                'quantity' => $item['qty'],
                'price' => $item['product']->price,
            ]);
        }

        session()->forget('cart');

        // F-05: notifikasi pesanan baru ke nomor WhatsApp Admin/Owner.
        $order->load('details');
        $this->whatsapp->notifyNewOrder($order);

        // Ingat kode pesanan ini di perangkat pelanggan, supaya nanti
        // otomatis muncul di halaman Lacak Pesanan tanpa perlu isi ulang.
        OrderDeviceMemory::remember($request, $order->order_code);

        return redirect()->route('checkout.success', $order->order_code);
    }

    /**
     * Halaman konfirmasi setelah pesanan berhasil dibuat. Menjelaskan
     * bahwa status masih 'pending' dan konfirmasi via WhatsApp akan
     * dikirim setelah Admin memverifikasi pembayaran (F-06, F-07).
     */
    public function success(Order $order): View
    {
        return view('checkout.success', [
            'order' => $order->load('details'),
        ]);
    }
}
