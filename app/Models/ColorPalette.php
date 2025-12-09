<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ColorPalette extends Model
{
    protected $fillable = [
        'name',
        'year',
        'order',
        'active',
    ];

    protected $casts = [
        'year' => 'integer',
        'order' => 'integer',
        'active' => 'boolean',
    ];

    /**
     * Get all colors in this palette
     */
    public function colors(): HasMany
    {
        return $this->hasMany(Color::class, 'palette_id')->orderBy('order');
    }
}
