<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchService extends Model
{
    protected $fillable = ['type', 'provider', 'account', 'service_number', 'amount', 'frequency', 'billing_day', 'due_day', 'status', 'contact_name', 'contact_phone', 'notes'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
