<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Color extends Model
{
    protected $fillable = [
        'palette_id',
        'ref',
        'name',
        'type',
        'image',
        'order',
    ];

    protected $casts = [
        'palette_id' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the palette that owns the color
     */
    public function palette(): BelongsTo
    {
        return $this->belongsTo(ColorPalette::class, 'palette_id');
    }

    /**
     * Get all products that have this color
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
