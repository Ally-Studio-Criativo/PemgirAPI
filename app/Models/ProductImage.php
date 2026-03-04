<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'ref',
        'color_name',
        'image_type',
        'in_2027_palette',
        'has_cuff_collar',
        'filename',
        'path',
        'order',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'order' => 'integer',
        'in_2027_palette' => 'boolean',
    ];

    /**
     * Get the product that owns the image
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the color information based on ref
     */
    public function color()
    {
        if (!$this->ref) {
            return null;
        }

        return Color::where('ref', $this->ref)->first();
    }
}
