<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualSale;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManualSaleController extends Controller
{
    /**
     * F-10: Form pencatatan penjualan dari kanal di luar website
     * (WhatsApp, Instagram, Facebook, Walk-in) + daftar penjualan
     * manual terbaru.
     */
    public function index(): View
    {
        $sales = ManualSale::with('details')
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.manual-sales.index', [
            'sales' => $sales,
            'products' => Product::where('stock', '>', 0)->orderBy('brand')->orderBy('name')->get(),
            'today' => now()->toDateString(),
        ]);
    }

    /**
     * F-10/F-11/F-12: Menyimpan satu transaksi penjualan manual.
     * Stok produk berkurang otomatis & omzet tercatat di laporan
     * (ditangani oleh ManualSale::record()).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'channel' => ['required', 'in:WhatsApp,Instagram,Facebook,Walk-in'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'sale_date' => ['required', 'date'],
        ], [], [
            'product_id' => 'Produk',
            'quantity' => 'Jumlah',
            'price' => 'Harga Jual',
            'channel' => 'Kanal Penjualan',
            'customer_name' => 'Nama Pelanggan',
            'sale_date' => 'Tanggal',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($validated['quantity'] > $product->stock) {
            return back()->withInput()->with('error', 'Stok '.$product->full_name.' hanya tersisa '.$product->stock.'.');
        }

        ManualSale::record(
            $product,
            (int) $validated['quantity'],
            (float) $validated['price'],
            $validated['channel'],
            $validated['customer_name'] ?? null,
            $validated['sale_date']
        );

        return redirect()->route('admin.manual-sales.index')
            ->with('success', 'Penjualan manual berhasil dicatat. Stok '.$product->full_name.' kini '.($product->stock).'.');
    }
}
