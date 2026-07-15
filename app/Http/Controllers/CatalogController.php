<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * F-02: Menampilkan katalog produk online beserta informasi merek,
     * model, ukuran, harga, foto, dan status stok secara real-time
     * (Ready / Menipis / Habis). Mendukung filter kategori melalui
     * query string ?category=slug dan pencarian ?q=kata-kunci.
     */
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::query()
            ->active()
            ->with(['category', 'images'])
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('slug', $request->input('category'));
                });
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->input('q');

                $query->where(function ($q) use ($search) {
                    $q->where('brand', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('brand')
            ->orderBy('name')
            ->get();

        return view('catalog.index', [
            'categories' => $categories,
            'products' => $products,
            'activeCategory' => $request->input('category', 'all'),
            'search' => (string) $request->input('q', ''),
        ]);
    }
}
