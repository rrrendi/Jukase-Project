<?php

namespace App\Services;

use App\Models\ManualSale;
use App\Models\ManualSaleDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Collection;

/**
 * Menyatukan logika perhitungan keuangan agar Dashboard (F-14) dan
 * Laporan Keuangan (F-13) selalu konsisten. Omzet & HPP dihitung dari
 * dua sumber:
 *  - Pesanan website berstatus 'approved', berdasarkan tanggal approved_at
 *  - Penjualan manual (WA/IG/FB/Walk-in), berdasarkan tanggal sale_date
 * HPP diambil dari kolom snapshot 'cost_price' (hasil Moving Average
 * pada saat transaksi disetujui/dicatat - F-12), bukan dari avg_cost
 * produk saat ini, agar laporan periode lampau tetap akurat.
 */
class FinanceService
{
    /**
     * Rekapitulasi omzet, HPP, dan laba/rugi untuk rentang tanggal
     * (inklusif) tertentu, format Y-m-d.
     *
     * @return array{revenue: float, cogs: float, profit: float, order_count: int, manual_count: int, transaction_count: int}
     */
    public function summary(string $startDate, string $endDate): array
    {
        $orderQuery = Order::query()
            ->where('status', 'approved')
            ->whereDate('approved_at', '>=', $startDate)
            ->whereDate('approved_at', '<=', $endDate);

        $manualQuery = ManualSale::query()
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);

        $orderRevenue = (float) (clone $orderQuery)->sum('total');
        $manualRevenue = (float) (clone $manualQuery)->sum('total');

        $orderIds = (clone $orderQuery)->pluck('id');
        $manualSaleIds = (clone $manualQuery)->pluck('id');

        $orderCogs = (float) OrderDetail::query()
            ->whereIn('order_id', $orderIds)
            ->selectRaw('COALESCE(SUM(cost_price * quantity), 0) as total')
            ->value('total');

        $manualCogs = (float) ManualSaleDetail::query()
            ->whereIn('manual_sale_id', $manualSaleIds)
            ->selectRaw('COALESCE(SUM(cost_price * quantity), 0) as total')
            ->value('total');

        $revenue = $orderRevenue + $manualRevenue;
        $cogs = $orderCogs + $manualCogs;

        return [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'profit' => $revenue - $cogs,
            'order_count' => $orderIds->count(),
            'manual_count' => $manualSaleIds->count(),
            'transaction_count' => $orderIds->count() + $manualSaleIds->count(),
        ];
    }

    /**
     * Produk terlaris berdasarkan total quantity terjual dalam rentang
     * tanggal, digabung dari pesanan website (approved) dan penjualan
     * manual. Mengembalikan koleksi: product_name, qty, total.
     */
    public function topProducts(string $startDate, string $endDate, int $limit = 5): Collection
    {
        $orderIds = Order::query()
            ->where('status', 'approved')
            ->whereDate('approved_at', '>=', $startDate)
            ->whereDate('approved_at', '<=', $endDate)
            ->pluck('id');

        $manualSaleIds = ManualSale::query()
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->pluck('id');

        $fromOrders = OrderDetail::query()
            ->whereIn('order_id', $orderIds)
            ->get(['product_name', 'quantity', 'price']);

        $fromManual = ManualSaleDetail::query()
            ->whereIn('manual_sale_id', $manualSaleIds)
            ->get(['product_name', 'quantity', 'price']);

        return $fromOrders->concat($fromManual)
            ->groupBy('product_name')
            ->map(function (Collection $rows, string $name) {
                return [
                    'product_name' => $name,
                    'qty' => $rows->sum('quantity'),
                    'total' => $rows->sum(fn ($r) => (float) $r->price * $r->quantity),
                ];
            })
            ->sortByDesc('qty')
            ->take($limit)
            ->values();
    }

    /**
     * Omzet harian untuk grafik penjualan Dashboard (F-14), dari
     * $days hari terakhir hingga hari ini (urut dari paling lama).
     *
     * @return array<int, array{date: string, revenue: float}>
     */
    public function dailyRevenue(int $days = 7): array
    {
        $result = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $summary = $this->summary($date, $date);
            $result[] = ['date' => $date, 'revenue' => $summary['revenue']];
        }

        return $result;
    }
}
