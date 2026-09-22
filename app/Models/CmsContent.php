<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsContent extends Model
{
    protected $fillable = ['key', 'area', 'type', 'label', 'value', 'image_path', 'alt_text', 'status', 'scheduled_at'];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime'];
    }
}
