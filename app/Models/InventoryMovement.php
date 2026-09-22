<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $fillable = ['branch_id', 'inventory_item_id', 'user_id', 'type', 'quantity_delta', 'quantity_before', 'quantity_after', 'reason', 'notes'];

    protected function casts(): array
    {
        return ['quantity_delta' => 'decimal:3', 'quantity_before' => 'decimal:3', 'quantity_after' => 'decimal:3'];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
