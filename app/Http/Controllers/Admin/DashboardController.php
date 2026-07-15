<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\FinanceService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected FinanceService $finance)
    {
    }

    /**
     * F-14: Dashboard ringkasan Admin berisi omzet hari ini, jumlah
     * pesanan baru (pending), jumlah produk dengan stok kritis,
     * laba bersih bulan ini, grafik penjualan 7 hari terakhir,
     * daftar produk stok menipis, dan pesanan terbaru.
     */
    public function index(): View
    {
        $today = now()->toDateString();
        $todaySummary = $this->finance->summary($today, $today);

        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $monthSummary = $this->finance->summary($monthStart, $monthEnd);

        return view('admin.dashboard', [
            'todayRevenue' => $todaySummary['revenue'],
            'pendingCount' => Order::where('status', 'pending')->count(),
            'lowStockCount' => Product::lowStock()->count(),
            'monthProfit' => $monthSummary['profit'],
            'lowStockProducts' => Product::lowStock()->orderBy('stock')->limit(5)->get(),
            'latestOrders' => Order::latest()->limit(5)->get(),
            'chart' => $this->finance->dailyRevenue(7),
        ]);
    }
}
