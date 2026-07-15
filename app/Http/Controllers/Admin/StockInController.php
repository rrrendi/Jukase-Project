<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\Supplier;
use App\Support\Format;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockInController extends Controller
{
    /**
     * F-09: Form pencatatan stok masuk dari supplier + riwayat
     * stok masuk terbaru (menjadi dasar HPP Moving Average - F-12).
     */
    public function index(): View
    {
        $stockIns = StockIn::with(['product', 'supplier'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.stock-ins.index', [
            'stockIns' => $stockIns,
            'products' => Product::orderBy('brand')->orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'today' => now()->toDateString(),
        ]);
    }

    /**
     * F-09/F-12: Menyimpan stok masuk baru (jumlah, harga modal/satuan,
     * tanggal, supplier), lalu menghitung ulang HPP produk menggunakan
     * metode Moving Average dan menambah stok produk.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
        ], [], [
            'product_id' => 'Produk',
            'supplier_id' => 'Supplier',
            'quantity' => 'Jumlah Masuk',
            'unit_cost' => 'Harga Modal/Satuan',
            'date' => 'Tanggal',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        StockIn::create($validated);

        $product->applyStockIn((int) $validated['quantity'], (float) $validated['unit_cost']);

        return redirect()->route('admin.stock-ins.index')->with(
            'success',
            'Stok masuk dicatat. Stok '.$product->full_name.' kini '.$product->stock.
                ', HPP baru '.Format::rupiah($product->avg_cost).' (Moving Average).'
        );
    }
}
