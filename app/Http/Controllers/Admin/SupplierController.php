<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Data master Supplier (Entitas Supplier - Tabel 1.5 No.4),
     * mendukung F-09 (pencatatan stok masuk per supplier).
     */
    public function index(): View
    {
        $suppliers = Supplier::withCount('stockIns')->orderBy('name')->get();

        return view('admin.suppliers.index', ['suppliers' => $suppliers]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $this->validateData($request);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->stockIns()->exists()) {
            return back()->with('error', 'Supplier "'.$supplier->name.'" tidak dapat dihapus karena memiliki riwayat stok masuk.');
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [], [
            'name' => 'Nama Supplier',
            'contact' => 'Kontak',
            'address' => 'Alamat',
        ]);
    }
}
