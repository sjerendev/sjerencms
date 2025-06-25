<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ProductCategory;
use App\Models\OrderItem;
use App\Models\ProductAddon;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_category_id',
        'name',
        'slug',
        'description',
        'featured_image',
        'product_images',
        'price',
        'download_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'product_images' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addons()
    {
        return $this->hasMany(ProductAddon::class)->orderBy('sort_order');
    }

    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image
            ? Storage::url($this->featured_image)
            : null;
    }

    public function getProductImagesUrlsAttribute()
    {
        return $this->product_images
            ? collect($this->product_images)->map(fn($image) => Storage::url($image))
            : collect([]);
    }
}
