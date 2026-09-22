<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchInventory extends Model
{
    protected $fillable = ['branch_id', 'inventory_item_id', 'quantity', 'reorder_point', 'maximum_quantity', 'expires_at'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:3', 'reorder_point' => 'decimal:3', 'maximum_quantity' => 'decimal:3', 'expires_at' => 'datetime'];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
