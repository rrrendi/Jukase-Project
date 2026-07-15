<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManualSaleDetail extends Model
{
    protected $fillable = [
        'manual_sale_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
        'cost_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    public function manualSale(): BelongsTo
    {
        return $this->belongsTo(ManualSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->price * $this->quantity;
    }
}
