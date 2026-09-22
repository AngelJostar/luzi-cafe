<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = ['code', 'name', 'type', 'amount', 'minimum_order_amount', 'maximum_discount', 'usage_limit', 'starts_at', 'ends_at', 'is_active'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'minimum_order_amount' => 'decimal:2', 'maximum_discount' => 'decimal:2', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_active' => 'boolean'];
    }
}
