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
        'observations',
        'order',
        'is_launch',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'gramatura' => 'integer',
        'order' => 'integer',
        'is_launch' => 'boolean',
    ];

    /**
     * Get the category that owns the product (mantido para compatibilidade)
     * @deprecated Use categories() para relacionamento many-to-many
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all categories for this product (many-to-many)
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product')
            ->withTimestamps();
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
