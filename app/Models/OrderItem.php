<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $fillable = ['product_id', 'product_name', 'product_sku', 'category_name', 'quantity', 'unit_price', 'discount_total', 'total', 'cost_snapshot', 'modifiers', 'notes'];

    protected function casts(): array
    {
        return ['unit_price' => 'decimal:2', 'discount_total' => 'decimal:2', 'total' => 'decimal:2', 'cost_snapshot' => 'decimal:2', 'modifiers' => 'array'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(OrderItemIngredient::class);
    }
}
