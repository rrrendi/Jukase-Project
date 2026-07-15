<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Data master Kategori (Entitas Kategori - Tabel 1.5 No.2),
     * mendukung F-08 (pengelompokan produk pada katalog & form produk).
     */
    public function index(): View
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ], [], ['name' => 'Nama Kategori']);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,'.$category->id],
        ], [], ['name' => 'Nama Kategori']);

        // slug dikosongkan agar dibuat ulang otomatis dari nama baru (lihat Category::booted()).
        $category->update(['name' => $validated['name'], 'slug' => null]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori "'.$category->name.'" tidak dapat dihapus karena masih digunakan oleh produk.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
