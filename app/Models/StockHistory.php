<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity_before',
        'quantity_after',
        'quantity_change',
        'note',
        'user_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'in' => 'green',
            'out' => 'red',
            'adjustment' => 'blue',
            default => 'gray'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'in' => '⬆️',
            'out' => '⬇️',
            'adjustment' => '🔄',
            default => '📝'
        };
    }
}