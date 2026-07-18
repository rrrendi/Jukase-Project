<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\NotificationLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManualSaleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockInController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderTrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Publik - Pelanggan (Guest, tanpa akun / guest checkout)
|--------------------------------------------------------------------------
| F-02 Katalog Produk Online
| F-03 Form Pemesanan Guest Checkout
| F-04 Upload Bukti Pembayaran
| F-05 Notifikasi Pesanan Baru ke Admin (dipicu di CheckoutController)
*/

Route::get('/', [CatalogController::class, 'index'])->name('home');

Route::prefix('keranjang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/{product}', [CartController::class, 'store'])->name('store');
    Route::patch('/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('/{product}', [CartController::class, 'destroy'])->name('destroy');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
});

// Halaman sukses pesanan, dicari berdasarkan order_code (mis. JKS-2041).
Route::get('/pesanan/{order:order_code}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/lacak-pesanan', [OrderTrackingController::class, 'index'])->name('order-tracking.index');

/*
|--------------------------------------------------------------------------
| Redirect bantu untuk Laravel Breeze
|--------------------------------------------------------------------------
| Breeze mengarahkan pengguna ke route bernama 'dashboard' setelah login
| berhasil (lihat AuthenticatedSessionController). Karena dashboard utama
| sistem ini berada di /admin/dashboard, route ini hanya melempar (redirect)
| ke sana agar proses login Breeze tetap berjalan tanpa perlu diubah.
*/
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'admin'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Panel Admin (Owner Jukase Project) - F-01, F-06 s/d F-15
|--------------------------------------------------------------------------
| Middleware 'auth'  -> wajib login (Laravel Breeze) - F-01
| Middleware 'admin' -> wajib role = admin (App\Http\Middleware\AdminMiddleware) - NF-02
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // F-14: Dashboard ringkasan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // F-08: Kelola Produk
    Route::resource('products', ProductController::class)->except(['show']);
    Route::delete('/products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');

    // Data master pendukung (Kategori & Supplier)
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);

    // F-09/F-12: Stok Masuk & HPP Moving Average
    Route::get('/stock-ins', [StockInController::class, 'index'])->name('stock-ins.index');
    Route::post('/stock-ins', [StockInController::class, 'store'])->name('stock-ins.store');

    // F-06/F-07/F-11: Pesanan Website (verifikasi pembayaran)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::post('/orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');

    // F-10/F-11/F-12: Penjualan Manual (WA/IG/FB/Walk-in)
    Route::get('/manual-sales', [ManualSaleController::class, 'index'])->name('manual-sales.index');
    Route::post('/manual-sales', [ManualSaleController::class, 'store'])->name('manual-sales.store');

    // F-13: Laporan Keuangan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');

    // F-15: Konfigurasi Sistem
    Route::get('/configuration', [ConfigurationController::class, 'edit'])->name('configuration.edit');
    Route::put('/configuration', [ConfigurationController::class, 'update'])->name('configuration.update');

    // F-05/F-07: Riwayat Notifikasi WhatsApp (log kirim Fonnte)
    Route::get('/notification-logs', [NotificationLogController::class, 'index'])->name('notification-logs.index');
});

require __DIR__ . '/auth.php';
