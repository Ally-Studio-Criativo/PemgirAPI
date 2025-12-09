<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'reference',
        'composition',
        'gramatura',
        'width',
        'yield',
        'order',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'gramatura' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the category that owns the product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all images for this product
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    /**
     * Get all colors for this product
     */
    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class);
    }
}
