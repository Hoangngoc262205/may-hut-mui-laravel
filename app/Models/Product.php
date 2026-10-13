<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'brand_id', 'name', 'slug', 'description', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function detail()
    {
        return $this->hasOne(ProductDetail::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasManyThrough(OrderItem::class, ProductVariant::class, 'product_id', 'product_variant_id', 'id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getPrimaryImagePathAttribute(): string
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();
        $image = $images->firstWhere('is_primary', true) ?: $images->first();

        return $this->imagePath($image?->image);
    }

    public function imagePath(?string $path): string
    {
        if (! $path) {
            return 'images/products/hood-default.svg';
        }

        if (file_exists(public_path($path))) {
            return $path;
        }

        if (file_exists(public_path('storage/' . $path))) {
            return 'storage/' . $path;
        }

        if (str_starts_with($path, 'storage/') && file_exists(public_path($path))) {
            return $path;
        }

        return 'images/products/hood-default.svg';
    }

    public function getMinPriceAttribute(): int
    {
        if (isset($this->variants_min_price)) {
            return (int) $this->variants_min_price;
        }

        $price = $this->relationLoaded('variants')
            ? $this->variants->where('status', 'active')->min('price')
            : $this->variants()->where('status', 'active')->min('price');

        return (int) ($price ?: 0);
    }

    public function getInStockAttribute(): bool
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->where('status', 'active')->sum('stock') > 0;
        }

        return $this->variants()->where('status', 'active')->where('stock', '>', 0)->exists();
    }

    public function getAverageRatingAttribute(): float
    {
        $rating = $this->relationLoaded('reviews')
            ? $this->reviews->where('status', 'approved')->avg('rating')
            : $this->reviews()->where('status', 'approved')->avg('rating');

        return round((float) $rating, 1);
    }
}
