<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function isCritical(): bool
    {
        return $this->quantity > 0 && $this->quantity <= 10;
    }

    public function isEmpty(): bool
    {
        return $this->quantity <= 0;
    }

    public function isAvailable(): bool
    {
        return $this->quantity > 10;
    }
}