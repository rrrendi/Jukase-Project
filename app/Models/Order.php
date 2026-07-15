<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'customer_name',
        'address',
        'whatsapp',
        'subtotal',
        'shipping_cost',
        'total',
        'payment_proof',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Setelah pesanan baru tersimpan, buat kode pesanan unik
     * dengan format "JKS-XXXX" (mis. JKS-2041) berdasarkan id.
     */
    protected static function booted(): void
    {
        static::created(function (Order $order) {
            if (empty($order->order_code)) {
                $order->order_code = 'JKS-'.(2000 + $order->id);
                $order->saveQuietly();
            }
        });
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        if (! $this->payment_proof) {
            return null;
        }

        return Storage::disk('public')->exists($this->payment_proof)
            ? Storage::url($this->payment_proof)
            : null;
    }

    /**
     * Ringkasan item pesanan untuk ditampilkan di tabel admin,
     * mis. "Air Force 1 x1, Old Skool x1".
     */
    public function getItemsSummaryAttribute(): string
    {
        return $this->details
            ->map(fn (OrderDetail $d) => $d->product_name.' x'.$d->quantity)
            ->implode(', ');
    }

    /*
    |--------------------------------------------------------------------
    | Logika Bisnis: Verifikasi Pesanan (F-06, F-07, F-11, F-12)
    |--------------------------------------------------------------------
    */

    /**
     * Menyetujui pesanan. Untuk setiap item:
     * - Catat HPP (cost_price) produk saat ini (F-12) sebagai snapshot
     *   untuk laporan keuangan.
     * - Kurangi stok produk sejumlah qty (F-11).
     * Status pesanan berubah menjadi 'approved'.
     * Pengiriman notifikasi WhatsApp (F-07) dilakukan terpisah
     * oleh controller melalui WhatsAppService.
     */
    public function approve(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        foreach ($this->details as $detail) {
            $product = $detail->product;

            if ($product) {
                $detail->cost_price = $product->avg_cost;
                $detail->save();

                $product->reduceStock($detail->quantity);
            }
        }

        $this->status = 'approved';
        $this->approved_at = now();

        return $this->save();
    }

    /**
     * Menolak pesanan. Stok TIDAK berkurang.
     */
    public function reject(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'rejected';

        return $this->save();
    }
}
