<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'price',
        'wholesale_price',
        'unit',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-delete cart items when product is deleted
        static::deleting(function ($product) {
            // Remove from all user carts
            \App\Models\Cart::where('product_id', $product->id)->delete();
            
            // Log the deletion
            \Log::info("Product deleted: {$product->name} (ID: {$product->id}). Removed from all carts.");
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function cluster()
    {
        return $this->hasOne(ProductCluster::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getStockStatusAttribute()
    {
        $quantity = $this->stock ? $this->stock->quantity : 0;
        
        if ($quantity > 10) {
            return [
                'status' => 'Tersedia',
                'color' => 'green',
                'badge' => '✅ Tersedia'
            ];
        } elseif ($quantity >= 1 && $quantity <= 10) {
            return [
                'status' => 'Stok Terbatas',
                'color' => 'yellow',
                'badge' => '⚠️ Stok Terbatas'
            ];
        } else {
            return [
                'status' => 'Habis',
                'color' => 'red',
                'badge' => '❌ Stok Habis'
            ];
        }
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedWholesalePriceAttribute()
    {
        return 'Rp ' . number_format($this->wholesale_price, 0, ',', '.');
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('images/products/' . $this->image);
        }
        return asset('images/products/placeholder.jpg');
    }
}