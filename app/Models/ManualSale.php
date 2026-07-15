<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManualSale extends Model
{
    protected $fillable = [
        'customer_name',
        'channel',
        'total',
        'sale_date',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'sale_date' => 'date',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(ManualSaleDetail::class);
    }

    /**
     * Mencatat satu transaksi penjualan manual (F-10):
     * - Menyimpan header Penjualan_Manual + satu baris detail.
     * - Snapshot harga jual & HPP (Moving Average) saat transaksi (F-12).
     * - Mengurangi stok produk secara otomatis (F-11).
     */
    public static function record(
        Product $product,
        int $qty,
        float $price,
        string $channel,
        ?string $customerName,
        string $saleDate
    ): self {
        $sale = static::create([
            'customer_name' => $customerName,
            'channel' => $channel,
            'sale_date' => $saleDate,
            'total' => $price * $qty,
        ]);

        $sale->details()->create([
            'product_id' => $product->id,
            'product_name' => $product->full_name,
            'quantity' => $qty,
            'price' => $price,
            'cost_price' => $product->avg_cost,
        ]);

        $product->reduceStock($qty);

        return $sale;
    }

    public function getItemsSummaryAttribute(): string
    {
        return $this->details
            ->map(fn (ManualSaleDetail $d) => $d->product_name.' x'.$d->quantity)
            ->implode(', ');
    }
}
