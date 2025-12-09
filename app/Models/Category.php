<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'order',
        'featured_product_id',
        'path',
    ];

    protected $casts = [
        'order' => 'integer',
        'featured_product_id' => 'integer',
    ];

    /**
     * Get all products for this category
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->orderBy('order');
    }

    /**
     * Get the featured product for this category
     */
    public function featuredProduct()
    {
        return $this->belongsTo(Product::class, 'featured_product_id');
    }
}
