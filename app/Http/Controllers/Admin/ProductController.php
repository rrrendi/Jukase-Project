<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\ProductImage;

class ProductController extends Controller
{
    /**
     * F-08: Daftar seluruh produk untuk dikelola Admin (tambah, ubah,
     * hapus, aktif/non-aktif).
     */
    public function index(Request $request): View
    {
        $products = Product::with(['category', 'images'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->input('q');

                $query->where(function ($q) use ($search) {
                    $q->where('brand', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'search' => (string) $request->input('q', ''),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('name')->get(),
            'defaultMinStock' => (int) Configuration::get('default_min_stock', '5'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);
        $validated['is_active'] = $request->boolean('is_active');

        $galleryFiles = $request->file('images', []);
        unset($validated['images']);

        $product = Product::create($validated);
        $this->storeGalleryImages($product, $galleryFiles);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateData($request, $product);
        $validated['is_active'] = $request->boolean('is_active');

        $galleryFiles = $request->file('images', []);
        unset($validated['images']);

        $product->update($validated);
        $this->storeGalleryImages($product, $galleryFiles);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk. Produk yang sudah memiliki riwayat transaksi
     * (stok masuk, pesanan, atau penjualan manual) tidak dapat dihapus
     * agar riwayat & laporan keuangan tetap konsisten - nonaktifkan saja.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $hasHistory = $product->stockIns()->exists()
            || $product->orderDetails()->exists()
            || $product->manualSaleDetails()->exists();

        if ($hasHistory) {
            return back()->with('error', 'Produk "' . $product->full_name . '" tidak dapat dihapus karena sudah memiliki riwayat transaksi. Nonaktifkan saja produk ini.');
        }

        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $product->images()->delete();

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    protected function storeGalleryImages(Product $product, array $files): void
    {
        $nextOrder = (int) $product->images()->max('sort_order') + 1;

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }

            $product->images()->create([
                'path' => $file->store('products/gallery', 'public'),
                'sort_order' => $nextOrder++,
            ]);
        }
    }

    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_if($image->product_id !== $product->id, 404);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Foto galeri dihapus.');
    }

    /**
     * Validasi data form tambah/ubah produk (F-08). Stok & HPP TIDAK
     * diisi di sini - keduanya hanya berubah melalui modul Stok Masuk
     * (F-09/F-12) dan transaksi penjualan (F-11) agar konsisten.
     */
    protected function validateData(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'size_range' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [], [
            'category_id' => 'Kategori',
            'brand' => 'Merek',
            'name' => 'Nama Model',
            'size_range' => 'Ukuran',
            'color' => 'Warna',
            'price' => 'Harga Jual',
            'min_stock' => 'Batas Stok Minimum',
            'images' => 'Foto Produk',
        ]);
    }
}