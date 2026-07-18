<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'brand',
        'name',
        'size_range',
        'color',
        'price',
        'avg_cost',
        'stock',
        'min_stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'avg_cost' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class)->latest('date');
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function manualSaleDetails(): HasMany
    {
        return $this->hasMany(ManualSaleDetail::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    /*
    |--------------------------------------------------------------------
    | Accessors / Helper
    |--------------------------------------------------------------------
    */

    /**
     * Nama lengkap produk: "Merek Nama Model" (F-02).
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->brand . ' ' . $this->name);
    }

    /**
     * URL foto sampul produk: foto pertama (sort_order terkecil) di
     * relasi images(), atau null jika produk belum punya foto sama
     * sekali (fallback ke placeholder ikon di Blade) (F-02).
     */
    public function getImageUrlAttribute(): ?string
    {
        $cover = $this->images->first();

        if (!$cover) {
            return null;
        }

        return Storage::disk('public')->exists($cover->path)
            ? Storage::url($cover->path)
            : null;
    }

    /**
     * Seluruh foto galeri, untuk lightbox di katalog (F-02).
     */
    public function getGalleryUrlsAttribute(): array
    {
        return $this->images->pluck('url')->filter()->values()->all();
    }

    /**
     * Status stok untuk badge katalog & dashboard:
     * 'habis' (stok 0), 'menipis' (stok <= batas minimum), atau 'ready'.
     * (F-02, F-14, F-15)
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'habis';
        }

        if ($this->stock <= $this->min_stock) {
            return 'menipis';
        }

        return 'ready';
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->min_stock;
    }

    /**
     * Margin keuntungan (%) antara harga jual dan HPP rata-rata.
     * Ditampilkan pada tabel Kelola Produk admin.
     */
    public function getMarginPercentAttribute(): float
    {
        if ((float) $this->price <= 0) {
            return 0;
        }

        return round((((float) $this->price - (float) $this->avg_cost) / (float) $this->price) * 100, 1);
    }

    /*
    |--------------------------------------------------------------------
    | Logika Bisnis: HPP Moving Average & Stok
    |--------------------------------------------------------------------
    */

    /**
     * Menghitung ulang HPP (avg_cost) menggunakan metode Moving Average
     * setiap kali ada stok masuk baru, lalu menambah stok (F-09, F-12).
     *
     * Rumus:
     *   HPP baru = ((stok lama x HPP lama) + (qty masuk x harga modal masuk))
     *              / (stok lama + qty masuk)
     *
     * @param  int  $incomingQty  Jumlah barang masuk
     * @param  float  $incomingUnitCost  Harga modal per satuan barang masuk
     */
    public function applyStockIn(int $incomingQty, float $incomingUnitCost): void
    {
        $oldStock = (int) $this->stock;
        $oldAvgCost = (float) $this->avg_cost;

        $totalQtyAfter = $oldStock + $incomingQty;

        if ($totalQtyAfter > 0) {
            $newAvgCost = (($oldStock * $oldAvgCost) + ($incomingQty * $incomingUnitCost)) / $totalQtyAfter;
        } else {
            $newAvgCost = $incomingUnitCost;
        }

        $this->avg_cost = round($newAvgCost, 2);
        $this->stock = $totalQtyAfter;
        $this->save();
    }

    /**
     * Mengurangi stok produk sebanyak $qty (F-11), dipanggil saat:
     * - Admin menyetujui pesanan website (F-06/F-07), atau
     * - Admin mencatat penjualan manual (F-10).
     * Stok tidak dipaksa menjadi negatif (minimal 0).
     */
    public function reduceStock(int $qty): void
    {
        $this->stock = max(0, (int) $this->stock - $qty);
        $this->save();
    }
}