<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'price_snapshot',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute()
    {
        $price = $this->quantity >= 5 ? $this->product->wholesale_price : $this->product->price;
        return $price * $this->quantity;
    }

    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getPriceUsedAttribute()
    {
        return $this->quantity >= 5 ? $this->product->wholesale_price : $this->product->price;
    }

    public function getFormattedPriceUsedAttribute()
    {
        return 'Rp ' . number_format($this->price_used, 0, ',', '.');
    }

    public function hasPriceChanged()
    {
        if (!$this->price_snapshot) {
            return false;
        }
        return $this->price_snapshot != $this->price_used;
    }

    public function getPriceChangeAttribute()
    {
        if (!$this->hasPriceChanged()) {
            return null;
        }

        $difference = $this->price_used - $this->price_snapshot;
        $percentage = ($difference / $this->price_snapshot) * 100;

        return [
            'old_price' => $this->price_snapshot,
            'new_price' => $this->price_used,
            'difference' => $difference,
            'percentage' => round($percentage, 1),
            'type' => $difference > 0 ? 'increase' : 'decrease'
        ];
    }
}