<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = ['code', 'name', 'category', 'unit', 'unit_cost', 'is_active'];

    protected function casts(): array
    {
        return ['unit_cost' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function branchInventories(): HasMany
    {
        return $this->hasMany(BranchInventory::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
