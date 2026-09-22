<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemIngredient extends Model
{
    protected $fillable = ['inventory_item_id', 'quantity_per_product', 'quantity_consumed'];

    protected function casts(): array
    {
        return ['quantity_per_product' => 'decimal:3', 'quantity_consumed' => 'decimal:3'];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
