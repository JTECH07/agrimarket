<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MultitenantProducer;

class Product extends Model
{
    use HasFactory, SoftDeletes, MultitenantProducer;

    protected $fillable = [
        'producer_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'unit',
        'stock_quantity',
        'min_order_quantity',
        'is_available',
        'is_organic',
        'origin',
        'certifications',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_organic' => 'boolean',
        'certifications' => 'array',
    ];

    // Relations
    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessors
    public function getFinalPriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    public function getIsInStockAttribute()
    {
        return $this->stock_quantity > 0;
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                    ->where('stock_quantity', '>', 0);
    }

    public function scopeOrganic($query)
    {
        return $query->where('is_organic', true);
    }
}