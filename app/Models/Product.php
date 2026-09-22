<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'short_name', 'slug', 'sku', 'barcode', 'internal_code', 'description', 'commercial_description',
        'base_price', 'promo_price', 'estimated_cost', 'status', 'image_path', 'estimated_prep_minutes', 'max_per_order', 'is_active', 'display_order',
    ];

    protected function casts(): array
    {
        return ['base_price' => 'decimal:2', 'direct_cost' => 'decimal:2', 'gross_profit' => 'decimal:2', 'margin_percent' => 'decimal:2', 'promo_price' => 'decimal:2', 'estimated_cost' => 'decimal:2', 'tags' => 'array', 'is_active' => 'boolean'];
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class)->withPivot(['price', 'is_available'])->withTimestamps();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withPivot('display_order')->withTimestamps()->orderByPivot('display_order');
    }

    public function modifierGroups(): BelongsToMany
    {
        return $this->belongsToMany(ModifierGroup::class)->withPivot('display_order')->withTimestamps();
    }

    public function recipe(): HasOne
    {
        return $this->hasOne(Recipe::class);
    }
}
