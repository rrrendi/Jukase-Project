<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Menampilkan isi keranjang (disimpan di session, tanpa akun
     * pelanggan) sebelum lanjut ke form pemesanan / checkout (F-03).
     */
    public function index(): View
    {
        [$items, $subtotal] = self::resolveCart(session('cart', []));

        return view('cart.index', [
            'items' => $items,
            'subtotal' => $subtotal,
        ]);
    }

    /**
     * Menambahkan produk ke keranjang. Jumlah dibatasi sesuai stok
     * yang tersedia saat ini (F-02).
     */
    public function store(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        if (!$product->is_active || $product->isOutOfStock()) {
            $message = 'Maaf, ' . $product->full_name . ' sedang habis.';

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'type' => 'error', 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $qty = max(1, (int) $request->input('qty', 1));
        $cart = session('cart', []);
        $current = (int) ($cart[$product->id] ?? 0);

        // Sudah mentok batas stok, tidak bisa ditambah lagi
        if ($current >= $product->stock) {
            $message = 'Stok ' . $product->full_name . ' di keranjang sudah mencapai batas maksimal (' . $product->stock . ' unit).';

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'type' => 'warning', 'message' => $message]);
            }

            return back()->with('error', $message);
        }

        $wasCapped = ($current + $qty) > $product->stock;
        $cart[$product->id] = min($current + $qty, $product->stock);
        session(['cart' => $cart]);

        $type = $wasCapped ? 'warning' : 'success';
        $message = $wasCapped
            ? 'Hanya bisa menambahkan sampai batas stok tersedia (' . $product->stock . ' unit) untuk ' . $product->full_name . '.'
            : $product->full_name . ' ditambahkan ke keranjang.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'type' => $type,
                'message' => $message,
                'cartCount' => array_sum(session('cart', [])),
            ]);
        }

        return back()->with($wasCapped ? 'error' : 'success', $message);
    }

    /**
     * Mengubah jumlah (qty) salah satu item di keranjang.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $qty = (int) $request->input('qty', 1);
        $cart = session('cart', []);

        if ($qty <= 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = min($qty, max($product->stock, 0));
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    /**
     * Menghapus satu item dari keranjang.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    /**
     * Mengambil data produk terkini untuk setiap item di session cart,
     * memastikan produk masih aktif & jumlahnya tidak melebihi stok.
     *
     * @return array{0: array<int, array{product: Product, qty: int}>, 1: float}
     */
    public static function resolveCart(array $cart): array
    {
        $items = [];
        $subtotal = 0.0;

        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);

            if (!$product || !$product->is_active || $product->stock <= 0) {
                continue;
            }

            $qty = min((int) $qty, $product->stock);

            if ($qty <= 0) {
                continue;
            }

            $items[] = ['product' => $product, 'qty' => $qty];
            $subtotal += (float) $product->price * $qty;
        }

        return [$items, $subtotal];
    }
}
